@include('errors._shell', [
    'code' => 'Soon',
    'title' => 'This page is coming soon',
    'message' => 'The feature exists in the roadmap, but this screen is not open for production use yet. Please return to the dashboard or homepage for now.',
    'primaryLabel' => 'Go Home',
    'primaryUrl' => url('/'),
    'secondaryLabel' => 'Go Back',
    'secondaryUrl' => url()->previous() ?: url('/'),
])
