<?php

use App\Http\Controllers\API\ListingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\TransactionController;


// tempat register endpoint API

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return response()->json([
        'success' => true,
        'message' => 'Detail login user',
        'data' => $request->user()
    ]);

});

// route resouerce( /urlEndpoint, controller )
// “Bikin route untuk menampilkan semua listing (index), tapi jangan bikin route CRUD lainnya.”
Route::resource('listing', ListingController::class)->only(['index', 'show']);

// Route Manual
// Di sini kamu tentukan sendiri endpoint dan method:
// URL: /transaction/is-available
// HTTP Method: POST
// Controller + function: TransactionController@isAvailable
// Middleware: auth:sanctum (biar harus login dengan token dulu)
Route::post('transaction/is-available', [TransactionController::class, 'isAvailable'])->middleware(['auth:sanctum']);

// kenapa kita menggunakan middleware(['auth:sanctum']), karena user harus login dulu dengan token baru bisa membuat Transaction baru
Route::resource('transaction', TransactionController::class)->only(['store', 'index', 'show'])->middleware(['auth:sanctum']);


require __DIR__.'/auth.php';
