<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Account;
use Faker\Generator as Faker;

$factory->define(Account::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'birthday' =>$faker->date,
        'address' => $faker->address,
        'position' => $faker->jobTitle,
        'gender' => 1
    ];
});
