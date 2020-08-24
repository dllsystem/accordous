<?php

namespace Tests\Feature;

use App\Model\Contract;
use App\Model\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ContractTest extends TestCase
{
    use RefreshDatabase;

    public function test_contract_api_list_route()
    {
        factory(Property::class, 5)->create();
        $response = $this->getJson('api/contract');
        $response->assertStatus(200);
    }

    public function test_store_contract_and_same_property()
    {
        // new property to a new contract
        $property = factory(Property::class)->create();
        $contract = factory(Contract::class)->make(['property_id' => $property->id]);
        $response = $this->postJson('/api/contract', $contract->toArray());
        $response->assertStatus(201);

        // same property to a new contract
        $contract = factory(Contract::class)->make(['property_id' => $property->id]);
        $response = $this->postJson('/api/contract', $contract->toArray());
        $response->assertStatus(422);
    }

    public function test_contract_store_validation()
    {
        $this->checkContractValidation('tenant_document_type', null); // -> required

        $this->checkContractValidation('tenant_document', null); // -> required
        $this->checkContractValidation('tenant_document', '00000000000'); // -> cpf_cnpj

        $this->checkContractValidation('tenant_name', null); // -> required

        $this->checkContractValidation('tenant_email', null); // -> required
        $this->checkContractValidation('tenant_email', 'asd@asd'); // -> email

        $this->checkContractValidation('property_id', null); // -> required
    }

    public function test_contract_destroy_and_tag_is_rent()
    {
        $property = factory(Property::class)->create(['is_rent' => true]);
        $contract = factory(Contract::class)->create(['property_id' => $property->id]);

        $responseDelete = $this->deleteJson("/api/contract/{$contract->id}");
        $responseDelete->assertStatus(204);

        // check "is_rent" tag has been set to false
        $propertyFind = Property::find($property->id);
        if ($propertyFind->is_rent || $propertyFind->is_rent == '1') {
            $this->assertTrue(false);
        }
    }

    private function checkContractValidation($field, $value)
    {
        $property = factory(Property::class)->create();
        $contract = factory(Contract::class)->make([
            'property_id' => $property->id,
            $field => $value
        ]);
        $response = $this->postJson('/api/contract', $contract->toArray());
        $response->assertStatus(422)->assertJsonValidationErrors([$field]);
        $property->delete();
    }

}
