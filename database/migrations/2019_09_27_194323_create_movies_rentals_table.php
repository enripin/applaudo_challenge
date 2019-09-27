<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMoviesRentalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movies_rentals', function (Blueprint $table) {
            $table->increments('id_rental')->unsigned();
            $table->dateTime('rent_date');
            $table->dateTime('return_date');
            $table->char('state',1)->default('p');//States can be p (pending), r (returned)
            $table->decimal('payment',8,2);
            $table->integer('id_movie')->unsigned();
            $table->integer('id_user')->unsigned();
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
        Schema::table('movies_rentals', function(Blueprint $table) {
            $table -> dropForeign('movies_rentals_id_movie_foreign');
            $table -> dropForeign('movies_rentals_id_user_foreign');
        });
        Schema::dropIfExists('movies_rentals');
    }
}
