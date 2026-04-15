<?php

namespace App\Services\Cards;

use App\Models\VirtualCard;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class InterswitchVirtualCardService
{
    public function createCard(array $settings, array $payload, string $cardType): array
    {
        $response = $this->client($settings)->post(
            'card-management/api/v1/card/request?'.http_build_query([
                'isActiveActive' => 'false',
                'cardType' => $cardType,
            ]),
            $payload
        );

        return $this->parseResponse($response->json());
    }

    public function blockCard(array $settings, VirtualCard $card): array
    {
        return $this->suspendAction($settings, $card, true);
    }

    public function unblockCard(array $settings, VirtualCard $card): array
    {
        return $this->suspendAction($settings, $card, false);
    }

    public function fetchBalance(array $settings, VirtualCard $card): array
    {
        if ($this->isPrepaid($card)) {
            $response = $this->client($settings)->post('card-management/api/v1/card/prepaid/balance', [
                'pan' => $card->pan,
                'seqNr' => $card->card_sequence_number,
                'expiryDate' => $card->expiry_date,
            ]);
        } else {
            $response = $this->client($settings)->post('card-management/api/v1/card/debit/balance', [
                'issuerNr' => (int) $settings['issuer_nr'],
                'accountId' => $card->account_id,
                'accountType' => $card->account_type ?: $settings['account_type'],
            ]);
        }

        return $this->parseResponse($response->json(), false);
    }

    public function formattedMaskedPan(string $pan): string
    {
        $prefix = substr($pan, 0, 6);
        $suffix = substr($pan, -4);
        $maskLength = max(strlen($pan) - 10, 0);

        return $prefix.str_repeat('*', $maskLength).$suffix;
    }

    public function isPrepaid(VirtualCard $card): bool
    {
        return str_contains((string) $card->card_type, 'PREPAID');
    }

    protected function suspendAction(array $settings, VirtualCard $card, bool $block): array
    {
        $endpoint = $this->isPrepaid($card)
            ? 'card-management/api/v1/card/prepaid/'.($block ? 'block' : 'unblock')
            : 'card-management/api/v1/card/'.($block ? 'block' : 'unblock');

        $response = $this->client($settings)->post($endpoint, [
            'pan' => $card->pan,
            'seqNr' => $card->card_sequence_number,
            'expiryDate' => $card->expiry_date,
        ]);

        return $this->parseResponse($response->json(), false);
    }

    protected function client(array $settings): PendingRequest
    {
        $client = Http::baseUrl(rtrim((string) $settings['base_url'], '/').'/')
            ->acceptJson()
            ->asJson()
            ->withToken($this->accessToken($settings))
            ->timeout(45)
            ->retry(1, 300);

        if (app()->environment('local')) {
            $client = $client->withoutVerifying();
        }

        return $client;
    }

    protected function accessToken(array $settings): string
    {
        $cacheKey = 'interswitch:token:'.md5((string) $settings['client_id']);

        return Cache::remember($cacheKey, now()->addMinutes(45), function () use ($settings) {
            $client = Http::acceptJson()
                ->asForm()
                ->withBasicAuth((string) $settings['client_id'], (string) $settings['client_secret'])
                ->timeout(30)
                ->retry(1, 250);

            if (app()->environment('local')) {
                $client = $client->withoutVerifying();
            }

            $response = $client->post(rtrim((string) $settings['auth_url'], '/').'?grant_type=client_credentials');
            $payload = $response->json();
            $token = (string) data_get($payload, 'access_token', '');

            if ($token === '') {
                throw new RuntimeException($this->messageFromPayload($payload, 'Unable to authenticate with Interswitch.'));
            }

            return $token;
        });
    }

    protected function parseResponse(?array $payload, bool $requireSuccessful = true): array
    {
        $payload ??= [];
        $code = (string) data_get($payload, 'code', '');
        $successful = (bool) data_get($payload, 'successful', true);

        if ($code === '' && Arr::has($payload, 'error')) {
            throw new RuntimeException($this->messageFromPayload($payload));
        }

        if (($requireSuccessful && ! $successful) || ! in_array($code, ['00', '200'], true)) {
            throw new RuntimeException($this->messageFromPayload($payload));
        }

        return $payload;
    }

    protected function messageFromPayload(array $payload, string $fallback = 'Interswitch request failed.'): string
    {
        $message = (string) data_get($payload, 'description', '');

        if ($message === '') {
            $message = (string) data_get($payload, 'error_description', data_get($payload, 'error', ''));
        }

        if ($message === '') {
            $message = (string) data_get($payload, 'errors.0.message', '');
        }

        return $message !== '' ? $message : $fallback;
    }
}
