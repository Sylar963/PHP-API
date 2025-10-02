<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ProjectModel extends Model
{
    use HasUuids;

    protected $table = 'projects';

    protected $fillable = [
        'name',
        'description',
        'status',
        'owner_id',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function owner()
    {
        return $this->belongsTo(UserModel::class, 'owner_id');
    }

    public function tasks()
    {
        return $this->hasMany(TaskModel::class, 'project_id');
    }

    public function milestones()
    {
        return $this->hasMany(MilestoneModel::class, 'project_id');
    }
}
