<?php

namespace App\Services\Passkeys;

use App\Models\User;

class PasskeyStateService
{
    public function syncForUser(User $user): void
    {
        $credentials = $user->passkeys()
            ->orderBy('id')
            ->pluck('credential_id')
            ->filter()
            ->values()
            ->all();

        $firstPasskey = $user->passkeys()->orderBy('created_at')->first();

        $user->forceFill([
            'passkey_enabled' => ! empty($credentials),
            'passkey_credentials' => $credentials === [] ? null : $credentials,
            'passkey_created_at' => $firstPasskey?->created_at,
        ])->save();
    }

    public function syncForUserId(?int $userId): void
    {
        if (! $userId) {
            return;
        }

        $user = User::find($userId);

        if ($user) {
            $this->syncForUser($user);
        }
    }
}
