<?php

use App\Http\Controllers\Api\PostalPackageController;
use Illuminate\Support\Facades\Route;

//-------------------------------------
Route::get('/postal-packages', [PostalPackageController::class, 'index']);
Route::post('/postal-packages', [PostalPackageController::class, 'store']);
//------------------------------








