<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectCategory5TowerDetail extends Model
{
    protected $fillable = [
        'project_id',
        'tower_name',
        'total_units',
        'total_floor',
        'sub_category_ids',
    ];

    protected function casts(): array
    {
        return [
            'sub_category_ids' => 'array',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}

