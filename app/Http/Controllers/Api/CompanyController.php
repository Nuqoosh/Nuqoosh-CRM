<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Company;

class CompanyController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | GET Companies of Logged-in User
    |--------------------------------------------------------------------------
    */
    public function index(Request $request)
    {
        return response()->json([
            'companies' => $request->user()->companies
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE Company + Attach User
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string'
        ]);

        $user = $request->user();

        // Create Company
        $company = Company::create([
            'name'  => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);

        // Attach user with company (FIXED)
        $user->companies()->attach($company->id);

        // Optional: first company auto select
        if (!$user->active_company_id) {
            $user->active_company_id = $company->id;
            $user->save();
        }

        return response()->json([
            'message' => 'Company created & attached successfully',
            'company' => $company,
            'active_company_id' => $user->active_company_id
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | SELECT Active Company
    |--------------------------------------------------------------------------
    */
    public function select(Request $request)
    {
        $request->validate([
            'company_id' => 'required|integer'
        ]);

        $user = $request->user();

        // Security check: user belongs to this company
        $belongs = $user->companies()
            ->where('companies.id', $request->company_id)
            ->exists();

        if (!$belongs) {
            return response()->json([
                'message' => 'Unauthorized company access'
            ], 403);
        }

        $user->active_company_id = $request->company_id;
        $user->save();

        return response()->json([
            'message' => 'Company selected successfully',
            'active_company_id' => $user->active_company_id
        ]);
    }
}