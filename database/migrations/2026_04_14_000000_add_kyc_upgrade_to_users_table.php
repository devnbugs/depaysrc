<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add subscription/upgrade fields
            $table->boolean('is_kyc_upgraded')->default(false)->after('BVN')->comment('User has upgraded to access KYC services');
            $table->string('kyc_plan')->nullable()->after('is_kyc_upgraded')->comment('kyc_basic, kyc_premium, kyc_enterprise');
            $table->dateTime('kyc_upgrade_date')->nullable()->after('kyc_plan')->comment('Date when user upgraded KYC services');
            $table->dateTime('kyc_expiry_date')->nullable()->after('kyc_upgrade_date')->comment('When KYC services expire (null = lifetime)');
            $table->decimal('kyc_monthly_limit', 15, 2)->default(0)->after('kyc_expiry_date')->comment('Monthly spending limit for KYC services');
            $table->decimal('kyc_monthly_spent', 15, 2)->default(0)->after('kyc_monthly_limit')->comment('Amount spent this month on KYC');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_kyc_upgraded');
            $table->dropColumn('kyc_plan');
            $table->dropColumn('kyc_upgrade_date');
            $table->dropColumn('kyc_expiry_date');
            $table->dropColumn('kyc_monthly_limit');
            $table->dropColumn('kyc_monthly_spent');
        });
    }
};
