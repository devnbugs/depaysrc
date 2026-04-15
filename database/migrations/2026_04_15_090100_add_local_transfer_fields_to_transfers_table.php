<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transfers', function (Blueprint $table) {
            $table->string('provider', 40)->nullable()->after('method_id');
            $table->string('provider_reference', 120)->nullable()->after('provider');
            $table->string('bank_name', 191)->nullable()->after('amount');
            $table->string('bank_code', 80)->nullable()->after('bank_name');
            $table->string('account_name', 191)->nullable()->after('bank_code');
            $table->string('account_number', 32)->nullable()->after('account_name');
            $table->string('narration', 191)->nullable()->after('account_number');
            $table->json('meta')->nullable()->after('reason');
        });
    }

    public function down(): void
    {
        Schema::table('transfers', function (Blueprint $table) {
            $table->dropColumn([
                'provider',
                'provider_reference',
                'bank_name',
                'bank_code',
                'account_name',
                'account_number',
                'narration',
                'meta',
            ]);
        });
    }
};
