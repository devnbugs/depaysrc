@include('errors._shell', [
    'code' => '429',
    'title' => 'Too many requests',
    'message' => 'Too many requests were sent in a short time. Please wait a moment, then try again.',
    'primaryLabel' => 'Try Home',
    'primaryUrl' => url('/'),
    'secondaryLabel' => 'Go Back',
    'secondaryUrl' => url()->previous() ?: url('/'),
])
