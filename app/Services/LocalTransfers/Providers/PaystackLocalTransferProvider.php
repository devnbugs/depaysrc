<?php

namespace App\Services\LocalTransfers\Providers;

use Illuminate\Support\Arr;
use RuntimeException;

class PaystackLocalTransferProvider extends AbstractLocalTransferProvider
{
    public function key(): string
    {
        return 'paystack';
    }

    public function listBanks(array $settings): array
    {
        $response = $this->requestWithSslFallback(fn ($client) => $client->get('bank', [
            'country' => 'nigeria',
            'currency' => 'NGN',
            'perPage' => 500,
        ]), $settings);

        $body = $this->responseData($response);
        if (! $response->successful() || ! ($body['status'] ?? false)) {
            throw new RuntimeException($body['message'] ?? 'Unable to load Paystack banks.');
        }

        $banks = array_map(function (array $bank) {
            return [
                'name' => $bank['name'] ?? '',
                'code' => $bank['code'] ?? '',
                'slug' => $bank['slug'] ?? '',
            ];
        }, Arr::wrap($body['data'] ?? []));

        return $this->normalizeBankList($banks);
    }

    public function resolveAccount(array $bank, string $accountNumber, array $settings): array
    {
        $providerBank = $this->matchBank($bank, $this->listBanks($settings));
        $response = $this->requestWithSslFallback(fn ($client) => $client->get('bank/resolve', [
            'account_number' => $accountNumber,
            'bank_code' => $providerBank['code'],
        ]), $settings);

        $body = $this->responseData($response);
        if (! $response->successful() || ! ($body['status'] ?? false)) {
            throw new RuntimeException($body['message'] ?? 'Paystack could not resolve this account.');
        }

        return $this->bankPayload(
            $providerBank,
            $accountNumber,
            (string) data_get($body, 'data.account_name', ''),
            null,
            ['response' => $body]
        );
    }

    public function transfer(array $payload, array $settings): array
    {
        $resolved = $payload['resolved_bank'];

        $recipientResponse = $this->requestWithSslFallback(fn ($client) => $client->post('transferrecipient', [
            'type' => 'nuban',
            'name' => $resolved['account_name'],
            'account_number' => $resolved['account_number'],
            'bank_code' => $resolved['bank_code'],
            'currency' => 'NGN',
        ]), $settings);

        if ($pending = $this->pendingFromServerError($recipientResponse, 'Paystack recipient creation is still being verified.')) {
            return $pending;
        }

        $recipientBody = $this->responseData($recipientResponse);
        if (! $recipientResponse->successful() || ! ($recipientBody['status'] ?? false)) {
            return $this->failed($recipientBody['message'] ?? 'Paystack could not create a transfer recipient.', [
                'response' => $recipientBody,
            ]);
        }

        $transferResponse = $this->requestWithSslFallback(fn ($client) => $client->post('transfer', [
            'source' => 'balance',
            'amount' => (int) round(((float) $payload['amount']) * 100),
            'reference' => $payload['reference'],
            'reason' => $payload['narration'],
            'recipient' => data_get($recipientBody, 'data.recipient_code'),
        ]), $settings);

        if ($pending = $this->pendingFromServerError($transferResponse, 'Paystack transfer is being verified before final confirmation.')) {
            return $pending;
        }

        $transferBody = $this->responseData($transferResponse);
        if (! $transferResponse->successful() || ! ($transferBody['status'] ?? false)) {
            return $this->failed($transferBody['message'] ?? 'Paystack transfer failed.', [
                'response' => $transferBody,
            ]);
        }

        $transferStatus = strtolower((string) data_get($transferBody, 'data.status', ''));
        if (in_array($transferStatus, ['pending', 'queued', 'otp'], true)) {
            return $this->pending($transferBody['message'] ?? 'Transfer queued on Paystack.', [
                'provider_reference' => (string) data_get($transferBody, 'data.reference', $payload['reference']),
                'response' => $transferBody,
            ]);
        }

        return $this->success($transferBody['message'] ?? 'Transfer completed on Paystack.', [
            'provider_reference' => (string) data_get($transferBody, 'data.reference', $payload['reference']),
            'meta' => [
                'recipient' => $recipientBody,
                'transfer' => $transferBody,
            ],
        ]);
    }
}
