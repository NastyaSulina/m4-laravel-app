<?php

namespace App\Models;

use Database\Factories\AccommodationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Accommodation extends Model implements HasMedia
{
    /** @use HasFactory<AccommodationFactory> */
    use HasFactory;

    use InteractsWithMedia;
    use SoftDeletes;

    protected $fillable = ['user_id', 'title', 'source_url', 'address', 'status', 'property_type', 'note'];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images')
            ->singleFile()
            ->useFallbackUrl('/img/default-accommodation.png');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumbnail')
            ->fit(Fit::Contain, 600, 600)
            ->format('jpg')
            ->quality(90)
            ->performOnCollections('images')
            ->nonQueued();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function selection(): BelongsTo
    {
        return $this->belongsTo(Selection::class);
    }

    public function criteria(): BelongsToMany
    {
        return $this->belongsToMany(Criterion::class)->withPivot('is_met');
    }
}
