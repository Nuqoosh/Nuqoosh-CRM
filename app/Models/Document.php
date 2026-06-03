<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = [
        'company_id',
        'client_id',
        'document_template_id',
        'content',
        'pdf_path',
        'contract_number'
    ];
}