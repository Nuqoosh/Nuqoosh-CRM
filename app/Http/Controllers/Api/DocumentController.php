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
    */
    public function generate(Request $request)
    {
        $request->validate([
    'client_id' => 'required|exists:clients,id',
    'template_id' => 'required|exists:document_templates,id',

    'price' => 'required',

    'contract_number' => 'nullable|string',
    'client_address' => 'nullable|string',
    'contract_date' => 'nullable|string',
    'delivery_date' => 'nullable|string',
    'amount' => 'nullable|string',
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

/*
|--------------------------------------------------------------------------
| VALIDATE FETCHED DATA
|--------------------------------------------------------------------------
*/
if (!$template || !$client || !$company) {
    return response()->json([
        'message' => 'Invalid data'
    ], 404);
}

/*
|--------------------------------------------------------------------------
| AUTO CONTRACT NUMBER GENERATION
|--------------------------------------------------------------------------
*/
$year = Carbon::now()->year;

$prefix = 'DOC';

if (strtolower($company->name) === 'vmc') {

    if (strtolower($template->category) === 'nda') {
        $prefix = 'VMC-NDA';
    } else {
        $prefix = 'VMC-CON';
    }

} elseif (strtolower($company->name) === 'nuqoosh') {

    if ($template->sub_category === 'Website Only') {
        $prefix = 'NQ-WE';
    }
    elseif ($template->sub_category === 'Website + Branding') {
        $prefix = 'NQ-WB';
    }
    elseif ($template->sub_category === 'Branding Only') {
        $prefix = 'NQ-BR';
    }
    else {
        $prefix = 'NQ-NDA';
    }

} else {

    $prefix = 'HOBS';

}

$count = Document::whereYear('created_at', $year)->count() + 1;

$generatedContractNumber =
    $prefix . '-' .
    $year . '-' .
    str_pad($count, 3, '0', STR_PAD_LEFT);



        // TEMPLATE ENGINE
        $content = $template->content;
    
$content = str_replace('{{client_name}}', $client->name, $content);
$content = str_replace('{{company_name}}', $company->name, $content);
$content = str_replace('{{price}}', $request->price, $content);

$content = str_replace('{{contract_number}}', $generatedContractNumber, $content);
$content = str_replace('{{client_address}}', $request->client_address, $content);
$content = str_replace('{{contract_date}}', $request->contract_date, $content);
$content = str_replace('{{delivery_date}}', $request->delivery_date, $content);
$content = str_replace('{{amount}}', $request->amount, $content);
        
        // SAVE DOCUMENT
        $document = Document::create([
    'company_id' => $companyId,
    'client_id' => $client->id,
    'document_template_id' => $template->id,
    'content' => $content,
    'contract_number' => $generatedContractNumber
]);

        // PDF GENERATION
         $logo = '';

if (strtolower($company->name) === 'vmc') {
    $logo = public_path('logos/vmc.png');
} elseif (strtolower($company->name) === 'nuqoosh') {
    $logo = public_path('logos/nuqoosh.png');
}

$html = '



<html>

<head>

<style>



body{

font-family: DejaVu Sans;

font-size:13px;

color:#333;

margin:25px;

}



.header{

text-align:center;

margin-bottom:20px;

}



.logo{

height:90px;

margin-bottom:10px;

}



.company{

font-size:24px;

font-weight:bold;

color:#0b1f3a;

}



.generated-date{

font-size:12px;

color:#666;

margin-top:5px;

}



.doc-title{

text-align:center;

font-size:20px;

font-weight:bold;

margin:20px 0;

border-bottom:2px solid #0b1f3a;

padding-bottom:10px;

}



.info-box{

border:1px solid #ddd;

padding:12px;

margin-bottom:20px;

background:#f8f8f8;

}



.info-box h3{

margin-top:0;

margin-bottom:15px;

color:#0b1f3a;

}



.info-box table{

width:100%;

border-collapse:collapse;

}



.info-box td{

padding:8px;

vertical-align:top;

}



.content{

line-height:1.8;

margin-top:20px;

text-align:justify;

}



.signature{

margin-top:80px;

width:100%;

}



.signature td{

width:50%;

text-align:center;

padding-top:40px;

}



.line{

border-top:1px solid #000;

width:220px;

margin:0 auto 10px auto;

}



.footer{

margin-top:40px;

text-align:center;

font-size:11px;

color:#777;

}



</style>

</head>



<body>



<div class="header">



'.($logo ? '<img src="'.$logo.'" class="logo">' : '').'



<div class="company">

'.$company->name.'

</div>



<div class="generated-date">

Generated On: '.date('d-m-Y').'

</div>



</div>



<div class="doc-title">

'.$template->name.'

</div>



<div class="info-box">



<h3>Contract Information</h3>



<table width="100%">



<tr>

<td><strong>Client Name</strong></td>

<td>'.$client->name.'</td>



<td><strong>Contract Number</strong></td>

<td>'.$generatedContractNumber.'</td>

</tr>



<tr>

<td><strong>Client Address</strong></td>

<td>'.($request->client_address ?? '-').'</td>



<td><strong>Contract Date</strong></td>

<td>'.($request->contract_date ?? '-').'</td>

</tr>



<tr>

<td><strong>Delivery Date</strong></td>

<td>'.($request->delivery_date ?? '-').'</td>



<td><strong>Amount</strong></td>

<td>'.($request->amount ?? '-').'</td>

</tr>



</table>



</div>



<div class="content">

'.nl2br($content).'

</div>



<table class="signature">



<tr>



<td>

<div class="line"></div>

Authorized Signatory<br>

'.$company->name.'

</td>



<td>

<div class="line"></div>

'.$client->name.'<br>

Client Signature

</td>



</tr>



</table>



<div class="footer">

This document was generated electronically by  CRM System.

</div>



</body>

</html>

';  

$pdf = Pdf::loadHTML($html);

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