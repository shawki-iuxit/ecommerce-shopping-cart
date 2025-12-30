<?php

namespace App\Console\Commands;

use App\Jobs\SendDailySalesReport;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendDailySalesReportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'daily-sales:report {--date= : The date for the sales report (Y-m-d format, defaults to today)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate and send daily sales report email to admin';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $date = $this->option('date');

        // Validate date format if provided
        if ($date) {
            try {
                $reportDate = Carbon::createFromFormat('Y-m-d', $date);
            } catch (\Exception $e) {
                $this->error('Invalid date format. Please use Y-m-d format (e.g., 2023-12-25)');

                return Command::FAILURE;
            }
        } else {
            $reportDate = Carbon::now();
        }

        $this->info("Generating sales report for {$reportDate->format('F j, Y')}...");

        // Dispatch the job
        try {
            SendDailySalesReport::dispatch($reportDate->toDateString());

            $this->info('✅ Daily sales report has been generated and sent successfully!');
            $this->line("📧 Report sent to admin email for date: {$reportDate->format('F j, Y')}");

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("❌ Failed to generate sales report: {$e->getMessage()}");

            return Command::FAILURE;
        }
    }
}
