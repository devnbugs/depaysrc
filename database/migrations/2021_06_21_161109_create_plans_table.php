<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name', 191);
            $table->decimal('min_amont', 28, 8);
            $table->decimal('max_amont', 28, 8);
            $table->integer('total_return');
            $table->boolean('interest_type')->comment('1=>Percent, 0=>Fixed');
            $table->decimal('interest_amount', 28, 8);
            $table->boolean('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('plans');
    }
}
