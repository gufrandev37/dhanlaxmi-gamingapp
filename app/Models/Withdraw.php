<?php

namespace App\Models;



use Illuminate\Database\Eloquent\Model;

class Withdraw extends Model
{
    protected $fillable = [
        'user_id',
        'cin',
        'payment_mode',
        'mobile',
        'amount',
        'available_points',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

