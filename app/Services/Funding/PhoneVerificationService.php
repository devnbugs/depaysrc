<?php

namespace App\Services\Funding;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class PhoneVerificationService
{
    public function __construct(protected FundingSettings $settings)
    {
    }

    public function requestOtp(User $user): array
    {
        $settings = $this->settings->identity();

        if (! $settings['phone_verification_enabled']) {
            throw new RuntimeException('WhatsApp phone verification is disabled.');
        }

        if (blank($settings['phone_verification_send_url'])) {
            throw new RuntimeException('WhatsApp phone verification send URL is missing.');
        }

        $reference = 'WOTP-'.date('ymdHis').getTrx(6);
        $response = $this->client($settings)
            ->post($settings['phone_verification_send_url'], array_filter([
                'phone' => $user->mobile,
                'reference' => $reference,
                'channel' => 'whatsapp',
                'sender_id' => $settings['phone_verification_sender_id'],
                'template' => $settings['phone_verification_template'],
            ]));

        $body = $response->json() ?? [];

        if (! $response->successful()) {
            throw new RuntimeException((string) data_get($body, 'message', 'Unable to request WhatsApp OTP.'));
        }

        $user->forceFill([
            'phone_verification_channel' => 'whatsapp',
            'phone_verification_reference' => $reference,
            'phone_verification_requested_at' => now(),
        ])->save();

        return $body;
    }

    public function verifyOtp(User $user, string $otp): array
    {
        $settings = $this->settings->identity();

        if (! $settings['phone_verification_enabled']) {
            throw new RuntimeException('WhatsApp phone verification is disabled.');
        }

        if (blank($settings['phone_verification_verify_url'])) {
            throw new RuntimeException('WhatsApp phone verification verify URL is missing.');
        }

        $response = $this->client($settings)
            ->post($settings['phone_verification_verify_url'], [
                'phone' => $user->mobile,
                'reference' => $user->phone_verification_reference,
                'otp' => $otp,
                'channel' => 'whatsapp',
            ]);

        $body = $response->json() ?? [];

        if (! $response->successful()) {
            throw new RuntimeException((string) data_get($body, 'message', 'Unable to verify WhatsApp OTP.'));
        }

        $approved = (bool) data_get($body, 'status', false) || (bool) data_get($body, 'success', false);

        if (! $approved) {
            throw new RuntimeException((string) data_get($body, 'message', 'OTP verification failed.'));
        }

        $user->forceFill([
            'phone_verified_externally_at' => now(),
        ])->save();

        return $body;
    }

    protected function client(array $settings)
    {
        $client = Http::acceptJson()
            ->asJson()
            ->timeout(25)
            ->retry(1, 250);

        if (app()->environment('local')) {
            $client = $client->withoutVerifying();
        }

        if (filled($settings['phone_verification_auth_url']) && filled($settings['phone_verification_client_id']) && filled($settings['phone_verification_client_secret'])) {
            $tokenResponse = $client->asForm()->post($settings['phone_verification_auth_url'], [
                'grant_type' => 'client_credentials',
                'client_id' => $settings['phone_verification_client_id'],
                'client_secret' => $settings['phone_verification_client_secret'],
            ]);

            $token = (string) data_get($tokenResponse->json() ?? [], 'access_token', '');
            if (filled($token)) {
                $client = $client->withToken($token);
            }
        }

        return $client;
    }
}
