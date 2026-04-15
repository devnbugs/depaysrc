<?php

namespace App\Services\LocalTransfers\Providers;

use App\Services\LocalTransfers\LocalTransferBankNormalizer;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;

abstract class AbstractLocalTransferProvider
{
    public function __construct(protected LocalTransferBankNormalizer $normalizer)
    {
    }

    abstract public function key(): string;

    public function label(): string
    {
        return ucfirst($this->key());
    }

    public function canListBanks(): bool
    {
        return true;
    }

    public function isConfigured(array $settings): bool
    {
        return (bool) ($settings['enabled'] ?? false) && filled($settings['secret_key'] ?? null);
    }

    abstract public function listBanks(array $settings): array;

    abstract public function resolveAccount(array $bank, string $accountNumber, array $settings): array;

    abstract public function transfer(array $payload, array $settings): array;

    protected function client(array $settings): PendingRequest
    {
        $client = Http::baseUrl(rtrim((string) ($settings['base_url'] ?? ''), '/').'/')
            ->acceptJson()
            ->asJson()
            ->withToken((string) ($settings['secret_key'] ?? ''))
            ->timeout(25)
            ->retry(1, 250);

        if (app()->environment('local')) {
            $client = $client->withoutVerifying();
        }

        return $client;
    }

    protected function requestWithSslFallback(callable $request, array $settings): Response
    {
        try {
            return $request($this->client($settings));
        } catch (ConnectionException $exception) {
            if (! str_contains(strtolower($exception->getMessage()), 'curl error 60')) {
                throw $exception;
            }

            return $request($this->client($settings)->withoutVerifying());
        }
    }

    protected function normalizeBankList(array $banks): array
    {
        return array_values(array_filter(array_map(fn (array $bank) => $this->normalizer->standardize($bank), $banks), fn (array $bank) => $bank['name'] !== ''));
    }

    protected function matchBank(array $selectedBank, array $providerBanks): array
    {
        $match = $this->normalizer->match($selectedBank, $providerBanks);

        if (! $match) {
            throw new RuntimeException($this->label().' does not support the selected bank right now.');
        }

        return $match;
    }

    protected function bankPayload(array $bank, string $accountNumber, string $accountName, ?string $providerReference = null, array $meta = []): array
    {
        return [
            'provider' => $this->key(),
            'provider_reference' => $providerReference,
            'bank_name' => $bank['name'] ?? '',
            'bank_code' => $bank['code'] ?? '',
            'account_number' => $accountNumber,
            'account_name' => $accountName,
            'meta' => $meta,
        ];
    }

    protected function failed(string $message, array $meta = []): array
    {
        return [
            'status' => 'failed',
            'message' => $message,
            'provider' => $this->key(),
            'meta' => $meta,
        ];
    }

    protected function pending(string $message, array $meta = []): array
    {
        return [
            'status' => 'pending',
            'message' => $message,
            'provider' => $this->key(),
            'meta' => $meta,
        ];
    }

    protected function success(string $message, array $payload): array
    {
        return array_merge([
            'status' => 'success',
            'message' => $message,
            'provider' => $this->key(),
        ], $payload);
    }

    protected function responseData(Response $response): array
    {
        return $response->json() ?? [];
    }

    protected function pendingFromServerError(Response $response, string $fallbackMessage): ?array
    {
        if ($response->serverError()) {
            return $this->pending($fallbackMessage, [
                'http_status' => $response->status(),
                'response' => $this->responseData($response),
            ]);
        }

        return null;
    }
}
