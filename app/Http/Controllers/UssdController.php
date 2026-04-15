<?php

namespace App\Http\Controllers;

use App\Models\Network;
use Illuminate\Http\Request;

class UssdController extends Controller
{
    public function __construct(protected ApiController $apiController)
    {
    }

    public function handleUssd(Request $request)
    {
        $phoneNumber = $request->input('phoneNumber');
        $text = trim((string) $request->input('text', ''));
        $textArray = $text === '' ? [] : explode('*', $text);

        if ($text === '') {
            return response(
                "CON Welcome to CityPress DATA.\n".
                "1. Check Wallet Balance\n".
                "2. Buy Airtime\n".
                "3. Exit"
            )->header('Content-Type', 'text/plain');
        }

        if ($textArray[0] === '1') {
            if (count($textArray) === 1) {
                return response("CON Enter your PIN to check your balance:")->header('Content-Type', 'text/plain');
            }

            $pin = $textArray[1] ?? '';
            $isValidPin = json_decode($this->apiController->validatePin($phoneNumber, $pin)->getContent(), true)['valid'] ?? false;

            if (! $isValidPin) {
                return response("END Invalid PIN or Phone Number.")->header('Content-Type', 'text/plain');
            }

            $accountInfo = json_decode($this->apiController->getWalletBalance($phoneNumber)->getContent(), true);
            if (! isset($accountInfo['balance'])) {
                return response("END Unable to retrieve account information.")->header('Content-Type', 'text/plain');
            }

            return response("END Your balance is {$accountInfo['balance']}.\nAccount Name: {$accountInfo['name']}")->header('Content-Type', 'text/plain');
        }

        if ($textArray[0] === '2') {
            $networkChoices = [
                '1' => 'MTN',
                '2' => 'AIRTEL',
                '3' => 'GLO',
                '4' => '9MOBILE',
            ];

            if (count($textArray) === 1) {
                return response(
                    "CON Select network:\n".
                    "1. MTN\n".
                    "2. Airtel\n".
                    "3. Glo\n".
                    "4. 9mobile"
                )->header('Content-Type', 'text/plain');
            }

            if (count($textArray) === 2) {
                $networkChoice = $textArray[1];
                if (! isset($networkChoices[$networkChoice])) {
                    return response("END Invalid network selection.")->header('Content-Type', 'text/plain');
                }

                return response("CON Enter amount to buy airtime:")->header('Content-Type', 'text/plain');
            }

            if (count($textArray) === 3) {
                return response("CON Enter your PIN to confirm airtime purchase:")->header('Content-Type', 'text/plain');
            }

            if (count($textArray) === 4) {
                $networkChoice = $textArray[1];
                $amount = $textArray[2];
                $pin = $textArray[3];
                $networkSymbol = $networkChoices[$networkChoice] ?? null;

                if (! $networkSymbol) {
                    return response("END Invalid network selection.")->header('Content-Type', 'text/plain');
                }

                $isValidPin = json_decode($this->apiController->validatePin($phoneNumber, $pin)->getContent(), true)['valid'] ?? false;
                if (! $isValidPin) {
                    return response("END Invalid PIN. Transaction failed.")->header('Content-Type', 'text/plain');
                }

                $result = json_decode($this->apiController->buyAirtime($phoneNumber, $networkSymbol, $amount, $pin)->getContent(), true);
                if (($result['success'] ?? false) === true) {
                    return response('END Airtime purchase successful!')->header('Content-Type', 'text/plain');
                }

                return response('END Airtime purchase failed. '.($result['message'] ?? ''))->header('Content-Type', 'text/plain');
            }
        }

        if ($textArray[0] === '3') {
            return response("END Thank you for using our service!")->header('Content-Type', 'text/plain');
        }

        return response("END Invalid input. Please try again.")->header('Content-Type', 'text/plain');
    }
}
