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
    $companyId = $request->company_id;

    return Client::where('company_id', $companyId)->get();
}
    // CREATE client
    public function store(Request $request)
{
    $request->validate([
        'company_id' => 'required',
        'name' => 'required'
    ]);

    $client = Client::create([
        'company_id' => $request->company_id,
        'name' => $request->name,
        'email' => $request->email,
        'phone' => $request->phone,
    ]);

    return response()->json([
        'message' => 'Client created',
        'client' => $client
    ]);
}
}