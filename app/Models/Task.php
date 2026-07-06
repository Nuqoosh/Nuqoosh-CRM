<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Task
 * @package App\Models
 *
 * @property int $id
 * @property int $company_id
 * @property string $title
 * @property string|null $description
 * @property int|null $assigned_to
 * @property int|null $assigned_by
 * @property string $status  pending|in_progress|completed
 * @property \Carbon\Carbon|null $due_date
 * @property \Carbon\Carbon|null $completed_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @property-read Company $company
 * @property-read User|null $assignee
 * @property-read User|null $assigner
 */
class Task extends Model
{
    use HasFactory;

    protected $table = 'tasks';

    public const STATUS_PENDING     = 'pending';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED   = 'completed';

    public const STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_IN_PROGRESS,
        self::STATUS_COMPLETED,
    ];

    protected $fillable = [
        'company_id',
        'title',
        'description',
        'assigned_to',
        'assigned_by',
        'status',
        'due_date',
        'completed_at',
    ];

    protected $casts = [
        'company_id'   => 'integer',
        'assigned_to'  => 'integer',
        'assigned_by'  => 'integer',
        'due_date'     => 'date',
        'completed_at' => 'datetime',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /** The user this task is assigned TO. */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /** The user who created/assigned this task. */
    public function assigner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}