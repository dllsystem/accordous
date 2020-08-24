<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProperty;
use App\Model\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PropertyController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->all();

        $perPage    = $data['perPage'] ?? 10;
        $searchTerm = $data['searchTerm'] ?? null;
        $sortBy     = isset($data['sortBy']) ? explode(',', $data['sortBy']) : null;
        $sortDesc   = isset($data['sortDesc']) ? explode(',', $data['sortDesc']) : null;

        $property = Property::query();

        if ($searchTerm) {
            $property->where( function ($query) use ($searchTerm) {
                $query->where('address_street', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('address_city', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('address_state', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('owner_email', 'LIKE', "%{$searchTerm}%");
            });
        }

        if ($sortBy && $sortDesc) {
            for($i = 0; $i < count($sortBy); ++$i) {
                $property->orderBy($sortBy[$i], strtolower($sortDesc[$i]) == 'true' ? 'DESC' : 'ASC');
            }
        } else {
            $property->orderBy('created_at', 'DESC');
        }

        $property->with('contract');
        $properties = $property->paginate($perPage);

        return $properties;
    }

    public function store(StoreProperty $request)
    {
        $property = Property::create($request->all());
        return $property;
    }

    public function show($id)
    {
        $property = Property::with('contract')->find($id);

        if (!$property) {
            return response('Imóvel não encontrado', 404);
        }

        return $property;
    }

    public function destroy($id)
    {
        $property = Property::withCount('contract')->find($id);

        if (!$property) {
            return response('Imóvel não encontrado', 404);
        }

        if ($property->contract_count > 0) {
            return response('Este imóvel não pode ser deletado pois está associado à outro contrato.', 422);
        }

        $property->delete();

        return response(null, 204);
    }


}
