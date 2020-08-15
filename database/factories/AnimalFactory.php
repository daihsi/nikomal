<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use App\Post;
use App\Animal;
use Faker\Generator as Faker;

$factory->define(Animal::class, function (Faker $faker) {

    $i = rand(1,6);
    $animals_name = array_rand(config('animals.animals'.$i), 1);

    return [
        'name' => $animals_name,
    ];
});
