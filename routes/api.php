<?php

use App\Http\Resources\GenderCollection;
use App\Http\Resources\StateCollection;
use App\Models\Gender;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::apiResource('customers', App\Http\Controllers\Api\CustomerController::class);

Route::get('/genders', function () {
    return new GenderCollection(Gender::all());
});

Route::get('/states', function () {
    return new StateCollection(State::all());
});