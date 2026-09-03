<?php

namespace App\Services;

use App\Models\Inquiry;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class LeadAllocationService
{
    /**
     * Allocate an inquiry to sales executives using Round-Robin algorithm
     */
    public function allocateInquiry(Inquiry $inquiry): ?User
    {
        $company = $inquiry->company;
        if (!$company || $company->lead_allocation_method === 'manual') {
            return null;
        }

        // Get active sales executives for this company
        $executives = User::where('company_id', $company->id)
            ->orderBy('id')
            ->get();

        if ($executives->isEmpty()) {
            return null;
        }

        $lastAllocatedId = $company->last_allocated_user_id;
        $nextExecutive = null;

        if ($lastAllocatedId) {
            // Find executive immediately after last_allocated_user_id
            $nextExecutive = $executives->firstWhere('id', '>', $lastAllocatedId);
        }

        // If at the end of the array or first time, wrap around to first executive
        if (!$nextExecutive) {
            $nextExecutive = $executives->first();
        }

        // Assign inquiry
        $inquiry->update([
            'assigned_to' => $nextExecutive->id,
            'allocated_at' => Carbon::now(),
        ]);

        // Update company state
        $company->update([
            'last_allocated_user_id' => $nextExecutive->id,
        ]);

        Log::info("Lead #{$inquiry->id} ({$inquiry->customer_name}) auto-allocated to {$nextExecutive->name} via Round-Robin.");

        return $nextExecutive;
    }
}
