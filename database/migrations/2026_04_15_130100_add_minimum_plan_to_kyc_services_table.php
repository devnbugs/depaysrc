<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kyc_services', function (Blueprint $table) {
            if (! Schema::hasColumn('kyc_services', 'minimum_plan')) {
                $table->string('minimum_plan')->default('kyc_basic')->after('enabled');
            }
        });
    }

    public function down(): void
    {
        Schema::table('kyc_services', function (Blueprint $table) {
            if (Schema::hasColumn('kyc_services', 'minimum_plan')) {
                $table->dropColumn('minimum_plan');
            }
        });
    }
};
