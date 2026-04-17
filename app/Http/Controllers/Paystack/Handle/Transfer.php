<?php

namespace App\Http\Controllers\Paystack\Handle;

use App\Http\Controllers\Controller;

/**
 * Paystack Transfer Webhook Handler
 * 
 * This controller handles Paystack transfer callbacks.
 * Bank transfers are managed through LocalTransferController.
 */
class Transfer extends Controller
{
    /**
     * Handle Paystack transfer webhook callback
     */
    public function handle()
    {
        // Transfer handling is managed through LocalTransferController
        // This is a placeholder for potential Paystack transfer webhooks
        return response()->json([
            'status' => 'ok',
            'message' => 'Transfer webhook received',
        ]);
    }
}
