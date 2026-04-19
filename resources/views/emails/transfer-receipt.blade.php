<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
        }
        .header {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 40px 20px;
            text-align: center;
        }
        .header h1 {
            font-size: 28px;
            margin-bottom: 8px;
        }
        .header p {
            font-size: 14px;
            opacity: 0.9;
        }
        .content {
            padding: 40px 20px;
        }
        .greeting {
            font-size: 16px;
            margin-bottom: 24px;
        }
        .receipt-section {
            background: #f9fafb;
            border-left: 4px solid #10b981;
            padding: 20px;
            margin: 24px 0;
            border-radius: 4px;
        }
        .receipt-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .receipt-row:last-child {
            border-bottom: none;
        }
        .receipt-row-label {
            color: #6b7280;
            font-size: 14px;
            font-weight: 500;
        }
        .receipt-row-value {
            color: #1f2937;
            font-size: 14px;
            font-weight: 600;
        }
        .amount-section {
            background: linear-gradient(135deg, #ecfdf5 0%, #f0fdf4 100%);
            border: 1px solid #86efac;
            padding: 24px;
            margin: 24px 0;
            border-radius: 8px;
            text-align: center;
        }
        .amount-label {
            color: #059669;
            font-size: 13px;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }
        .amount-value {
            color: #065f46;
            font-size: 36px;
            font-weight: 700;
            font-family: 'Monaco', 'Courier New', monospace;
        }
        .details-box {
            background: #f3f4f6;
            padding: 16px;
            margin: 16px 0;
            border-radius: 6px;
            font-size: 14px;
        }
        .details-box-title {
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 8px;
        }
        .details-box-value {
            color: #6b7280;
            word-break: break-all;
        }
        .status-badge {
            display: inline-block;
            background: #d1fae5;
            color: #065f46;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            margin: 16px 0;
        }
        .footer {
            background: #f9fafb;
            padding: 24px 20px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
            font-size: 12px;
            color: #6b7280;
        }
        .download-button {
            display: inline-block;
            background: #10b981;
            color: white;
            padding: 12px 24px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            margin: 16px 0;
            transition: background 0.2s;
        }
        .download-button:hover {
            background: #059669;
        }
        .divider {
            height: 1px;
            background: #e5e7eb;
            margin: 24px 0;
        }
        .info-text {
            background: #efe5ff;
            border-left: 4px solid #a855f7;
            padding: 16px;
            margin: 16px 0;
            border-radius: 4px;
            font-size: 13px;
            color: #6b21a8;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>{{ $type === 'sender' ? '✓ Transfer Sent' : '💰 Funds Received' }}</h1>
            <p>{{ $type === 'sender' ? 'Your transfer has been successfully completed' : 'You have received a transfer' }}</p>
        </div>

        <!-- Main Content -->
        <div class="content">
            <!-- Greeting -->
            <div class="greeting">
                <strong>Hello {{ $type === 'sender' ? $sender->name : $receiver->name ?? 'User' }},</strong>
            </div>

            <!-- Status Badge -->
            <div style="text-align: center;">
                <span class="status-badge">✓ Completed Successfully</span>
            </div>

            <!-- Amount Section -->
            <div class="amount-section">
                <div class="amount-label">
                    {{ $type === 'sender' ? 'Amount Sent' : 'Amount Received' }}
                </div>
                <div class="amount-value">
                    {{ $general->cur_sym }}{{ number_format($amount, 2) }}
                </div>
                @if($type === 'sender' && $charge > 0)
                    <div style="color: #6b7280; font-size: 13px; margin-top: 12px;">
                        Fee: {{ $general->cur_sym }}{{ number_format($charge, 2) }} 
                        | Total Debit: {{ $general->cur_sym }}{{ number_format($amount + $charge, 2) }}
                    </div>
                @endif
            </div>

            <!-- Transfer Details -->
            <div class="receipt-section">
                <div class="receipt-row">
                    <span class="receipt-row-label">Reference ID</span>
                    <span class="receipt-row-value">{{ $reference }}</span>
                </div>
                <div class="receipt-row">
                    <span class="receipt-row-label">{{ $type === 'sender' ? 'Recipient' : 'Sender' }}</span>
                    <span class="receipt-row-value">
                        {{ $type === 'sender' ? ($receiver?->name ?? 'User Account') : $sender->name }}
                    </span>
                </div>
                <div class="receipt-row">
                    <span class="receipt-row-label">Date & Time</span>
                    <span class="receipt-row-value">{{ $date->format('M d, Y • h:i A') }}</span>
                </div>
                <div class="receipt-row">
                    <span class="receipt-row-label">Status</span>
                    <span class="receipt-row-value" style="color: #10b981;">Completed</span>
                </div>
            </div>

            <!-- Download Receipt Button -->
            <div style="text-align: center;">
                <a href="{{ route('user.transfer.receipt.download', $transfer->id) }}" class="download-button">
                    📥 Download Receipt (PDF)
                </a>
            </div>

            <!-- Additional Information -->
            <div class="info-text">
                <strong>💡 Keep this receipt safe:</strong> This email serves as your transfer receipt. You can also download a PDF version using the button above for your records.
            </div>

            <!-- Divider -->
            <div class="divider"></div>

            <!-- Support Message -->
            <div style="font-size: 13px; color: #6b7280;">
                <p style="margin-bottom: 12px;">
                    If you have any questions or notice any issues with this transfer, please don't hesitate to contact our support team.
                </p>
                <p>
                    <strong>Transaction Reference:</strong> {{ $reference }}
                </p>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            <p style="margin-top: 8px;">
                <a href="{{ route('home') }}" style="color: #3b82f6; text-decoration: none;">Visit Dashboard</a> | 
                <a href="{{ route('user.transfer-history') ?? '#' }}" style="color: #3b82f6; text-decoration: none;">View History</a>
            </p>
        </div>
    </div>
</body>
</html>
