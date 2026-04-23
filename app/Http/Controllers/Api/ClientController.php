<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;

class ClientController extends Controller
{
    // GET clients of active company
    public function index(Request $request)
{
    $user = $request->user();
    $companyId = $user->active_company_id;

    if (!$companyId) {
        return response()->json([
            'message' => 'No active company selected'
        ], 400);
    }

    //  SECURITY CHECK
    if (!$user->belongsToCompany($companyId)) {
        return response()->json([
            'message' => 'Unauthorized company access'
        ], 403);
    }

    return Client::where('company_id', $companyId)->get();
}
    // CREATE client
   public function store(Request $request)
{
    $request->validate([
        'name' => 'required',
        'email' => 'nullable|email',
        'phone' => 'nullable|string'
    ]);

    $user = $request->user();
    $companyId = $user->active_company_id;

    if (!$companyId) {
        return response()->json([
            'message' => 'No active company selected'
        ], 400);
    }

    //  SECURITY CHECK
    if (!$user->belongsToCompany($companyId)) {
        return response()->json([
            'message' => 'Unauthorized company access'
        ], 403);
    }

    $client = Client::create([
        'company_id' => $companyId,
        'name' => $request->name,
        'email' => $request->email,
        'phone' => $request->phone,
    ]);

    return response()->json([
        'message' => 'Client created successfully',
        'client' => $client
    ]);
}
}