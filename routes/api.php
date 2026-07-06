<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Api\DocumentTemplateController;
use App\Http\Controllers\Api\AnalyticsController;
use App\Http\Controllers\Api\UserController;

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
    ->middleware(['auth:sanctum', 'permission:companies.manage,api']);

// SELECT ACTIVE COMPANY — any authenticated user
Route::post('/companies/select', [CompanyController::class, 'select'])
    ->middleware('auth:sanctum');

/*
|--------------------------------------------------------------------------
| USER MANAGEMENT (no company.access — admins manage users across companies)
|--------------------------------------------------------------------------
| Company/role scoping is enforced inside UserController:
| - super-admin: all users, any role below super-admin... (any role)
| - admin: own-company users only, roles below admin only
*/
Route::middleware('auth:sanctum')->group(function () {

    Route::get('/users', [UserController::class, 'index'])
        ->middleware('permission:users.view,api');

    Route::get('/users/assignable-roles', [UserController::class, 'assignableRoles'])
        ->middleware('permission:users.create,api');

    Route::post('/users', [UserController::class, 'store'])
        ->middleware('permission:users.create,api');

    Route::put('/users/{id}', [UserController::class, 'update'])
        ->middleware('permission:users.edit,api');

    Route::delete('/users/{id}', [UserController::class, 'destroy'])
        ->middleware('permission:users.delete,api');
});

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
        ->middleware('permission:documents.generate,api');

    Route::delete('/clients/{id}', [ClientController::class, 'destroy'])
        ->middleware('permission:documents.delete,api');

    /*
    |--------------------------------------------------------------------------
    | DOCUMENTS
    |--------------------------------------------------------------------------
    */
    Route::get('/documents', [DocumentController::class, 'index'])
        ->middleware('permission:documents.view,api');

    Route::post('/documents/generate', [DocumentController::class, 'generate'])
        ->middleware('permission:documents.generate,api');

    Route::get('/documents/download/{id}', [DocumentController::class, 'download'])
        ->middleware('permission:documents.view,api');

    Route::get('/analytics', [AnalyticsController::class, 'index'])
        ->middleware('permission:analytics.view,api');

    /*
    |--------------------------------------------------------------------------
    | DOCUMENT TEMPLATES
    |--------------------------------------------------------------------------
    */
    Route::get('/document-template-categories', [DocumentTemplateController::class, 'categories'])
        ->middleware('permission:templates.view,api');

    Route::get('/document-templates', [DocumentTemplateController::class, 'index'])
        ->middleware('permission:templates.view,api');

    Route::post('/document-templates', [DocumentTemplateController::class, 'store'])
        ->middleware('permission:templates.create,api');

    Route::post('/document-templates/{id}/generate', [DocumentTemplateController::class, 'generate'])
        ->middleware('permission:documents.generate,api');

});