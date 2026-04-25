<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    /**
     * Mass assignable fields
     */
    protected $fillable = [
        'user_id',
        'amount',
        'payment_method',
        'transaction_id',
        'status'
    ];

    /**
     * Type Casting
     */
    protected $casts = [
        'amount' => 'decimal:2',
    ];

    /**
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
