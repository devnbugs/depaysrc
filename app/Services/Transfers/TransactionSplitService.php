<?php

namespace App\Services\Transfers;

/**
 * Transaction Split Service
 * Handles splitting of large transfers into smaller chunks for compliance
 */
class TransactionSplitService
{
    /**
     * Default split threshold
     */
    public const DEFAULT_SPLIT_THRESHOLD = 10000;

    /**
     * Split amount into chunks if needed
     * 
     * @param float $amount Total amount to transfer
     * @param float|null $threshold Custom threshold (default: 10,000)
     * @return array Array of split amounts
     */
    public static function split(float $amount, ?float $threshold = null): array
    {
        $threshold = $threshold ?? self::DEFAULT_SPLIT_THRESHOLD;

        if ($amount <= $threshold) {
            return [(float) $amount];
        }

        $chunks = [];
        $remaining = $amount;

        while ($remaining > 0) {
            if ($remaining > $threshold) {
                $chunks[] = (float) $threshold;
                $remaining -= $threshold;
            } else {
                $chunks[] = round($remaining, 2);
                $remaining = 0;
            }
        }

        return $chunks;
    }

    /**
     * Calculate optimal split strategy for an amount
     * Returns the most efficient way to split an amount
     * 
     * @param float $amount Total amount
     * @param float $maxChunk Maximum chunk size (default: 10,000)
     * @return array Array with 'chunks' and 'chunk_count'
     */
    public static function calculateOptimalSplit(
        float $amount,
        float $maxChunk = 10000
    ): array {
        $chunks = self::split($amount, $maxChunk);

        return [
            'total_amount' => round($amount, 2),
            'chunk_size' => round($maxChunk, 2),
            'chunks' => $chunks,
            'chunk_count' => count($chunks),
            'requires_split' => $amount > $maxChunk,
        ];
    }

    /**
     * Verify split amounts sum to original
     * 
     * @param array $chunks Split amounts
     * @param float $original Original amount
     * @return bool
     */
    public static function verify(array $chunks, float $original): bool
    {
        $sum = array_sum($chunks);
        return abs($sum - $original) < 0.01; // Allow for rounding
    }

    /**
     * Get split description for UI
     * 
     * @param float $amount Amount to be transferred
     * @param float $threshold Split threshold
     * @return string Human-readable description
     */
    public static function getDescription(
        float $amount,
        float $threshold = 10000
    ): string {
        $split = self::calculateOptimalSplit($amount, $threshold);

        if (!$split['requires_split']) {
            return sprintf(
                'Transfer amount: %s (No split required)',
                money_format('%.2n', $amount)
            );
        }

        return sprintf(
            'Amount %s will be split into %d transfer(s) of %s each',
            money_format('%.2n', $amount),
            $split['chunk_count'],
            money_format('%.2n', $threshold)
        );
    }
}
