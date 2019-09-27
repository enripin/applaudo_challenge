<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMoviesReturnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movies_returns', function (Blueprint $table) {
            $table->increments('id_return')->unsigned();
            $table->dateTime('return_date');
            $table->char('status',1)->default('d');//States can be o (on time), d (delayed)
            $table->decimal('penalty',4,2)->default(0);//Recharge over delayed return
            $table->integer('id_rent')->unsigned();
            $table->foreign('id_rent')->references('id_rent')->on('movies_rentals');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('movies_returns', function(Blueprint $table) {
            $table -> dropForeign('movies_returns_id_rent_foreign');
        });
        Schema::dropIfExists('movies_returns');
    }
}
