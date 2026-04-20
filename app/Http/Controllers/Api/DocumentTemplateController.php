<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DocumentTemplate;
use App\Models\Client;
use App\Models\Company;
use App\Services\TemplateService;
use Barryvdh\DomPDF\Facade\Pdf;

class DocumentTemplateController extends Controller
{
    // GET /api/document-templates
    public function index()
    {
        return response()->json(DocumentTemplate::all());
    }

    // POST /api/document-templates
    public function store(Request $request)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'name' => 'required|string',
            'type' => 'required|string',
            'content' => 'required|string',
        ]);

        $template = DocumentTemplate::create($request->all());

        return response()->json([
            'message' => 'Template created successfully',
            'data' => $template
        ]);
    }

    // 🔥 FINAL GENERATE (PDF + JSON OPTION)
    public function generate(Request $request, $id, TemplateService $service)
    {
        // ✅ Validation
        $request->validate([
            'client_id' => 'required|exists:clients,id'
        ]);

        // ✅ Fetch data
        $template = DocumentTemplate::findOrFail($id);
        $client = Client::findOrFail($request->client_id);
        $company = Company::findOrFail($template->company_id);

        // ✅ Dynamic mapping
        $data = [
            '{{client_name}}' => $client->name,
            '{{company_name}}' => $company->name,
            '{{price}}' => $request->input('price', 0),
        ];

        // ✅ Render content
        $output = $service->render($template->content, $data);

        // 👉 Agar sirf preview chahiye (optional)
        if ($request->input('preview') == true) {
            return response()->json([
                'generated' => $output
            ]);
        }

        // ✅ HTML for PDF (styled)
        $html = "
            <h2 style='text-align:center;'>{$template->name}</h2>
            <hr>
            <p style='font-size:14px; line-height:1.6;'>{$output}</p>
        ";

        // ✅ Generate PDF
        $pdf = Pdf::loadHTML($html);

        // 👉 Download
        return $pdf->download('document.pdf');

        // 👉 Agar browser preview chahiye:
        // return $pdf->stream('document.pdf');
    }
}