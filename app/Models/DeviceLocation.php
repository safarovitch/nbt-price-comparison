<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeviceLocation extends Model
{
    use HasUuids;

    protected $fillable = [
        'organization_uuid',
        'type',
        'city',
        'address',
        'latitude',
        'longitude',
        'working_hours',
        'meta_data',
    ];

    protected $casts = [
        'meta_data' => 'array',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'organization_uuid', 'uuid');
    }

    protected $appends = ['landmark'];

    public function getLandmarkAttribute()
    {
        return $this->meta_data['landmark'] ?? null;
    }
}
