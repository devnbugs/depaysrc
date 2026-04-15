@include('errors._shell', [
    'code' => '503',
    'title' => 'Service temporarily unavailable',
    'message' => 'The platform is temporarily unavailable while maintenance or recovery is in progress. Please check back shortly.',
    'primaryLabel' => 'Go Home',
    'primaryUrl' => url('/'),
    'secondaryLabel' => 'Refresh Page',
    'secondaryUrl' => url()->current(),
])
