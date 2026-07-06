<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\User;
use Spatie\Permission\Models\Role;

/**
 * Class UserController
 * @package App\Http\Controllers\Api
 *
 * User Management module:
 * - super-admin: manage ALL users, assign ANY role
 * - admin:       manage users of their own companies only, and can only
 *                assign roles BELOW admin (hr-manager, office-manager, employee)
 *
 * SECURITY: role hierarchy is enforced here on the backend — frontend
 * gating alone is not sufficient (users can tamper with browser storage).
 */
class UserController extends Controller
{
    /**
     * Guard used across the app for Spatie roles/permissions.
     * Must match RolesAndPermissionsSeeder::GUARD and routes/api.php.
     */
    private const GUARD = 'api';

    /**
     * Role hierarchy — lower number = higher privilege.
     * A user can only assign roles STRICTLY BELOW their own level.
     */
    private const ROLE_LEVELS = [
        'super-admin'    => 1,
        'admin'          => 2,
        'hr-manager'     => 3,
        'office-manager' => 3,
        'employee'       => 4,
    ];

    /**
     * Returns the acting user's highest privilege level (lowest number).
     */
    private function actorLevel(User $actor): int
    {
        $levels = $actor->getRoleNames()
            ->map(fn ($r) => self::ROLE_LEVELS[$r] ?? 99);

        return $levels->isEmpty() ? 99 : $levels->min();
    }

    /**
     * Whether the actor is allowed to assign the given role.
     * - super-admin: can assign ANY role (including another super-admin,
     *   so a backup super-admin can be created from the UI)
     * - everyone else: target role must be STRICTLY below their own level
     *   (admin can assign hr/office/employee, never admin or super-admin)
     */
    private function canAssignRole(User $actor, string $roleName): bool
    {
        $targetLevel = self::ROLE_LEVELS[$roleName] ?? null;
        if ($targetLevel === null) return false;

        if ($actor->hasRole('super-admin')) {
            return true;
        }

        return $targetLevel > $this->actorLevel($actor);
    }

    /**
     * Whether the actor may manage (edit/delete) the target user at all.
     * - Cannot manage someone at your own level or above
     * - admin can only manage users who share at least one of their companies
     */
    private function canManageUser(User $actor, User $target): bool
    {
        // Can't manage peers or superiors (prevents admin editing another admin
        // or a super-admin, and prevents self-role-escalation paths)
        $targetLevel = $this->actorLevel($target);
        if ($targetLevel <= $this->actorLevel($actor)) {
            return false;
        }

        // super-admin manages everyone below them, no company restriction
        if ($actor->hasRole('super-admin')) {
            return true;
        }

        // admin: target must share at least one company with the actor
        $actorCompanyIds  = $actor->companies()->pluck('companies.id');
        $sharesCompany    = $target->companies()
            ->whereIn('companies.id', $actorCompanyIds)
            ->exists();

        return $sharesCompany;
    }

    /*
    |--------------------------------------------------------------------------
    | LIST USERS
    |--------------------------------------------------------------------------
    | super-admin: all users
    | admin:       users belonging to any of the admin's companies
    | Includes each user's role and companies for the management table.
    */
    public function index(Request $request)
    {
        $actor = $request->user();

        $query = User::with(['companies:id,name', 'roles:id,name']);

        if (!$actor->hasRole('super-admin')) {
            // Scope to the actor's companies
            $companyIds = $actor->companies()->pluck('companies.id');
            $query->whereHas('companies', function ($q) use ($companyIds) {
                $q->whereIn('companies.id', $companyIds);
            });
        }

        // Optional search by name/email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('name')->get()->map(function (User $u) {
            return [
                'id'                => $u->id,
                'name'              => $u->name,
                'email'             => $u->email,
                'role'              => $u->getRoleNames()->first(),
                'companies'         => $u->companies->map(fn ($c) => ['id' => $c->id, 'name' => $c->name]),
                'active_company_id' => $u->active_company_id,
                'created_at'        => $u->created_at,
            ];
        });

        return response()->json([
            'status' => 'success',
            'data'   => $users,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | ASSIGNABLE ROLES
    |--------------------------------------------------------------------------
    | Returns only the roles the acting user is allowed to assign, so the
    | frontend role dropdown never shows options the backend would reject.
    */
    public function assignableRoles(Request $request)
    {
        $actor = $request->user();

        $roles = Role::where('guard_name', self::GUARD)
            ->get()
            ->filter(fn (Role $r) => $this->canAssignRole($actor, $r->name))
            ->values()
            ->map(fn (Role $r) => ['id' => $r->id, 'name' => $r->name]);

        return response()->json([
            'status' => 'success',
            'data'   => $roles,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE USER
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $actor = $request->user();

        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|max:255|unique:users,email',
            'password'      => 'required|string|min:8',
            'role'          => ['required', 'string', Rule::in(array_keys(self::ROLE_LEVELS))],
            'company_ids'   => 'required|array|min:1',
            'company_ids.*' => 'integer|exists:companies,id',
        ]);

        // ── SECURITY: privilege escalation prevention ───────────────────────
        if (!$this->canAssignRole($actor, $validated['role'])) {
            return response()->json([
                'status'  => 'error',
                'message' => 'You are not allowed to assign this role.',
            ], 403);
        }

        // ── SECURITY: admin can only attach users to their own companies ────
        if (!$actor->hasRole('super-admin')) {
            $actorCompanyIds = $actor->companies()->pluck('companies.id')->all();
            $invalid = array_diff($validated['company_ids'], $actorCompanyIds);
            if (!empty($invalid)) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'You can only assign users to your own companies.',
                ], 403);
            }
        }

        $user = User::create([
            'name'              => $validated['name'],
            'email'             => $validated['email'],
            'password'          => Hash::make($validated['password']),
            'active_company_id' => $validated['company_ids'][0],
        ]);

        $user->companies()->attach($validated['company_ids']);
        $user->assignRole(Role::findByName($validated['role'], self::GUARD));

        return response()->json([
            'status'  => 'success',
            'message' => 'User created successfully',
            'data'    => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'role'  => $validated['role'],
            ],
        ], 201);
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE USER (name, email, password, role, companies)
    |--------------------------------------------------------------------------
    */
    public function update(Request $request, $id)
    {
        $actor  = $request->user();
        $target = User::find($id);

        if (!$target) {
            return response()->json([
                'status'  => 'error',
                'message' => 'User not found',
            ], 404);
        }

        // Users may not edit themselves through this management endpoint
        // (self profile editing, if needed, should be a separate endpoint
        // that cannot change one's own role).
        if ($actor->id === $target->id) {
            return response()->json([
                'status'  => 'error',
                'message' => 'You cannot manage your own account from here.',
            ], 403);
        }

        if (!$this->canManageUser($actor, $target)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'You are not allowed to manage this user.',
            ], 403);
        }

        $validated = $request->validate([
            'name'          => 'sometimes|required|string|max:255',
            'email'         => ['sometimes', 'required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($target->id)],
            'password'      => 'sometimes|nullable|string|min:8',
            'role'          => ['sometimes', 'required', 'string', Rule::in(array_keys(self::ROLE_LEVELS))],
            'company_ids'   => 'sometimes|required|array|min:1',
            'company_ids.*' => 'integer|exists:companies,id',
        ]);

        // Role change — re-check escalation rules for the NEW role too
        if (isset($validated['role'])) {
            if (!$this->canAssignRole($actor, $validated['role'])) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'You are not allowed to assign this role.',
                ], 403);
            }
        }

        // Company reassignment — admin restricted to own companies
        if (isset($validated['company_ids']) && !$actor->hasRole('super-admin')) {
            $actorCompanyIds = $actor->companies()->pluck('companies.id')->all();
            $invalid = array_diff($validated['company_ids'], $actorCompanyIds);
            if (!empty($invalid)) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'You can only assign users to your own companies.',
                ], 403);
            }
        }

        // Apply updates
        if (isset($validated['name']))  $target->name  = $validated['name'];
        if (isset($validated['email'])) $target->email = $validated['email'];
        if (!empty($validated['password'])) {
            $target->password = Hash::make($validated['password']);
        }
        $target->save();

        if (isset($validated['company_ids'])) {
            $target->companies()->sync($validated['company_ids']);

            // If their active company was removed, reset it to the first assigned one
            if (!in_array($target->active_company_id, $validated['company_ids'])) {
                $target->update(['active_company_id' => $validated['company_ids'][0]]);
            }
        }

        if (isset($validated['role'])) {
            $target->syncRoles([Role::findByName($validated['role'], self::GUARD)]);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'User updated successfully',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE USER
    |--------------------------------------------------------------------------
    | Route is gated by users.delete (super-admin only per permission matrix),
    | but hierarchy is still re-checked here as defense in depth.
    */
    public function destroy(Request $request, $id)
    {
        $actor  = $request->user();
        $target = User::find($id);

        if (!$target) {
            return response()->json([
                'status'  => 'error',
                'message' => 'User not found',
            ], 404);
        }

        // Never allow self-deletion
        if ($actor->id === $target->id) {
            return response()->json([
                'status'  => 'error',
                'message' => 'You cannot delete your own account.',
            ], 403);
        }

        if (!$this->canManageUser($actor, $target)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'You are not allowed to delete this user.',
            ], 403);
        }

        // Preserve document history: created_by on documents is nullable
        // with onDelete('set null'), so records survive user deletion.
        $target->companies()->detach();
        $target->syncRoles([]);
        $target->tokens()->delete(); // revoke all their API tokens immediately
        $target->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'User deleted successfully',
        ]);
    }
}