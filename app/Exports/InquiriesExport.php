<?php

namespace App\Exports;

use App\Models\Inquiry;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class InquiriesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $query;

    public function __construct($query = null)
    {
        $this->query = $query;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        if ($this->query) {
            return $this->query->get();
        }

        // For testing or when no authenticated user, return empty collection
        if (!auth()->check()) {
            return collect();
        }

        return Inquiry::with(['assignedUser', 'project'])
            ->where('company_id', auth()->user()->company_id)
            ->get();
    }

    /**
     * Define the headings for the Excel file
     */
    public function headings(): array
    {
        return [
            'ID',
            'Customer Name',
            'Phone',
            'Email',
            'Budget',
            'Unit/Property Type',
            'Message',
            'Description',
            'Status',
            'Assigned To',
            'Project',
            'Created Date',
            'Last Updated'
        ];
    }

    /**
     * Map the data for each row
     */
    public function map($inquiry): array
    {
        return [
            $inquiry->id,
            $inquiry->customer_name,
            $inquiry->phone,
            $inquiry->email,
            $inquiry->budget ? '₹' . number_format($inquiry->budget) : '',
            $inquiry->selectedUnitOption ? $inquiry->selectedUnitOption->option_name : '',
            $inquiry->message,
            $inquiry->description,
            ucfirst($inquiry->status),
            $inquiry->assignedUser ? $inquiry->assignedUser->name : 'Unassigned',
            $inquiry->project ? $inquiry->project->name : '',
            $inquiry->created_at->format('Y-m-d H:i:s'),
            $inquiry->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
