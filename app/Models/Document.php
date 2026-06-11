<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Document
 * @package App\Models
 * 
 * @property int $id
 * @property int $company_id
 * @property int $client_id
 * @property int $document_template_id
 * @property string $content
 * @property string|null $pdf_path
 * @property string|null $contract_number
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * 
 * @property-read Company $company
 * @property-read Client $client
 * @property-read DocumentTemplate $template
 */
class Document extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'documents';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'company_id',
        'client_id',
        'document_template_id',
        'content',
        'pdf_path',
        'contract_number',
        'created_by'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the company that owns the document
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the client this document belongs to
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the template used for this document
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(DocumentTemplate::class, 'document_template_id');
    }

    /**
     * Get the user who created this document
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope by company
     */
    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Scope by client
     */
    public function scopeForClient($query, $clientId)
    {
        return $query->where('client_id', $clientId);
    }

    /**
     * Get the full PDF URL
     */
    public function getPdfUrlAttribute(): ?string
    {
        if ($this->pdf_path) {
            return asset('storage/' . $this->pdf_path);
        }
        return null;
    }

    /**
     * Get a formatted contract number with prefix
     */
    public function getFormattedContractNumberAttribute(): string
    {
        return $this->contract_number ?? 'DOC-' . str_pad($this->id, 5, '0', STR_PAD_LEFT);
    }
}