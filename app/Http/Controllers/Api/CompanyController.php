<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Company;
use Illuminate\Validation\Rule;

/**
 * Class CompanyController
 * @package App\Http\Controllers\Api
 * 
 * Manages companies and user-company relationships
 */
class CompanyController extends Controller
{
    /**
     * Get all companies for the authenticated user
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $user->load('companies');
        
        return response()->json([
            'status'    => 'success',
            'data'      => $user->companies,
            'active_company_id' => $user->active_company_id
        ]);
    }

    /**
     * Create a new company and attach to user
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:255|unique:companies,name',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'tax_number' => 'nullable|string|max:100',
            'logo' => 'nullable|image|mimes:png,jpg,jpeg|max:2048'
        ]);

        $user = $request->user();

        // Handle logo upload if provided
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('logos', 'public');
            $validated['logo_path'] = $logoPath;
        }

        // Create company
        $company = Company::create($validated);

        // Attach user to company with 'admin' role
        $user->companies()->attach($company->id, ['role' => 'admin']);

        // Set as active company if user has none
        if (!$user->active_company_id) {
            $user->update(['active_company_id' => $company->id]);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Company created successfully',
            'data'    => $company,
            'active_company_id' => $user->active_company_id
        ], 201);
    }

    /**
     * Switch active company
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function select(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|integer|exists:companies,id'
        ]);

        $user = $request->user();

        // Verify user belongs to this company
        $belongsTo = $user->companies()
            ->where('companies.id', $validated['company_id'])
            ->exists();

        if (!$belongsTo) {
            return response()->json([
                'status'  => 'error',
                'message' => 'You do not have access to this company'
            ], 403);
        }

        $user->update(['active_company_id' => $validated['company_id']]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Company selected successfully',
            'active_company_id' => $user->active_company_id
        ]);
    }

    /**
     * Get company details
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        
        $company = $user->companies()
            ->where('companies.id', $id)
            ->first();
            
        if (!$company) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Company not found'
            ], 404);
        }
        
        // Get user's role in this company
        $role = $user->companies()
            ->where('companies.id', $id)
            ->first()
            ->pivot
            ->role ?? 'user';
        
        return response()->json([
            'status' => 'success',
            'data'   => $company,
            'user_role' => $role
        ]);
    }
}