<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Application extends Model
{
    use HasFactory, HasUuids;

    /**
     * The primary key for the model.
     */
    protected $primaryKey = 'uuid';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'product_uuid',
        'first_name',
        'last_name',
        'phone',
        'phone_verified_at',
        'status',
        'loan_amount',
        'loan_term',
        'extra',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'phone_verified_at' => 'datetime',
        'extra' => 'array',
    ];

    /**
     * Get the product associated with the application.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_uuid', 'uuid');
    }

    /**
     * Check if phone is verified.
     */
    public function isPhoneVerified(): bool
    {
        return $this->phone_verified_at !== null;
    }

    /**
     * Mark phone as verified.
     */
    public function markPhoneAsVerified(): void
    {
        $this->phone_verified_at = now();
        $this->save();
    }

    /**
     * Scope for pending applications.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for verified applications.
     */
    public function scopeVerified($query)
    {
        return $query->whereNotNull('phone_verified_at');
    }
}
