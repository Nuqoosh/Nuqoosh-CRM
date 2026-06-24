<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Document;
use App\Models\Client;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Class AnalyticsController
 * @package App\Http\Controllers\Api
 *
 * Provides dashboard analytics for the active company:
 * revenue by month, top clients, and document generation trends.
 */
class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $companyId = $request->user()->active_company_id;

        if (!$companyId) {
            return response()->json(['message' => 'No active company selected'], 400);
        }

        return response()->json([
            'revenue_by_month'   => $this->revenueByMonth($companyId),
            'top_clients'        => $this->topClients($companyId),
            'documents_by_month' => $this->documentsByMonth($companyId),
        ]);
    }

    /**
     * Total revenue (sum of amount) grouped by month, for the last 6 months.
     */
    private function revenueByMonth(int $companyId): array
    {
        $months = collect(range(5, 0))->map(function ($i) {
            return Carbon::now()->subMonths($i)->format('Y-m');
        });

        $rows = Document::where('company_id', $companyId)
            ->where('created_at', '>=', Carbon::now()->subMonths(5)->startOfMonth())
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, SUM(amount) as total")
            ->groupBy('month')
            ->pluck('total', 'month');

        return $months->map(function ($month) use ($rows) {
            return [
                'month' => Carbon::createFromFormat('Y-m', $month)->format('M Y'),
                'revenue' => (float) ($rows[$month] ?? 0),
            ];
        })->values()->all();
    }

    /**
     * Top 5 clients by number of documents generated.
     */
    private function topClients(int $companyId): array
    {
        return Document::where('documents.company_id', $companyId)
            ->join('clients', 'clients.id', '=', 'documents.client_id')
            ->selectRaw('clients.name as client_name, COUNT(documents.id) as document_count, SUM(documents.amount) as total_amount')
            ->groupBy('clients.id', 'clients.name')
            ->orderByDesc('document_count')
            ->limit(5)
            ->get()
            ->map(function ($row) {
                return [
                    'client_name'    => $row->client_name,
                    'document_count' => (int) $row->document_count,
                    'total_amount'   => (float) $row->total_amount,
                ];
            })
            ->all();
    }

    /**
     * Number of documents generated per month, for the last 6 months.
     */
    private function documentsByMonth(int $companyId): array
    {
        $months = collect(range(5, 0))->map(function ($i) {
            return Carbon::now()->subMonths($i)->format('Y-m');
        });

        $rows = Document::where('company_id', $companyId)
            ->where('created_at', '>=', Carbon::now()->subMonths(5)->startOfMonth())
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as total")
            ->groupBy('month')
            ->pluck('total', 'month');

        return $months->map(function ($month) use ($rows) {
            return [
                'month' => Carbon::createFromFormat('Y-m', $month)->format('M Y'),
                'count' => (int) ($rows[$month] ?? 0),
            ];
        })->values()->all();
    }
}