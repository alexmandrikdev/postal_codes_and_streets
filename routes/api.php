<?php

use App\Http\Controllers\PostalCodeController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1'], function () {
    Route::apiResource('postal_codes', PostalCodeController::class)->only(['store']);
});
