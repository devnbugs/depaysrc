<?php

namespace App\Livewire;

use App\Services\Passkeys\PasskeyStateService;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Spatie\LaravelPasskeys\Actions\GeneratePasskeyRegisterOptionsAction;
use Spatie\LaravelPasskeys\Actions\StorePasskeyAction;
use Spatie\LaravelPasskeys\Models\Concerns\HasPasskeys;
use Spatie\LaravelPasskeys\Support\Config;
use Throwable;

class PasskeyManager extends Component
{
    #[Validate('required|string|max:255')]
    public string $name = '';

    public function render(): View
    {
        return view('livewire.passkey-manager', [
            'passkeys' => $this->currentUser()->passkeys()->latest()->get(),
            'user' => $this->currentUser(),
        ]);
    }

    public function validatePasskeyProperties(): void
    {
        $this->validate();

        $this->dispatch('passkeyPropertiesValidated', [
            'passkeyOptions' => json_decode($this->generatePasskeyOptions()),
        ]);
    }

    public function storePasskey(string $passkey): void
    {
        $storePasskeyAction = Config::getAction('store_passkey', StorePasskeyAction::class);

        try {
            $storePasskeyAction->execute(
                $this->currentUser(),
                $passkey,
                $this->previouslyGeneratedPasskeyOptions(),
                request()->getHost(),
                ['name' => $this->name]
            );
        } catch (Throwable $e) {
            throw ValidationException::withMessages([
                'name' => 'We could not register this passkey on the current device. Try again and approve the browser prompt.',
            ])->errorBag('passkeyForm');
        }

        app(PasskeyStateService::class)->syncForUser($this->currentUser()->fresh());
        $this->reset('name');
        session()->flash('passkey-status', 'Passkey created successfully.');
    }

    public function deletePasskey(int $passkeyId): void
    {
        $this->currentUser()->passkeys()->where('id', $passkeyId)->delete();
        app(PasskeyStateService::class)->syncForUser($this->currentUser()->fresh());
        session()->flash('passkey-status', 'Passkey removed successfully.');
    }

    protected function currentUser(): Authenticatable&HasPasskeys
    {
        /** @var Authenticatable&HasPasskeys $user */
        $user = auth()->user();

        return $user;
    }

    protected function generatePasskeyOptions(): string
    {
        $generatePassKeyOptionsAction = Config::getAction('generate_passkey_register_options', GeneratePasskeyRegisterOptionsAction::class);
        $options = $generatePassKeyOptionsAction->execute($this->currentUser());

        session()->put('passkey-registration-options', $options);

        return $options;
    }

    protected function previouslyGeneratedPasskeyOptions(): ?string
    {
        return session()->pull('passkey-registration-options');
    }
}
