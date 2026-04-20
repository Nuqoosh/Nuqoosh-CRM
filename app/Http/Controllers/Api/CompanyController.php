<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Company;

class CompanyController extends Controller
{
    // GET companies of logged-in user
    public function index(Request $request)
  {
    return response()->json([
        'companies' => $request->user()->companies
    ]);
  }

    // CREATE company + attach to user
    public function store(Request $request)
{
    $request->validate([
        'name' => 'required'
    ]);

    $company = Company::create([
        'name' => $request->name,
        'email' => $request->email,
        'phone' => $request->phone,
    ]);

    // AUTO ATTACH USER
    $request->user()->companies()->attach($company->id);

    return response()->json([
        'message' => 'Company created & attached',
        'company' => $company
    ]);
}

public function select(Request $request)
{
    $request->validate([
        'company_id' => 'required'
    ]);

    session(['active_company' => $request->company_id]);

    return response()->json([
        'message' => 'Company selected successfully'
    ]);
}

}