<?php

namespace App\Imports;

use App\Models\Inquiry;
use App\Models\ProjectUnitOption;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class InquiriesImport implements ToCollection, WithHeadingRow
{
    protected int $projectId;
    protected int $companyId;
    protected ?int $defaultAssignedTo;
    protected bool $skipDuplicates;
    protected bool $autoScore;
    protected bool $autoAllocate;

    public int $importedCount = 0;
    public int $skippedDuplicatesCount = 0;
    public int $failedRowsCount = 0;
    public array $errors = [];
    protected array $unitOptionsMap = [];

    public function __construct(
        int $projectId,
        int $companyId,
        ?int $defaultAssignedTo = null,
        bool $skipDuplicates = true,
        bool $autoScore = true,
        bool $autoAllocate = false
    ) {
        $this->projectId = $projectId;
        $this->companyId = $companyId;
        $this->defaultAssignedTo = $defaultAssignedTo;
        $this->skipDuplicates = $skipDuplicates;
        $this->autoScore = $autoScore;
        $this->autoAllocate = $autoAllocate;

        // Pre-fetch unit options for fast matching
        $this->unitOptionsMap = ProjectUnitOption::where('project_id', $projectId)
            ->pluck('id', 'option_name')
            ->mapWithKeys(function ($id, $name) {
                return [strtolower(trim($name)) => $id];
            })
            ->toArray();
    }

    /**
     * Process imported rows collection
     */
    public function collection(Collection $rows)
    {
        $rowIndex = 1; // 1-based data row counter (excluding header)

        foreach ($rows as $row) {
            $rowIndex++;

            // Normalize row keys to lowercase without special characters
            $data = [];
            foreach ($row as $key => $val) {
                $cleanKey = strtolower(trim((string)$key));
                $cleanKey = preg_replace('/[^a-z0-9_]/', '_', $cleanKey);
                $cleanKey = preg_replace('/_+/', '_', $cleanKey);
                $cleanKey = trim($cleanKey, '_');
                $data[$cleanKey] = is_string($val) ? trim($val) : $val;
            }

            // Extract Name
            $name = $data['customer_name'] 
                ?? $data['customername'] 
                ?? $data['name'] 
                ?? $data['customer'] 
                ?? $data['full_name'] 
                ?? $data['fullname'] 
                ?? $data['client_name'] 
                ?? $data['lead_name'] 
                ?? $data['client'] 
                ?? null;

            // Extract Phone
            $phone = $data['phone'] 
                ?? $data['phonenumber'] 
                ?? $data['phone_number'] 
                ?? $data['mobile'] 
                ?? $data['mobilenumber'] 
                ?? $data['mobile_number'] 
                ?? $data['contact'] 
                ?? $data['contact_number'] 
                ?? $data['whatsapp'] 
                ?? $data['tel'] 
                ?? null;

            // Extract Email
            $email = $data['email'] 
                ?? $data['emailaddress'] 
                ?? $data['email_address'] 
                ?? $data['mail'] 
                ?? null;

            // Extract Budget
            $budgetRaw = $data['budget'] 
                ?? $data['budget_inr'] 
                ?? $data['price'] 
                ?? $data['amount'] 
                ?? $data['budget_range'] 
                ?? null;

            // Extract Unit / Property Type
            $unitType = $data['unit_property_type'] 
                ?? $data['unit_type'] 
                ?? $data['unittype'] 
                ?? $data['flat_type'] 
                ?? $data['flattype'] 
                ?? $data['property_type'] 
                ?? $data['bhk'] 
                ?? $data['type'] 
                ?? $data['requirement'] 
                ?? null;

            // Extract Message / Notes
            $message = $data['message'] 
                ?? $data['notes'] 
                ?? $data['remarks'] 
                ?? $data['query'] 
                ?? $data['requirement_details'] 
                ?? null;

            // Extract Description
            $description = $data['description'] ?? $data['details'] ?? null;

            // Extract Status & normalize to valid database enum ('new','contacted','interested','site_visit','booked','lost')
            $statusRaw = $data['status'] ?? $data['lead_status'] ?? 'new';
            $status = $this->normalizeStatus($statusRaw);

            // Skip completely blank rows (common in Excel files)
            if (empty($name) && empty($phone) && empty($email) && empty($budgetRaw) && empty($message)) {
                continue;
            }

            // Validate phone presence
            if (empty($phone)) {
                $this->failedRowsCount++;
                $this->errors[] = "Row {$rowIndex}: Missing phone number for customer '{$name}'.";
                continue;
            }

            $phoneStr = (string)$phone;
            $digitsOnly = preg_replace('/[^0-9]/', '', $phoneStr);
            if (strlen($digitsOnly) < 7) {
                $this->failedRowsCount++;
                $this->errors[] = "Row {$rowIndex}: Invalid phone number '{$phoneStr}'.";
                continue;
            }

            // Check duplicate phone if skipDuplicates is active
            if ($this->skipDuplicates && Inquiry::isPhoneDuplicateForProject($this->projectId, $phoneStr)) {
                $this->skippedDuplicatesCount++;
                continue;
            }

            // Parse budget cleanly (handling Lakhs, Crores, commas, and currency symbols)
            $budget = $this->parseBudget($budgetRaw);

            // Match unit option ID if matching name exists in project
            $selectedUnitOptionId = null;
            if (!empty($unitType)) {
                $normUnit = strtolower(trim((string)$unitType));
                $selectedUnitOptionId = $this->unitOptionsMap[$normUnit] ?? null;
            }

            // Create inquiry record
            $inquiry = Inquiry::create([
                'company_id' => $this->companyId,
                'project_id' => $this->projectId,
                'customer_name' => $name ? (string)$name : ('Lead ' . ($this->importedCount + 1)),
                'phone' => $phoneStr,
                'email' => filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : null,
                'budget' => $budget,
                'flat_type' => $unitType ? (string)$unitType : null,
                'selected_unit_option_id' => $selectedUnitOptionId,
                'message' => $message ? (string)$message : null,
                'description' => $description ? (string)$description : null,
                'status' => $status,
                'source' => 'excel_import',
                'assigned_to' => $this->defaultAssignedTo,
            ]);

            // Calculate AI Intent Score & Grade
            if ($this->autoScore) {
                try {
                    app(\App\Services\LeadScoringService::class)->evaluateAndUpdate($inquiry);
                } catch (\Throwable $e) {
                    // Fail gracefully on AI scoring
                }
            }

            // Allocate via Round-Robin if requested and unassigned
            if ($this->autoAllocate && !$inquiry->assigned_to) {
                try {
                    app(\App\Services\LeadAllocationService::class)->allocateInquiry($inquiry);
                } catch (\Throwable $e) {
                    // Fail gracefully on allocation
                }
            }

            $this->importedCount++;
        }
    }

    /**
     * Clean and parse budget strings into decimal numbers.
     * Supports formats: "7500000", "75,00,000", "₹75 Lakhs", "1.2 Cr", "50k", etc.
     */
    protected function parseBudget($val): ?float
    {
        if (empty($val)) {
            return null;
        }

        $str = strtolower(trim((string)$val));

        // Handle Crores: "1.5 Cr", "1.5 Crore", "1.5 Crores"
        if (preg_match('/([\d.]+)\s*(cr|crore|crores)/', $str, $matches)) {
            return (float)$matches[1] * 10000000;
        }

        // Handle Lakhs: "75 Lakh", "75 Lakhs", "75 Lac", "75 Lacs", "75 L"
        if (preg_match('/([\d.]+)\s*(lakh|lakhs|lac|lacs|\bl\b)/', $str, $matches)) {
            return (float)$matches[1] * 100000;
        }

        // Handle Thousands: "50k"
        if (preg_match('/([\d.]+)\s*k/', $str, $matches)) {
            return (float)$matches[1] * 1000;
        }

        // Remove non-digit, non-decimal characters
        $clean = preg_replace('/[^\d.]/', '', $str);
        if (is_numeric($clean)) {
            return (float)$clean;
        }

        return null;
    }

    /**
     * Map arbitrary status text from Excel to valid database enum values:
     * ['new', 'contacted', 'interested', 'site_visit', 'booked', 'lost']
     */
    protected function normalizeStatus($statusRaw): string
    {
        if (empty($statusRaw)) {
            return 'new';
        }

        $s = strtolower(trim((string)$statusRaw));
        $s = str_replace(['-', ' '], '_', $s);

        return match ($s) {
            'new', 'new_lead', 'fresh', 'open' => 'new',
            'contacted', 'called', 'connected', 'in_progress', 'follow_up' => 'contacted',
            'interested', 'qualified', 'warm', 'hot', 'negotiation', 'discussion' => 'interested',
            'site_visit', 'visit', 'site_visit_scheduled', 'visit_scheduled' => 'site_visit',
            'booked', 'closed', 'won', 'sold', 'converted' => 'booked',
            'lost', 'rejected', 'dropped', 'uninterested', 'not_interested', 'cold', 'junk' => 'lost',
            default => 'new',
        };
    }
}
