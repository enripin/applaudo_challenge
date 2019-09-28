<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Faker\Generator as Faker;

$factory->define(\App\Models\Movie::class, function (Faker $faker) {
    return [
        'title' =>  $faker->sentence(5),
        'description'   => $faker->text,
        'rental_price'  => $faker->randomFloat(2, 1, 20),
        'sale_price'  => $faker->randomFloat(2, 5, 100),
        'availability'  => $faker->numberBetween(0, 50)
    ];
});
