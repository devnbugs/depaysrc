<?php

namespace App\Http\Controllers;

use App\Models\Transfer;
use App\Models\Transaction;
use App\Models\TransferBeneficiary;
use App\Models\User;
use App\Services\LocalTransfers\LocalTransferManager;
use App\Services\LocalTransfers\LocalTransferSettings;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LocalTransferController extends Controller
{
    protected string $activeTemplate;

    public function __construct(
        protected LocalTransferManager $transferManager,
        protected LocalTransferSettings $transferSettings,
    ) {
        $this->activeTemplate = activeTemplate();
    }

    public function index()
    {
        $pageTitle = 'Local Bank Transfer';
        $user = Auth::user();
        $settings = $this->transferSettings->values();
        $banks = [];
        $bankLoadError = null;

        if ($settings['enabled']) {
            try {
                $banks = $this->transferManager->banks($settings);
            } catch (\Throwable $exception) {
                $bankLoadError = $exception->getMessage();
            }
        }

        $log = Transfer::whereMethod_id(2)
            ->whereUserId($user->id)
            ->latest()
            ->paginate(10);

        return view($this->activeTemplate.'user.transfer.othertransfer', compact(
            'pageTitle',
            'user',
            'log',
            'banks',
            'settings',
            'bankLoadError'
        ));
    }

    public function resolve(Request $request): JsonResponse
    {
        $settings = $this->transferSettings->values();
        if (! $settings['enabled']) {
            return response()->json([
                'success' => false,
                'message' => 'Local transfer is not enabled right now.',
            ], 422);
        }

        $validated = $request->validate([
            'bank_name' => ['required', 'string', 'max:191'],
            'bank_code' => ['nullable', 'string', 'max:80'],
            'account_number' => ['required', 'digits:10'],
        ]);

        try {
            $resolved = $this->transferManager->resolve([
                'name' => $validated['bank_name'],
                'code' => $validated['bank_code'] ?? '',
            ], $validated['account_number'], $settings);

            return response()->json([
                'success' => true,
                'message' => 'Account resolved successfully.',
                'data' => $resolved,
            ]);
        } catch (\Throwable $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 422);
        }
    }

    public function submit(Request $request)
    {
        $settings = $this->transferSettings->values();
        if (! $settings['enabled']) {
            return back()->withAlertx([['error', 'Local transfer is not enabled right now.']]);
        }

        $user = Auth::user();
        $validated = $request->validate([
            'bank_name' => ['required', 'string', 'max:191'],
            'bank_code' => ['nullable', 'string', 'max:80'],
            'account_number' => ['required', 'digits:10'],
            'account_name' => ['required', 'string', 'max:191'],
            'amount' => ['required', 'numeric', 'min:1'],
            'narration' => ['nullable', 'string', 'max:191'],
            'pin_code' => ['nullable', 'string', 'size:4'],
        ]);

        if ($settings['require_pin'] && (int) $user->pin_state === 1) {
            if (! hash_equals((string) $user->pin, (string) $request->input('pin_code'))) {
                return back()->withAlertx([['error', 'Incorrect PIN.']])->withInput();
            }
        }

        $amount = round((float) $validated['amount'], 2);
        $general = $this->transferSettings->general();
        if ($amount < $settings['minimum']) {
            return back()->withAlertx([['error', 'Minimum transfer amount is '.$general->cur_sym.showAmount($settings['minimum']).'.']])->withInput();
        }

        if ($settings['maximum'] > 0 && $amount > $settings['maximum']) {
            return back()->withAlertx([['error', 'Maximum transfer amount is '.$general->cur_sym.showAmount($settings['maximum']).'.']])->withInput();
        }

        try {
            $resolved = $this->transferManager->resolve([
                'name' => $validated['bank_name'],
                'code' => $validated['bank_code'] ?? '',
            ], $validated['account_number'], $settings);
        } catch (\Throwable $exception) {
            return back()->withAlertx([['error', $exception->getMessage()]])->withInput();
        }

        if (! hash_equals(
            strtolower(trim($resolved['account_name'] ?? '')),
            strtolower(trim($validated['account_name']))
        )) {
            return back()->withAlertx([['error', 'Account details changed. Please resolve the account again.']])->withInput();
        }

        $charge = round($amount * ((float) $general->transferfee / 100), 2);
        $totalDebit = round($amount + $charge, 2);
        $reference = 'LTF'.date('ymdHis').getTrx(6);
        $narration = trim((string) ($validated['narration'] ?: 'Local transfer'));

        try {
            $transfer = $this->reserveTransfer($user, [
                'reference' => $reference,
                'amount' => $amount,
                'charge' => $charge,
                'total_debit' => $totalDebit,
                'bank_name' => $resolved['bank_name'] ?: $validated['bank_name'],
                'bank_code' => $resolved['bank_code'] ?: ($validated['bank_code'] ?? ''),
                'account_name' => $resolved['account_name'],
                'account_number' => $resolved['account_number'],
                'narration' => $narration,
                'meta' => [
                    'resolved_by' => $resolved['resolved_by'] ?? null,
                    'resolved_payload' => $resolved['meta'] ?? null,
                ],
            ]);
        } catch (\RuntimeException $exception) {
            return back()->withAlertx([['error', $exception->getMessage()]])->withInput();
        }

        $providerResponse = $this->transferManager->transfer([
            'reference' => $reference,
            'amount' => $amount,
            'narration' => $narration,
            'sender_name' => trim(($user->firstname ?? '').' '.($user->lastname ?? '')) ?: ($user->fullname ?? $user->username ?? 'Wallet User'),
            'resolved_bank' => $resolved,
        ], $settings);

        $status = $providerResponse['status'] ?? 'failed';
        if ($status === 'success') {
            $this->markTransferSuccess($transfer->id, $providerResponse);
            $this->storeBeneficiary($user->id, $resolved);

            return redirect()->route('user.othertransfer')
                ->withAlertx([['success', $providerResponse['message'] ?? 'Transfer completed successfully.']]);
        }

        if ($status === 'pending') {
            $this->markTransferPending($transfer->id, $providerResponse);
            $this->storeBeneficiary($user->id, $resolved);

            return redirect()->route('user.othertransfer')
                ->withAlertx([['warning', $providerResponse['message'] ?? 'Transfer submitted and is awaiting confirmation.']]);
        }

        $this->markTransferFailedAndRefund($transfer->id, $providerResponse, $totalDebit);

        return back()->withAlertx([['error', $providerResponse['message'] ?? 'Transfer failed and your wallet has been refunded.']])->withInput();
    }

    protected function reserveTransfer(User $user, array $payload): Transfer
    {
        return DB::transaction(function () use ($user, $payload) {
            $lockedUser = User::whereKey($user->id)->lockForUpdate()->firstOrFail();
            if ((float) $lockedUser->balance < (float) $payload['total_debit']) {
                throw new \RuntimeException('You do not have enough balance for this transfer.');
            }

            $lockedUser->balance = round((float) $lockedUser->balance - (float) $payload['total_debit'], 2);
            $lockedUser->save();

            $transfer = new Transfer();
            $transfer->method_id = 2;
            $transfer->user_id = $lockedUser->id;
            $transfer->provider = null;
            $transfer->provider_reference = null;
            $transfer->charge = $payload['charge'];
            $transfer->amount = $payload['amount'];
            $transfer->bank_name = $payload['bank_name'];
            $transfer->bank_code = $payload['bank_code'];
            $transfer->account_name = $payload['account_name'];
            $transfer->account_number = $payload['account_number'];
            $transfer->narration = $payload['narration'];
            $transfer->details = $this->transferDetails(
                $payload['bank_name'],
                $payload['account_name'],
                $payload['account_number'],
                $payload['narration']
            );
            $transfer->reason = null;
            $transfer->meta = array_merge($payload['meta'] ?? [], [
                'reserved_balance' => $lockedUser->balance,
                'refunded' => false,
            ]);
            $transfer->status = 0;
            $transfer->trx = $payload['reference'];
            $transfer->save();

            $transaction = new Transaction();
            $transaction->user_id = $lockedUser->id;
            $transaction->amount = $payload['amount'];
            $transaction->charge = $payload['charge'];
            $transaction->trx = $payload['reference'];
            $transaction->trx_type = '-';
            $transaction->post_balance = $lockedUser->balance;
            $transaction->details = 'Local transfer to '.$payload['bank_name'].' '.$payload['account_number'];
            $transaction->save();

            return $transfer;
        });
    }

    protected function markTransferSuccess(int $transferId, array $providerResponse): void
    {
        DB::transaction(function () use ($transferId, $providerResponse) {
            $transfer = Transfer::whereKey($transferId)->lockForUpdate()->firstOrFail();
            $transfer->provider = $providerResponse['provider'] ?? $transfer->provider;
            $transfer->provider_reference = $providerResponse['provider_reference'] ?? $transfer->trx;
            $transfer->reason = $providerResponse['message'] ?? null;
            $transfer->meta = array_merge($transfer->meta ?? [], $providerResponse['meta'] ?? []);
            $transfer->status = 1;
            $transfer->save();
        });
    }

    protected function markTransferPending(int $transferId, array $providerResponse): void
    {
        DB::transaction(function () use ($transferId, $providerResponse) {
            $transfer = Transfer::whereKey($transferId)->lockForUpdate()->firstOrFail();
            $transfer->provider = $providerResponse['provider'] ?? $transfer->provider;
            $transfer->provider_reference = $providerResponse['provider_reference'] ?? $transfer->provider_reference;
            $transfer->reason = $providerResponse['message'] ?? null;
            $transfer->meta = array_merge($transfer->meta ?? [], $providerResponse['meta'] ?? []);
            $transfer->status = 0;
            $transfer->save();
        });
    }

    protected function markTransferFailedAndRefund(int $transferId, array $providerResponse, float $refundAmount): void
    {
        DB::transaction(function () use ($transferId, $providerResponse, $refundAmount) {
            $transfer = Transfer::whereKey($transferId)->lockForUpdate()->firstOrFail();
            $user = User::whereKey($transfer->user_id)->lockForUpdate()->firstOrFail();
            $meta = $transfer->meta ?? [];

            if (! ($meta['refunded'] ?? false)) {
                $user->balance = round((float) $user->balance + $refundAmount, 2);
                $user->save();

                $refund = new Transaction();
                $refund->user_id = $user->id;
                $refund->amount = $refundAmount;
                $refund->charge = 0;
                $refund->trx = $transfer->trx.'R';
                $refund->trx_type = '+';
                $refund->post_balance = $user->balance;
                $refund->details = 'Reversal for failed local transfer '.$transfer->trx;
                $refund->save();
            }

            $transfer->provider = $providerResponse['provider'] ?? $transfer->provider;
            $transfer->provider_reference = $providerResponse['provider_reference'] ?? $transfer->provider_reference;
            $transfer->reason = $providerResponse['message'] ?? 'Transfer failed.';
            $transfer->meta = array_merge($meta, $providerResponse['meta'] ?? [], [
                'refunded' => true,
                'refund_amount' => $refundAmount,
            ]);
            $transfer->status = 2;
            $transfer->save();
        });
    }

    protected function transferDetails(string $bankName, string $accountName, string $accountNumber, string $narration): string
    {
        return 'Bank Name: '.$bankName.'<br>'
            .'Account Name: '.$accountName.'<br>'
            .'Account Number: '.$accountNumber.'<br>'
            .'Narration: '.$narration;
    }

    protected function storeBeneficiary(int $userId, array $resolved): void
    {
        $beneficiary = TransferBeneficiary::firstOrNew([
            'method_id' => 2,
            'user_id' => $userId,
        ], [
            'details' => [],
        ]);

        $details = $beneficiary->details ?? [];
        $beneficiaries = collect($details['items'] ?? [])
            ->reject(fn (array $item) => ($item['account_number'] ?? null) === ($resolved['account_number'] ?? null) && ($item['bank_code'] ?? null) === ($resolved['bank_code'] ?? null))
            ->prepend([
                'bank_name' => $resolved['bank_name'] ?? '',
                'bank_code' => $resolved['bank_code'] ?? '',
                'account_name' => $resolved['account_name'] ?? '',
                'account_number' => $resolved['account_number'] ?? '',
            ])
            ->take(10)
            ->values()
            ->all();

        $beneficiary->details = ['items' => $beneficiaries];
        $beneficiary->status = 1;
        $beneficiary->save();
    }
}
