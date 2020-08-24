<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model\Contract;
use App\Model\Property;
use Faker\Generator as Faker;

$factory->define(Contract::class, function (Faker $faker) {

    $documentType = $faker->randomElement(['cpf', 'cnpj']);
    if ($documentType == 'cpf') {
        $document = $faker->cpf(false);
    } else {
        $document = $faker->cnpj(false);
    }

    return [
        'tenant_document_type' => $documentType,
        'tenant_document' => $document,
        'tenant_name' => $faker->name,
        'tenant_email' => $faker->email,
    ];
});
