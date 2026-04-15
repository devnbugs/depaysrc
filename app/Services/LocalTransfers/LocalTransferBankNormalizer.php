<?php

namespace App\Services\LocalTransfers;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class LocalTransferBankNormalizer
{
    public function standardize(array $bank): array
    {
        $name = trim((string) ($bank['name'] ?? $bank['bank_name'] ?? ''));
        $code = trim((string) ($bank['code'] ?? $bank['bank_code'] ?? ''));
        $slug = trim((string) ($bank['slug'] ?? ''));

        return [
            'name' => $name,
            'code' => $code,
            'slug' => $slug !== '' ? $slug : $this->slugify($name),
            'signature' => $this->signature($name),
        ];
    }

    public function match(array $selectedBank, array $providerBanks): ?array
    {
        $selected = $this->standardize($selectedBank);
        $selectedCode = preg_replace('/\D+/', '', $selected['code']);

        foreach ($providerBanks as $providerBank) {
            $candidate = $this->standardize($providerBank);
            $candidateCode = preg_replace('/\D+/', '', $candidate['code']);

            if ($selectedCode !== '' && $candidateCode !== '' && $candidateCode === $selectedCode) {
                return $candidate;
            }

            if ($candidate['slug'] !== '' && $candidate['slug'] === $selected['slug']) {
                return $candidate;
            }

            if ($candidate['signature'] !== '' && $candidate['signature'] === $selected['signature']) {
                return $candidate;
            }
        }

        return null;
    }

    public function signature(string $value): string
    {
        $signature = Str::of(Str::lower($value))
            ->replaceMatches('/[^a-z0-9]+/', ' ')
            ->replace(['guaranty trust', 'guarantee trust'], 'gtb')
            ->replace(['united bank for africa'], 'uba')
            ->replace(['first bank of nigeria'], 'first')
            ->replace(['stanbic ibtc', 'stanbicibtc'], 'stanbic')
            ->replace(['opay digital services', 'opay digital services ltd'], 'opay')
            ->replace(['palmpay limited'], 'palmpay')
            ->replace(['payment service bank'], 'psb')
            ->replaceMatches('/\b(bank|plc|limited|ltd|nigeria|services|service|digital|microfinance|mfb)\b/', ' ')
            ->replaceMatches('/\s+/', ' ')
            ->trim();

        return (string) $signature;
    }

    public function slugify(string $value): string
    {
        return (string) Str::of($value)->slug('');
    }
}
