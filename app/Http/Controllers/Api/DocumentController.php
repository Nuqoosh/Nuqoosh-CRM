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

class DocumentController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | GENERATE DOCUMENT
    |--------------------------------------------------------------------------
    */
    public function generate(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'template_id' => 'required|exists:document_templates,id',
            'price' => 'required'
        ]);

        $user = $request->user();
        $companyId = $user->active_company_id;

        // SECURITY CHECK
        if (!$companyId || !$user->companies()->where('companies.id', $companyId)->exists()) {
            return response()->json([
                'message' => 'Unauthorized company access'
            ], 403);
        }

        $template = DocumentTemplate::where('id', $request->template_id)
            ->where('company_id', $companyId)
            ->first();

        $client = Client::where('id', $request->client_id)
            ->where('company_id', $companyId)
            ->first();

        $company = Company::find($companyId);

        if (!$template || !$client || !$company) {
            return response()->json([
                'message' => 'Invalid data'
            ], 404);
        }

        // TEMPLATE ENGINE
        $content = $template->content;

        $content = str_replace('{{client_name}}', $client->name, $content);
        $content = str_replace('{{company_name}}', $company->name, $content);
        $content = str_replace('{{price}}', $request->price, $content);

        // SAVE DOCUMENT
        $document = Document::create([
            'company_id' => $companyId,
            'client_id' => $client->id,
            'document_template_id' => $template->id,
            'content' => $content,
            'created_by' => $user->id ?? null
        ]);

        // PDF GENERATION
        $pdf = Pdf::loadHTML("
            <h2>Document</h2>
            <hr>
            <p>{$content}</p>
        ");

        $fileName = 'document_' . time() . '.pdf';

        //  FIXED LINE
        Storage::disk('public')->put($fileName, $pdf->output());

        // UPDATE DOCUMENT
        $document->update([
            'pdf_path' => $fileName
        ]);

        return response()->json([
            'message' => 'Document + PDF generated successfully',
            'document' => $document,
            'download_url' => url('storage/' . $fileName)
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | DOCUMENT LIST
    |--------------------------------------------------------------------------
    */
    public function index(Request $request)
    {
        $user = $request->user();
        $companyId = $user->active_company_id;

        if (!$companyId) {
            return response()->json([
                'message' => 'No active company selected'
            ], 400);
        }

        return Document::where('company_id', $companyId)
            ->latest()
            ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | DOWNLOAD DOCUMENT
    |--------------------------------------------------------------------------
    */
    public function download($id, Request $request)
    {
        $user = $request->user();
        $companyId = $user->active_company_id;

        $document = Document::where('id', $id)
            ->where('company_id', $companyId)
            ->first();

        if (!$document || !$document->pdf_path) {
            return response()->json([
                'message' => 'File not found'
            ], 404);
        }

        return response()->download(
            storage_path('app/public/' . $document->pdf_path)
        );
    }
}