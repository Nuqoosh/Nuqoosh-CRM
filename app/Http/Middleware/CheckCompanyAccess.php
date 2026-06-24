<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Ensures the authenticated user has an active company selected, and that
 * they actually belong to that company. Used on routes that require
 * company-scoped data (clients, templates, documents, etc.).
 */
class CheckCompanyAccess
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }

        $companyId = $user->active_company_id;

        if (!$companyId) {
            return response()->json([
                'message' => 'No active company selected'
            ], 400);
        }

        // Important: confirm the user is actually a member of this company,
        // not just that active_company_id happens to be set.
        $belongs = $user->companies()
            ->where('companies.id', $companyId)
            ->exists();

        if (!$belongs) {
            return response()->json([
                'message' => 'Unauthorized company access'
            ], 403);
        }

        return $next($request);
    }
}