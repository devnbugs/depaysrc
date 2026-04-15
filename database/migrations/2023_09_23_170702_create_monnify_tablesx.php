<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMonnifyTablesx extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
        public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->json('bankNames')->nullable();
            $table->json('accountNumbers')->nullable();
            $table->json('accountNames')->nullable();
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('bankNames');
            $table->dropColumn('accountNumbers');
            $table->dropColumn('accountNames');
        });
    }
}
