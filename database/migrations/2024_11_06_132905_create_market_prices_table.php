<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMarketPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('market_prices', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., MTN, GLO, Airtel, 9Mobile
			$table->string('type'); // e.g., Data, Airtime, Neco, Waec
            $table->decimal('price', 10, 2); // e.g., 450,00
            $table->enum('price_change', ['up', 'down'])->default('up'); // Indicates price direction
            $table->timestamps(); // Adds created_at and updated_at columns
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('market_prices');
    }
}
