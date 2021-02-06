<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostalCodeStoreRequest;
use App\Models\PostalCode;

class PostalCodeController extends Controller
{
    public function index()
    {
        return PostalCode::all();
    }

    public function store(PostalCodeStoreRequest $request)
    {
        return [
            'created' => PostalCode::create($request->validated()),
            'all' => PostalCode::all(),
        ];
    }
}
