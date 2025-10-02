<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class TimeEntryModel extends Model
{
    use HasUuids;

    protected $table = 'time_entries';

    protected $fillable = [
        'user_id',
        'task_id',
        'start_time',
        'end_time',
        'duration',
        'description',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'duration' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(UserModel::class, 'user_id');
    }

    public function task()
    {
        return $this->belongsTo(TaskModel::class, 'task_id');
    }
}
