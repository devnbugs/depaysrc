<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('general_settings') && ! Schema::hasColumn('general_settings', 'virtual_card_settings')) {
            Schema::table('general_settings', function (Blueprint $table) {
                $table->json('virtual_card_settings')->nullable()->after('deposit_checkout_settings');
            });
        }

        if (Schema::hasTable('virtual_cards')) {
            Schema::table('virtual_cards', function (Blueprint $table) {
                if (! Schema::hasColumn('virtual_cards', 'provider')) {
                    $table->string('provider')->nullable()->after('reference');
                }

                if (! Schema::hasColumn('virtual_cards', 'provider_reference')) {
                    $table->string('provider_reference')->nullable()->after('provider');
                }

                if (! Schema::hasColumn('virtual_cards', 'customer_id')) {
                    $table->string('customer_id')->nullable()->after('name_on_card');
                }

                if (! Schema::hasColumn('virtual_cards', 'pan')) {
                    $table->text('pan')->nullable()->after('customer_id');
                }

                if (! Schema::hasColumn('virtual_cards', 'cvv2')) {
                    $table->text('cvv2')->nullable()->after('pan');
                }

                if (! Schema::hasColumn('virtual_cards', 'card_sequence_number')) {
                    $table->string('card_sequence_number', 12)->nullable()->after('cvv2');
                }

                if (! Schema::hasColumn('virtual_cards', 'expiry_date')) {
                    $table->string('expiry_date', 10)->nullable()->after('card_sequence_number');
                }

                if (! Schema::hasColumn('virtual_cards', 'currency')) {
                    $table->string('currency', 10)->nullable()->after('expiry_date');
                }

                if (! Schema::hasColumn('virtual_cards', 'account_id')) {
                    $table->string('account_id')->nullable()->after('currency');
                }

                if (! Schema::hasColumn('virtual_cards', 'account_type')) {
                    $table->string('account_type', 10)->nullable()->after('account_id');
                }

                if (! Schema::hasColumn('virtual_cards', 'available_balance')) {
                    $table->decimal('available_balance', 18, 2)->nullable()->after('account_type');
                }

                if (! Schema::hasColumn('virtual_cards', 'ledger_balance')) {
                    $table->decimal('ledger_balance', 18, 2)->nullable()->after('available_balance');
                }

                if (! Schema::hasColumn('virtual_cards', 'blocked_at')) {
                    $table->timestamp('blocked_at')->nullable()->after('ledger_balance');
                }

                if (! Schema::hasColumn('virtual_cards', 'last_synced_at')) {
                    $table->timestamp('last_synced_at')->nullable()->after('blocked_at');
                }

                if (! Schema::hasColumn('virtual_cards', 'provider_payload')) {
                    $table->json('provider_payload')->nullable()->after('last_synced_at');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('general_settings') && Schema::hasColumn('general_settings', 'virtual_card_settings')) {
            Schema::table('general_settings', function (Blueprint $table) {
                $table->dropColumn('virtual_card_settings');
            });
        }

        if (Schema::hasTable('virtual_cards')) {
            Schema::table('virtual_cards', function (Blueprint $table) {
                $columns = [
                    'provider',
                    'provider_reference',
                    'customer_id',
                    'pan',
                    'cvv2',
                    'card_sequence_number',
                    'expiry_date',
                    'currency',
                    'account_id',
                    'account_type',
                    'available_balance',
                    'ledger_balance',
                    'blocked_at',
                    'last_synced_at',
                    'provider_payload',
                ];

                $existing = array_values(array_filter($columns, fn ($column) => Schema::hasColumn('virtual_cards', $column)));

                if ($existing !== []) {
                    $table->dropColumn($existing);
                }
            });
        }
    }
};
