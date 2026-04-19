<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Onboarding and KYC verification level fields
            if (!Schema::hasColumn('users', 'kyc_verification_level')) {
                $table->tinyInteger('kyc_verification_level')->default(0)->index()->comment('0=None, 1=Basic, 2=Advanced, 3=Premium');
            }
            if (!Schema::hasColumn('users', 'kyc_liveness_verified')) {
                $table->boolean('kyc_liveness_verified')->default(false)->index();
            }
            if (!Schema::hasColumn('users', 'kyc_liveness_verified_at')) {
                $table->timestamp('kyc_liveness_verified_at')->nullable();
            }
            if (!Schema::hasColumn('users', 'kora_liveness_id')) {
                $table->string('kora_liveness_id')->nullable()->index();
            }
            if (!Schema::hasColumn('users', 'kora_liveness_status')) {
                $table->string('kora_liveness_status')->nullable()->comment('pending, completed, failed');
            }
            if (!Schema::hasColumn('users', 'required_kyc_fields_completed')) {
                $table->json('required_kyc_fields_completed')->nullable()->comment('Track which required fields have been completed');
            }
            if (!Schema::hasColumn('users', 'onboarding_step')) {
                $table->string('onboarding_step')->default('personal_info')->comment('personal_info, identity_verification, liveness_check, completed');
            }
            if (!Schema::hasColumn('users', 'transfer_limit')) {
                $table->decimal('transfer_limit', 15, 2)->default(50000)->comment('Daily transfer limit based on verification level');
            }
            if (!Schema::hasColumn('users', 'account_creation_limit')) {
                $table->integer('account_creation_limit')->default(1)->comment('Number of accounts user can create based on level');
            }
            if (!Schema::hasColumn('users', 'deposit_requirement_for_level_3')) {
                $table->decimal('deposit_requirement_for_level_3', 15, 2)->default(400)->comment('Minimum deposit to reach level 3');
            }
            if (!Schema::hasColumn('users', 'total_deposited')) {
                $table->decimal('total_deposited', 15, 2)->default(0)->comment('Track total deposits for level tracking');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columnsToDrop = [
                'kyc_verification_level',
                'kyc_liveness_verified',
                'kyc_liveness_verified_at',
                'kora_liveness_id',
                'kora_liveness_status',
                'required_kyc_fields_completed',
                'onboarding_step',
                'transfer_limit',
                'account_creation_limit',
                'deposit_requirement_for_level_3',
                'total_deposited',
            ];

            foreach ($columnsToDrop as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
