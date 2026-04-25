<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GamePlay extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'game_id',
        'play_type',
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

    // Exclude config rows from normal bid queries
    public function scopeRealBids($query)
    {
        return $query->where('is_price_config', false);
    }

    // Get only config rows
    public function scopeConfigRows($query)
    {
        return $query->where('is_price_config', true);
    }

    public function getGrandAmountAttribute(): float
    {
        return $this->price * 10;
    }

    public static function label(string $type): string
    {
        return match ($type) {
            'jodi'       => 'Jodi',
            'crossing'   => 'Crossing',
            'copy_paste' => 'Copy Paste',
            default      => ucfirst($type),
        };
    }

    // Get current price setting for a play_type
    public static function getPrice(string $playType): float
    {
        $row = static::configRows()->where('play_type', $playType)->first();
        return $row ? (float) $row->price : 0;
    }

    // Set/update price — uses first real user & game to satisfy FK
    public static function setPrice(string $playType, float $price): void
    {
        $user = User::first();
        $game = Game::first();

        if (!$user || !$game) {
            throw new \Exception('Users or Games table is empty. Cannot create price config.');
        }

        static::updateOrCreate(
            [
                'play_type'       => $playType,
                'is_price_config' => 1,
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