<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerCredit extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'customer_name',
        'customer_phone',
        'invoice',
        'amount',
        'reason',
        'status',
        'amount_cleared',
        'due_date',
        'notes',
        'user_id'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'amount_cleared' => 'decimal:2',
        'due_date' => 'date'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Get balance due
    public function getBalanceDueAttribute()
    {
        return $this->amount - $this->amount_cleared;
    }

    // Check if fully cleared
    public function getIsFullyClearedAttribute()
    {
        return $this->balance_due <= 0;
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePartiallyCleared($query)
    {
        return $query->where('status', 'partially_cleared');
    }

    public function scopeFullyCleared($query)
    {
        return $query->where('status', 'fully_cleared');
    }
}

