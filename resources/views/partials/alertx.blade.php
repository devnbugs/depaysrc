<script>
    window.__depayFlashQueue = window.__depayFlashQueue || [];

    @if (session()->has('alertx'))
        @foreach (session('alertx') as $msg)
            window.__depayFlashQueue.push({
                kind: 'alertx',
                type: @json($msg[0] ?? 'info'),
                message: @json(__($msg[1] ?? '')),
                receiptUrl: @json(session('receipt_url', isset($bill) && isset($bill->id) ? route('user.beta.receipt', ['billId' => $bill->id]) : '')),
            });
        @endforeach
    @endif
</script>
