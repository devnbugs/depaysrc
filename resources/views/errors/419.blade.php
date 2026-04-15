@include('errors._shell', [
    'code' => '419',
    'title' => 'Session expired',
    'message' => 'Your session token expired or the last request could not be confirmed. Refresh the page and try the action again.',
    'primaryLabel' => 'Go Home',
    'primaryUrl' => url('/'),
    'secondaryLabel' => 'Go Back',
    'secondaryUrl' => url()->previous() ?: url('/'),
])
