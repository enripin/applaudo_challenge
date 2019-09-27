<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMoviesPurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movies_purchases', function (Blueprint $table) {
            $table->increments('id_rent')->unsigned();
            $table->dateTime('purchase_date');
            $table->decimal('total_payment',8,2);
            $table->integer('id_user')->unsigned();
            $table->foreign('id_user')->references('id_user')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('movies_purchases', function(Blueprint $table) {
            $table -> dropForeign('movies_purchases_id_user_foreign');
        });
        Schema::dropIfExists('movies_purchases');
    }
}
