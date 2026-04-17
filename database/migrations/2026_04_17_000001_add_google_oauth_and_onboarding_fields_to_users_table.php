<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Basic profile fields used throughout the app (add if missing for fresh installs).
            if (!Schema::hasColumn('users', 'firstname')) {
                $table->string('firstname')->nullable()->after('name');
            }
            if (!Schema::hasColumn('users', 'lastname')) {
                $table->string('lastname')->nullable()->after('firstname');
            }
            if (!Schema::hasColumn('users', 'username')) {
                $table->string('username')->nullable()->after('email');
                $table->unique('username');
            }

            // Google OAuth / Social login fields
            if (!Schema::hasColumn('users', 'oauth_provider')) {
                $table->string('oauth_provider')->nullable()->index()->after('remember_token');
            }
            if (!Schema::hasColumn('users', 'oauth_provider_id')) {
                $table->string('oauth_provider_id')->nullable()->index()->after('oauth_provider');
            }
            if (!Schema::hasColumn('users', 'oauth_avatar')) {
                $table->string('oauth_avatar')->nullable()->after('oauth_provider_id');
            }

            if (!Schema::hasColumn('users', 'onboarding_completed_at')) {
                $table->timestamp('onboarding_completed_at')->nullable()->index()->after('oauth_avatar');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'onboarding_completed_at')) {
                $table->dropColumn('onboarding_completed_at');
            }
            if (Schema::hasColumn('users', 'oauth_avatar')) {
                $table->dropColumn('oauth_avatar');
            }
            if (Schema::hasColumn('users', 'oauth_provider_id')) {
                $table->dropColumn('oauth_provider_id');
            }
            if (Schema::hasColumn('users', 'oauth_provider')) {
                $table->dropColumn('oauth_provider');
            }

            if (Schema::hasColumn('users', 'username')) {
                $table->dropUnique(['username']);
                $table->dropColumn('username');
            }
            if (Schema::hasColumn('users', 'lastname')) {
                $table->dropColumn('lastname');
            }
            if (Schema::hasColumn('users', 'firstname')) {
                $table->dropColumn('firstname');
            }
        });
    }
};

