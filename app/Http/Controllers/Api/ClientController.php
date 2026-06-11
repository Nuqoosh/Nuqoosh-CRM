<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;
use Illuminate\Validation\Rule;

/**
 * Class ClientController
 * @package App\Http\Controllers\Api
 * 
 * Manages clients for the authenticated user's active company
 */
class ClientController extends Controller
{
    /**
     * Get all clients for the active company (with pagination)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $companyId = $user->active_company_id;

        // Validate company context
        if (!$companyId) {
            return response()->json([
                'status'  => 'error',
                'message' => 'No active company selected'
            ], 400);
        }

        // Security: Verify user belongs to company
        if (!$user->belongsToCompany($companyId)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Unauthorized company access'
            ], 403);
        }

        // Build query with search and pagination
        $query = Client::where('company_id', $companyId);

        // Search by name or email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Paginate results (15 per page)
        $perPage = $request->input('per_page', 15);
        $clients = $query->paginate($perPage);

        return response()->json([
            'status'  => 'success',
            'data'    => $clients->items(),
            'meta'    => [
                'current_page' => $clients->currentPage(),
                'last_page'    => $clients->lastPage(),
                'per_page'     => $clients->perPage(),
                'total'        => $clients->total()
            ]
        ]);
    }

    /**
     * Create a new client
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $user = $request->user();
        $companyId = $user->active_company_id;

        if (!$companyId) {
            return response()->json([
                'status'  => 'error',
                'message' => 'No active company selected'
            ], 400);
        }

        if (!$user->belongsToCompany($companyId)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Unauthorized company access'
            ], 403);
        }

        // Enhanced validation with unique email per company
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('clients')->where(function ($query) use ($companyId) {
                    return $query->where('company_id', $companyId);
                })
            ],
            'phone'      => 'nullable|string|max:50',
            'address'    => 'nullable|string|max:500',
            'tax_number' => 'nullable|string|max:100',
            'notes'      => 'nullable|string'
        ]);

        $client = Client::create(array_merge($validated, [
            'company_id' => $companyId
        ]));

        return response()->json([
            'status'  => 'success',
            'message' => 'Client created successfully',
            'data'    => $client
        ], 201);
    }

    /**
     * Get single client details
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        $companyId = $user->active_company_id;

        $client = Client::where('id', $id)
            ->where('company_id', $companyId)
            ->first();

        if (!$client) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Client not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data'   => $client
        ]);
    }

    /**
     * Update client
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $user = $request->user();
        $companyId = $user->active_company_id;

        $client = Client::where('id', $id)
            ->where('company_id', $companyId)
            ->first();

        if (!$client) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Client not found'
            ], 404);
        }

        $validated = $request->validate([
            'name'  => 'sometimes|required|string|max:255',
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('clients')->where(function ($query) use ($companyId, $id) {
                    return $query->where('company_id', $companyId)->where('id', '!=', $id);
                })
            ],
            'phone'      => 'nullable|string|max:50',
            'address'    => 'nullable|string|max:500',
            'tax_number' => 'nullable|string|max:100',
            'notes'      => 'nullable|string'
        ]);

        $client->update($validated);

        return response()->json([
            'status'  => 'success',
            'message' => 'Client updated successfully',
            'data'    => $client
        ]);
    }

    /**
     * Delete client
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        $companyId = $user->active_company_id;

        $client = Client::where('id', $id)
            ->where('company_id', $companyId)
            ->first();

        if (!$client) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Client not found'
            ], 404);
        }

        // Check if client has documents
        if ($client->documents()->count() > 0) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Cannot delete client with existing documents. Archive instead.'
            ], 422);
        }

        $client->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Client deleted successfully'
        ]);
    }
}