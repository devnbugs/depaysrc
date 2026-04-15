<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('general_settings', function (Blueprint $table) {
            $table->boolean('bill_payment_enabled')->default(true)->after('kyc_subscription_enabled');
            $table->string('bill_payment_default_provider')->default('budpay')->after('bill_payment_enabled');
            $table->json('bill_payment_service_providers')->nullable()->after('bill_payment_default_provider');
            $table->json('bill_payment_settings')->nullable()->after('bill_payment_service_providers');
            $table->boolean('bill_payment_auto_sync_enabled')->default(true)->after('bill_payment_settings');
            $table->unsignedSmallInteger('bill_payment_auto_sync_hours')->default(8)->after('bill_payment_auto_sync_enabled');
            $table->timestamp('bill_payment_catalog_last_synced_at')->nullable()->after('bill_payment_auto_sync_hours');
        });

        Schema::table('cabletvbundles', function (Blueprint $table) {
            $table->string('provider')->nullable()->after('image');
        });

        Schema::table('powers', function (Blueprint $table) {
            $table->string('provider')->nullable()->after('type');
        });
    }

    public function down(): void
    {
        Schema::table('powers', function (Blueprint $table) {
            $table->dropColumn('provider');
        });

        Schema::table('cabletvbundles', function (Blueprint $table) {
            $table->dropColumn('provider');
        });

        Schema::table('general_settings', function (Blueprint $table) {
            $table->dropColumn([
                'bill_payment_enabled',
                'bill_payment_default_provider',
                'bill_payment_service_providers',
                'bill_payment_settings',
                'bill_payment_auto_sync_enabled',
                'bill_payment_auto_sync_hours',
                'bill_payment_catalog_last_synced_at',
            ]);
        });
    }
};
