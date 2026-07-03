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
// Changed from role:admin to permission:companies.manage (more precise —
// super-admin has this permission, admin does not)
Route::post('/companies', [CompanyController::class, 'store'])
    ->middleware(['auth:sanctum', 'permission:companies.manage']);

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
    // All authenticated users with company access can list companies
    Route::get('/companies', [CompanyController::class, 'index']);

    /*
    |--------------------------------------------------------------------------
    | CLIENTS
    |--------------------------------------------------------------------------
    */
    // All authenticated users can view clients
    Route::get('/clients', [ClientController::class, 'index']);

    // Only users who can generate documents can add clients
    // (super-admin, admin, office-manager, employee)
    Route::post('/clients', [ClientController::class, 'store'])
        ->middleware('permission:documents.generate');

    // Only users who can delete documents can delete clients
    // (super-admin, admin)
    Route::delete('/clients/{id}', [ClientController::class, 'destroy'])
        ->middleware('permission:documents.delete');

    /*
    |--------------------------------------------------------------------------
    | DOCUMENTS
    |--------------------------------------------------------------------------
    */
    // View documents — all roles except those with no permissions at all
    Route::get('/documents', [DocumentController::class, 'index'])
        ->middleware('permission:documents.view');

    // Generate documents — super-admin, admin, office-manager, employee
    Route::post('/documents/generate', [DocumentController::class, 'generate'])
        ->middleware('permission:documents.generate');

    // Download PDF — same as view
    Route::get('/documents/download/{id}', [DocumentController::class, 'download'])
        ->middleware('permission:documents.view');

    // Analytics — super-admin, admin, hr-manager, office-manager
    Route::get('/analytics', [AnalyticsController::class, 'index'])
        ->middleware('permission:analytics.view');

    /*
    |--------------------------------------------------------------------------
    | DOCUMENT TEMPLATES
    |--------------------------------------------------------------------------
    */
    // View template categories — super-admin, admin, office-manager
    Route::get('/document-template-categories', [DocumentTemplateController::class, 'categories'])
        ->middleware('permission:templates.view');

    // View templates list — super-admin, admin, office-manager
    Route::get('/document-templates', [DocumentTemplateController::class, 'index'])
        ->middleware('permission:templates.view');

    // Create template — changed from role:admin to permission:templates.create
    // (super-admin, admin, office-manager all have this permission)
    Route::post('/document-templates', [DocumentTemplateController::class, 'store'])
        ->middleware('permission:templates.create');

    // Generate document from template — super-admin, admin, office-manager, employee
    Route::post('/document-templates/{id}/generate', [DocumentTemplateController::class, 'generate'])
        ->middleware('permission:documents.generate');

});