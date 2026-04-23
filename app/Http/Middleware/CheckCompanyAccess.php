<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

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

        // 🔥 IMPORTANT: check user belongs to company
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