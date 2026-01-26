<?php

namespace App\Models;

use Actuallymab\LaravelComment\HasComments;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\InteractsWithMedia;

class Merchant extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\MerchantFactory> */
    use HasFactory, HasComments, InteractsWithMedia;

    public function canBeRated(): bool
    {
        return true;
    }

    public function mustBeApproved(): bool
    {
        return true;
    }

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'type',
        'category',
        'name',
        'description',
        'legal_address',
        'registration_number',
        'website',
        'tin',
        'emails',
        'phones',
        'mobile_apps',
        'social_media',
        'auth_type',
        'auth_value',
        'base_url',
        'api_key',
        'sync_type',
        'status',
        'last_synced_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'auth_value' => 'encrypted',
            'last_synced_at' => 'datetime',
        ];
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this
            ->addMediaConversion('preview')
            ->fit(Fit::Contain, 300, 300)
            ->nonQueued();
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
