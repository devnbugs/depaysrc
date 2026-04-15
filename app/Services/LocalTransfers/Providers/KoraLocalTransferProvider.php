<?php

namespace App\Services\LocalTransfers\Providers;

use Illuminate\Support\Arr;
use RuntimeException;

class KoraLocalTransferProvider extends AbstractLocalTransferProvider
{
    public function key(): string
    {
        return 'kora';
    }

    public function listBanks(array $settings): array
    {
        $response = $this->client($settings)->get('merchant/api/v1/misc/banks', [
            'countryCode' => 'NG',
        ]);

        $body = $this->responseData($response);
        if (! $response->successful() || ! ($body['status'] ?? false)) {
            throw new RuntimeException($body['message'] ?? 'Unable to load Kora banks.');
        }

        return $this->normalizeBankList(Arr::wrap($body['data'] ?? []));
    }

    public function resolveAccount(array $bank, string $accountNumber, array $settings): array
    {
        $providerBank = $this->matchBank($bank, $this->listBanks($settings));
        $response = $this->client($settings)->post('merchant/api/v1/misc/banks/resolve', [
            'bank' => $providerBank['code'],
            'account' => $accountNumber,
            'currency' => 'NG',
        ]);

        $body = $this->responseData($response);
        if (! $response->successful() || ! ($body['status'] ?? false)) {
            throw new RuntimeException($body['message'] ?? 'Kora could not resolve this account.');
        }

        return $this->bankPayload(
            [
                'name' => (string) data_get($body, 'data.bank_name', $providerBank['name']),
                'code' => (string) data_get($body, 'data.bank_code', $providerBank['code']),
            ],
            (string) data_get($body, 'data.account_number', $accountNumber),
            (string) data_get($body, 'data.account_name', ''),
            null,
            ['response' => $body]
        );
    }

    public function transfer(array $payload, array $settings): array
    {
        $resolved = $payload['resolved_bank'];
        $response = $this->client($settings)->post('merchant/api/v1/transactions/disburse', [
            'reference' => $payload['reference'],
            'destination' => [
                'type' => 'bank_account',
                'amount' => round((float) $payload['amount'], 2),
                'currency' => 'NGN',
                'narration' => $payload['narration'],
                'bank_account' => [
                    'bank' => $resolved['bank_code'],
                    'account' => $resolved['account_number'],
                ],
                'customer' => [
                    'name' => $resolved['account_name'],
                ],
            ],
        ]);

        if ($pending = $this->pendingFromServerError($response, 'Kora transfer is being verified before final confirmation.')) {
            return $pending;
        }

        $body = $this->responseData($response);
        if (! $response->successful() || ! ($body['status'] ?? false)) {
            return $this->failed($body['message'] ?? 'Kora transfer failed.', [
                'response' => $body,
            ]);
        }

        $status = strtolower((string) data_get($body, 'data.status', ''));
        if (in_array($status, ['pending', 'processing', 'queued'], true)) {
            return $this->pending($body['message'] ?? 'Transfer queued on Kora.', [
                'provider_reference' => (string) data_get($body, 'data.reference', $payload['reference']),
                'response' => $body,
            ]);
        }

        return $this->success($body['message'] ?? 'Transfer completed on Kora.', [
            'provider_reference' => (string) data_get($body, 'data.reference', $payload['reference']),
            'meta' => [
                'response' => $body,
            ],
        ]);
    }
}
