<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Api\DocumentTemplateController;

/*
|--------------------------------------------------------------------------
| TEST ROUTE
|--------------------------------------------------------------------------
*/
Route::get('/test', function () {
    return response()->json([
        'message' => 'CRM API is working fine 🚀'
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
| COMPANY SETUP ROUTES (NO company.access HERE)
|--------------------------------------------------------------------------
*/

// CREATE COMPANY (ADMIN ONLY)
Route::post('/companies', [CompanyController::class, 'store'])
    ->middleware(['auth:sanctum', 'role:admin']);

// SELECT ACTIVE COMPANY
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
    Route::get('/companies', [CompanyController::class, 'index'])
        ->middleware('permission:view');

    /*
    |--------------------------------------------------------------------------
    | CLIENTS
    |--------------------------------------------------------------------------
    */
    Route::get('/clients', [ClientController::class, 'index'])
        ->middleware('permission:view');

    Route::post('/clients', [ClientController::class, 'store'])
        ->middleware('permission:create');

    Route::delete('/clients/{id}', [ClientController::class, 'destroy'])
        ->middleware('permission:delete');

    /*
    |--------------------------------------------------------------------------
    | DOCUMENTS
    |--------------------------------------------------------------------------
    */
    Route::get('/documents', [DocumentController::class, 'index'])
        ->middleware('permission:view');

    Route::post('/documents/generate', [DocumentController::class, 'generate'])
        ->middleware('permission:create');

    Route::get('/documents/download/{id}', [DocumentController::class, 'download'])
        ->middleware('permission:view');

    /*
    |--------------------------------------------------------------------------
    | DOCUMENT TEMPLATES
    |--------------------------------------------------------------------------
    */
    Route::get('/document-template-categories', [DocumentTemplateController::class, 'categories'])
    ->middleware('permission:view');
    Route::get('/document-templates', [DocumentTemplateController::class, 'index'])
        ->middleware('permission:view');

    Route::post('/document-templates', [DocumentTemplateController::class, 'store'])
        ->middleware('role:admin');

    Route::post('/document-templates/{id}/generate', [DocumentTemplateController::class, 'generate'])
        ->middleware('permission:create');

});