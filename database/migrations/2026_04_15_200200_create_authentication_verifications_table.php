<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuthenticationVerificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('authentication_verifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->enum('type', ['pin', 'two_factor', 'passkey'])->comment('Type of authentication');
            $table->enum('context', ['login', 'payment', 'purchase', 'withdrawal', 'transfer'])->comment('Where verification was needed');
            $table->enum('status', ['pending', 'verified', 'failed'])->default('pending');
            $table->string('reference_id')->nullable()->comment('Reference to transaction or login');
            $table->datetime('attempted_at')->default(now());
            $table->datetime('verified_at')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['user_id', 'type', 'status']);
            $table->index('verified_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('authentication_verifications');
    }
}
