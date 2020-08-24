<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContract;
use App\Jobs\OwnerContractJob;
use App\Jobs\TenantContractJob;
use App\Model\Contract;
use App\Model\Property;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    public function index(Request $request)
    {

        $data = $request->all();

        $perPage    = $data['perPage'] ?? 10;
        $searchTerm = $data['searchTerm'] ?? null;
        $sortBy     = isset($data['sortBy']) ? explode(',', $data['sortBy']) : null;
        $sortDesc   = isset($data['sortBy']) ? explode(',', $data['sortDesc']) : null;

        $contract = Contract::query();

        if ($searchTerm) {
            $contract->where( function ($query) use ($searchTerm) {
                $query->where('tenant_name', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('tenant_email', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('tenant_document', 'LIKE', "%{$searchTerm}%");
            });
        }

        if ($sortBy && $sortDesc) {
            for($i = 0; $i < count($sortBy); ++$i) {
                $contract->orderBy($sortBy[$i], strtolower($sortDesc[$i]) == 'true' ? 'DESC' : 'ASC');
            }
        } else {
            $contract->orderBy('created_at', 'DESC');
        }

        $contract->with('property');

        $contracts = $contract->paginate($perPage);

        return $contracts;
    }

    public function store(StoreContract $request)
    {
        $data = $request->all();

        $property = Property::withCount('contract')->find($data['property_id']);

        if (!$property) {
            return response()
                ->json([
                    "property_id" => [
                        "Imóvel não encontrado."
                    ]
                ], 404);
        }

        if ($property->contract_count > 0) {
            return response()
                ->json([
                    "errors" => [
                        "property_id" => [
                          "Este imóvel já está associado à outro contrato."
                        ]
                    ]
                ], 422);
        }

        $contract = Contract::create($request->all());

        if ($contract) {
            $contract->property->is_rent = true;
            $contract->property->save();
        }

        $this->sendContractMail($contract);

        return $contract;
    }

    public function show($id)
    {
        $contract = Contract::with('property')->find($id);

        if (!$contract) {
            return response('Contrato não encontrado', 404);
        }

        return $contract;
    }

    public function sendContractMail(Contract $contract)
    {
        OwnerContractJob::dispatch($contract)
            ->delay(now()
            ->addSeconds(5));

        TenantContractJob::dispatch($contract)
            ->delay(now()
            ->addSeconds(5));
    }


    public function destroy($id)
    {
        $contract = Contract::find($id);

        if (!$contract) {
            return response('Contrato não encontrado', 404);
        }

        // remove flag do imóvel
        $contract->property->is_rent = false;
        $contract->property->save();

        $contract->delete();

        return response(null, 204);
    }

}
