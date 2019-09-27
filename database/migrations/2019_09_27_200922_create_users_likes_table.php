<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersFavoritesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_likes', function (Blueprint $table) {
            $table->integer('id_user')->unsigned();
            $table->integer('id_movie')->unsigned();
            $table->foreign('id_user')->references('id_user')->on('users');
            $table->foreign('id_movie')->references('id_movie')->on('movies');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users_likes', function(Blueprint $table) {
            $table -> dropForeign('users_likes_id_movie_foreign');
            $table -> dropForeign('users_likes_id_user_foreign');
        });
        Schema::dropIfExists('users_likes');
    }
}
