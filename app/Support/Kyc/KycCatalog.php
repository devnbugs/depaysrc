<?php

namespace App\Support\Kyc;

class KycCatalog
{
    public const PLAN_LEVELS = [
        'kyc_basic' => 1,
        'kyc_premium' => 2,
        'kyc_enterprise' => 3,
    ];

    public static function levelFor(?string $planKey): int
    {
        return self::PLAN_LEVELS[$planKey] ?? 0;
    }

    public static function plans(): array
    {
        return [
            [
                'key' => 'kyc_basic',
                'name' => 'Basic',
                'description' => 'Essential KYC services for light usage.',
                'price' => 5000,
                'monthly_limit' => 100000,
                'sort_order' => 1,
                'badge' => null,
                'enabled' => true,
                'paystack_interval' => 'monthly',
                'paystack_currency' => 'NGN',
                'invoice_limit' => 0,
                'features' => [
                    '6 Korapay services',
                    'Monthly spending limit: ₦100,000',
                    'Monthly recurring billing',
                ],
            ],
            [
                'key' => 'kyc_premium',
                'name' => 'Premium',
                'description' => 'Full Korapay access plus a wider Interswitch bundle.',
                'price' => 15000,
                'monthly_limit' => 500000,
                'sort_order' => 2,
                'badge' => 'Popular',
                'enabled' => true,
                'paystack_interval' => 'monthly',
                'paystack_currency' => 'NGN',
                'invoice_limit' => 0,
                'features' => [
                    'All Korapay + 10 Interswitch services',
                    'Monthly spending limit: ₦500,000',
                    'Priority support',
                ],
            ],
            [
                'key' => 'kyc_enterprise',
                'name' => 'Enterprise',
                'description' => 'All KYC services with the highest monthly coverage.',
                'price' => 50000,
                'monthly_limit' => 999999999,
                'sort_order' => 3,
                'badge' => null,
                'enabled' => true,
                'paystack_interval' => 'monthly',
                'paystack_currency' => 'NGN',
                'invoice_limit' => 0,
                'features' => [
                    'All 29 KYC services',
                    'Unlimited monthly usage',
                    'Dedicated account manager',
                ],
            ],
        ];
    }

    public static function services(): array
    {
        return [
            ['service_id' => 'korapay_nin_phone', 'name' => 'NIN Phone', 'description' => 'Verify National ID with phone number', 'provider' => 'korapay', 'price' => 500, 'enabled' => true, 'minimum_plan' => 'kyc_basic'],
            ['service_id' => 'korapay_nin_details', 'name' => 'NIN Details', 'description' => 'Get full NIN holder information', 'provider' => 'korapay', 'price' => 750, 'enabled' => true, 'minimum_plan' => 'kyc_basic'],
            ['service_id' => 'korapay_bvn_details', 'name' => 'BVN Details', 'description' => 'Verify Bank Verification Number', 'provider' => 'korapay', 'price' => 600, 'enabled' => true, 'minimum_plan' => 'kyc_basic'],
            ['service_id' => 'korapay_phone_info', 'name' => 'Phone Number Info', 'description' => 'Get carrier and network info', 'provider' => 'korapay', 'price' => 350, 'enabled' => true, 'minimum_plan' => 'kyc_basic'],
            ['service_id' => 'korapay_vnin', 'name' => 'vNIN', 'description' => 'Virtual NIN verification', 'provider' => 'korapay', 'price' => 450, 'enabled' => true, 'minimum_plan' => 'kyc_basic'],
            ['service_id' => 'korapay_phone_search', 'name' => 'Advanced Phone Search', 'description' => 'In-depth phone number analysis', 'provider' => 'korapay', 'price' => 800, 'enabled' => true, 'minimum_plan' => 'kyc_basic'],
            ['service_id' => 'interswitch_transaction_search', 'name' => 'Transaction Search API', 'description' => 'Real-time transaction status', 'provider' => 'interswitch', 'price' => 200, 'enabled' => true, 'minimum_plan' => 'kyc_premium'],
            ['service_id' => 'interswitch_nin', 'name' => 'NIN API', 'description' => 'National ID verification', 'provider' => 'interswitch', 'price' => 550, 'enabled' => true, 'minimum_plan' => 'kyc_premium'],
            ['service_id' => 'interswitch_bvn_boolean', 'name' => 'BVN Boolean Match API', 'description' => 'Quick BVN validation', 'provider' => 'interswitch', 'price' => 400, 'enabled' => true, 'minimum_plan' => 'kyc_premium'],
            ['service_id' => 'interswitch_tin', 'name' => 'TIN API', 'description' => 'Tax ID verification', 'provider' => 'interswitch', 'price' => 450, 'enabled' => true, 'minimum_plan' => 'kyc_premium'],
            ['service_id' => 'interswitch_drivers_license', 'name' => 'Driver\'s License Verification', 'description' => 'Verify FRSC database', 'provider' => 'interswitch', 'price' => 600, 'enabled' => true, 'minimum_plan' => 'kyc_premium'],
            ['service_id' => 'interswitch_bank_account', 'name' => 'Bank Account Verification', 'description' => 'Validate bank details', 'provider' => 'interswitch', 'price' => 300, 'enabled' => true, 'minimum_plan' => 'kyc_premium'],
            ['service_id' => 'interswitch_aml_domestic', 'name' => 'Domestic AML Search', 'description' => 'Local watchlist screening', 'provider' => 'interswitch', 'price' => 1200, 'enabled' => true, 'minimum_plan' => 'kyc_premium'],
            ['service_id' => 'interswitch_address', 'name' => 'Address Verification API', 'description' => 'Validate physical addresses', 'provider' => 'interswitch', 'price' => 250, 'enabled' => true, 'minimum_plan' => 'kyc_premium'],
            ['service_id' => 'interswitch_bvn_full', 'name' => 'BVN Full Details API', 'description' => 'Complete BVN information', 'provider' => 'interswitch', 'price' => 650, 'enabled' => true, 'minimum_plan' => 'kyc_premium'],
            ['service_id' => 'interswitch_nin_full', 'name' => 'NIN Full Details API', 'description' => 'Complete NIN information', 'provider' => 'interswitch', 'price' => 700, 'enabled' => true, 'minimum_plan' => 'kyc_premium'],
            ['service_id' => 'interswitch_safetoken_otp', 'name' => 'Safetoken OTP API', 'description' => 'Secure OTP generation', 'provider' => 'interswitch', 'price' => 150, 'enabled' => true, 'minimum_plan' => 'kyc_enterprise'],
            ['service_id' => 'interswitch_aml_global', 'name' => 'Global AML Search', 'description' => 'International watchlist check', 'provider' => 'interswitch', 'price' => 1500, 'enabled' => true, 'minimum_plan' => 'kyc_enterprise'],
            ['service_id' => 'interswitch_facial', 'name' => 'Facial Comparison API', 'description' => 'Compare facial images', 'provider' => 'interswitch', 'price' => 1000, 'enabled' => true, 'minimum_plan' => 'kyc_enterprise'],
            ['service_id' => 'interswitch_safetoken_send', 'name' => 'Generate & Send Safetoken', 'description' => 'OTP via SMS & Email', 'provider' => 'interswitch', 'price' => 200, 'enabled' => true, 'minimum_plan' => 'kyc_enterprise'],
            ['service_id' => 'interswitch_cac', 'name' => 'CAC Lookup API', 'description' => 'Corporate information', 'provider' => 'interswitch', 'price' => 800, 'enabled' => true, 'minimum_plan' => 'kyc_enterprise'],
            ['service_id' => 'interswitch_bank_lookup', 'name' => 'Bank Accounts Lookup', 'description' => 'Find all linked accounts', 'provider' => 'interswitch', 'price' => 500, 'enabled' => true, 'minimum_plan' => 'kyc_enterprise'],
            ['service_id' => 'interswitch_credit_history', 'name' => 'Credit History API', 'description' => 'Get credit reports', 'provider' => 'interswitch', 'price' => 2000, 'enabled' => true, 'minimum_plan' => 'kyc_enterprise'],
            ['service_id' => 'interswitch_passport', 'name' => 'International Passport', 'description' => 'Passport verification', 'provider' => 'interswitch', 'price' => 900, 'enabled' => true, 'minimum_plan' => 'kyc_enterprise'],
            ['service_id' => 'interswitch_bvn_igree', 'name' => 'BVN iGree API', 'description' => 'NIBSS consent platform', 'provider' => 'interswitch', 'price' => 350, 'enabled' => true, 'minimum_plan' => 'kyc_enterprise'],
            ['service_id' => 'interswitch_drivers_boolean', 'name' => 'Driver\'s License Boolean', 'description' => 'Quick DL validation', 'provider' => 'interswitch', 'price' => 400, 'enabled' => true, 'minimum_plan' => 'kyc_enterprise'],
            ['service_id' => 'interswitch_bills', 'name' => 'Bills Payment API', 'description' => 'Utility & subscription payments', 'provider' => 'interswitch', 'price' => 100, 'enabled' => true, 'minimum_plan' => 'kyc_enterprise'],
            ['service_id' => 'interswitch_whatsapp_otp', 'name' => 'WhatsApp OTP API', 'description' => 'OTP via WhatsApp', 'provider' => 'interswitch', 'price' => 180, 'enabled' => true, 'minimum_plan' => 'kyc_enterprise'],
            ['service_id' => 'interswitch_adverse_media', 'name' => 'Adverse Media API', 'description' => 'Global risk screening', 'provider' => 'interswitch', 'price' => 1800, 'enabled' => true, 'minimum_plan' => 'kyc_enterprise'],
        ];
    }
}
