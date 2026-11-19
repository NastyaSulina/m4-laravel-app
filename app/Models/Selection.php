<?php

namespace App\Models;

use Database\Factories\SelectionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Selection extends Model
{
    /** @use HasFactory<SelectionFactory> */
    use HasFactory;

    protected $fillable = ['title', 'search_type', 'status', 'start_date', 'end_date'];

    public function accommodations(): HasMany
    {
        return $this->hasMany(Accommodation::class);
    }

    public function criteria(): HasMany
    {
        return $this->hasMany(Criterion::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
