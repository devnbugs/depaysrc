<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('psbank')->nullable();
            $table->string('psacname')->nullable();
            $table->string('psnuban')->nullable();
            $table->string('psid')->nullable();
            $table->string('monnify_token')->nullable();
            $table->string('bankName')->nullable();
            $table->string('accountNumber')->nullable();
            $table->string('accountName')->nullable();
            $table->string('bN1')->nullable();
            $table->string('bN2')->nullable();
            $table->string('bN3')->nullable();
            $table->string('aN1')->nullable();
            $table->string('aN2')->nullable();
            $table->string('aN3')->nullable();
            $table->string('aNo1')->nullable();
            $table->string('aNo2')->nullable();
            $table->string('aNo3')->nullable();
            $table->string('ussd')->nullable();
            $table->string('mobile')->nullable();
            $table->string('NIN')->nullable();
            $table->string('BVN')->nullable();
            $table->string('pin_state')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
