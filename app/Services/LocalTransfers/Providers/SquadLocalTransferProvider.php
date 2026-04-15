<?php

namespace App\Services\LocalTransfers\Providers;

use Illuminate\Support\Str;

class SquadLocalTransferProvider extends AbstractLocalTransferProvider
{
    public function key(): string
    {
        return 'squad';
    }

    public function canListBanks(): bool
    {
        return false;
    }

    public function listBanks(array $settings): array
    {
        return [];
    }

    public function isConfigured(array $settings): bool
    {
        return parent::isConfigured($settings) && filled($settings['merchant_id'] ?? null);
    }

    public function resolveAccount(array $bank, string $accountNumber, array $settings): array
    {
        $response = $this->client($settings)->post('payout/account/lookup', [
            'bank_code' => $bank['code'],
            'account_number' => $accountNumber,
        ]);

        $body = $this->responseData($response);
        if (! $response->successful() || ! ($body['success'] ?? false)) {
            throw new \RuntimeException($body['message'] ?? 'Squad could not resolve this account.');
        }

        return $this->bankPayload(
            $bank,
            (string) data_get($body, 'data.account_number', $accountNumber),
            (string) data_get($body, 'data.account_name', ''),
            null,
            ['response' => $body]
        );
    }

    public function transfer(array $payload, array $settings): array
    {
        $resolved = $payload['resolved_bank'];
        $merchantReference = Str::upper((string) $settings['merchant_id']).'_'.$payload['reference'];

        $response = $this->client($settings)->post('payout/transfer', [
            'remark' => $payload['narration'],
            'bank_code' => $resolved['bank_code'],
            'currency_id' => 'NGN',
            'amount' => (string) (int) round(((float) $payload['amount']) * 100),
            'account_number' => $resolved['account_number'],
            'transaction_reference' => $merchantReference,
            'account_name' => $resolved['account_name'],
        ]);

        if ($pending = $this->pendingFromServerError($response, 'Squad transfer is being verified before final confirmation.')) {
            return $pending;
        }

        $body = $this->responseData($response);
        if (! $response->successful() || ! ($body['success'] ?? false)) {
            return $this->failed($body['message'] ?? 'Squad transfer failed.', [
                'response' => $body,
            ]);
        }

        $status = strtolower((string) data_get($body, 'data.response_description', 'success'));
        if (str_contains($status, 'pending') || str_contains($status, 'processing')) {
            return $this->pending($body['message'] ?? 'Transfer queued on Squad.', [
                'provider_reference' => (string) data_get($body, 'data.transaction_reference', $merchantReference),
                'response' => $body,
            ]);
        }

        return $this->success($body['message'] ?? 'Transfer completed on Squad.', [
            'provider_reference' => (string) data_get($body, 'data.transaction_reference', $merchantReference),
            'meta' => [
                'response' => $body,
            ],
        ]);
    }
}
