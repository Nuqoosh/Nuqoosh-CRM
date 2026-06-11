<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DocumentTemplate;
use App\Models\Client;
use App\Models\Company;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;

class DocumentTemplateController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | LIST TEMPLATES
    | GET /api/document-templates
    |--------------------------------------------------------------------------
    */
    public function index(Request $request)
    {
        $companyId = $request->user()->active_company_id;

        $query = DocumentTemplate::where('company_id', $companyId);

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('sub_category')) {
            $query->where('sub_category', $request->sub_category);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $templates = $query->orderBy('name')->get();

        // Return direct array — frontend expects this
        return response()->json($templates);
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE TEMPLATE
    | POST /api/document-templates  (admin only — enforced in routes)
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $companyId = $request->user()->active_company_id;

        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'type'         => 'required|string|max:100',
            'content'      => 'required|string',
            'category'     => 'nullable|string|max:100',
            'sub_category' => 'nullable|string|max:100',
        ]);

        // Duplicate name check within company
        $exists = DocumentTemplate::where('company_id', $companyId)
            ->where('name', $validated['name'])
            ->exists();

        if ($exists) {
            return response()->json([
                'status'  => 'error',
                'message' => 'A template with this name already exists'
            ], 422);
        }

        $template = DocumentTemplate::create(array_merge($validated, [
            'company_id' => $companyId,
        ]));

        return response()->json([
            'status'  => 'success',
            'message' => 'Template created successfully',
            'data'    => $template
        ], 201);
    }

    /*
    |--------------------------------------------------------------------------
    | SINGLE TEMPLATE
    | GET /api/document-templates/{id}
    |--------------------------------------------------------------------------
    */
    public function show(Request $request, $id)
    {
        $companyId = $request->user()->active_company_id;

        $template = DocumentTemplate::where('id', $id)
            ->where('company_id', $companyId)
            ->first();

        if (!$template) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Template not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data'   => $template
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE TEMPLATE
    | PUT /api/document-templates/{id}  (admin only)
    |--------------------------------------------------------------------------
    */
    public function update(Request $request, $id)
    {
        $companyId = $request->user()->active_company_id;

        $template = DocumentTemplate::where('id', $id)
            ->where('company_id', $companyId)
            ->first();

        if (!$template) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Template not found'
            ], 404);
        }

        $validated = $request->validate([
            'name'         => 'sometimes|required|string|max:255',
            'type'         => 'sometimes|required|string|max:100',
            'content'      => 'sometimes|required|string',
            'category'     => 'nullable|string|max:100',
            'sub_category' => 'nullable|string|max:100',
        ]);

        $template->update($validated);

        return response()->json([
            'status'  => 'success',
            'message' => 'Template updated successfully',
            'data'    => $template
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE TEMPLATE
    | DELETE /api/document-templates/{id}  (admin only)
    |--------------------------------------------------------------------------
    */
    public function destroy(Request $request, $id)
    {
        $companyId = $request->user()->active_company_id;

        $template = DocumentTemplate::where('id', $id)
            ->where('company_id', $companyId)
            ->first();

        if (!$template) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Template not found'
            ], 404);
        }

        // Prevent delete if used in documents
        if ($template->documents()->count() > 0) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Cannot delete template used in existing documents.'
            ], 422);
        }

        $template->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Template deleted successfully'
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | CATEGORIES LIST
    | GET /api/document-template-categories
    |--------------------------------------------------------------------------
    */
    public function categories(Request $request)
    {
        $companyId = $request->user()->active_company_id;

        $categories = DocumentTemplate::where('company_id', $companyId)
            ->whereNotNull('category')
            ->distinct()
            ->pluck('category');

        return response()->json([
            'status' => 'success',
            'data'   => $categories
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | GENERATE FROM TEMPLATE
    | POST /api/document-templates/{id}/generate
    |--------------------------------------------------------------------------
    */
    public function generate(Request $request, $id)
    {
        $request->validate([
            'client_id'     => 'required|exists:clients,id',
            'price'         => 'nullable|numeric',
            'contract_date' => 'nullable|string',
            'delivery_date' => 'nullable|string',
        ]);

        $companyId = $request->user()->active_company_id;

        $template = DocumentTemplate::where('id', $id)
            ->where('company_id', $companyId)
            ->firstOrFail();

        $client  = Client::where('id', $request->client_id)
            ->where('company_id', $companyId)
            ->firstOrFail();

        $company = Company::findOrFail($companyId);

        // Replace placeholders
        $placeholders = [
            '{{client_name}}'   => $client->name,
            '{{company_name}}'  => $company->name,
            '{{price}}'         => $request->input('price', 0),
            '{{contract_date}}' => $request->input('contract_date', Carbon::now()->format('Y-m-d')),
            '{{delivery_date}}' => $request->input('delivery_date', ''),
            '{{date}}'          => Carbon::now()->format('d/m/Y'),
        ];

        $content = str_replace(
            array_keys($placeholders),
            array_values($placeholders),
            $template->content
        );

        // Preview mode
        if ($request->input('preview')) {
            return response()->json([
                'status'    => 'success',
                'generated' => $content
            ]);
        }

        // Generate PDF
        $pdf = Pdf::loadView('pdf.document', [
            'client'         => $client,
            'company'        => $company,
            'logo'           => $this->resolveLogo(strtolower($company->name)),
            'contractNumber' => 'PREVIEW-' . time(),
            'clientAddress'  => '',
            'contractDate'   => $request->input('contract_date', ''),
            'deliveryDate'   => $request->input('delivery_date', ''),
            'amount'         => $request->input('price', ''),
            'content'        => $content,
            'documentTitle'  => $template->name,
        ]);

        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('document_' . $template->id . '_' . time() . '.pdf');
    }

    /*
    |--------------------------------------------------------------------------
    | PRIVATE: LOGO RESOLVER
    |--------------------------------------------------------------------------
    */
    private function resolveLogo(string $slug): string
    {
        foreach (['png', 'jpg', 'jpeg'] as $ext) {
            $path = public_path("logos/{$slug}.{$ext}");
            if (file_exists($path)) return $path;
        }
        return '';
    }
}