@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-12">
        <div class="max-w-md mx-auto text-center">
            <!-- Error Icon -->
            <div class="mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-red-100 dark:bg-red-900/20">
                    <svg class="w-8 h-8 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4v2m0 4v2M9 3h6m0 0a3 3 0 013 3v12a3 3 0 01-3 3H9a3 3 0 01-3-3V6a3 3 0 013-3z" />
                    </svg>
                </div>
            </div>

            <!-- Error Message -->
            <h1 class="text-3xl font-bold text-slate-900 dark:text-white mb-4">Access Blocked</h1>
            
            <p class="text-slate-600 dark:text-zinc-400 mb-6">
                Your access has been temporarily blocked due to suspicious activity.
            </p>

            @if($reason)
                <div class="mb-6 p-4 bg-amber-50 border border-amber-200 rounded-lg dark:bg-amber-900/20 dark:border-amber-900/30">
                    <p class="text-sm text-amber-900 dark:text-amber-200">
                        <strong>Reason:</strong> {{ $reason }}
                    </p>
                </div>
            @endif

            <p class="text-slate-500 dark:text-zinc-400 text-sm mb-8">
                This is a security measure to protect your account. Please try again later or contact support if you believe this is an error.
            </p>

            <!-- Action Buttons -->
            <div class="space-y-3">
                <a href="{{ route('home') }}" class="block w-full px-4 py-3 bg-slate-950 text-white rounded-lg hover:bg-slate-800 dark:bg-white dark:text-slate-950 dark:hover:bg-slate-200 font-medium transition">
                    Go to Home
                </a>
                <a href="{{ route('contact') }}" class="block w-full px-4 py-3 border border-slate-200 text-slate-900 rounded-lg hover:bg-slate-50 dark:border-white/10 dark:text-white dark:hover:bg-white/5 font-medium transition">
                    Contact Support
                </a>
            </div>

            <!-- Information -->
            <div class="mt-12 pt-8 border-t border-slate-200 dark:border-white/10">
                <p class="text-xs text-slate-500 dark:text-zinc-500 mb-3">
                    <strong>Reason Code:</strong> BLOCKED_IP
                </p>
                <p class="text-xs text-slate-500 dark:text-zinc-500">
                    If you need immediate assistance, please contact our support team with reference ID {{ request()->session()->get('request_id') ?? 'N/A' }}
                </p>
            </div>
        </div>
    </div>
@endsection

<style>
    body {
        background: #f8fafc;
    }
    
    body.dark {
        background: #0f172a;
    }
</style>
