<?php

namespace App\Console\Commands;

use App\Exports\InquiriesExport;
use Illuminate\Console\Command;

class TestInquiryExport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:inquiry-export';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test inquiry export functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing Inquiry Export Functionality...');

        // Test export class instantiation
        $export = new InquiriesExport();
        $this->info('✓ InquiriesExport class instantiated successfully');

        // Test collection method
        $collection = $export->collection();
        $this->info('✓ Collection method works: ' . $collection->count() . ' inquiries found');

        // Test headings
        $headings = $export->headings();
        $this->info('✓ Headings defined: ' . count($headings) . ' columns');

        $this->info('Inquiry export test completed successfully!');
    }
}
