<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Transfer Receipt</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Arial', sans-serif;
            color: #333;
            font-size: 14px;
            line-height: 1.6;
        }
        .receipt-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            border-bottom: 3px solid #10b981;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .logo-section {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 20px;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #10b981;
        }
        .receipt-title {
            text-align: right;
        }
        .receipt-title h1 {
            font-size: 20px;
            margin-bottom: 5px;
        }
        .receipt-number {
            text-align: right;
            color: #6b7280;
            font-size: 13px;
        }
        .transfer-amount {
            background: linear-gradient(135deg, #ecfdf5 0%, #f0fdf4 100%);
            border: 2px solid #86efac;
            padding: 30px;
            margin: 20px 0;
            text-align: center;
            border-radius: 8px;
        }
        .amount-label {
            color: #059669;
            font-size: 12px;
            text-transform: uppercase;
            font-weight: bold;
            letter-spacing: 1px;
            margin-bottom: 10px;
        }
        .amount-value {
            font-size: 40px;
            font-weight: bold;
            color: #065f46;
            font-family: 'Courier New', monospace;
        }
        .section {
            margin-bottom: 30px;
        }
        .section-title {
            font-weight: bold;
            color: #1f2937;
            padding-bottom: 10px;
            border-bottom: 2px solid #10b981;
            margin-bottom: 15px;
            font-size: 14px;
        }
        .row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .row:last-child {
            border-bottom: none;
        }
        .label {
            color: #6b7280;
            font-weight: 500;
        }
        .value {
            text-align: right;
            font-weight: 600;
            color: #1f2937;
        }
        .footer {
            border-top: 2px solid #e5e7eb;
            padding-top: 20px;
            margin-top: 30px;
            text-align: center;
            color: #6b7280;
            font-size: 12px;
        }
        .timestamp {
            text-align: center;
            color: #9ca3af;
            font-size: 11px;
            margin-top: 15px;
        }
        .badge {
            display: inline-block;
            background: #d1fae5;
            color: #065f46;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            margin: 15px 0;
        }
        .info-box {
            background: #f3f4f6;
            padding: 15px;
            margin: 15px 0;
            border-left: 4px solid #3b82f6;
            border-radius: 4px;
            font-size: 12px;
        }
        table {
            width: 100%;
        }
        table td {
            padding: 8px 0;
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <!-- Header -->
        <div class="header">
            <div class="logo-section">
                <div class="company-name">{{ config('app.name') }}</div>
                <div class="receipt-title">
                    <h1>{{ $type === 'sender' ? '✓ Transfer Receipt' : '💰 Transfer Notification' }}</h1>
                    <div class="receipt-number">{{ $reference }}</div>
                </div>
            </div>
        </div>

        <!-- Status Badge -->
        <div style="text-align: center;">
            <span class="badge">✓ COMPLETED SUCCESSFULLY</span>
        </div>

        <!-- Amount Section -->
        <div class="transfer-amount">
            <div class="amount-label">{{ $type === 'sender' ? 'Amount Sent' : 'Amount Received' }}</div>
            <div class="amount-value">{{ $general->cur_sym }}{{ number_format($amount, 2) }}</div>
            @if($type === 'sender' && $charge > 0)
                <div style="margin-top: 15px; color: #6b7280; font-size: 12px;">
                    Fee: {{ $general->cur_sym }}{{ number_format($charge, 2) }} | Total Debit: {{ $general->cur_sym }}{{ number_format($amount + $charge, 2) }}
                </div>
            @endif
        </div>

        <!-- Transfer Details -->
        <div class="section">
            <div class="section-title">TRANSFER DETAILS</div>
            <div class="row">
                <span class="label">Transaction ID</span>
                <span class="value">{{ $reference }}</span>
            </div>
            <div class="row">
                <span class="label">{{ $type === 'sender' ? 'Sent To' : 'Received From' }}</span>
                <span class="value">{{ $type === 'sender' ? ($receiver?->name ?? $transfer->details) : $sender->name }}</span>
            </div>
            <div class="row">
                <span class="label">Date & Time</span>
                <span class="value">{{ $date->format('M d, Y • h:i A') }}</span>
            </div>
            <div class="row">
                <span class="label">Status</span>
                <span class="value" style="color: #10b981;">COMPLETED</span>
            </div>
        </div>

        <!-- Account Details -->
        <div class="section">
            <div class="section-title">ACCOUNT INFORMATION</div>
            <div class="row">
                <span class="label">{{ $type === 'sender' ? 'Your Account' : 'Your Account' }}</span>
                <span class="value">{{ $type === 'sender' ? $sender->username : $receiver?->username }}</span>
            </div>
            <div class="row">
                <span class="label">Account Name</span>
                <span class="value">{{ $type === 'sender' ? $sender->name : $receiver?->name }}</span>
            </div>
            <div class="row">
                <span class="label">Email</span>
                <span class="value">{{ $type === 'sender' ? $sender->email : $receiver?->email }}</span>
            </div>
        </div>

        <!-- Information Box -->
        <div class="info-box">
            <strong>📌 Important:</strong> This receipt is a confirmation of your transfer transaction. Please keep it for your records. If you have any questions, contact our support team.
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>This receipt was automatically generated by {{ config('app.name') }}</p>
            <p>For support, visit {{ config('app.url') }}</p>
            <div class="timestamp">
                Generated on {{ now()->format('M d, Y • h:i A') }}
            </div>
        </div>
    </div>
</body>
</html>
