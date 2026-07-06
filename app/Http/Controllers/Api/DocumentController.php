<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Document;
use App\Models\DocumentTemplate;
use App\Models\Client;
use App\Models\Company;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class DocumentController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | GENERATE DOCUMENT
    |--------------------------------------------------------------------------
    | Validates the request, resolves the template/client/company, builds the
    | auto-generated contract number (per-company prefix logic), replaces
    | template placeholders, saves the Document row, and renders the PDF.
    */
    public function generate(Request $request)
    {
        $request->validate([
            'client_id'       => 'required|exists:clients,id',
            'template_id'     => 'required|exists:document_templates,id',
            'price'           => 'required',
            'contract_number' => 'nullable|string',
            'client_address'  => 'nullable|string',
            'contract_date'   => 'nullable|string',
            'delivery_date'   => 'nullable|string',
            'amount'          => 'nullable|string',
            'language'        => 'nullable|in:en,ar',
        ]);

        $user      = $request->user();
        $companyId = $user->active_company_id;

        // ── SECURITY CHECK ──────────────────────────────────────────────────
        // The user must have an active company, and must actually belong to it.
        if (!$companyId || !$user->companies()->where('companies.id', $companyId)->exists()) {
            return response()->json(['message' => 'Unauthorized company access'], 403);
        }

        $template = DocumentTemplate::where('id', $request->template_id)
            ->where('company_id', $companyId)
            ->first();

        $client = Client::where('id', $request->client_id)
            ->where('company_id', $companyId)
            ->first();

        $company = Company::find($companyId);

        if (!$template || !$client || !$company) {
            return response()->json(['message' => 'Invalid data'], 404);
        }

        // ── LANGUAGE ────────────────────────────────────────────────────────
        $language = $request->language ?? 'en';

        // ── CONTRACT NUMBER AUTO-GENERATION ─────────────────────────────────
        // Prefix depends on the active company and, for some companies, the
        // template's category/sub-category. Add new companies/categories here.
        $year        = Carbon::now()->year;
        $companySlug = strtolower(trim($company->name));
        $category    = strtolower(trim($template->category ?? ''));
        $subCategory = trim($template->sub_category ?? '');

        if ($companySlug === 'vmc') {
            // VMC: prefix is driven by category (NDA / MNDA / everything else).
            $prefix = match ($category) {
                'nda'   => 'VMC-NDA',
                'mnda'  => 'VMC-MNDA',
                default => 'VMC-CON',
            };

        } elseif ($companySlug === 'nuqoosh') {
            // Nuqoosh: prefix is driven by the Contract sub-category.
            $prefix = match ($subCategory) {
                'Website Only'        => 'NQ-WE',
                'Website + Branding'  => 'NQ-WB',
                'Branding Only'       => 'NQ-BR',
                default               => 'NQ-NDA',
            };

        } else {
            // Fallback for any other company: first 4 letters of its name, uppercased.
            $prefix = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $company->name), 0, 4));
        }

        $count = Document::whereYear('created_at', $year)->count() + 1;

        $generatedContractNumber = $prefix . '-' . $year . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);

        // ── TEMPLATE ENGINE ──────────────────────────────────────────────────
        // Simple {{placeholder}} substitution against the template's stored HTML.
        $content = $template->content;

        $placeholders = [
            '{{client_name}}'     => $client->name,
            '{{company_name}}'    => $company->name,
            '{{price}}'           => $request->price,
            '{{contract_number}}' => $generatedContractNumber,
            '{{client_address}}'  => $request->client_address ?? '',
            '{{contract_date}}'   => $request->contract_date  ?? '',
            '{{delivery_date}}'   => $request->delivery_date  ?? '',
            '{{amount}}'          => $request->amount         ?? '',
            '{{currency}}'        => 'AED',
        ];

        $content = str_replace(array_keys($placeholders), array_values($placeholders), $content);

       // ── SAVE DOCUMENT ────────────────────────────────────────────────────
    $document = Document::create([
    'company_id'           => $companyId,
    'client_id'             => $client->id,
    'document_template_id' => $template->id,
    'content'               => $content,
    'contract_number'       => $generatedContractNumber,
    'amount'                => $request->amount ? (float) preg_replace('/[^0-9.]/', '', $request->amount) : null,
]);

        // ── LOGO RESOLUTION ──────────────────────────────────────────────────
        $logo = $this->resolveLogo($companySlug);

        // ── PDF GENERATION ───────────────────────────────────────────────────
        $pdf = Pdf::loadView('pdf.document', [
            'client'         => $client,
            'company'        => $company,
            'logo'           => $logo,
            'contractNumber' => $generatedContractNumber,
            'clientAddress'  => $request->client_address ?? '',
            'contractDate'   => $request->contract_date  ?? '',
            'deliveryDate'   => $request->delivery_date  ?? '',
            'amount'         => $request->amount         ?? '',
            'content'        => $content,
            'documentTitle'  => $template->name,
            'language'       => $language,
        ]);

        $pdf->setPaper('A4', 'portrait');

        $fileName = 'document_' . $generatedContractNumber . '_' . time() . '.pdf';

        Storage::disk('public')->put($fileName, $pdf->output());

        $document->update(['pdf_path' => $fileName]);

        return response()->json([
            'message'      => 'Document generated successfully',
            'document'     => $document,
            'download_url' => url('storage/' . $fileName),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | LOGO RESOLUTION (private helper)
    |--------------------------------------------------------------------------
    | Looks up /public/logos/{company-slug}.{png|jpg|jpeg} and returns the
    | first match, or an empty string if no logo file exists.
    */
    private function resolveLogo(string $companySlug): string
    {
        $extensions = ['png', 'jpg', 'jpeg'];

        foreach ($extensions as $ext) {
            $path = public_path("logos/{$companySlug}.{$ext}");
            if (file_exists($path)) {
                return $path;
            }
        }

        return '';
    }

    /*
    |--------------------------------------------------------------------------
    | DOCUMENT LIST
    |--------------------------------------------------------------------------
    | Returns all documents belonging to the user's active company.
    */
    public function index(Request $request)
    {
        $user      = $request->user();
        $companyId = $user->active_company_id;

        if (!$companyId) {
            return response()->json(['message' => 'No active company selected'], 400);
        }

        return Document::where('company_id', $companyId)
            ->latest()
            ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | DOWNLOAD DOCUMENT
    |--------------------------------------------------------------------------
    | Streams the previously generated PDF for a document owned by the
    | user's active company.
    */
    public function download($id, Request $request)
    {
        $user      = $request->user();
        $companyId = $user->active_company_id;

        $document = Document::where('id', $id)
            ->where('company_id', $companyId)
            ->first();

        if (!$document || !$document->pdf_path) {
            return response()->json(['message' => 'File not found'], 404);
        }

        $fullPath = storage_path('app/public/' . $document->pdf_path);

        // Guard against DB/disk drift: pdf_path set but file deleted from
        // disk would otherwise throw an exception (500) instead of a clean 404.
        if (!file_exists($fullPath)) {
            return response()->json(['message' => 'File not found on server'], 404);
        }

        return response()->download($fullPath);
    }
}