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
    /*
    |--------------------------------------------------------------------------
    | GET Templates (Only Active Company)
    |--------------------------------------------------------------------------
    */
    public function index(Request $request)
    {
        $companyId = $request->user()->active_company_id;

        return response()->json(
            DocumentTemplate::where('company_id', $companyId)->get()
        );
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE Template
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'type'    => 'required|string|max:100',
            'content' => 'required|string',
        ]);

        $companyId = $request->user()->active_company_id;

        $template = DocumentTemplate::create([
            'company_id' => $companyId,
            'name'       => $request->name,
            'type'       => $request->type,
            'content'    => $request->content,
        ]);

        return response()->json([
            'message' => 'Template created successfully',
            'data' => $template
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | GENERATE DOCUMENT
    |--------------------------------------------------------------------------
    */
    public function generate(Request $request, $id, TemplateService $service)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id'
        ]);

        $companyId = $request->user()->active_company_id;

        $template = DocumentTemplate::where('id', $id)
            ->where('company_id', $companyId)
            ->firstOrFail();

        $client = Client::where('id', $request->client_id)
            ->where('company_id', $companyId)
            ->firstOrFail();

        $company = Company::findOrFail($companyId);

        $data = [
            '{{client_name}}'  => $client->name,
            '{{company_name}}' => $company->name,
            '{{price}}'        => $request->input('price', 0),
        ];

        $output = $service->render($template->content, $data);

        if ($request->input('preview') == true) {
            return response()->json([
                'generated' => $output
            ]);
        }

        $html = "
            <h2 style='text-align:center;'>{$template->name}</h2>
            <hr>
            <p style='font-size:14px; line-height:1.6;'>{$output}</p>
        ";

        $pdf = Pdf::loadHTML($html);

        return $pdf->download('document.pdf');
    }
}