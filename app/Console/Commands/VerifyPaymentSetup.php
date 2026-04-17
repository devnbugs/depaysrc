<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class VerifyPaymentSetup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payment:verify-setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verify payment system setup and configuration';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🔍 Payment System Verification\n');

        $checks = [
            'files' => $this->checkFiles(),
            'config' => $this->checkConfiguration(),
            'database' => $this->checkDatabase(),
            'dependencies' => $this->checkDependencies(),
            'routes' => $this->checkRoutes(),
        ];

        $allPassed = collect($checks)->every(fn($check) => $check);

        if ($allPassed) {
            $this->info('\n✅ All checks passed! Payment system is ready.\n');
            return 0;
        } else {
            $this->error('\n❌ Some checks failed. See details above.\n');
            return 1;
        }
    }

    protected function checkFiles(): bool
    {
        $this->line('📁 Checking required files...');
        $files = [
            'app/Jobs/ProcessPaymentRequest.php',
            'app/Http/Controllers/PaymentConfirmationController.php',
            'app/Http/Requests/PaymentConfirmationRequest.php',
            'app/Livewire/PaymentConfirmationModal.php',
            'app/Services/PaymentProcessingManager.php',
            'resources/views/livewire/payment-confirmation-modal.blade.php',
            'resources/js/payment-helper.js',
        ];

        $asyncFiles = [
            'app/Services/AsyncPaymentProcessor.php',
        ];

        $passed = true;
        foreach ($files as $file) {
            if (file_exists(base_path($file))) {
                $this->line("   ✓ {$file}");
            } else {
                $this->error("   ✗ {$file} - NOT FOUND");
                $passed = false;
            }
        }

        $this->line('\n   Optional Async Files:');
        foreach ($asyncFiles as $file) {
            if (file_exists(base_path($file))) {
                $this->line("   ✓ {$file} (Async support enabled)");
            } else {
                $this->line("   ⓘ {$file} (Async support disabled)");
            }
        }

        return $passed;
    }

    protected function checkConfiguration(): bool
    {
        $this->line('\n⚙️  Checking configuration...');

        $processor = config('services.payment.processor', 'queue');
        $this->line("   • Payment Processor: {$processor}");

        if ($processor === 'async') {
            if (class_exists('Spatie\Async\Pool')) {
                $this->line('   ✓ Spatie Async is installed');
                $this->line('   • Processes: ' . config('services.async.processes', 4));
                $this->line('   • Timeout: ' . config('services.async.timeout', 30) . 's');
            } else {
                $this->error('   ✗ Spatie Async configured but not installed');
                $this->line('   Run: composer require spatie/async');
                return false;
            }
        } elseif ($processor === 'queue') {
            $queueConnection = config('queue.default', 'database');
            $this->line("   ✓ Using Queue: {$queueConnection}");

            if ($queueConnection === 'database') {
                $this->line('   • Queue table: jobs');
            }
        } else {
            $this->error("   ✗ Invalid payment processor: {$processor}");
            return false;
        }

        return true;
    }

    protected function checkDatabase(): bool
    {
        $this->line('\n💾 Checking database...');

        if (config('services.payment.processor') !== 'queue') {
            $this->line('   ⓘ Skipped (not using queue)');
            return true;
        }

        try {
            $hasJobsTable = DB::getSchemaBuilder()->hasTable('jobs');
            if ($hasJobsTable) {
                $this->line('   ✓ Jobs table exists');
            } else {
                $this->error('   ✗ Jobs table not found');
                $this->line('   Run: php artisan queue:table && php artisan migrate');
                return false;
            }
        } catch (\Exception $e) {
            $this->error('   ✗ Database connection failed: ' . $e->getMessage());
            return false;
        }

        return true;
    }

    protected function checkDependencies(): bool
    {
        $this->line('\n📦 Checking dependencies...');

        $dependencies = [
            'Laravel Framework' => 'Illuminate\Foundation\Application',
            'Livewire' => 'Livewire\Component',
            'Tailwind CSS' => 'tailwind.config',
        ];

        $passed = true;
        foreach ($dependencies as $name => $check) {
            if (strpos($check, '.') !== false) {
                // Check file
                if (file_exists(base_path($check))) {
                    $this->line("   ✓ {$name}");
                } else {
                    $this->line("   ⓘ {$name} (optional)");
                }
            } else {
                // Check class
                if (class_exists($check)) {
                    $this->line("   ✓ {$name}");
                } else {
                    $this->error("   ✗ {$name}");
                    $passed = false;
                }
            }
        }

        if (class_exists('Spatie\Async\Pool')) {
            $this->line('   ✓ Spatie Async');
        } else {
            $this->line('   ⓘ Spatie Async (optional for async mode)');
        }

        return $passed;
    }

    protected function checkRoutes(): bool
    {
        $this->line('\n🛣️  Checking routes...');

        $requiredRoutes = [
            'user.payment.confirm',
            'user.payment.validate',
        ];

        try {
            $registeredRoutes = [];
            foreach (\Route::getRoutes() as $route) {
                if ($route->getName()) {
                    $registeredRoutes[] = $route->getName();
                }
            }

            $passed = true;
            foreach ($requiredRoutes as $routeName) {
                if (in_array($routeName, $registeredRoutes)) {
                    $this->line("   ✓ {$routeName}");
                } else {
                    $this->error("   ✗ {$routeName} not found");
                    $passed = false;
                }
            }

            return $passed;
        } catch (\Exception $e) {
            $this->error('   ✗ Error checking routes: ' . $e->getMessage());
            return false;
        }
    }
}
