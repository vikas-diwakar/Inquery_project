<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InquiriesTemplateExport implements FromArray, WithHeadings, ShouldAutoSize, WithStyles
{
    /**
     * Define the headings for the template Excel file
     */
    public function headings(): array
    {
        return [
            'Customer Name',
            'Phone',
            'Email',
            'Budget',
            'Unit/Property Type',
            'Message',
            'Status'
        ];
    }

    /**
     * Provide sample dummy rows so users can understand the expected format
     */
    public function array(): array
    {
        return [
            [
                'Rahul Sharma',
                '9876543210',
                'rahul.sharma@example.com',
                '7500000',
                '3 BHK',
                'Interested in 3 BHK facing green park, floor 5+',
                'new'
            ],
            [
                'Priya Patel',
                '9823456789',
                'priya.patel@example.com',
                '12500000',
                '4 BHK Penthouse',
                'Looking for ready-to-move unit with 2 car parking spaces',
                'interested'
            ],
            [
                'Amit Verma',
                '9912345678',
                'amit.verma@example.com',
                '5200000',
                '2 BHK',
                'Need details on home loan eligibility and payment schedules',
                'new'
            ],
        ];
    }

    /**
     * Style the header row with Indigo background and bold white text
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['argb' => 'FFFFFFFF'],
                    'size' => 11
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF4F46E5'] // Indigo 600
                ]
            ],
        ];
    }
}
