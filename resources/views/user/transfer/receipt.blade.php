@extends('layouts.master')
@section('content')
<div class="container-fluid pt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>Transfer Receipt</h4>
                </div>
                <div class="card-body">
                    <div class="receipt-container">
                        <div class="receipt-header text-center mb-4">
                            <h2>{{ config('app.name') }}</h2>
                            <p class="text-muted">Transfer Receipt</p>
                            <hr>
                        </div>

                        <div class="receipt-details">
                            <div class="row mb-3">
                                <div class="col-6">
                                    <label class="fw-bold">Transaction ID:</label>
                                    <p>{{ $transfer->trx ?? 'N/A' }}</p>
                                </div>
                                <div class="col-6 text-end">
                                    <label class="fw-bold">Date:</label>
                                    <p>{{ $transfer->created_at->format('d M Y, H:i A') }}</p>
                                </div>
                            </div>

                            <hr>

                            <div class="row mb-3">
                                <div class="col-6">
                                    <label class="fw-bold">From:</label>
                                    <p>{{ $transfer->senderUser->firstname ?? 'N/A' }} {{ $transfer->senderUser->lastname ?? '' }}</p>
                                    <small class="text-muted">{{ $transfer->senderUser->email ?? '' }}</small>
                                </div>
                                <div class="col-6">
                                    <label class="fw-bold">To:</label>
                                    <p>{{ $transfer->receiverUser->firstname ?? 'N/A' }} {{ $transfer->receiverUser->lastname ?? '' }}</p>
                                    <small class="text-muted">{{ $transfer->receiverUser->email ?? '' }}</small>
                                </div>
                            </div>

                            <hr>

                            <div class="row mb-3">
                                <div class="col-6">
                                    <label class="fw-bold">Amount:</label>
                                    <p class="h5">{{ $transfer->amount ?? 0 }} {{ $transfer->currency ?? 'NGN' }}</p>
                                </div>
                                <div class="col-6">
                                    <label class="fw-bold">Status:</label>
                                    <p>
                                        @if ($transfer->status == 'completed')
                                            <span class="badge bg-success">Completed</span>
                                        @elseif ($transfer->status == 'pending')
                                            <span class="badge bg-warning">Pending</span>
                                        @elseif ($transfer->status == 'failed')
                                            <span class="badge bg-danger">Failed</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $transfer->status }}</span>
                                        @endif
                                    </p>
                                </div>
                            </div>

                            @if ($transfer->description)
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <label class="fw-bold">Description:</label>
                                        <p>{{ $transfer->description }}</p>
                                    </div>
                                </div>
                            @endif

                            <hr>

                            <div class="row mb-3">
                                <div class="col-6">
                                    <label class="fw-bold">Transaction Charge:</label>
                                    <p>{{ $transfer->charge ?? 0 }} {{ $transfer->currency ?? 'NGN' }}</p>
                                </div>
                                <div class="col-6">
                                    <label class="fw-bold">Total Debited:</label>
                                    <p>{{ ($transfer->amount ?? 0) + ($transfer->charge ?? 0) }} {{ $transfer->currency ?? 'NGN' }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="receipt-footer text-center mt-5 pt-4 border-top">
                            <p class="text-muted small mb-3">
                                Thank you for using our service. Please keep this receipt for your records.
                            </p>
                            <button onclick="window.print()" class="btn btn-primary btn-sm me-2">
                                <i class="fas fa-print"></i> Print Receipt
                            </button>
                            <a href="javascript:history.back()" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> Back
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        .btn, .btn-group {
            display: none !important;
        }
        
        .card {
            border: none !important;
            box-shadow: none !important;
        }
        
        body {
            background: white;
        }
    }
    
    .receipt-container {
        padding: 20px;
    }
    
    .receipt-header h2 {
        margin-bottom: 0;
    }
    
    .receipt-details label {
        color: #666;
        margin-bottom: 5px;
    }
    
    .receipt-details p {
        margin: 0;
        font-weight: 500;
    }
</style>
@endsection
