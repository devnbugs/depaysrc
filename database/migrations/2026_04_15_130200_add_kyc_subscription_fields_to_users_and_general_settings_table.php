<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'kyc_pending_plan')) {
                $table->string('kyc_pending_plan')->nullable()->after('kyc_plan');
            }
            if (! Schema::hasColumn('users', 'kyc_paystack_reference')) {
                $table->string('kyc_paystack_reference')->nullable()->after('kyc_monthly_spent');
            }
            if (! Schema::hasColumn('users', 'kyc_paystack_plan_code')) {
                $table->string('kyc_paystack_plan_code')->nullable()->after('kyc_paystack_reference');
            }
            if (! Schema::hasColumn('users', 'kyc_paystack_subscription_code')) {
                $table->string('kyc_paystack_subscription_code')->nullable()->after('kyc_paystack_plan_code');
            }
            if (! Schema::hasColumn('users', 'kyc_paystack_email_token')) {
                $table->string('kyc_paystack_email_token')->nullable()->after('kyc_paystack_subscription_code');
            }
            if (! Schema::hasColumn('users', 'kyc_subscription_status')) {
                $table->string('kyc_subscription_status')->nullable()->after('kyc_paystack_email_token');
            }
            if (! Schema::hasColumn('users', 'kyc_subscription_channel')) {
                $table->string('kyc_subscription_channel')->nullable()->after('kyc_subscription_status');
            }
            if (! Schema::hasColumn('users', 'kyc_subscription_started_at')) {
                $table->dateTime('kyc_subscription_started_at')->nullable()->after('kyc_subscription_channel');
            }
            if (! Schema::hasColumn('users', 'kyc_subscription_next_payment_at')) {
                $table->dateTime('kyc_subscription_next_payment_at')->nullable()->after('kyc_subscription_started_at');
            }
            if (! Schema::hasColumn('users', 'kyc_subscription_cancelled_at')) {
                $table->dateTime('kyc_subscription_cancelled_at')->nullable()->after('kyc_subscription_next_payment_at');
            }
            if (! Schema::hasColumn('users', 'kyc_subscription_last_payment_at')) {
                $table->dateTime('kyc_subscription_last_payment_at')->nullable()->after('kyc_subscription_cancelled_at');
            }
            if (! Schema::hasColumn('users', 'kyc_subscription_last_invoice_code')) {
                $table->string('kyc_subscription_last_invoice_code')->nullable()->after('kyc_subscription_last_payment_at');
            }
        });

        Schema::table('general_settings', function (Blueprint $table) {
            if (! Schema::hasColumn('general_settings', 'kyc_subscription_enabled')) {
                $table->boolean('kyc_subscription_enabled')->default(false)->after('local_transfer_settings');
            }
            if (! Schema::hasColumn('general_settings', 'kyc_subscription_settings')) {
                $table->longText('kyc_subscription_settings')->nullable()->after('kyc_subscription_enabled');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'kyc_pending_plan',
                'kyc_paystack_reference',
                'kyc_paystack_plan_code',
                'kyc_paystack_subscription_code',
                'kyc_paystack_email_token',
                'kyc_subscription_status',
                'kyc_subscription_channel',
                'kyc_subscription_started_at',
                'kyc_subscription_next_payment_at',
                'kyc_subscription_cancelled_at',
                'kyc_subscription_last_payment_at',
                'kyc_subscription_last_invoice_code',
            ]);
        });

        Schema::table('general_settings', function (Blueprint $table) {
            $table->dropColumn([
                'kyc_subscription_enabled',
                'kyc_subscription_settings',
            ]);
        });
    }
};
