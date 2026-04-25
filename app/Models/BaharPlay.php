<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BaharPlay extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'game_id',
        'number',
        'amount',
        'win_amount',
        'status',
        'is_price_config',
        'price',
    ];

    protected $casts = [
        'is_price_config' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function game()
    {
        return $this->belongsTo(\App\Models\Game::class);
    }

    public function scopeRealBids($query)
    {
        return $query->where('is_price_config', false);
    }

    public function scopeConfigRows($query)
    {
        return $query->where('is_price_config', true);
    }

    public function getGrandAmountAttribute(): float
    {
        return $this->price * 10;
    }

    public static function getPrice(): float
    {
        $row = static::configRows()->first();
        return $row ? (float) $row->price : 0;
    }

    // ✅ FIXED: uses real user_id & game_id to satisfy FK constraint
    public static function setPrice(float $price): void
    {
        $user = User::first();
        $game = Game::first();

        if (!$user || !$game) {
            throw new \Exception('Users or Games table is empty. Cannot create price config.');
        }

        static::updateOrCreate(
            [
                'is_price_config' => true,
            ],
            [
                'price'      => $price,
                'user_id'    => $user->id,
                'game_id'    => $game->id,
                'number'     => '00',
                'amount'     => 0,
                'win_amount' => 0,
                'status'     => 'pending',
            ]
        );
    }
}