<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Api\DocumentTemplateController;

// TEST ROUTE
Route::get('/test', function () {
    return response()->json(['message' => 'API Working']);
});

// LOGIN (PUBLIC)
Route::post('/login', [AuthController::class, 'login']);

// AUTH ROUTES
Route::middleware('auth:sanctum')->group(function () {

    // Companies
    Route::get('/companies', [CompanyController::class, 'index']);
    Route::post('/companies', [CompanyController::class, 'store']);
    Route::post('/companies/select', [CompanyController::class, 'select']);

    // Clients
    Route::get('/clients', [ClientController::class, 'index']);
    Route::post('/clients', [ClientController::class, 'store']);

    // Documents
    Route::get('/documents', [DocumentController::class, 'index']);
    Route::post('/documents/generate', [DocumentController::class, 'generate']);

    // Document Templates
    Route::get('/document-templates', [DocumentTemplateController::class, 'index']);
    Route::post('/document-templates', [DocumentTemplateController::class, 'store']);

    // 🔥 PRO GENERATE (SECURE + POST)
    Route::post('/document-templates/{id}/generate', [DocumentTemplateController::class, 'generate']);
});