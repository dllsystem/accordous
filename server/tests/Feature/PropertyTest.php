<?php

namespace Tests\Feature;

use App\Model\Property;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

class PropertyTest extends TestCase
{
    //use DatabaseMigrations;
    use RefreshDatabase;

    /**
     * Pegar lista dos imóveis
     *
     * @return void
     */
    public function test_property_api_list_route()
    {
        factory(Property::class, 5)->create();
        $response = $this->getJson('api/property');
        $response->assertStatus(200);
    }

    public function test_property_store_validation()
    {
        $this->checkPropertyValidation('owner_name', null ); // -> 'required'
        $this->checkPropertyValidation('owner_name', 'aa' ); // -> 'min:3'
        $this->checkPropertyValidation('owner_name', Str::random(300) ); // -> 'max:255'

        $this->checkPropertyValidation('owner_email', null ); // -> 'required'
        $this->checkPropertyValidation('owner_email', 'aa@aa' ); // -> 'email'

        $this->checkPropertyValidation('address_street', null ); // -> 'required'
        $this->checkPropertyValidation('address_street', 'aa' ); // -> 'min:3'
        $this->checkPropertyValidation('address_street', Str::random(300) ); // -> 'max:255'

        $this->checkPropertyValidation('address_neighborhood', null ); // -> 'required'
        $this->checkPropertyValidation('address_neighborhood', Str::random(300) ); // -> 'max:255'

        $this->checkPropertyValidation('address_city', null ); // -> 'required'
        $this->checkPropertyValidation('address_city', 'aa' ); // -> 'min:3'
        $this->checkPropertyValidation('address_city', Str::random(300) ); // -> 'max:255'

        $this->checkPropertyValidation('address_state', null ); // -> 'required'
        $this->checkPropertyValidation('address_state', 'a' ); // -> 'min:2'
        $this->checkPropertyValidation('address_state', Str::random(300) ); // -> 'max:255'
    }

    public function test_property_api_store_route()
    {
        $property = factory(Property::class)->make();
        $response = $this->postJson('api/property', $property->toArray() );
        $response->assertStatus(201);
    }

    private function checkPropertyValidation($field, $value)
    {
        $property = factory(Property::class)->make([$field => $value]);
        $response = $this->postJson('api/property', $property->toArray() );
        $response->assertStatus(422)->assertJsonValidationErrors([$field]);
    }

}
