<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Company
 * @package App\Models
 */
class Company extends Model
{
    use HasFactory;

    protected $table = 'companies';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'tax_number',
        'logo_path',
        'website',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $appends = ['logo_url'];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'company_user')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    public function clients(): HasMany
    {
        return $this->hasMany(Client::class);
    }

    public function templates(): HasMany
    {
        return $this->hasMany(DocumentTemplate::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function getLogoUrlAttribute(): ?string
    {
        if ($this->logo_path) {
            return asset('storage/' . $this->logo_path);
        }
        
        $slug = strtolower(preg_replace('/[^a-zA-Z]/', '', $this->name));
        $fallbackPath = public_path("logos/{$slug}.png");
        
        if (file_exists($fallbackPath)) {
            return asset("logos/{$slug}.png");
        }
        
        return null;
    }

    public function getSlugAttribute(): string
    {
        return strtolower(preg_replace('/[^a-zA-Z]/', '', $this->name));
    }

    public function hasUser($user): bool
    {
        $userId = $user instanceof User ? $user->id : $user;
        return $this->users()->where('users.id', $userId)->exists();
    }

    public function getUserRole($user): ?string
    {
        $userId = $user instanceof User ? $user->id : $user;
        $company = $this->users()->where('users.id', $userId)->first();
        return $company ? $company->pivot->role : null;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}