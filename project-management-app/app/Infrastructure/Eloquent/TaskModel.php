<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class TaskModel extends Model
{
    use HasUuids;

    protected $table = 'tasks';

    protected $fillable = [
        'title',
        'description',
        'status',
        'priority',
        'project_id',
        'assigned_to',
        'due_date',
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function project()
    {
        return $this->belongsTo(ProjectModel::class, 'project_id');
    }

    public function assignedUser()
    {
        return $this->belongsTo(UserModel::class, 'assigned_to');
    }

    public function timeEntries()
    {
        return $this->hasMany(TimeEntryModel::class, 'task_id');
    }
}
