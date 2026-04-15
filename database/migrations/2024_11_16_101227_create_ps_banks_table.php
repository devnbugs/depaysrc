<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePsBanksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('psbanks', function (Blueprint $table) {
            $table->id();
			$table->unsignedBigInteger('user_id')->index();
            $table->json('primary')->nullable(); // Store the first provider as JSON
            $table->json('secondary')->nullable(); // Store the second provider as JSON
            $table->timestamps(); // Created_at and updated_at columns
            
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
        Schema::dropIfExists('psbanks');
    }
}
