<?php

namespace App\Services;

use App\Models\Inquiry;

class LeadScoringService
{
    /**
     * Calculate AI Lead Intent Score (0–100) & Grade (hot, warm, cold)
     */
    public function calculateScore(Inquiry $inquiry): array
    {
        $score = 0;
        $breakdown = [];

        // 1. Site Visit or Specific Flat/Unit Requested (+30 pts)
        if ($inquiry->status === 'site_visit' || $inquiry->project_unit_id || $inquiry->selected_unit_option_id) {
            $pts = $inquiry->status === 'site_visit' ? 30 : 20;
            $score += $pts;
            $breakdown[] = [
                'factor' => 'Site Visit / Specific Flat Selected',
                'points' => $pts,
                'type' => 'positive',
            ];
        }

        // 2. Budget Matching (+25 pts)
        if ($inquiry->budget && $inquiry->budget > 0) {
            $score += 25;
            $breakdown[] = [
                'factor' => 'Budget Provided ($' . number_format($inquiry->budget) . ')',
                'points' => 25,
                'type' => 'positive',
            ];
        }

        // 3. Contact Details Completeness (+20 pts)
        if (!empty($inquiry->phone) && !empty($inquiry->email)) {
            $score += 20;
            $breakdown[] = [
                'factor' => 'Verified Phone & Email Provided',
                'points' => 20,
                'type' => 'positive',
            ];
        } else if (!empty($inquiry->phone)) {
            $score += 10;
            $breakdown[] = [
                'factor' => 'Phone Number Provided',
                'points' => 10,
                'type' => 'positive',
            ];
        }

        // 4. WhatsApp Delivered / Engaged (+15 pts)
        if ($inquiry->whatsapp_sent_at) {
            $score += 15;
            $breakdown[] = [
                'factor' => 'Automated WhatsApp Brochure Delivered',
                'points' => 15,
                'type' => 'positive',
            ];
        }

        // 5. Message / Requirement Detail (+10 pts)
        if (!empty($inquiry->message) && strlen($inquiry->message) > 10) {
            $score += 10;
            $breakdown[] = [
                'factor' => 'Custom Requirement Notes Provided',
                'points' => 10,
                'type' => 'positive',
            ];
        }

        // Cap score between 0 and 100
        $finalScore = min(100, max(0, $score));

        // Determine Lead Grade: HOT (70+), WARM (40-69), COLD (<40)
        $grade = 'cold';
        if ($finalScore >= 70) {
            $grade = 'hot';
        } elseif ($finalScore >= 40) {
            $grade = 'warm';
        }

        return [
            'score' => $finalScore,
            'grade' => $grade,
            'breakdown' => $breakdown,
        ];
    }

    /**
     * Evaluate and update inquiry score & grade in database
     */
    public function evaluateAndUpdate(Inquiry $inquiry): Inquiry
    {
        $evaluation = $this->calculateScore($inquiry);

        $inquiry->update([
            'lead_score' => $evaluation['score'],
            'lead_grade' => $evaluation['grade'],
            'score_breakdown' => $evaluation['breakdown'],
        ]);

        return $inquiry;
    }
}
