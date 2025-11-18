<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Employee;
use App\Services\QrCodeService;

class RegenerateQrCodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'qrcode:regenerate {--force : Force regenerate all QR codes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Regenerate QR codes for all employees';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $qrCodeService = new QrCodeService();
        $force = $this->option('force');

        $employees = Employee::where('is_active', true)->get();
        $this->info("Found {$employees->count()} active employees");

        $bar = $this->output->createProgressBar($employees->count());
        $bar->start();

        $generated = 0;
        $skipped = 0;

        foreach ($employees as $employee) {
            // If force or QR code doesn't exist
            if ($force || !$employee->qrcode_data) {
                try {
                    // Clear old data
                    $employee->update(['qrcode_data' => null]);
                    
                    // Generate new QR code
                    $qrCodeService->getQrCodeUrl($employee);
                    $generated++;
                } catch (\Exception $e) {
                    $this->error("\nError generating QR code for {$employee->employee_code}: {$e->getMessage()}");
                }
            } else {
                $skipped++;
            }
            
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Generated: {$generated} | Skipped: {$skipped}");
        $this->info('QR codes regeneration completed!');

        return Command::SUCCESS;
    }
}
