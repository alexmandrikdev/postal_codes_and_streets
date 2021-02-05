<?php

namespace App\Http\Controllers;

use App\Http\Requests\StreetIndexRequest;
use App\Http\Requests\StreetStoreRequest;
use App\Models\Street;

class StreetController extends Controller
{
    public function index(StreetIndexRequest $request)
    {
        $query = Street::query();

        if ($request->postal_code !== null && $request->postal_code !== '') {
            $query->where('postal_code', 'LIKE', $request->postal_code . "%");
        }

        if ($request->name !== null && $request->name !== '') {
            $query->where('name', 'LIKE', $request->name . "%");
        }

        return $query->with('postalCode2')->get();
    }

    public function store(StreetStoreRequest $request)
    {
        return Street::create($request->validated());
    }
}
