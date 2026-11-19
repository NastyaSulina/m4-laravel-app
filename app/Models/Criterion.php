<?php

namespace App\Models;

use Database\Factories\CriterionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Criterion extends Model
{
    /** @use HasFactory<CriterionFactory> */
    use HasFactory;

    protected $fillable = ['title', 'is_required'];

    public function selection(): BelongsTo
    {
        return $this->belongsTo(Selection::class);
    }
}
