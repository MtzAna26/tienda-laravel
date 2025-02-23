<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiAuthController;
use App\Http\Controllers\Api\ProductoController;
use App\Http\Controllers\Api\TiendaController;

/*
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
*/

// Rutas públicas (registro y login)
Route::post('/registrar-cliente', [ApiAuthController::class, 'registrarCliente']);
Route::post('/registrar-vendedor', [ApiAuthController::class, 'registrarVendedor']);
Route::post('/login-cliente', [ApiAuthController::class, 'loginCliente']);
Route::post('/login-vendedor', [ApiAuthController::class, 'loginVendedor']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return response()->json($request->user());
});
Route::get('/login', function () {
    return response()->json(['error' => 'No autenticado'], 401);
})->name('login');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [ApiAuthController::class, 'logout']);
});
Route::middleware('auth:sanctum')->post('/logout', [ApiAuthController::class, 'logout']);

Route::middleware('auth:sanctum')->group(function () {
    // CRUD Tiendas
    Route::apiResource('tiendas', TiendaController::class);

    // CRUD Productos
    Route::apiResource('productos', ProductoController::class);
});
/*
Route::get('/students', function (){
    return 'obtniendo estudiantes';
});*/