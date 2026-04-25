<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    protected $fillable = [
        'user_id',
        'cin',
        'amount',
        'type'
        
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function gamePlay()
    {
        return $this->belongsTo(GamePlay::class, 'game_play_id');
    }
    
}

