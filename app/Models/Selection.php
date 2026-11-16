<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Selection extends Model
{
    /** @use HasFactory<\Database\Factories\SelectionFactory> */
    use HasFactory;

    public function accommodations(): HasMany
    {
        return $this->hasMany(Accommodation::class);
    }
}
