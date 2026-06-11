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
    Route::get('/companies', [CompanyController::class, 'index']);

    /*
    |--------------------------------------------------------------------------
    | CLIENTS
    |--------------------------------------------------------------------------
    */
    Route::get('/clients', [ClientController::class, 'index']);

    Route::post('/clients', [ClientController::class, 'store']);

    Route::delete('/clients/{id}', [ClientController::class, 'destroy']);

    /*
    |--------------------------------------------------------------------------
    | DOCUMENTS
    |--------------------------------------------------------------------------
    */
    Route::get('/documents', [DocumentController::class, 'index']);

    Route::post('/documents/generate', [DocumentController::class, 'generate']);

    Route::get('/documents/download/{id}', [DocumentController::class, 'download']);

    /*
    |--------------------------------------------------------------------------
    | DOCUMENT TEMPLATES
    |--------------------------------------------------------------------------
    */
    Route::get('/document-template-categories', [DocumentTemplateController::class, 'categories']);

    Route::get('/document-templates', [DocumentTemplateController::class, 'index']);

    Route::post('/document-templates', [DocumentTemplateController::class, 'store'])
        ->middleware('role:admin');  // sirf yahan role check hai

    Route::post('/document-templates/{id}/generate', [DocumentTemplateController::class, 'generate']);
     
    // routes/api.php mein add karo:
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/me', [AuthController::class, 'me'])->middleware('auth:sanctum');

    });