@include('errors._shell', [
    'code' => '404',
    'title' => 'Page not found',
    'message' => 'The page you requested does not exist anymore, may have moved, or the link may be incomplete.',
    'primaryLabel' => 'Go Home',
    'primaryUrl' => url('/'),
    'secondaryLabel' => 'Go Back',
    'secondaryUrl' => url()->previous() ?: url('/'),
])
