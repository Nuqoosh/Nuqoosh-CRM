<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Api\DocumentTemplateController;
use App\Http\Controllers\Api\AnalyticsController;

/*
|--------------------------------------------------------------------------
| TEST ROUTE
|--------------------------------------------------------------------------
*/
Route::get('/test', function () {
    return response()->json([
        'message' => 'CRM API is working fine'
    ]);
});

/*
|--------------------------------------------------------------------------
| AUTH (PUBLIC)
|--------------------------------------------------------------------------
*/
Route::post('/login', [AuthController::class, 'login']);

/*
|--------------------------------------------------------------------------
| AUTH (AUTHENTICATED — no active company required)
|--------------------------------------------------------------------------
*/
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/me', [AuthController::class, 'me'])->middleware('auth:sanctum');

/*
|--------------------------------------------------------------------------
| COMPANY SETUP ROUTES (NO company.access HERE)
|--------------------------------------------------------------------------
*/

// CREATE COMPANY — only super-admin can create new companies
Route::post('/companies', [CompanyController::class, 'store'])
    ->middleware(['auth:sanctum', 'permission:companies.manage,sanctum']);

// SELECT ACTIVE COMPANY — any authenticated user
Route::post('/companies/select', [CompanyController::class, 'select'])
    ->middleware('auth:sanctum');

/*
|--------------------------------------------------------------------------
| PROTECTED CRM ROUTES (ONLY AFTER COMPANY SELECT)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', 'company.access'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | COMPANIES (VIEW ONLY)
    |--------------------------------------------------------------------------
    */
    Route::get('/companies', [CompanyController::class, 'index']);

    /*
    |--------------------------------------------------------------------------
    | CLIENTS
    |--------------------------------------------------------------------------
    */
    Route::get('/clients', [ClientController::class, 'index']);

    Route::post('/clients', [ClientController::class, 'store'])
        ->middleware('permission:documents.generate,sanctum');

    Route::delete('/clients/{id}', [ClientController::class, 'destroy'])
        ->middleware('permission:documents.delete,sanctum');

    /*
    |--------------------------------------------------------------------------
    | DOCUMENTS
    |--------------------------------------------------------------------------
    */
    Route::get('/documents', [DocumentController::class, 'index'])
        ->middleware('permission:documents.view,sanctum');

    Route::post('/documents/generate', [DocumentController::class, 'generate'])
        ->middleware('permission:documents.generate,sanctum');

    Route::get('/documents/download/{id}', [DocumentController::class, 'download'])
        ->middleware('permission:documents.view,sanctum');

    Route::get('/analytics', [AnalyticsController::class, 'index'])
        ->middleware('permission:analytics.view,sanctum');

    /*
    |--------------------------------------------------------------------------
    | DOCUMENT TEMPLATES
    |--------------------------------------------------------------------------
    */
    Route::get('/document-template-categories', [DocumentTemplateController::class, 'categories'])
        ->middleware('permission:templates.view,sanctum');

    Route::get('/document-templates', [DocumentTemplateController::class, 'index'])
        ->middleware('permission:templates.view,sanctum');

    Route::post('/document-templates', [DocumentTemplateController::class, 'store'])
        ->middleware('permission:templates.create,sanctum');

    Route::post('/document-templates/{id}/generate', [DocumentTemplateController::class, 'generate'])
        ->middleware('permission:documents.generate,sanctum');

});