@extends('admin.layouts.master')

@section('content')
    <section class="space-y-6">
        <div class="flex flex-col gap-3 rounded-[2rem] border border-slate-200 bg-white p-5 shadow-[0_18px_60px_rgba(15,23,42,0.06)] dark:border-white/10 dark:bg-zinc-900/70 sm:p-6">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.28em] text-sky-600 dark:text-sky-400">Admin panel</p>
                    <h2 class="mt-1 text-2xl font-semibold tracking-tight text-slate-950 dark:text-white">{{ __($pageTitle ?? 'Dashboard') }}</h2>
                </div>

                <nav class="flex flex-wrap items-center gap-2 text-sm text-slate-500 dark:text-zinc-400" aria-label="Breadcrumb">
                    <a href="{{ route('admin.dashboard') }}" class="transition hover:text-slate-950 dark:hover:text-white">Dashboard</a>
                    <span>/</span>
                    <span class="font-medium text-slate-900 dark:text-white">{{ __($pageTitle ?? 'Dashboard') }}</span>
                </nav>
            </div>
        </div>

        <div class="glass-panel p-4 sm:p-6">
            @yield('panel')
        </div>
    </section>
@endsection
