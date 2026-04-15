<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class NetworkController extends Controller
{
    public function easyaccess()
    {
        $urls = [
            config('services.easyaccess.base_url', 'https://easyaccess.com.ng/api/').'wallet_balance.php',
            config('services.easyaccess.base_url', 'https://easyaccess.com.ng/api/').'data.php',
            config('services.easyaccess.base_url', 'https://easyaccess.com.ng/api/').'airtime.php',
        ];

        $authorizationToken = (string) config('services.easyaccess.auth_token', '');
        $successfulResponses = 0;
        $totalTime = 0;

        foreach ($urls as $url) {
            $startTime = microtime(true);

            $response = Http::timeout(5)
                ->withHeaders([
                    'AuthorizationToken' => $authorizationToken,
                    'cache-control' => 'no-cache',
                ])
                ->get($url);

            $totalTime += microtime(true) - $startTime;

            if ($response->successful()) {
                $successfulResponses++;
            }
        }

        $signalStrength = min($successfulResponses, 4);
        $averageTime = $successfulResponses > 0 ? $totalTime / $successfulResponses : 0;

        $speed = 'Slow';
        if ($averageTime <= 2) {
            $speed = 'Fast';
        } elseif ($averageTime <= 7) {
            $speed = 'Moderate';
        }

        return response()->json([
            'signal' => $signalStrength,
            'average_response_time' => round($averageTime, 2).' seconds',
            'speed' => $speed,
        ]);
    }
}
