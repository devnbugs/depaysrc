<?php

namespace App\Services;

use App\Models\Bill;
use App\Models\Deposit;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class WalletService
{
    public function findByPhone(string $phoneNumber): ?User
    {
        return User::where('mobile', $phoneNumber)->first();
    }

    public function validatePin(User $user, string $pin): bool
    {
        return hash_equals((string) $user->pin, (string) $pin);
    }

    public function creditDeposit(User $user, float $grossAmount, float $charge, string $reference, string $source = 'Paystack'): array
    {
        return DB::transaction(function () use ($user, $grossAmount, $charge, $reference, $source) {
            $existingTransaction = Transaction::where('trx', $reference)->first();
            if ($existingTransaction) {
                return [
                    'transaction' => $existingTransaction,
                    'deposit' => Deposit::where('trx', $reference)->first(),
                    'user' => $user,
                    'duplicate' => true,
                ];
            }

            $lockedUser = User::whereKey($user->id)->lockForUpdate()->firstOrFail();
            $creditAmount = max(0, $grossAmount - $charge);
            $postBalance = $lockedUser->balance + $creditAmount;

            $transaction = new Transaction();
            $transaction->user_id = $lockedUser->id;
            $transaction->amount = $creditAmount;
            $transaction->charge = $charge;
            $transaction->trx = $reference;
            $transaction->details = 'Wallet funding via '.$source;
            $transaction->bywho = $source;
            $transaction->post_balance = $postBalance;
            $transaction->trx_type = '+';
            $transaction->save();

            $deposit = new Deposit();
            $deposit->user_id = $lockedUser->id;
            $deposit->amount = $creditAmount;
            $deposit->charge = $charge;
            $deposit->status = 1;
            $deposit->trx = $reference;
            $deposit->method_code = '006';
            $deposit->final_amo = $postBalance;
            $deposit->save();

            $lockedUser->balance = $postBalance;
            $lockedUser->save();

            return [
                'transaction' => $transaction,
                'deposit' => $deposit,
                'user' => $lockedUser,
                'duplicate' => false,
            ];
        });
    }

    public function purchaseBill(User $user, float $amount, string $details, string $reference, array $meta = []): Bill
    {
        return DB::transaction(function () use ($user, $amount, $details, $reference, $meta) {
            $lockedUser = User::whereKey($user->id)->lockForUpdate()->firstOrFail();
            $debitAmount = (float) ($meta['debit_amount'] ?? $amount);

            if ($lockedUser->balance < $debitAmount) {
                throw new RuntimeException('Insufficient balance.');
            }

            $lockedUser->balance -= $debitAmount;
            $lockedUser->save();

            $bill = new Bill();
            $bill->user_id = $lockedUser->id;
            $bill->amount = $amount;
            $bill->token = $meta['token'] ?? $reference;
            $bill->bundle = $details;
            $bill->profit = $meta['profit'] ?? 0;
            $bill->trx = $reference;
            $bill->phone = $meta['phone'] ?? null;
            $bill->network = $meta['network'] ?? null;
            $bill->newbalance = $lockedUser->balance;
            $bill->type = $meta['type'] ?? 0;
            $bill->status = $meta['status'] ?? 1;
            foreach (['accountnumber', 'accountname', 'plan', 'btype', 'validity', 'api'] as $field) {
                if (array_key_exists($field, $meta)) {
                    $bill->{$field} = $meta[$field];
                }
            }
            if (array_key_exists('response', $meta)) {
                $bill->response = is_string($meta['response']) ? $meta['response'] : json_encode($meta['response']);
            }
            $bill->save();

            if (($meta['log_transaction'] ?? true) === true) {
                $transaction = new Transaction();
                $transaction->user_id = $lockedUser->id;
                $transaction->amount = $amount;
                $transaction->charge = (float) ($meta['charge'] ?? 0);
                $transaction->trx_type = $meta['trx_type'] ?? '-';
                $transaction->details = $meta['transaction_details'] ?? $details;
                $transaction->trx = $reference;
                if (isset($meta['bywho'])) {
                    $transaction->bywho = $meta['bywho'];
                }
                if (isset($meta['post_balance'])) {
                    $transaction->post_balance = $meta['post_balance'];
                } else {
                    $transaction->post_balance = $lockedUser->balance;
                }
                $transaction->save();
            }

            return $bill;
        });
    }
}
