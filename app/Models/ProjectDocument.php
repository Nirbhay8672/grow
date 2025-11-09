<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectDocument extends Model
{
    protected $fillable = [
        'project_id',
        'category',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}

