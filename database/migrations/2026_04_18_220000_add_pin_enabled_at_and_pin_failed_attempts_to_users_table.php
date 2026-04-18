<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'pin_enabled_at')) {
                $table->timestamp('pin_enabled_at')->nullable()->after('pin_state');
            }
            if (!Schema::hasColumn('users', 'pin_failed_attempts')) {
                $table->integer('pin_failed_attempts')->default(0)->after('pin_enabled_at');
            }
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'pin_enabled_at')) {
                $table->dropColumn('pin_enabled_at');
            }
            if (Schema::hasColumn('users', 'pin_failed_attempts')) {
                $table->dropColumn('pin_failed_attempts');
            }
        });
    }
};
