<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'pin')) {
                $table->string('pin', 20)->nullable()->after('password');
            }

            if (!Schema::hasColumn('users', 'pin_state')) {
                $table->boolean('pin_state')->default(false)->after('pin');
            }

            if (!Schema::hasColumn('users', 'pin_enabled_at')) {
                $table->timestamp('pin_enabled_at')->nullable()->after('pin_state');
            }

            if (!Schema::hasColumn('users', 'pin_failed_attempts')) {
                $table->unsignedInteger('pin_failed_attempts')->default(0)->after('pin_enabled_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = [];

            foreach (['pin', 'pin_state', 'pin_enabled_at', 'pin_failed_attempts'] as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $columns[] = $column;
                }
            }

            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};