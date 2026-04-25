<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GamePrice;

class AdminGamePriceController extends Controller
{
    // ✅ Only 3 types — crossing & copy_paste use jodi price
    private array $gameTypes = [
        'jodi'  => 'Jodi',
        'andar' => 'Andar',
        'bahar' => 'Bahar',
    ];

    public function index()
    {
        $priceRows = [];

        foreach ($this->gameTypes as $type => $label) {
            $row   = GamePrice::where('game_type', $type)->first();
            $price = $row ? (float) $row->price : 0;

            $priceRows[] = [
                'type'     => $type,
                'label'    => $label,
                'price'    => $price,
                'multiply' => 10,
                'grand'    => $price * 10,
                'status'   => $row ? $row->status : 'not set',
            ];
        }

        return view('admin.game-price', compact('priceRows'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'game_type' => 'required|in:jodi,andar,bahar',
            'price'     => 'required|numeric|min:0',
        ]);

        $type  = $request->game_type;
        $price = (float) $request->price;

        GamePrice::setPrice($type, $price);

        $label = $this->gameTypes[$type];

        return back()->with('success',
            "{$label} price updated to ₹{$price} per 10 Rs. (Grand: ₹" . ($price * 10) . ")"
        );
    }
}