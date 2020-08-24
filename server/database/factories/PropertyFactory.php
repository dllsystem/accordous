<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model\Property;
use Faker\Generator as Faker;

$factory->define(Property::class, function (Faker $faker) {
    return [
        'address_zipcode' => $faker->numerify('########'),
        'address_street' => $faker->streetName,
        'address_number' => $faker->buildingNumber,
        'address_complement' => $faker->randomElement([null, $faker->cityPrefix]),
        'address_neighborhood' => $faker->region,
        'address_city' => $faker->city,
        'address_state' => $faker->state,
        'owner_name' => $faker->name,
        'owner_email' => $faker->email,
        'is_rent' => false,
    ];
});
