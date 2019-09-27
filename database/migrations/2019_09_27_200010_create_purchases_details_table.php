<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchasesDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchases_details', function (Blueprint $table) {
            $table->increments('id_detail')->unsigned();
            $table->decimal('unity_price',4,2);
            $table->tinyInteger('discount')->unsigned()->default(0);//Percentage of discount in purchase 0-100
            $table->integer('number')->unsigned()->default(1);
            $table->integer('id_movie')->unsigned();
            $table->foreign('id_movie')->references('id_movie')->on('movies');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchases_details', function(Blueprint $table) {
            $table -> dropForeign('purchases_details_id_movie_foreign');
        });
        Schema::dropIfExists('purchases_details');
    }
}
