<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    protected $fillable = [
        'user_id',
        'game_name',
        'city_name',
        'status',
        'correct_answer',
        'open_time',
        'close_time',
        'result_time',
        'play_next_day',
        'play_days',
    ];

    protected $casts = [
        'open_time'   => 'datetime:H:i',
        'close_time'  => 'datetime:H:i',
        'result_time' => 'datetime:H:i',
        'play_days'   => 'array', // for JSON column
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plays()
    {
        return $this->hasMany(GamePlay::class);
    }
}