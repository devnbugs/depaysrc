@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-12">
        <div class="max-w-md mx-auto text-center">
            <!-- Error Icon -->
            <div class="mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-amber-100 dark:bg-amber-900/20">
                    <svg class="w-8 h-8 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>

            <!-- Error Message -->
            <h1 class="text-3xl font-bold text-slate-900 dark:text-white mb-4">Too Many Requests</h1>
            
            <p class="text-slate-600 dark:text-zinc-400 mb-6">
                You've made too many requests in a short period of time. Please slow down and try again in a moment.
            </p>

            <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg dark:bg-blue-900/20 dark:border-blue-900/30">
                <p class="text-sm text-blue-900 dark:text-blue-200">
                    This is a security measure to prevent abuse and protect our service.
                </p>
            </div>

            <p class="text-slate-500 dark:text-zinc-400 text-sm mb-8">
                Please wait a moment and refresh the page to continue. Your request limit will reset shortly.
            </p>

            <!-- Action Buttons -->
            <div class="space-y-3">
                <button onclick="location.reload()" class="block w-full px-4 py-3 bg-slate-950 text-white rounded-lg hover:bg-slate-800 dark:bg-white dark:text-slate-950 dark:hover:bg-slate-200 font-medium transition">
                    Retry Request
                </button>
                <a href="{{ route('home') }}" class="block w-full px-4 py-3 border border-slate-200 text-slate-900 rounded-lg hover:bg-slate-50 dark:border-white/10 dark:text-white dark:hover:bg-white/5 font-medium transition">
                    Go to Home
                </a>
            </div>

            <!-- Information -->
            <div class="mt-12 pt-8 border-t border-slate-200 dark:border-white/10">
                <p class="text-xs text-slate-500 dark:text-zinc-500 mb-3">
                    <strong>Error Code:</strong> 429 TOO_MANY_REQUESTS
                </p>
                <p class="text-xs text-slate-500 dark:text-zinc-500">
                    If you continue to experience issues, please contact our support team.
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
