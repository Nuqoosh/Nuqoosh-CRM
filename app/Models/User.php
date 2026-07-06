<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class User
 * @package App\Models
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property int|null $active_company_id
 * @property string|null $remember_token
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|Company[] $companies
 * @property-read \Illuminate\Database\Eloquent\Collection|Document[] $documents
 * @property-read \Illuminate\Database\Eloquent\Collection|DocumentTemplate[] $templates
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * Forces Spatie to always resolve permissions against the 'web' guard,
     * consistent with how roles/permissions were seeded in
     * RolesAndPermissionsSeeder (guard_name = 'api').
     *
     * Without this, Spatie tries to resolve the guard from the Sanctum
     * authentication context at request time — which doesn't match the
     * 'web' guard used when the roles/permissions were created — causing
     * permission checks to silently fail with 403 even when the user has
     * the correct role assigned.
     */
    protected $guard_name = 'api';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'active_company_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'created_at'        => 'datetime',
        'updated_at'        => 'datetime',
        'active_company_id' => 'integer',
    ];

    /**
     * Get the companies this user belongs to (many-to-many).
     */
    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class, 'company_user')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    /**
     * Get the active company.
     */
    public function activeCompany()
    {
        return $this->belongsTo(Company::class, 'active_company_id');
    }

    /**
     * Get documents created by this user.
     */
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class, 'created_by');
    }

    /**
     * Check if user belongs to a specific company.
     */
    public function belongsToCompany(int $companyId): bool
    {
        return $this->companies()->where('companies.id', $companyId)->exists();
    }

    /**
     * Get user's pivot role in a specific company (company_user.role).
     * This is the per-company pivot role — separate from Spatie global roles.
     */
    public function getCompanyRole(int $companyId): ?string
    {
        $company = $this->companies()
            ->where('companies.id', $companyId)
            ->first();

        return $company ? $company->pivot->role : null;
    }

    /**
     * Check if user has a global admin role (Spatie).
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin') || $this->hasRole('super-admin');
    }

    /**
     * Check if user has the admin pivot role for a specific company.
     */
    public function isCompanyAdmin(int $companyId): bool
    {
        return $this->getCompanyRole($companyId) === 'admin';
    }
}