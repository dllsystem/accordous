<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Model\Property::class, rand(50,80))
        ->create()
        ->each(function ($property) {
            if (rand(0,1) == 1) {
            factory(App\Model\Contract::class)->create(['property_id' => $property->id]);
            $property->is_rent = true;
            $property->save();
            }
        });
    }
}
