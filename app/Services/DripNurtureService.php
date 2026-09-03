<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Inquiry;
use App\Models\InquiryDripLog;
use App\Models\LeadDripStep;
use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class DripNurtureService
{
    /**
     * Ensure default Day 1, 3, 7, 14 drip steps exist for a company
     */
    public function seedDefaultSteps(Company $company, ?Project $project = null): void
    {
        $defaults = [
            [
                'day_offset' => 1,
                'step_title' => 'Day 1: Welcome & Digital Brochure Download',
                'channel' => 'whatsapp',
                'message_template' => "Hello {customer_name}! 👋\n\nThank you for exploring {project_name} at {company_name}.\n\n📄 Download Official Brochure:\n{brochure_url}\n\nOur property executive {executive_name} is available to answer any questions!",
            ],
            [
                'day_offset' => 3,
                'step_title' => 'Day 3: 360° Walkthrough & Location Advantage',
                'channel' => 'whatsapp',
                'message_template' => "Hi {customer_name}! 🌆\n\nTake an interactive 360° Virtual Walkthrough of {project_name}!\n\nExplore floor layouts, amenities, and prime connectivity features.\n\nReply to this message to schedule a virtual presentation with our team.",
            ],
            [
                'day_offset' => 7,
                'step_title' => 'Day 7: Limited-Time Pricing & Festival Offer',
                'channel' => 'whatsapp',
                'message_template' => "Greetings {customer_name}! 🎁\n\nExclusive Festival Advantage for {project_name}!\n\nBook your unit this week to unlock special pricing and zero processing fees.\n\nContact {executive_name} today to hold your preferred floor!",
            ],
            [
                'day_offset' => 14,
                'step_title' => 'Day 14: Site Visit Invitation & VIP Pickup',
                'channel' => 'whatsapp',
                'message_template' => "Hello {customer_name}! 🏡\n\nWe would love to invite you for an exclusive VIP Site Visit at {project_name}.\n\nComplimentary cab pickup & site walkthrough available.\n\nWhen would be a convenient time for your visit?",
            ],
        ];

        foreach ($defaults as $data) {
            LeadDripStep::firstOrCreate(
                [
                    'company_id' => $company->id,
                    'project_id' => $project?->id,
                    'day_offset' => $data['day_offset'],
                ],
                [
                    'step_title' => $data['step_title'],
                    'channel' => $data['channel'],
                    'message_template' => $data['message_template'],
                    'is_active' => true,
                ]
            );
        }
    }

    /**
     * Enroll an inquiry into active drip steps
     */
    public function enrollInquiry(Inquiry $inquiry): int
    {
        $company = $inquiry->company;
        if (!$company) {
            return 0;
        }

        // Seed defaults if empty
        if (LeadDripStep::where('company_id', $company->id)->count() === 0) {
            $this->seedDefaultSteps($company);
        }

        $activeSteps = LeadDripStep::where('company_id', $company->id)
            ->where(function ($q) use ($inquiry) {
                $q->whereNull('project_id')->orWhere('project_id', $inquiry->project_id);
            })
            ->where('is_active', true)
            ->orderBy('day_offset')
            ->get();

        $enrolledCount = 0;
        foreach ($activeSteps as $step) {
            $scheduledFor = Carbon::parse($inquiry->created_at)->addDays($step->day_offset);

            InquiryDripLog::firstOrCreate(
                [
                    'company_id' => $company->id,
                    'inquiry_id' => $inquiry->id,
                    'lead_drip_step_id' => $step->id,
                ],
                [
                    'scheduled_for' => $scheduledFor,
                    'status' => 'pending',
                ]
            );
            $enrolledCount++;
        }

        return $enrolledCount;
    }

    /**
     * Bulk enroll all existing past inquiries for a company
     */
    public function enrollExistingInquiries(Company $company): int
    {
        $inquiries = Inquiry::where('company_id', $company->id)
            ->whereNotIn('status', ['booked', 'lost'])
            ->get();

        $totalEnrolled = 0;
        foreach ($inquiries as $inquiry) {
            $totalEnrolled += $this->enrollInquiry($inquiry);
        }

        return $totalEnrolled;
    }

    /**
     * Process & dispatch due pending drip logs
     */
    public function processPendingDrips(?int $companyId = null, bool $forceNow = false): array
    {
        $query = InquiryDripLog::with(['inquiry.project', 'inquiry.company', 'inquiry.assignedUser', 'step'])
            ->where('status', 'pending');

        if (!$forceNow) {
            $query->where('scheduled_for', '<=', Carbon::now());
        }

        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        $dueLogs = $query->get();
        $sentCount = 0;
        $failedCount = 0;

        $whatsAppService = app(WhatsAppService::class);

        foreach ($dueLogs as $log) {
            $inquiry = $log->inquiry;
            $step = $log->step;

            // Skip if inquiry is already booked or lost
            if (in_array($inquiry->status, ['booked', 'lost'])) {
                $log->update([
                    'status' => 'skipped',
                    'last_error' => "Skipped because inquiry status is '{$inquiry->status}'",
                ]);
                continue;
            }

            // Compile template
            $company = $inquiry->company;
            $project = $inquiry->project;
            $executive = $inquiry->assignedUser ? $inquiry->assignedUser->name : ($company ? $company->name : 'Sales Team');
            $brochure = $project->brochures()->latest()->first();
            $brochureUrl = $brochure ? route('public.brochure.download', $brochure->id) : url('/');

            $message = str_replace(
                ['{customer_name}', '{project_name}', '{company_name}', '{brochure_url}', '{executive_name}'],
                [$inquiry->customer_name, $project->name, $company->name ?? 'Real Estate SaaS', $brochureUrl, $executive],
                $step->message_template
            );

            // Dispatch message via WhatsApp
            try {
                $result = $whatsAppService->sendInstantBrochure($inquiry, true);

                if ($result['success']) {
                    $log->update([
                        'status' => 'sent',
                        'sent_at' => Carbon::now(),
                        'sent_message' => $message,
                    ]);
                    $sentCount++;
                } else {
                    $log->update([
                        'status' => 'failed',
                        'last_error' => $result['message'] ?? 'Dispatch failed',
                    ]);
                    $failedCount++;
                }
            } catch (\Exception $e) {
                Log::error("Drip Dispatch Error for Log #{$log->id}: " . $e->getMessage());
                $log->update([
                    'status' => 'failed',
                    'last_error' => $e->getMessage(),
                ]);
                $failedCount++;
            }
        }

        return [
            'total_processed' => $dueLogs->count(),
            'sent' => $sentCount,
            'failed' => $failedCount,
        ];
    }
}
