<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Faker\Generator as Faker;

$factory->define(App\Client::class, function (Faker $faker) {
    $faker = \Faker\Factory::create('ru_RU');
    return [
        'name' => $faker->firstName . ' ' . $faker->lastName,
        'birth_date' => $faker->date(),
        'client_type' => $faker->numberBetween(1, 3),
        'comment' => $faker->text(),
    ];
});
