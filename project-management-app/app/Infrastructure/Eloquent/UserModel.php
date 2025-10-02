<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class UserModel extends Model
{
    use HasApiTokens;

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
        'remember_token',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'email_verified_at' => 'datetime',
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
