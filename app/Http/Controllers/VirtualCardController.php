<?php

namespace App\Http\Controllers;

use App\Models\GeneralSetting;
use App\Models\Transaction;
use App\Models\VirtualCard;
use App\Services\Cards\InterswitchVirtualCardService;
use App\Services\Cards\VirtualCardSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use RuntimeException;

class VirtualCardController extends Controller
{
    protected string $activeTemplate;

    public function __construct(
        protected VirtualCardSettings $cardSettings,
        protected InterswitchVirtualCardService $provider,
    ) {
        $this->activeTemplate = activeTemplate();
    }

    public function requestcard()
    {
        $pageTitle = 'Manage Virtual Cards';
        $general = GeneralSetting::firstOrFail();
        $user = Auth::user();
        $settings = $this->cardSettings->settings($general);
        $cards = VirtualCard::where('user_id', $user->id)->where('status', '!=', 2)->latest()->get();
        $availableTypes = $this->cardSettings->availableTypes($general);
        $blockers = $this->cardSettings->blockersForUser($user, $general);
        $canCreate = $blockers === [];

        return view($this->activeTemplate.'user.cards.manage', compact(
            'pageTitle',
            'user',
            'general',
            'settings',
            'cards',
            'availableTypes',
            'blockers',
            'canCreate',
        ));
    }

    public function requestsubmit(Request $request)
    {
        $general = GeneralSetting::firstOrFail();
        $user = Auth::user();
        $settings = $this->cardSettings->settings($general);
        $availableTypes = $this->cardSettings->availableTypes($general);

        $request->validate([
            'card_type' => 'required|in:'.implode(',', array_keys($availableTypes)),
            'initial_pin' => 'nullable|digits:4',
            'linked_account_number' => 'nullable|string|max:28',
        ]);

        $blockers = $this->cardSettings->blockersForUser($user, $general);

        if ($blockers !== []) {
            $notify[] = ['error', $blockers[0]];
            return back()->withInput()->withNotify($notify);
        }

        $creationFee = (float) data_get($settings, 'creation_fee', 0);

        if ($creationFee > (float) $user->balance) {
            $notify[] = ['error', 'Insufficient wallet balance for the card creation fee.'];
            return back()->withInput()->withNotify($notify);
        }

        $cardType = (string) $request->card_type;
        $nameOnCard = Str::upper(Str::limit(trim((string) $user->fullname), 25, ''));
        $payload = [
            'issuerNr' => (string) $settings['issuer_nr'],
            'cardProgram' => (string) $settings['card_program'],
            'userId' => (string) $settings['user_id'],
            'lastName' => trim((string) ($user->lastname ?: 'User')),
            'firstName' => trim((string) ($user->firstname ?: 'User')),
            'nameOnCard' => $nameOnCard !== '' ? $nameOnCard : 'USER',
        ];

        if (filled($settings['branch_code'])) {
            $payload['branchCode'] = (string) $settings['branch_code'];
        }

        if ($request->filled('initial_pin')) {
            $payload['pin'] = (string) $request->initial_pin;
        }

        if ($cardType === 'DEBIT_EXISTING_ACCOUNT') {
            $accountId = trim((string) ($request->linked_account_number ?: $user->accountNumber ?: ''));

            if ($accountId === '') {
                $notify[] = ['error', 'A linked issuer account number is required before you can create a debit virtual card.'];
                return back()->withInput()->withNotify($notify);
            }

            $payload['accountId'] = $accountId;
            $payload['accountType'] = (string) $settings['account_type'];
        }

        try {
            $response = $this->provider->createCard($settings, $payload, $cardType);
        } catch (RuntimeException $exception) {
            $notify[] = ['error', $exception->getMessage()];
            return back()->withInput()->withNotify($notify);
        }

        $cardData = (array) data_get($response, 'card', []);
        $card = new VirtualCard();
        $card->user_id = $user->id;
        $card->status = 1;
        $card->reference = $this->generateReference();
        $card->provider = 'interswitch';
        $card->provider_reference = (string) data_get($response, 'correlationId');
        $card->masked_pan = $this->provider->formattedMaskedPan((string) data_get($cardData, 'pan', ''));
        $card->card_type = $cardType;
        $card->name_on_card = (string) $payload['nameOnCard'];
        $card->customer_id = (string) data_get($cardData, 'customerId');
        $card->pan = (string) data_get($cardData, 'pan');
        $card->cvv2 = (string) (data_get($cardData, 'cvv2') ?: data_get($cardData, 'cvv'));
        $card->card_sequence_number = (string) data_get($cardData, 'seqNr');
        $card->expiry_date = (string) data_get($cardData, 'expiryDate');
        $card->currency = (string) data_get($settings, 'default_currency', 'NGN');
        $card->account_id = (string) data_get($payload, 'accountId');
        $card->account_type = (string) data_get($payload, 'accountType');
        $card->available_balance = 0;
        $card->ledger_balance = 0;
        $card->provider_payload = $response;
        $card->last_synced_at = now();
        $card->save();

        if ($creationFee > 0) {
            $user->balance = (float) $user->balance - $creationFee;
            $user->save();

            $transaction = new Transaction();
            $transaction->user_id = $user->id;
            $transaction->amount = $creationFee;
            $transaction->post_balance = $user->balance;
            $transaction->charge = 0;
            $transaction->trx_type = '-';
            $transaction->details = 'Interswitch virtual card creation fee';
            $transaction->trx = getTrx();
            $transaction->save();
        }

        $notify[] = ['success', 'Your Interswitch virtual card has been created successfully.'];
        return redirect()->route('user.view.card', $card->reference)->withNotify($notify);
    }

    public function viewcard($id)
    {
        $general = GeneralSetting::firstOrFail();
        $user = Auth::user();
        $card = $this->findUserCard($user->id, $id);
        $pageTitle = 'My Virtual Card';
        $settings = $this->cardSettings->settings($general);
        $serviceEnabled = (bool) data_get($settings, 'enabled', false);
        $balanceError = null;

        if ($serviceEnabled && $this->cardSettings->isConfigured($general)) {
            try {
                $balance = $this->provider->fetchBalance($settings, $card);
                $card->available_balance = data_get($balance, 'availableBalance', $card->available_balance);
                $card->ledger_balance = data_get($balance, 'ledgerBalance', $card->ledger_balance);
                $card->last_synced_at = now();
                $card->save();
            } catch (RuntimeException $exception) {
                $balanceError = $exception->getMessage();
            }
        }

        return view($this->activeTemplate.'user.cards.view', compact(
            'pageTitle',
            'general',
            'user',
            'card',
            'settings',
            'serviceEnabled',
            'balanceError',
        ));
    }

    public function blockcard($id)
    {
        return $this->toggleCardBlock($id, true);
    }

    public function unblockcard($id)
    {
        return $this->toggleCardBlock($id, false);
    }

    public function fundcard(Request $request, $id)
    {
        $notify[] = ['info', 'Manual card funding is not yet exposed for the Interswitch virtual card flow.'];
        return back()->withNotify($notify);
    }

    public function trxcard(Request $request, $id)
    {
        $notify[] = ['info', 'Card statement export is not yet exposed for the Interswitch virtual card flow.'];
        return back()->withNotify($notify);
    }

    protected function toggleCardBlock(string $reference, bool $block)
    {
        $general = GeneralSetting::firstOrFail();
        $settings = $this->cardSettings->settings($general);
        $user = Auth::user();
        $card = $this->findUserCard($user->id, $reference);

        if (! (bool) data_get($settings, 'enabled', false) || ! $this->cardSettings->isConfigured($general)) {
            $notify[] = ['error', 'Card service is unavailable right now.'];
            return back()->withNotify($notify);
        }

        try {
            $block
                ? $this->provider->blockCard($settings, $card)
                : $this->provider->unblockCard($settings, $card);
        } catch (RuntimeException $exception) {
            $notify[] = ['error', $exception->getMessage()];
            return back()->withNotify($notify);
        }

        $card->status = $block ? 0 : 1;
        $card->blocked_at = $block ? now() : null;
        $card->last_synced_at = now();
        $card->save();

        $notify[] = ['success', $block ? 'Card blocked successfully.' : 'Card unblocked successfully.'];
        return back()->withNotify($notify);
    }

    protected function findUserCard(int $userId, string $reference): VirtualCard
    {
        $card = VirtualCard::where('user_id', $userId)->whereReference($reference)->first();

        if (! $card) {
            abort(404);
        }

        return $card;
    }

    protected function generateReference(): string
    {
        do {
            $reference = 'VC'.Str::upper(Str::random(18));
        } while (VirtualCard::whereReference($reference)->exists());

        return $reference;
    }
}
