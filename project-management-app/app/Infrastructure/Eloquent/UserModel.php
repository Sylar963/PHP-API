<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class UserModel extends Model
{
    use HasUuids;

    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function ownedProjects()
    {
        return $this->hasMany(ProjectModel::class, 'owner_id');
    }

    public function assignedTasks()
    {
        return $this->hasMany(TaskModel::class, 'assigned_to');
    }

    public function teams()
    {
        return $this->belongsToMany(TeamModel::class, 'team_user', 'user_id', 'team_id');
    }

    public function timeEntries()
    {
        return $this->hasMany(TimeEntryModel::class, 'user_id');
    }
}
