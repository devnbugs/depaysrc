<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('general_settings', function (Blueprint $table) {
            $table->boolean('local_transfer_enabled')->default(false)->after('transferfee');
            $table->boolean('local_transfer_require_pin')->default(true)->after('local_transfer_enabled');
            $table->decimal('local_transfer_min', 18, 2)->nullable()->after('local_transfer_require_pin');
            $table->decimal('local_transfer_max', 18, 2)->nullable()->after('local_transfer_min');
            $table->string('local_transfer_directory_provider', 40)->nullable()->after('local_transfer_max');
            $table->json('local_transfer_resolve_order')->nullable()->after('local_transfer_directory_provider');
            $table->json('local_transfer_transfer_order')->nullable()->after('local_transfer_resolve_order');
            $table->longText('local_transfer_settings')->nullable()->after('local_transfer_transfer_order');
        });
    }

    public function down(): void
    {
        Schema::table('general_settings', function (Blueprint $table) {
            $table->dropColumn([
                'local_transfer_enabled',
                'local_transfer_require_pin',
                'local_transfer_min',
                'local_transfer_max',
                'local_transfer_directory_provider',
                'local_transfer_resolve_order',
                'local_transfer_transfer_order',
                'local_transfer_settings',
            ]);
        });
    }
};
