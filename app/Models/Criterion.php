<?php

namespace App\Models;

use Database\Factories\CriterionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Criterion extends Model
{
    /** @use HasFactory<CriterionFactory> */
    use HasFactory;

    protected $fillable = ['title'];

    public function selection(): BelongsTo
    {
        return $this->belongsTo(Selection::class);
    }

    public function accommodations(): BelongsToMany
    {
        return $this->belongsToMany(Accommodation::class)->withPivot('is_met');
    }
}
