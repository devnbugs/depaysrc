<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kyc_plans', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 15, 2)->default(0);
            $table->decimal('monthly_limit', 15, 2)->default(0);
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('badge')->nullable();
            $table->boolean('enabled')->default(true);
            $table->json('features')->nullable();
            $table->string('paystack_plan_code')->nullable();
            $table->unsignedBigInteger('paystack_plan_id')->nullable();
            $table->string('paystack_plan_name')->nullable();
            $table->string('paystack_interval', 40)->default('monthly');
            $table->string('paystack_currency', 8)->default('NGN');
            $table->unsignedInteger('invoice_limit')->default(0);
            $table->dateTime('paystack_last_synced_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kyc_plans');
    }
};
