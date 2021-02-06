<?php

use App\Http\Controllers\PostalCodeController;
use App\Http\Controllers\StreetController;

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1'], function () {
    Route::apiResource('postal_codes', PostalCodeController::class)->only(['index', 'store']);

    Route::apiResource('streets', StreetController::class)->only(['index', 'store']);
});
