<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'kora_account_reference')) {
                $table->string('kora_account_reference')->nullable()->after('budpay_verified');
            }

            if (! Schema::hasColumn('users', 'kora_virtual_account_id')) {
                $table->string('kora_virtual_account_id')->nullable()->after('kora_account_reference');
            }

            if (! Schema::hasColumn('users', 'kora_bank_code')) {
                $table->string('kora_bank_code')->nullable()->after('kora_virtual_account_id');
            }

            if (! Schema::hasColumn('users', 'kora_linked')) {
                $table->boolean('kora_linked')->default(false)->after('kora_bank_code');
            }

            if (! Schema::hasColumn('users', 'kora_verified')) {
                $table->unsignedTinyInteger('kora_verified')->default(0)->after('kora_linked');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            foreach ([
                'kora_account_reference',
                'kora_virtual_account_id',
                'kora_bank_code',
                'kora_linked',
                'kora_verified',
            ] as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
