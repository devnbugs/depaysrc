<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvestmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('investments', function (Blueprint $table) {
            $table->id();
            $table->integer('plan_id');
            $table->integer('user_id');
            $table->decimal('amount', 28, 8);
            $table->boolean('interest_type')->comment('0=>Fixed, 1=>Percent');
            $table->decimal('interest_amount', 28, 8);
            $table->integer('total_return');
            $table->integer('total_paid');
            $table->dateTime('next_return_date');
            $table->tinyInteger('status')->comment('0=>Running, 1=>Completed');
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
        Schema::dropIfExists('investments');
    }
}
