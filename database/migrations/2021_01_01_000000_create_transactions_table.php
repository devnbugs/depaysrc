<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->decimal('amount', 15, 8)->default(0);
            $table->decimal('post_balance', 15, 8)->default(0);
            $table->decimal('charge', 15, 8)->default(0);
            $table->char('type', 1)->nullable(); // '+' or '-'
            $table->string('trx_type')->nullable(); // Alternative/additional type field
            $table->boolean('ref')->default(0);
            $table->unsignedSmallInteger('level')->nullable();
            $table->unsignedBigInteger('bywho')->nullable();
            $table->text('details')->nullable();
            $table->string('trx')->nullable()->unique()->index();
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
