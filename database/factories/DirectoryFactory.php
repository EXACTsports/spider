<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Directory;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(Directory::class, function (Faker $faker) {
    return [
        'id' => $faker->numberBetween(1, 9999999),
        'college_id' => $faker->numberBetween(1, 9999999),
        'uuid' => Str::orderedUuid(),
        'url' => config('env.test_site')
    ];
});
