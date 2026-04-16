<?php

namespace Tests\Unit;

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Http\Request;
use ReflectionMethod;
use Tests\TestCase;

class LoginControllerTest extends TestCase
{
    public function test_password_login_still_requires_legacy_two_factor_when_enabled(): void
    {
        $controller = new LoginController();
        $request = Request::create('/login', 'POST');

        $requiresChallenge = $this->invokeProtectedMethod(
            $controller,
            'shouldRequireLegacyTwoFactorChallenge',
            [$request, (object) ['ts' => 1]]
        );

        $this->assertTrue($requiresChallenge);
    }

    public function test_passkey_login_skips_legacy_two_factor_challenge_when_enabled(): void
    {
        $controller = new LoginController();
        $request = Request::create('/passkeys/authenticate', 'POST');
        $request->attributes->set('authenticated_via_passkey', true);

        $requiresChallenge = $this->invokeProtectedMethod(
            $controller,
            'shouldRequireLegacyTwoFactorChallenge',
            [$request, (object) ['ts' => 1]]
        );

        $this->assertFalse($requiresChallenge);
    }

    protected function invokeProtectedMethod(object $instance, string $method, array $arguments = []): mixed
    {
        $reflection = new ReflectionMethod($instance, $method);
        $reflection->setAccessible(true);

        return $reflection->invokeArgs($instance, $arguments);
    }
}
