<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Http\Request;
use HenryEjemuta\LaravelMonnify\Events\NewWebHookCallReceived;
use HenryEjemuta\LaravelMonnify\Exceptions\MonnifyFailedRequestException;
use HenryEjemuta\LaravelMonnify\Facades\Monnify;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Deposit;
use App\Models\Transaction;
use Illuminate\Support\Facades\Storage;
use HenryEjemuta\LaravelMonnify\Http\Controllers\MonnifyController;



class ProcessMonnifyWebhook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $webhookData;

    /**
     * Create a new job instance.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->webhookData = $request->all();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Validate the webhook data here if needed

        $payload = json_decode($this->webhookData['stringifiedData'], true);

        if ($payload['eventData']['paymentStatus'] == 'PAID') {
            $payloadHash = $payload['transactionHash'];

            $computedHash = Monnify::Transactions()->calculateHash(
                $payload['eventData']['paymentReference'],
                $payload['eventData']['amountPaid'],
                $payload['eventData']['paidOn'],
                $payload['eventData']['transactionReference']
            );

            if ($payloadHash === $computedHash) {
                try {
                    $transactionObject = Monnify::Transactions()->getTransactionStatus($payload['eventData']['transactionReference']);

                    if (($payload['eventData']['paymentStatus'] == $transactionObject->paymentStatus) &&
                        ($payload['eventData']['amountPaid'] == $transactionObject->amountPaid)
                    ) {
                        if ($payload['eventData']['product']['type'] == 'RESERVED_ACCOUNT') {
                            $uniqueTxnID = $payload['eventData']['transactionReference'];
                            $txn = Transaction::where('trx', $uniqueTxnID)->first();

                            if (!isset($txn->trx)) {
                                $amountReceived = $transactionObject->amountPaid;
                                $amountToCreditUser = $amountReceived - 50;

                                $user = User::where('email', $payload['eventData']['customer']['email'])->first();
                                $user->username = $payload['eventData']['product']['reference'];

                                $transaction = new Transaction();
                                $transaction->user_id = $user->id;
                                $transaction->trx = $uniqueTxnID;
                                $transaction->amount = $amountToCreditUser;
                                $transaction->type = 'credit';
                                $transaction->save();

                                $user->balance += $amountToCreditUser;
                                $user->save();

                                $dataToSave = [
                                    'user_from_payload' => [
                                        'email' => $payload['eventData']['customer']['email'],
                                        'username' => $payload['eventData']['product']['reference'],
                                    ],
                                    'amount_sent' => $amountReceived,
                                    'charges' => 50,
                                    'amount_to_be_credited' => $amountToCreditUser,
                                ];

                                $jsonData = json_encode($dataToSave, JSON_PRETTY_PRINT);

                                $filePath = storage_path('app/working.json');

                                Storage::disk('local')->put('working.json', $jsonData);

                                return 'Data saved to working.json in storage path.';
                            }
                        } else {
                            // Handle other payment types if needed
                        }
                    }
                } catch (MonnifyFailedRequestException $exception) {
                    Log::channel('monnify')->error($exception->getMessage() . "\n\r" . $payload['eventData']['transactionReference']);
                }
            }
        }
    }
}
