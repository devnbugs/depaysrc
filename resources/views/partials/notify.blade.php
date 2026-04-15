<script>
    window.__depayFlashQueue = window.__depayFlashQueue || [];

    @if (session()->has('notify'))
        @foreach (session('notify') as $msg)
            window.__depayFlashQueue.push({
                kind: 'notify',
                tone: @json($msg[0] ?? 'info'),
                title: 'Notice',
                message: @json(__($msg[1] ?? '')),
            });
        @endforeach
    @endif

    @if ($errors->any())
        @php
            $errorMessages = collect($errors->all())->unique()->values();
        @endphp

        @foreach ($errorMessages as $error)
            window.__depayFlashQueue.push({
                kind: 'notify',
                tone: 'error',
                title: 'Attention',
                message: @json(__($error)),
            });
        @endforeach
    @endif
</script>
