<?php

use App\Http\Controllers\API\ListingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


// tempat register endpoint API

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return response()->json([
        'success' => true,
        'message' => 'Detail login user',
        'data' => $request->user()
    ]);
});

// resouerce( /url, controller )
Route::resource('listing', ListingController::class)->only(['index', 'show']);


require __DIR__.'/auth.php';
