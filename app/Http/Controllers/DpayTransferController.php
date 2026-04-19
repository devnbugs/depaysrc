<?php

namespace App\Http\Controllers;

use App\Models\Transfer;
use App\Models\Transaction;
use App\Models\TransferBeneficiary;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

/**
 * Dpay Transfer Controller
 * Handles interbank transfers using Dpay API
 * Supports phone number (default) and username resolution
 */
class DpayTransferController extends Controller
{
    protected string $activeTemplate;

    public function __construct()
    {
        $this->activeTemplate = activeTemplate();
    }

    /**
     * Display Dpay transfer form
     */
    public function index()
    {
        $pageTitle = 'Dpay Interbank Transfer';
        $user = Auth::user();
        $general = gs();

        // Get saved beneficiaries
        $beneficiaries = TransferBeneficiary::where('method_id', 3) // 3 = Dpay method
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        // Get transfer history
        $transfers = Transfer::where('method_id', 3)
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(10);

        return view($this->activeTemplate . 'user.transfer.dpay', compact(
            'pageTitle',
            'user',
            'general',
            'beneficiaries',
            'transfers'
        ));
    }

    /**
     * Resolve recipient using phone (default) or username
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function resolve(Request $request): JsonResponse
    {
        $request->validate([
            'recipient' => ['required', 'string'], // Phone or username
            'type' => ['required', 'in:phone,username'], // Resolution type
        ]);

        $recipient = $request->input('recipient');
        $type = $request->input('type', 'phone'); // Default to phone

        try {
            $resolved = $this->resolveRecipient($recipient, $type);

            return response()->json([
                'success' => true,
                'message' => 'Recipient resolved successfully.',
                'data' => $resolved,
            ]);
        } catch (\Throwable $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 422);
        }
    }

    /**
     * Internal method to resolve recipient from Dpay
     * 
     * @param string $recipient Phone or username
     * @param string $type Resolution type (phone|username)
     * @return array
     * @throws \Exception
     */
    protected function resolveRecipient(string $recipient, string $type = 'phone'): array
    {
        $apiKey = config('services.dpay.api_key');
        $baseUrl = config('services.dpay.base_url');

        if (!$apiKey || !$baseUrl) {
            throw new \Exception('Dpay is not configured. Please contact support.');
        }

        // Map local users first for faster resolution
        if ($type === 'phone') {
            $user = User::where('phone', $recipient)->first();
        } else {
            $user = User::where('username', $recipient)->first();
        }

        if ($user) {
            return [
                'resolved_by' => 'local_user',
                'account_name' => $user->fullname,
                'account_number' => $user->account_number,
                'phone' => $user->phone,
                'email' => $user->email,
                'user_id' => $user->id,
            ];
        }

        // Fall back to Dpay API
        $endpoint = $type === 'phone' ? 'resolve/phone' : 'resolve/username';

        $response = Http::baseUrl($baseUrl)
            ->withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Accept' => 'application/json',
            ])
            ->timeout(10)
            ->post($endpoint, [
                'recipient' => $recipient,
            ]);

        if (!$response->successful()) {
            throw new \Exception(
                $response->json('message') ?? 'Unable to resolve recipient on Dpay.'
            );
        }

        $data = $response->json('data', []);

        return [
            'resolved_by' => 'dpay',
            'account_name' => $data['name'] ?? '',
            'account_number' => $data['account_number'] ?? '',
            'bank_code' => $data['bank_code'] ?? '',
            'bank_name' => $data['bank_name'] ?? '',
            'phone' => $data['phone'] ?? $recipient,
        ];
    }

    /**
     * Submit Dpay transfer
     */
    public function submit(Request $request)
    {
        $user = Auth::user();
        $general = gs();

        $validated = $request->validate([
            'recipient' => ['required', 'string'],
            'type' => ['required', 'in:phone,username'],
            'amount' => ['required', 'numeric', 'min:1'],
            'narration' => ['nullable', 'string', 'max:191'],
            'save_beneficiary' => ['nullable', 'boolean'],
        ]);

        $amount = round((float) $validated['amount'], 2);

        // Check minimum and maximum limits
        $minAmount = config('services.dpay.minimum', 100);
        $maxAmount = config('services.dpay.maximum', 1000000);

        if ($amount < $minAmount) {
            return back()->withAlertx([
                ['error', 'Minimum transfer amount is ' . $general->cur_sym . showAmount($minAmount) . '.']
            ])->withInput();
        }

        if ($amount > $maxAmount) {
            return back()->withAlertx([
                ['error', 'Maximum transfer amount is ' . $general->cur_sym . showAmount($maxAmount) . '.']
            ])->withInput();
        }

        if ($amount > $user->balance) {
            return back()->withAlertx([
                ['error', 'Insufficient balance for this transfer.']
            ])->withInput();
        }

        try {
            $resolved = $this->resolveRecipient(
                $validated['recipient'],
                $validated['type']
            );
        } catch (\Throwable $exception) {
            return back()->withAlertx([
                ['error', $exception->getMessage()]
            ])->withInput();
        }

        // Store in session for preview
        session()->put('dpay_transfer', [
            'recipient' => $validated['recipient'],
            'type' => $validated['type'],
            'amount' => $amount,
            'narration' => $validated['narration'] ?? '',
            'save_beneficiary' => $validated['save_beneficiary'] ?? false,
            'resolved' => $resolved,
        ]);

        return redirect()->route('user.dpay.preview');
    }

    /**
     * Preview transfer before confirmation
     */
    public function preview()
    {
        $transferData = session()->get('dpay_transfer');

        if (!$transferData) {
            return redirect()->route('user.dpay.index')
                ->withAlertx([['error', 'Please initiate a transfer first.']]);
        }

        $pageTitle = 'Dpay Transfer Preview';
        $user = Auth::user();
        $general = gs();

        return view($this->activeTemplate . 'user.transfer.dpay-preview', compact(
            'pageTitle',
            'user',
            'general',
            'transferData'
        ));
    }

    /**
     * Confirm and process transfer
     */
    public function confirm(Request $request)
    {
        $user = Auth::user();
        $general = gs();
        $transferData = session()->get('dpay_transfer');

        if (!$transferData) {
            return redirect()->route('user.dpay.index')
                ->withAlertx([['error', 'Transfer session expired. Please try again.']]);
        }

        $amount = $transferData['amount'];

        // Double-check balance
        if ($amount > $user->balance) {
            session()->forget('dpay_transfer');
            return redirect()->route('user.dpay.index')
                ->withAlertx([['error', 'Insufficient balance for this transfer.']]);
        }

        DB::beginTransaction();

        try {
            // Create transfer record
            $transfer = Transfer::create([
                'user_id' => $user->id,
                'method_id' => 3, // Dpay method ID
                'amount' => $amount,
                'details' => json_encode([
                    'recipient' => $transferData['recipient'],
                    'type' => $transferData['type'],
                    'resolved_by' => $transferData['resolved']['resolved_by'],
                    'account_name' => $transferData['resolved']['account_name'],
                    'narration' => $transferData['narration'],
                ]),
                'status' => 1, // Approved
                'trx' => getTrx(),
            ]);

            // If recipient is local user, process internal transfer
            if ($transferData['resolved']['resolved_by'] === 'local_user') {
                $recipient = User::find($transferData['resolved']['user_id']);

                // Deduct from sender
                $user->balance -= $amount;
                $user->save();

                // Add to recipient
                $recipient->balance += $amount;
                $recipient->save();

                // Create sender transaction
                Transaction::create([
                    'user_id' => $user->id,
                    'amount' => $amount,
                    'post_balance' => $user->balance,
                    'charge' => 0,
                    'trx_type' => '-',
                    'details' => 'Dpay Transfer to ' . $transferData['resolved']['account_name'],
                    'trx' => $transfer->trx,
                ]);

                // Create recipient transaction
                Transaction::create([
                    'user_id' => $recipient->id,
                    'amount' => $amount,
                    'post_balance' => $recipient->balance,
                    'charge' => 0,
                    'trx_type' => '+',
                    'details' => 'Dpay Transfer from ' . $user->fullname,
                    'trx' => $transfer->trx,
                ]);
            } else {
                // External bank transfer via Dpay API
                $this->processExternalTransfer($transfer, $transferData, $user);
            }

            // Save beneficiary if requested
            if ($transferData['save_beneficiary']) {
                TransferBeneficiary::create([
                    'user_id' => $user->id,
                    'method_id' => 3,
                    'details' => json_encode($transferData['resolved']),
                    'name' => $transferData['resolved']['account_name'],
                    'account_number' => $transferData['recipient'],
                ]);
            }

            DB::commit();
            session()->forget('dpay_transfer');

            return redirect()->route('user.dpay.index')
                ->withAlertx([['success', 'Transfer completed successfully.']]);

        } catch (\Throwable $exception) {
            DB::rollBack();
            session()->forget('dpay_transfer');

            return redirect()->route('user.dpay.index')
                ->withAlertx([['error', $exception->getMessage()]]);
        }
    }

    /**
     * Process external transfer via Dpay API
     * 
     * @param Transfer $transfer
     * @param array $transferData
     * @param User $user
     * @throws \Exception
     */
    protected function processExternalTransfer(Transfer $transfer, array $transferData, User $user): void
    {
        $apiKey = config('services.dpay.api_key');
        $baseUrl = config('services.dpay.base_url');

        $resolved = $transferData['resolved'];
        $amount = $transferData['amount'];

        $response = Http::baseUrl($baseUrl)
            ->withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Accept' => 'application/json',
            ])
            ->timeout(30)
            ->post('transfer', [
                'recipient' => $transferData['recipient'],
                'account_name' => $resolved['account_name'],
                'account_number' => $resolved['account_number'],
                'bank_code' => $resolved['bank_code'] ?? '',
                'amount' => $amount,
                'reference' => $transfer->trx,
                'description' => $transferData['narration'] ?? 'Fund Transfer',
            ]);

        if (!$response->successful()) {
            throw new \Exception(
                $response->json('message') ?? 'Dpay transfer failed. Please try again.'
            );
        }

        $responseData = $response->json('data', []);

        // Deduct from user balance
        $user->balance -= $amount;
        $user->save();

        // Create transaction record
        Transaction::create([
            'user_id' => $user->id,
            'amount' => $amount,
            'post_balance' => $user->balance,
            'charge' => 0,
            'trx_type' => '-',
            'details' => 'Dpay Transfer to ' . $resolved['account_name'],
            'trx' => $transfer->trx,
        ]);

        // Update transfer with Dpay response
        $transfer->update([
            'meta' => [
                'dpay_reference' => $responseData['reference'] ?? '',
                'dpay_status' => $responseData['status'] ?? 'pending',
            ],
        ]);
    }
}
