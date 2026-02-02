<?php

namespace App\Models;

use Actuallymab\LaravelComment\HasComments;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class Product extends Model
{
    use HasComments, HasUuids, HasTranslations;

    /**
     * Translatable fields for spatie/laravel-translatable
     * @var array<int, string>
     */
    public array $translatable = ['name', 'description', 'eligibility'];

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
        'organization_uuid',
        'name',
        'type',
        'currency_code',
        // Rate ranges
        'interest_rate_min',
        'interest_rate_max',
        // Term ranges
        'term_months_min',
        'term_months_max',
        // Rate tiers (term/amount-based rates)
        'rate_tiers',
        // Amount ranges
        'min_amount',
        'max_amount',
        // Fees
        'fee_structure',
        'fees', // Legacy single fee
        // Product categorization
        'purpose',
        // Card-specific
        'grace_period_days',
        // Application info
        'online_application',
        'approval_time',
        // Eligibility
        'eligibility',
        'required_documents',
        'min_age',
        'max_age',
        // General info
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
            // JSON fields
            'rate_tiers' => 'array',
            'fee_structure' => 'array',
            'required_documents' => 'array',
            'extra' => 'array',
            // Numeric fields
            'interest_rate_min' => 'float',
            'interest_rate_max' => 'float',
            'fees' => 'float',
            'min_amount' => 'float',
            'max_amount' => 'float',
            // Integer fields
            'term_months_min' => 'integer',
            'term_months_max' => 'integer',
            'grace_period_days' => 'integer',
            'min_age' => 'integer',
            'max_age' => 'integer',
            // Boolean
            'online_application' => 'boolean',
            // Datetime
            'remote_updated_at' => 'datetime',
        ];
    }

    /**
     * Get the display interest rate (formatted range or single value).
     */
    public function getInterestRateDisplayAttribute(): ?string
    {
        if ($this->interest_rate_min === null && $this->interest_rate_max === null) {
            return null;
        }

        if ($this->interest_rate_min === $this->interest_rate_max) {
            return $this->interest_rate_min . '%';
        }

        return $this->interest_rate_min . '% - ' . $this->interest_rate_max . '%';
    }

    /**
     * Get the display term (formatted range or single value).
     */
    public function getTermDisplayAttribute(): ?string
    {
        if ($this->term_months_min === null && $this->term_months_max === null) {
            return null;
        }

        if ($this->term_months_min === $this->term_months_max) {
            return $this->term_months_min . ' мес.';
        }

        return $this->term_months_min . ' - ' . $this->term_months_max . ' мес.';
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'organization_uuid', 'uuid');
    }
}
