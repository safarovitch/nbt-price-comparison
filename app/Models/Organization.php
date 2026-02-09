<?php

namespace App\Models;

use Actuallymab\LaravelComment\HasComments;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

class Organization extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\OrganizationFactory> */
    use HasFactory, HasComments, InteractsWithMedia, HasUuids, HasTranslations;

    /**
     * Translatable fields for spatie/laravel-translatable
     * @var array<int, string>
     */
    public array $translatable = ['name', 'description', 'legal_address'];

    protected $primaryKey = 'uuid';
    protected $keyType = 'string';
    public $incrementing = false;

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
        'webhook_secret',
        'endpoints',
        'sync_type',
        'status',
        'last_synced_at',
        'last_sync_status',
        'last_sync_error',
        'sync_started_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'auth_value' => 'encrypted',
            'last_synced_at' => 'datetime',
            'endpoints' => 'array',
            'emails' => 'array',
            'phones' => 'array',
            'mobile_apps' => 'array',
            'social_media' => 'array',
            'sync_started_at' => 'datetime',
        ];
    }

    public function getIsActiveAttribute(): bool
    {
        return $this->status === 'active';
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

    public function deviceLocations(): HasMany
    {
        return $this->hasMany(DeviceLocation::class, 'organization_uuid', 'uuid');
    }
}
