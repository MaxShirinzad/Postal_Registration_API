<?php

use App\Http\Controllers\Api\ParcelController;
use Illuminate\Support\Facades\Route;

//-------------------------------------
Route::get('/parcels', [ParcelController::class, 'index']);
Route::post('/parcels', [ParcelController::class, 'store']);
//------------------------------








