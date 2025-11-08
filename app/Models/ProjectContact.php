<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectContact extends Model
{
    protected $fillable = [
        'project_id',
        'name',
        'mobile',
        'email',
        'designation',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}

