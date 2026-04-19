<?php

namespace App\Services\Transfers;

use App\Models\Transfer;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class TransferReceiptService
{
    public static function generatePdf(Transfer $transfer, string $type = 'sender'): string
    {
        $sender = $transfer->user;
        $receiver = static::getReceiver($transfer);
        $general = gs();
        
        $html = view('receipts.transfer-receipt-pdf', [
            'transfer' => $transfer,
            'type' => $type,
            'sender' => $sender,
            'receiver' => $receiver,
            'amount' => $transfer->amount,
            'charge' => $transfer->charge,
            'total' => $transfer->amount + $transfer->charge,
            'date' => $transfer->created_at,
            'reference' => $transfer->trx,
            'general' => $general,
        ])->render();
        
        return Pdf::loadHTML($html)
            ->setPaper('a4')
            ->setOption('margin-top', 0)
            ->setOption('margin-right', 0)
            ->setOption('margin-bottom', 0)
            ->setOption('margin-left', 0)
            ->output();
    }

    public static function streamPdf(Transfer $transfer, string $type = 'sender')
    {
        $filename = "transfer-receipt-{$transfer->trx}.pdf";
        
        return response()->streamDownload(
            fn () => echo static::generatePdf($transfer, $type),
            $filename,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename=' . $filename,
            ]
        );
    }

    protected static function getReceiver(Transfer $transfer)
    {
        if ($transfer->method_id == 1) {
            // User-to-user transfer
            return User::where('username', $transfer->details)
                ->orWhere('id', $transfer->details)
                ->first();
        }
        
        return null;
    }
}
