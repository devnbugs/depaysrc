<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MarketPrice; // Ensure the MarketPrice model is imported

class MarketController extends Controller
{
    /**
     * Show the market prices in a ticker.
     *
     * @return \Illuminate\View\View
     */
    public function showTicker()
    {
        // Fetch all market prices from the database
        $prices = MarketPrice::all();

        // Pass the prices to the view
        return view('ticker', compact('prices'));
    }
}
