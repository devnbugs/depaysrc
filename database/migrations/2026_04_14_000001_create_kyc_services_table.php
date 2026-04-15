<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('kyc_services', function (Blueprint $table) {
            $table->id();
            $table->string('service_id')->unique();
            $table->string('name');
            $table->text('description');
            $table->enum('provider', ['korapay', 'interswitch']);
            $table->decimal('price', 15, 2);
            $table->boolean('enabled')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kyc_services');
    }
};
