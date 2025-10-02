<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class TeamModel extends Model
{
    use HasUuids;

    protected $table = 'teams';

    protected $fillable = [
        'name',
        'description',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function members()
    {
        return $this->belongsToMany(UserModel::class, 'team_user', 'team_id', 'user_id');
    }
}
