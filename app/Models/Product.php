<?php

namespace App\Models;

use Actuallymab\LaravelComment\HasComments;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasComments;

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
        'merchant_id',
        'name',
        'type',
        'currency',
        'interest_rate',
        'fees',
        'term_months',
        'min_amount',
        'max_amount',
        'eligibility',
        'description',
        'extra',
        'remote_updated_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'extra' => 'array',
            'remote_updated_at' => 'datetime',
            'interest_rate' => 'float',
            'fees' => 'float',
            'min_amount' => 'float',
            'max_amount' => 'float',
        ];
    }

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }
}
