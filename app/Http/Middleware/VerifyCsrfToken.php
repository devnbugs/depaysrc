<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;



class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
    'user/deposit',
    'ipn*',
    'ussd/callback',
    'laravel-monnify/webhook/transaction-completion',
    'laravel-monnify/webhook/refund-completion',
    'laravel-monnify/webhook/disbursement',
    'laravel-monnify/webhook/settlement',
    'laravel-monnify/webhook',
    'monnify/webhook',
    'status/*',
	'paystack/webhook',
    'check'
	];

}
