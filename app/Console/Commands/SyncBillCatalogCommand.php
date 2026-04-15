<?php

namespace App\Console\Commands;

use App\Services\Bills\BillPaymentManager;
use Illuminate\Console\Command;

class SyncBillCatalogCommand extends Command
{
    protected $signature = 'bills:sync-catalog {--force : Ignore the auto-sync toggle and sync anyway}';

    protected $description = 'Refresh airtime, data, TV, and electricity bill catalogs from configured providers.';

    public function handle(BillPaymentManager $billPayments): int
    {
        $result = $billPayments->syncCatalog((bool) $this->option('force'));

        foreach ($result['results'] ?? [] as $service => $payload) {
            $status = strtoupper((string) ($payload['status'] ?? 'unknown'));
            $message = (string) ($payload['message'] ?? '');

            $this->line(sprintf('%s [%s] %s', strtoupper($service), $status, $message));
        }

        return self::SUCCESS;
    }
}
