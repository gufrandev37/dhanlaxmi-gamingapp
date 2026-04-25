<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Winning extends Model
{
    protected $fillable = [
        'user_id',
        'amount',
        'game_name',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
