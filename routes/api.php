<?php

use App\Http\Controllers\Api\ItemApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| These routes are used by the QR scanner and offline sync features
*/

// Items API for QR Scanner
Route::get('/items', [ItemApiController::class, 'index']);
Route::get('/items/code/{code}', [ItemApiController::class, 'findByCode']);

// Authenticated routes (for future use)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});
