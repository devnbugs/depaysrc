<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'whatsapp_phone')) {
                $table->string('whatsapp_phone')->nullable()->after('mobile');
            }

            if (! Schema::hasColumn('users', 'mother_maiden_name')) {
                $table->string('mother_maiden_name')->nullable()->after('BVN');
            }

            if (! Schema::hasColumn('users', 'kyc_additional_data')) {
                $table->json('kyc_additional_data')->nullable()->after('mother_maiden_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'whatsapp_phone')) {
                $table->dropColumn('whatsapp_phone');
            }

            if (Schema::hasColumn('users', 'mother_maiden_name')) {
                $table->dropColumn('mother_maiden_name');
            }

            if (Schema::hasColumn('users', 'kyc_additional_data')) {
                $table->dropColumn('kyc_additional_data');
            }
        });
    }
};
