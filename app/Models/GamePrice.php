<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GamePrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_type',
        'price',
        'multiply',
        'grand_total',
        'status'
    ];

    // Get price for a game type
    public static function getPrice(string $gameType): float
    {
        $row = static::where('game_type', $gameType)->first();
        return $row ? (float) $row->price : 0;
    }

    // Set/update price — updateOrCreate so never duplicates
    public static function setPrice(string $gameType, float $price): void
    {
        static::updateOrCreate(
            [
                'game_type' => $gameType,  // find by this
            ],
            [
                'price'       => $price,
                'multiply'    => 10,
                'grand_total' => $price * 10,
                'status'      => 'active',  // default active
            ]
        );
    }
}