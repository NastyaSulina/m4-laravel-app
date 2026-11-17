<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Criterion extends Model
{
    /** @use HasFactory<\Database\Factories\CriterionFactory> */
    use HasFactory;

    public function selection(): BelongsTo
    {
        return $this->belongsTo(Selection::class);
    }
}
