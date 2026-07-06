<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Task;
use App\Models\User;

/**
 * Class TaskController
 * @package App\Http\Controllers\Api
 *
 * Task Management module:
 * - Managers (tasks.view.all): see/manage the ACTIVE COMPANY's tasks
 * - Employees (tasks.view.own only): see their OWN tasks across all
 *   their companies — so switching company is never needed just to
 *   find your work, and nothing assigned to you can be missed.
 *
 * The completed task rows themselves ARE the history record:
 * company_id + assigned_to + assigned_by + completed_at answer
 * "kis ne, kis company ka, konsa task, kab kiya".
 */
class TaskController extends Controller
{
    /** Shape a task for API responses (names resolved, no heavy nesting). */
    private function serialize(Task $t): array
    {
        return [
            'id'            => $t->id,
            'company_id'    => $t->company_id,
            'company_name'  => $t->company?->name,
            'title'         => $t->title,
            'description'   => $t->description,
            'assigned_to'   => $t->assigned_to,
            'assignee_name' => $t->assignee?->name,
            'assigned_by'   => $t->assigned_by,
            'assigner_name' => $t->assigner?->name,
            'status'        => $t->status,
            'due_date'      => $t->due_date?->toDateString(),
            'completed_at'  => $t->completed_at?->toIso8601String(),
            'created_at'    => $t->created_at?->toIso8601String(),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | LIST TASKS
    |--------------------------------------------------------------------------
    | ?scope=own            → my tasks (all my companies) — any role
    | default (managers)    → active company's tasks
    | default (employees)   → falls back to own automatically
    | Optional: ?status=pending|in_progress|completed
    */
    public function index(Request $request)
    {
        $user = $request->user();

        $ownScope = $request->query('scope') === 'own'
            || !$user->hasPermissionTo('tasks.view.all', 'api');

        $query = Task::with(['company:id,name', 'assignee:id,name', 'assigner:id,name']);

        if ($ownScope) {
            $query->where('assigned_to', $user->id);
        } else {
            // Manager view is scoped to the active company, consistent
            // with the rest of the app (clients, templates, documents).
            $companyId = $user->active_company_id;

            if (!$companyId) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'No active company selected',
                ], 400);
            }

            if (!$user->belongsToCompany($companyId)) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Unauthorized company access',
                ], 403);
            }

            $query->where('company_id', $companyId);
        }

        if ($request->filled('status') && in_array($request->status, Task::STATUSES, true)) {
            $query->where('status', $request->status);
        }

        $tasks = $query
            ->orderByRaw("FIELD(status, 'pending', 'in_progress', 'completed')")
            ->orderByRaw('due_date IS NULL, due_date ASC')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (Task $t) => $this->serialize($t));

        return response()->json([
            'status' => 'success',
            'data'   => $tasks,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | ASSIGNABLE USERS  (tasks.create — for the assignee dropdown)
    |--------------------------------------------------------------------------
    | Lightweight list of a company's members. Gated by tasks.create rather
    | than users.view so office-manager (who can assign tasks but cannot
    | manage users) can still populate the dropdown.
    */
    public function assignableUsers(Request $request)
    {
        $actor = $request->user();

        $validated = $request->validate([
            'company_id' => 'required|integer|exists:companies,id',
        ]);

        if (!$actor->hasRole('super-admin') && !$actor->belongsToCompany($validated['company_id'])) {
            return response()->json([
                'status'  => 'error',
                'message' => 'You can only view members of your own companies.',
            ], 403);
        }

        $users = User::whereHas('companies', function ($q) use ($validated) {
                $q->where('companies.id', $validated['company_id']);
            })
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        return response()->json([
            'status' => 'success',
            'data'   => $users,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE TASK  (tasks.create — managers)
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $actor = $request->user();

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'company_id'  => 'required|integer|exists:companies,id',
            'assigned_to' => 'required|integer|exists:users,id',
            'due_date'    => 'nullable|date',
        ]);

        // Creator must belong to the company the task is for
        // (super-admin belongs to all seeded companies; still checked for safety)
        if (!$actor->hasRole('super-admin') && !$actor->belongsToCompany($validated['company_id'])) {
            return response()->json([
                'status'  => 'error',
                'message' => 'You can only create tasks for your own companies.',
            ], 403);
        }

        // Assignee must belong to that same company — otherwise the task
        // would be invisible in that user's company context.
        $assignee = User::find($validated['assigned_to']);
        if (!$assignee->belongsToCompany($validated['company_id'])) {
            return response()->json([
                'status'  => 'error',
                'message' => 'The selected user does not belong to that company.',
            ], 422);
        }

        $task = Task::create([
            'company_id'  => $validated['company_id'],
            'title'       => $validated['title'],
            'description' => $validated['description'] ?? null,
            'assigned_to' => $validated['assigned_to'],
            'assigned_by' => $actor->id,
            'status'      => Task::STATUS_PENDING,
            'due_date'    => $validated['due_date'] ?? null,
        ]);

        $task->load(['company:id,name', 'assignee:id,name', 'assigner:id,name']);

        return response()->json([
            'status'  => 'success',
            'message' => 'Task created successfully',
            'data'    => $this->serialize($task),
        ], 201);
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE STATUS  (tasks.update.status — everyone, own tasks for employees)
    |--------------------------------------------------------------------------
    */
    public function updateStatus(Request $request, $id)
    {
        $actor = $request->user();
        $task  = Task::find($id);

        if (!$task) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Task not found',
            ], 404);
        }

        // Without tasks.view.all you may only update tasks assigned to you
        $isManager = $actor->hasPermissionTo('tasks.view.all', 'api');
        if (!$isManager && $task->assigned_to !== $actor->id) {
            return response()->json([
                'status'  => 'error',
                'message' => 'You can only update your own tasks.',
            ], 403);
        }

        // Managers may only touch tasks of companies they belong to
        if ($isManager && !$actor->hasRole('super-admin') && !$actor->belongsToCompany($task->company_id)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Unauthorized company access',
            ], 403);
        }

        $validated = $request->validate([
            'status' => ['required', Rule::in(Task::STATUSES)],
        ]);

        $task->status = $validated['status'];

        // completed_at is the permanent "kab kiya" record
        $task->completed_at = $validated['status'] === Task::STATUS_COMPLETED
            ? now()
            : null;

        $task->save();
        $task->load(['company:id,name', 'assignee:id,name', 'assigner:id,name']);

        return response()->json([
            'status'  => 'success',
            'message' => 'Task status updated',
            'data'    => $this->serialize($task),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE TASK  (tasks.delete — super-admin, admin)
    |--------------------------------------------------------------------------
    */
    public function destroy(Request $request, $id)
    {
        $actor = $request->user();
        $task  = Task::find($id);

        if (!$task) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Task not found',
            ], 404);
        }

        if (!$actor->hasRole('super-admin') && !$actor->belongsToCompany($task->company_id)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Unauthorized company access',
            ], 403);
        }

        $task->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Task deleted successfully',
        ]);
    }
}