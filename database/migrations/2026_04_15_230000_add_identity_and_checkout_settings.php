<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'paystackcode')) {
                $table->string('paystackcode')->nullable()->after('psid');
            }

            if (! Schema::hasColumn('users', 'pslinked')) {
                $table->boolean('pslinked')->default(false)->after('paystackcode');
            }

            if (! Schema::hasColumn('users', 'psverified')) {
                $table->unsignedTinyInteger('psverified')->default(0)->after('pslinked');
            }

            if (! Schema::hasColumn('users', 'aNid1')) {
                $table->string('aNid1')->nullable()->after('aNo1');
            }

            if (! Schema::hasColumn('users', 'aNid2')) {
                $table->string('aNid2')->nullable()->after('aNo2');
            }

            if (! Schema::hasColumn('users', 'budpay_customer_code')) {
                $table->string('budpay_customer_code')->nullable()->after('aNid2');
            }

            if (! Schema::hasColumn('users', 'budpay_customer_id')) {
                $table->string('budpay_customer_id')->nullable()->after('budpay_customer_code');
            }

            if (! Schema::hasColumn('users', 'budpay_virtual_account_id')) {
                $table->string('budpay_virtual_account_id')->nullable()->after('budpay_customer_id');
            }

            if (! Schema::hasColumn('users', 'budpay_virtual_account_reference')) {
                $table->string('budpay_virtual_account_reference')->nullable()->after('budpay_virtual_account_id');
            }

            if (! Schema::hasColumn('users', 'budpay_linked')) {
                $table->boolean('budpay_linked')->default(false)->after('budpay_virtual_account_reference');
            }

            if (! Schema::hasColumn('users', 'budpay_verified')) {
                $table->unsignedTinyInteger('budpay_verified')->default(0)->after('budpay_linked');
            }

            if (! Schema::hasColumn('users', 'squad_customer_reference')) {
                $table->string('squad_customer_reference')->nullable()->after('budpay_verified');
            }

            if (! Schema::hasColumn('users', 'squad_customer_status')) {
                $table->string('squad_customer_status')->nullable()->after('squad_customer_reference');
            }

            if (! Schema::hasColumn('users', 'identity_source')) {
                $table->string('identity_source')->nullable()->after('squad_customer_status');
            }

            if (! Schema::hasColumn('users', 'identity_locked_at')) {
                $table->dateTime('identity_locked_at')->nullable()->after('identity_source');
            }

            if (! Schema::hasColumn('users', 'identity_verified_at')) {
                $table->dateTime('identity_verified_at')->nullable()->after('identity_locked_at');
            }

            if (! Schema::hasColumn('users', 'identity_payload')) {
                $table->longText('identity_payload')->nullable()->after('identity_verified_at');
            }

            if (! Schema::hasColumn('users', 'identity_date_of_birth')) {
                $table->date('identity_date_of_birth')->nullable()->after('identity_payload');
            }

            if (! Schema::hasColumn('users', 'identity_gender')) {
                $table->string('identity_gender')->nullable()->after('identity_date_of_birth');
            }

            if (! Schema::hasColumn('users', 'identity_middle_name')) {
                $table->string('identity_middle_name')->nullable()->after('identity_gender');
            }

            if (! Schema::hasColumn('users', 'phone_verification_channel')) {
                $table->string('phone_verification_channel')->nullable()->after('identity_middle_name');
            }

            if (! Schema::hasColumn('users', 'phone_verification_reference')) {
                $table->string('phone_verification_reference')->nullable()->after('phone_verification_channel');
            }

            if (! Schema::hasColumn('users', 'phone_verification_requested_at')) {
                $table->dateTime('phone_verification_requested_at')->nullable()->after('phone_verification_reference');
            }

            if (! Schema::hasColumn('users', 'phone_verified_externally_at')) {
                $table->dateTime('phone_verified_externally_at')->nullable()->after('phone_verification_requested_at');
            }
        });

        Schema::table('general_settings', function (Blueprint $table) {
            if (! Schema::hasColumn('general_settings', 'identity_verification_settings')) {
                $table->longText('identity_verification_settings')->nullable()->after('kyc_subscription_settings');
            }

            if (! Schema::hasColumn('general_settings', 'deposit_checkout_settings')) {
                $table->longText('deposit_checkout_settings')->nullable()->after('identity_verification_settings');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            foreach ([
                'paystackcode',
                'pslinked',
                'psverified',
                'aNid1',
                'aNid2',
                'budpay_customer_code',
                'budpay_customer_id',
                'budpay_virtual_account_id',
                'budpay_virtual_account_reference',
                'budpay_linked',
                'budpay_verified',
                'squad_customer_reference',
                'squad_customer_status',
                'identity_source',
                'identity_locked_at',
                'identity_verified_at',
                'identity_payload',
                'identity_date_of_birth',
                'identity_gender',
                'identity_middle_name',
                'phone_verification_channel',
                'phone_verification_reference',
                'phone_verification_requested_at',
                'phone_verified_externally_at',
            ] as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('general_settings', function (Blueprint $table) {
            foreach ([
                'identity_verification_settings',
                'deposit_checkout_settings',
            ] as $column) {
                if (Schema::hasColumn('general_settings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
