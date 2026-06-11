<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class DocumentTemplate
 * @package App\Models
 * 
 * @property int $id
 * @property int $company_id
 * @property string $name
 * @property string $type
 * @property string|null $category
 * @property string|null $sub_category
 * @property string $content
 * @property bool $is_active
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * 
 * @property-read Company $company
 * @property-read \Illuminate\Database\Eloquent\Collection|Document[] $documents
 */
class DocumentTemplate extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'document_templates';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'company_id',
        'name',
        'type',
        'category',
        'sub_category',
        'content',
        'is_active'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the company that owns the template
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the documents created from this template
     */
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class, 'document_template_id');
    }

    /**
     * Scope active templates only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope by company
     */
    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Scope by category
     */
    public function scopeOfCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Get all available categories for this company
     */
    public static function getCategoriesForCompany(int $companyId)
    {
        return self::where('company_id', $companyId)
            ->whereNotNull('category')
            ->distinct()
            ->pluck('category');
    }

    /**
     * Get all available sub-categories for this company and category
     */
    public static function getSubCategoriesForCompany(int $companyId, ?string $category = null)
    {
        $query = self::where('company_id', $companyId)
            ->whereNotNull('sub_category');
            
        if ($category) {
            $query->where('category', $category);
        }
        
        return $query->distinct()->pluck('sub_category');
    }
}