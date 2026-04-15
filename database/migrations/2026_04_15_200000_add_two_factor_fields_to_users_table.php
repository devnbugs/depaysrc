<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTwoFactorFieldsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Add 2FA authenticator fields
            $table->string('two_factor_secret')->nullable()->after('pin_state')->comment('Google Authenticator secret key');
            $table->datetime('two_factor_enabled_at')->nullable()->after('two_factor_secret')->comment('Timestamp when 2FA was enabled');
            $table->boolean('two_factor_enabled')->default(false)->after('two_factor_enabled_at')->comment('Whether 2FA is active');
            
            // Add PIN enhancement fields
            $table->datetime('pin_enabled_at')->nullable()->after('two_factor_enabled')->comment('When PIN was enabled');
            $table->integer('pin_failed_attempts')->default(0)->after('pin_enabled_at')->comment('Failed PIN attempts for rate limiting');
            $table->datetime('pin_locked_until')->nullable()->after('pin_failed_attempts')->comment('PIN lockout time');
            
            // Add passkey support
            $table->boolean('passkey_enabled')->default(false)->after('pin_locked_until')->comment('Whether passkey authentication is enabled');
            $table->longText('passkey_credentials')->nullable()->after('passkey_enabled')->comment('JSON array of passkey credential IDs');
            $table->datetime('passkey_created_at')->nullable()->after('passkey_credentials')->comment('When passkey was first set up');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @var array
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'two_factor_secret',
                'two_factor_enabled_at',
                'two_factor_enabled',
                'pin_enabled_at',
                'pin_failed_attempts',
                'pin_locked_until',
                'passkey_enabled',
                'passkey_credentials',
                'passkey_created_at'
            ]);
        });
    }
}
