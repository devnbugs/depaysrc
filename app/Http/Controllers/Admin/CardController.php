<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GeneralSetting;
use App\Models\VirtualCard;
use App\Services\Cards\InterswitchVirtualCardService;
use App\Services\Cards\VirtualCardSettings;
use Illuminate\Http\Request;
use RuntimeException;

class CardController extends Controller
{
    public function __construct(
        protected VirtualCardSettings $cardSettings,
        protected InterswitchVirtualCardService $provider,
    ) {
    }

    public function active()
    {
        $pageTitle = 'Active Card';
        $emptyMessage = 'No Active Card.';
        $card = VirtualCard::where('status', '!=', 2)->orderBy('id', 'desc')->paginate(getPaginate());
        return view('admin.card.index', compact('pageTitle', 'emptyMessage', 'card'));
    }

    public function inactive()
    {
        $pageTitle = 'Terminated Card';
        $emptyMessage = 'No Terminated Card.';
        $card = VirtualCard::where('status', 2)->orderBy('id', 'desc')->paginate(getPaginate());
        return view('admin.card.index', compact('pageTitle', 'emptyMessage', 'card'));
    }

    public function view($id)
    {
        $general = GeneralSetting::firstOrFail();
        $settings = $this->cardSettings->settings($general);
        $card = $this->findCard($id);
        $pageTitle = 'View Virtual Card';
        $serviceEnabled = (bool) data_get($settings, 'enabled', false);
        $balanceError = null;

        if ($serviceEnabled && $this->cardSettings->isConfigured($general) && filled($card->pan)) {
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

        return view('admin.card.view', compact('pageTitle', 'card', 'settings', 'serviceEnabled', 'balanceError'));
    }

    public function fundcard(Request $request, $id)
    {
        $notify[] = ['info', 'Manual funding is not exposed for Interswitch virtual cards from the admin screen yet.'];
        return back()->withNotify($notify);
    }

    public function trxcard(Request $request, $id)
    {
        $notify[] = ['info', 'Card statement export is not exposed for Interswitch virtual cards from the admin screen yet.'];
        return back()->withNotify($notify);
    }

    public function block($id)
    {
        return $this->toggleBlock($id, true);
    }

    public function unblock($id)
    {
        return $this->toggleBlock($id, false);
    }

    public function terminate($id)
    {
        $card = $this->findCard($id);
        $card->status = 2;
        $card->save();

        $notify[] = ['success', 'Card archived locally. Provider-side termination is not automated for this Interswitch flow yet.'];
        return back()->withNotify($notify);
    }

    protected function toggleBlock(string $reference, bool $block)
    {
        $general = GeneralSetting::firstOrFail();
        $settings = $this->cardSettings->settings($general);
        $card = $this->findCard($reference);

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

    protected function findCard(string $reference): VirtualCard
    {
        $card = VirtualCard::whereReference($reference)->first();

        if (! $card) {
            abort(404);
        }

        return $card;
    }
}
