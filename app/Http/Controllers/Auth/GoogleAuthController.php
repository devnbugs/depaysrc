<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    private const SESSION_KEY = 'google_oauth_profile';

    public function redirect()
    {
        return Socialite::driver('google')
            ->scopes(['openid', 'email', 'profile'])
            ->redirect();
    }

    public function callback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Throwable $e) {
            $notify[] = ['error', 'Google login failed. Please try again.'];
            return redirect()->route('user.loginpage')->withNotify($notify);
        }

        $email = (string) ($googleUser->getEmail() ?? '');
        if (trim($email) === '') {
            $notify[] = ['error', 'Google did not return an email address for this account.'];
            return redirect()->route('user.loginpage')->withNotify($notify);
        }

        $existingUser = User::where('email', $email)->first();
        if ($existingUser) {
            $this->attachGoogleIdentity($existingUser, $googleUser);
            Auth::login($existingUser, true);

            return redirect()->intended(route('user.home'));
        }

        $request->session()->put(self::SESSION_KEY, [
            'provider' => 'google',
            'provider_id' => (string) ($googleUser->getId() ?? ''),
            'email' => $email,
            'name' => (string) ($googleUser->getName() ?? ''),
            'nickname' => (string) ($googleUser->getNickname() ?? ''),
            'avatar' => (string) ($googleUser->getAvatar() ?? ''),
        ]);

        return redirect()->route('user.google.onboarding.show');
    }

    public function showOnboarding(Request $request)
    {
        $profile = $this->getProfileFromSession($request);
        if (!$profile) {
            return redirect()->route('user.loginpage');
        }

        $pageTitle = 'Complete your profile';

        $name = trim((string) ($profile['name'] ?? ''));
        [$first, $last] = $this->splitName($name);

        $suggestedUsername = $this->suggestUsername($profile);

        return view(activeTemplate() . 'user.auth.google-onboarding', [
            'pageTitle' => $pageTitle,
            'profile' => $profile,
            'firstname' => $first,
            'lastname' => $last,
            'suggestedUsername' => $suggestedUsername,
        ]);
    }

    public function completeOnboarding(Request $request)
    {
        $profile = $this->getProfileFromSession($request);
        if (!$profile) {
            return redirect()->route('user.loginpage');
        }

        $rules = [
            'firstname' => ['required', 'string', 'max:50'],
            'lastname' => ['required', 'string', 'max:50'],
            'username' => ['required', 'alpha_num', 'min:3', 'max:30'],
            'mobile' => ['required', 'string', 'min:7', 'max:25'],
        ];

        // Only enforce uniqueness if columns exist (for safety across environments).
        if (Schema::hasColumn('users', 'username')) {
            $rules['username'][] = 'unique:users,username';
        }
        if (Schema::hasColumn('users', 'mobile')) {
            $rules['mobile'][] = 'unique:users,mobile';
        }

        $validated = $request->validate($rules, [
            'username.alpha_num' => 'Username may only contain letters and numbers.',
        ]);

        // If a user was created in parallel with the same email, just log into that one.
        $existingUser = User::where('email', (string) $profile['email'])->first();
        if ($existingUser) {
            $this->attachGoogleIdentity($existingUser, (object) $profile);
            Auth::login($existingUser, true);
            $request->session()->forget(self::SESSION_KEY);

            return redirect()->intended(route('user.home'));
        }

        $user = new User();

        $this->setIfColumnExists($user, 'firstname', $validated['firstname']);
        $this->setIfColumnExists($user, 'lastname', $validated['lastname']);
        $this->setIfColumnExists($user, 'username', $validated['username']);
        $this->setIfColumnExists($user, 'mobile', $validated['mobile']);

        $user->email = (string) $profile['email'];

        if (Schema::hasColumn('users', 'name')) {
            $user->name = trim($validated['firstname'] . ' ' . $validated['lastname']);
        }

        $user->password = bcrypt(Str::random(32));

        // Mark as verified when coming from Google.
        $this->setIfColumnExists($user, 'ev', 1);
        $this->setIfColumnExists($user, 'sv', 1);
        $this->setIfColumnExists($user, 'status', 1);
        $this->setIfColumnExists($user, 'tv', 1);
        $this->setIfColumnExists($user, 'ts', 0);

        $this->setIfColumnExists($user, 'email_verified_at', now());
        $this->setIfColumnExists($user, 'onboarding_completed_at', now());

        $this->setIfColumnExists($user, 'oauth_provider', 'google');
        $this->setIfColumnExists($user, 'oauth_provider_id', (string) ($profile['provider_id'] ?? ''));
        $this->setIfColumnExists($user, 'oauth_avatar', (string) ($profile['avatar'] ?? ''));

        $user->save();

        Auth::login($user, true);
        $request->session()->forget(self::SESSION_KEY);

        return redirect()->intended(route('user.home'));
    }

    private function attachGoogleIdentity(User $user, object $googleUser): void
    {
        $providerId = method_exists($googleUser, 'getId')
            ? (string) ($googleUser->getId() ?? '')
            : (string) ($googleUser->provider_id ?? '');

        $avatar = method_exists($googleUser, 'getAvatar')
            ? (string) ($googleUser->getAvatar() ?? '')
            : (string) ($googleUser->avatar ?? '');

        $this->setIfColumnExists($user, 'oauth_provider', 'google');
        $this->setIfColumnExists($user, 'oauth_provider_id', $providerId);
        $this->setIfColumnExists($user, 'oauth_avatar', $avatar);

        // If the account already existed, consider onboarding complete.
        if (Schema::hasColumn('users', 'onboarding_completed_at') && empty($user->onboarding_completed_at)) {
            $user->onboarding_completed_at = now();
        }

        if (Schema::hasColumn('users', 'email_verified_at') && empty($user->email_verified_at)) {
            $user->email_verified_at = now();
        }

        $this->setIfColumnExists($user, 'ev', 1);

        $user->save();
    }

    private function getProfileFromSession(Request $request): ?array
    {
        $profile = $request->session()->get(self::SESSION_KEY);

        return is_array($profile) ? $profile : null;
    }

    private function splitName(string $name): array
    {
        $name = trim(preg_replace('/\s+/', ' ', $name) ?? '');
        if ($name === '') {
            return ['', ''];
        }

        $parts = explode(' ', $name, 2);

        return [
            $parts[0] ?? '',
            $parts[1] ?? '',
        ];
    }

    private function suggestUsername(array $profile): string
    {
        $base = (string) ($profile['nickname'] ?? '');

        if (trim($base) === '') {
            $email = (string) ($profile['email'] ?? '');
            $base = strstr($email, '@', true) ?: $email;
        }

        $base = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $base) ?? '');
        $base = substr($base, 0, 20) ?: 'user';

        return $base . random_int(10, 99);
    }

    private function setIfColumnExists(User $user, string $column, mixed $value): void
    {
        try {
            if (Schema::hasColumn($user->getTable(), $column)) {
                $user->{$column} = $value;
            }
        } catch (\Throwable) {
            // Ignore when schema inspection isn't available (e.g. during install).
        }
    }
}

