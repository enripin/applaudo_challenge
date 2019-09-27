<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMoviesLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movies_log', function (Blueprint $table) {
            $table->increments('id_movie_log')->unsigned();
            $table->dateTime('change_date');
            $table->string('prev_title')->nullable();
            $table->string('new_title')->nullable();
            $table->decimal('prev_rental_price',4,2)->nullable();
            $table->decimal('new_rental_price',4,2)->nullable();
            $table->decimal('prev_sale_price',4,2)->nullable();
            $table->decimal('new_sale_price',4,2)->nullable();
            $table->integer('id_movie')->unsigned();
            $table->integer('id_user')->unsigned();//Responsible of the change
            $table->foreign('id_movie')->references('id_movie')->on('movies');
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
        Schema::table('movies_log', function(Blueprint $table) {
            $table -> dropForeign('movies_log_id_movie_foreign');
            $table -> dropForeign('movies_log_id_user_foreign');
        });
        Schema::dropIfExists('movies_log');
    }
}
