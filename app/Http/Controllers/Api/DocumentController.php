<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Document;
use App\Models\DocumentTemplate;
use App\Models\Client;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function generate(Request $request)
{
    $request->validate([
        'company_id' => 'required',
        'client_id' => 'required',
        'template_id' => 'required',
        'price' => 'required'
    ]);

    $template = DocumentTemplate::find($request->template_id);
    $client = Client::find($request->client_id);
    $company = Company::find($request->company_id);

    if (!$template || !$client || !$company) {
        return response()->json(['message' => 'Invalid data'], 404);
    }

    // TEMPLATE ENGINE
    $content = $template->content;

    $content = str_replace('{{client_name}}', $client->name, $content);
    $content = str_replace('{{company_name}}', $company->name, $content);
    $content = str_replace('{{price}}', $request->price, $content);

    // SAVE DOCUMENT
    $document = Document::create([
        'company_id' => $request->company_id,
        'client_id' => $request->client_id,
        'document_template_id' => $request->template_id,
        'content' => $content
    ]);

    // PDF GENERATION
    $pdf = Pdf::loadHTML("
        <h2>Document</h2>
        <hr>
        <p>{$content}</p>
    ");

    $fileName = 'document_' . time() . '.pdf';

    Storage::put('public/' . $fileName, $pdf->output());

    // UPDATE DOCUMENT WITH PDF PATH
    $document->update([
        'pdf_path' => $fileName
    ]);

    return response()->json([
        'message' => 'Document + PDF generated successfully',
        'document' => $document,
        'download_url' => url('storage/' . $fileName)
    ]);
}
}