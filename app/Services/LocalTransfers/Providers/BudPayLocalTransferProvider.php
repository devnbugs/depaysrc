<?php

namespace App\Services\LocalTransfers\Providers;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Arr;
use RuntimeException;

class BudPayLocalTransferProvider extends AbstractLocalTransferProvider
{
    public function key(): string
    {
        return 'budpay';
    }

    protected function client(array $settings): PendingRequest
    {
        $client = parent::client($settings);
        $publicKey = (string) ($settings['public_key'] ?? '');

        if ($publicKey !== '') {
            $client = $client->withHeaders([
                'Encryption' => hash_hmac('sha512', '{}', $publicKey),
            ]);
        }

        return $client;
    }

    protected function signedClient(array $settings, array $payload): PendingRequest
    {
        $json = json_encode($this->sortRecursive($payload), JSON_UNESCAPED_SLASHES);
        $publicKey = (string) ($settings['public_key'] ?? '');

        return parent::client($settings)->withHeaders([
            'Encryption' => $publicKey !== '' ? hash_hmac('sha512', $json ?: '{}', $publicKey) : '',
        ]);
    }

    public function isConfigured(array $settings): bool
    {
        return parent::isConfigured($settings) && filled($settings['public_key'] ?? null);
    }

    public function listBanks(array $settings): array
    {
        $response = parent::client($settings)->get('bank_list/NGN');
        $body = $this->responseData($response);

        if (! $response->successful() || ! ($body['success'] ?? false)) {
            throw new RuntimeException($body['message'] ?? 'Unable to load BudPay banks.');
        }

        return $this->normalizeBankList(array_map(function ($bank) {
            return [
                'name' => $bank['bank_name'] ?? '',
                'code' => $bank['bank_code'] ?? '',
            ];
        }, Arr::wrap($body['data'] ?? [])));
    }

    public function resolveAccount(array $bank, string $accountNumber, array $settings): array
    {
        $providerBank = $this->matchBank($bank, $this->listBanks($settings));
        $payload = [
            'bank_code' => $providerBank['code'],
            'account_number' => $accountNumber,
            'currency' => 'NGN',
        ];

        $response = parent::client($settings)->post('account_name_verify', $payload);
        $body = $this->responseData($response);

        if (! $response->successful() || ! ($body['success'] ?? false)) {
            throw new RuntimeException($body['message'] ?? 'BudPay could not resolve this account.');
        }

        return $this->bankPayload(
            $providerBank,
            $accountNumber,
            (string) data_get($body, 'data', ''),
            null,
            ['response' => $body]
        );
    }

    public function transfer(array $payload, array $settings): array
    {
        $resolved = $payload['resolved_bank'];
        $requestPayload = [
            'currency' => 'NGN',
            'amount' => (string) round((float) $payload['amount'], 2),
            'bank_code' => $resolved['bank_code'],
            'bank_name' => $resolved['bank_name'],
            'account_number' => $resolved['account_number'],
            'narration' => $payload['narration'],
            'reference' => strtolower($payload['reference']),
            'meta_data' => [
                [
                    'sender_name' => $payload['sender_name'],
                ],
            ],
        ];

        $response = $this->signedClient($settings, $requestPayload)->post('bank_transfer', $requestPayload);

        if ($pending = $this->pendingFromServerError($response, 'BudPay transfer is being verified before final confirmation.')) {
            return $pending;
        }

        $body = $this->responseData($response);
        if (! $response->successful() || ! ($body['success'] ?? false)) {
            return $this->failed($body['message'] ?? 'BudPay transfer failed.', [
                'response' => $body,
            ]);
        }

        $status = strtolower((string) data_get($body, 'data.status', 'pending'));
        if (in_array($status, ['pending', 'processing'], true)) {
            return $this->pending($body['message'] ?? 'Transfer queued on BudPay.', [
                'provider_reference' => (string) data_get($body, 'data.reference', strtolower($payload['reference'])),
                'response' => $body,
            ]);
        }

        return $this->success($body['message'] ?? 'Transfer completed on BudPay.', [
            'provider_reference' => (string) data_get($body, 'data.reference', strtolower($payload['reference'])),
            'meta' => [
                'response' => $body,
            ],
        ]);
    }

    protected function sortRecursive(array $payload): array
    {
        ksort($payload);

        foreach ($payload as $key => $value) {
            if (is_array($value)) {
                $payload[$key] = $this->sortRecursive($value);
            }
        }

        return $payload;
    }
}
