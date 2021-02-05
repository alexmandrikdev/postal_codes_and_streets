<?php

namespace App\Http\Controllers;

use App\Http\Requests\StreetStoreRequest;
use App\Models\Street;

class StreetController extends Controller
{
    public function store(StreetStoreRequest $request)
    {
        return Street::create($request->validated());
    }
}
