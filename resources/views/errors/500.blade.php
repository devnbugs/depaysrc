@include('errors._shell', [
    'code' => '500',
    'title' => 'Internal server error',
    'message' => 'The request could not be completed right now. Please return home or try again in a few minutes.',
    'primaryLabel' => 'Go Home',
    'primaryUrl' => url('/'),
    'secondaryLabel' => 'Try Again',
    'secondaryUrl' => url()->current(),
])
