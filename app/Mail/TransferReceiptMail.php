<?php

namespace App\Mail;

use App\Models\Transfer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TransferReceiptMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Transfer $transfer,
        public string $type = 'sender' // 'sender' or 'receiver'
    ) {}

    public function envelope(): Envelope
    {
        $subject = $this->type === 'sender' 
            ? 'Transfer Receipt - Funds Sent'
            : 'Transfer Notification - Funds Received';

        return new Envelope(
            from: new Address(config('mail.from.address'), config('mail.from.name')),
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.transfer-receipt',
            with: [
                'transfer' => $this->transfer,
                'type' => $this->type,
                'sender' => $this->transfer->user,
                'receiver' => $this->getReceiver(),
                'amount' => $this->transfer->amount,
                'charge' => $this->transfer->charge,
                'total' => $this->transfer->amount + $this->transfer->charge,
                'date' => $this->transfer->created_at,
                'reference' => $this->transfer->trx,
                'general' => gs(),
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }

    protected function getReceiver()
    {
        if ($this->transfer->method_id == 1) {
            // User-to-user transfer
            return \App\Models\User::where('username', $this->transfer->details)
                ->orWhere('id', $this->transfer->details)
                ->first();
        }
        
        return null;
    }
}
