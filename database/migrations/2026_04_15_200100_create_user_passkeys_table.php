<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserPasskeysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_passkeys', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('credential_id')->unique();
            $table->longText('public_key');
            $table->string('credential_public_key')->nullable();
            $table->integer('counter')->default(0);
            $table->string('transports')->nullable()->comment('JSON array of transports');
            $table->string('aaguid')->nullable();
            $table->string('name')->nullable()->comment('User-friendly name for this passkey');
            $table->datetime('used_at')->nullable()->comment('Last time this passkey was used');
            $table->datetime('registered_at');
            $table->boolean('backup_eligible')->default(false);
            $table->boolean('backup_state')->default(false);
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_passkeys');
    }
}
