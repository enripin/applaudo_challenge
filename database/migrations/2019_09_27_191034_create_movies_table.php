<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMoviesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movies', function (Blueprint $table) {
            $table->increments('id_movie')->unsigned();
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('rental_price',4,2);//Rental price per day
            $table->decimal('sale_price',4,2);
            $table->integer('available')->default(1);//If the movie will be available for clients (0-available, 1-unavailable)
            $table->integer('stock');
            $table->string('image')->nullable();
            $table->timestamps();
            $table->index('title');//For speeding up searches
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('movies', function(Blueprint $table) {
            $table -> dropIndex('movies_title_index');
        });
        Schema::dropIfExists('movies');
    }
}
