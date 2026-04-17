<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
    @include('partials.seo')
    <title>{{ $general->sitename(__($pageTitle ?? 'Dashboard')) }}</title>
    <script>
        (function () {
            try {
                const storedTheme = localStorage.getItem('depay-theme');
                const darkMode = storedTheme ? storedTheme === 'dark' : window.matchMedia('(prefers-color-scheme: dark)').matches;
                document.documentElement.classList.toggle('dark', darkMode);
            } catch (error) {
                document.documentElement.classList.toggle('dark', false);
            }
        })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('style-lib')
    @stack('style')
</head>
<body class="min-h-full bg-white text-slate-900 antialiased dark:bg-zinc-950 dark:text-zinc-100">
    @php
        $currentUser = auth()->user();
        $navItems = [
            ['label' => 'Dashboard', 'route' => 'user.home', 'icon' => 'home'],
            ['label' => 'Deposit', 'route' => 'user.deposit', 'icon' => 'wallet'],
            ['label' => 'Cards', 'route' => 'user.vcard', 'icon' => 'card'],
            ['label' => 'Transfer', 'route' => 'user.othertransfer', 'icon' => 'send'],
            ['label' => 'Data', 'route' => 'user.internet', 'icon' => 'wifi'],
            ['label' => 'Airtime', 'route' => 'user.airtime', 'icon' => 'phone'],
            ['label' => 'Cable TV', 'route' => 'user.cabletv', 'icon' => 'tv'],
            ['label' => 'Utilities', 'route' => 'user.utility', 'icon' => 'bolt'],
            ['label' => 'KYC Services', 'route' => 'user.kyc.services', 'icon' => 'shield'],
            ['label' => 'Networks', 'route' => 'user.beta.realtime', 'icon' => 'spark'],
            ['label' => 'Security', 'route' => 'user.security', 'icon' => 'shield'],
            ['label' => 'Settings', 'route' => 'user.profile.setting', 'icon' => 'settings'],
            ['label' => 'Support', 'route' => 'user.support', 'icon' => 'chat'],
        ];
    @endphp

    <div class="app-shell flex min-h-screen">
        <aside class="hidden w-80 flex-col border-r border-slate-200/80 bg-white/90 p-6 backdrop-blur dark:border-white/10 dark:bg-zinc-950/80 xl:flex xl:sticky xl:top-0 xl:h-screen xl:overflow-y-auto">
            <a href="{{ route('user.home') }}" class="flex items-center gap-3">
                <img src="{{ getImage(imagePath()['logoIcon']['path'] . '/logo.png') }}" alt="{{ $general->sitename }}" class="h-11 w-11 rounded-2xl border border-slate-200 object-contain shadow-sm dark:border-white/10">
                <div class="leading-tight">
                    <p class="text-sm font-semibold uppercase tracking-[0.28em] text-sky-600 dark:text-sky-400">{{ $general->sitename }}</p>
                    <p class="text-sm text-slate-500 dark:text-zinc-400">{{ __($pageTitle ?? 'Dashboard') }}</p>
                </div>
            </a>

            <div class="mt-6 rounded-3xl border border-slate-200 bg-slate-50 p-5 dark:border-white/10 dark:bg-white/5">
                <p class="text-sm font-medium text-slate-500 dark:text-zinc-400">Wallet balance</p>
                <div class="mt-3 flex items-end justify-between gap-3">
                    <div>
                        <p class="text-3xl font-semibold tracking-tight text-slate-950 dark:text-white">{{ $general->cur_sym }}{{ showAmount($currentUser?->balance ?? 0) }}</p>
                        <p class="mt-2 text-sm text-slate-500 dark:text-zinc-400">Signed in as {{ $currentUser?->username ?? 'Account' }}</p>
                    </div>
                    <a href="{{ route('user.deposit') }}" class="shell-chip">+</a>
                </div>
            </div>

            <nav class="mt-8 flex-1 space-y-1">
                @foreach ($navItems as $item)
                    <a href="{{ route($item['route']) }}" class="shell-link {{ request()->routeIs($item['route']) ? 'shell-link-active' : '' }}">
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-slate-100 text-slate-600 dark:bg-white/5 dark:text-zinc-300">
                            @switch($item['icon'])
                                @case('home')
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 11l9-8 9 8"></path><path d="M5 10v10h14V10"></path></svg>
                                    @break
                                @case('wallet')
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="6" width="20" height="12" rx="2"></rect><path d="M16 12h4"></path></svg>
                                    @break
                                @case('card')
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"></rect><path d="M2 10h20"></path></svg>
                                    @break
                                @case('send')
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m22 2-7 20-4-9-9-4Z"></path><path d="M22 2 11 13"></path></svg>
                                    @break
                                @case('shield')
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                                    @break
                                @case('settings')
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.7 1.7 0 0 0 .33 1.86l.05.05a2 2 0 0 1-1.41 3.41l-.05-.05A1.7 1.7 0 0 0 17 20.6a1.7 1.7 0 0 0-1 .3 1.7 1.7 0 0 0-.7 1.18V22a2 2 0 0 1-4 0v-.05a1.7 1.7 0 0 0-.7-1.18 1.7 1.7 0 0 0-1-.3 1.7 1.7 0 0 0-1.35.58l-.05.05A2 2 0 0 1 4.2 17l.05-.05A1.7 1.7 0 0 0 4.6 15a1.7 1.7 0 0 0-.3-1 1.7 1.7 0 0 0-1.18-.7H3a2 2 0 0 1 0-4h.12a1.7 1.7 0 0 0 1.18-.7 1.7 1.7 0 0 0 .3-1 1.7 1.7 0 0 0-.58-1.35l-.05-.05A2 2 0 0 1 6.4 4.6l.05.05A1.7 1.7 0 0 0 8 4.3a1.7 1.7 0 0 0 1-.3 1.7 1.7 0 0 0 .7-1.18V3a2 2 0 0 1 4 0v.12a1.7 1.7 0 0 0 .7 1.18 1.7 1.7 0 0 0 1 .3 1.7 1.7 0 0 0 1.35-.58l.05-.05A2 2 0 0 1 19.8 7l-.05.05A1.7 1.7 0 0 0 19.4 9a1.7 1.7 0 0 0 .3 1 1.7 1.7 0 0 0 1.18.7H21a2 2 0 0 1 0 4h-.12a1.7 1.7 0 0 0-1.18.7 1.7 1.7 0 0 0-.3 1z"></path></svg>
                                    @break
                                @case('wifi')
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12.55a11 11 0 0 1 14 0"></path><path d="M1.5 8.5a16 16 0 0 1 21 0"></path><path d="M8.5 16.5a6 6 0 0 1 7 0"></path><circle cx="12" cy="20" r="1"></circle></svg>
                                    @break
                                @case('phone')
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.8 19.8 0 0 1-8.63-3.07 19.4 19.4 0 0 1-6-6 19.8 19.8 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.07.6.21 1.19.41 1.76a2 2 0 0 1-.45 2.11L8 9.6a16 16 0 0 0 6.4 6.4l2-2a2 2 0 0 1 2.11-.45c.57.2 1.16.34 1.76.41A2 2 0 0 1 22 16.92z"></path></svg>
                                    @break
                                @case('tv')
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="12" rx="2"></rect><path d="M8 21h8"></path><path d="M12 3l-4 4h8z"></path></svg>
                                    @break
                                @case('bolt')
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M13 2 3 14h7l-1 8 10-12h-7z"></path></svg>
                                    @break
                                @case('spark')
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2l1.9 5.9H20l-4.9 3.6L17 17l-5-3.5L7 17l1.9-5.5L4 7.9h6.1z"></path></svg>
                                    @break
                                @default
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16M4 12h16M4 18h16"></path></svg>
                            @endswitch
                        </span>
                        <span>{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </nav>

            <div class="mt-6 rounded-3xl border border-slate-200 p-5 text-sm text-slate-600 dark:border-white/10 dark:text-zinc-400">
                <p class="font-semibold text-slate-900 dark:text-white">Need help?</p>
                <p class="mt-2 leading-6">Use the support link whenever you need a payment review, wallet fix, or a billing check.</p>
            </div>
        </aside>

        <div class="flex min-w-0 flex-1 flex-col">
            <header class="sticky top-0 z-40 border-b border-slate-200/70 bg-white/90 backdrop-blur dark:border-white/10 dark:bg-zinc-950/85">
                <div class="border-b border-slate-200/70 dark:border-white/10">
                    @include($activeTemplate.'partials.network-signal-bar')
                </div>

                <div class="mx-auto flex max-w-7xl flex-col gap-4 px-4 py-4 sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-sky-600 dark:text-sky-400">User console</p>
                            <h1 class="mt-1 truncate text-2xl font-semibold tracking-tight text-slate-950 dark:text-white">{{ __($pageTitle ?? 'Dashboard') }}</h1>
                        </div>

                        <div class="flex items-center gap-2">
                            <button type="button" data-theme-toggle class="inline-flex h-11 items-center gap-2 rounded-full border border-slate-200 bg-white px-4 text-sm font-medium text-slate-700 transition hover:border-sky-200 hover:text-sky-700 dark:border-white/10 dark:bg-zinc-900 dark:text-zinc-200 dark:hover:border-sky-500/30 dark:hover:text-sky-300">
                                <span class="inline-flex items-center justify-center">
                                    <svg data-theme-icon="light" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="4"></circle>
                                        <path d="M12 2v2"></path>
                                        <path d="M12 20v2"></path>
                                        <path d="M4.93 4.93l1.41 1.41"></path>
                                        <path d="M17.66 17.66l1.41 1.41"></path>
                                        <path d="M2 12h2"></path>
                                        <path d="M20 12h2"></path>
                                        <path d="M4.93 19.07l1.41-1.41"></path>
                                        <path d="M17.66 6.34l1.41-1.41"></path>
                                    </svg>
                                    <svg data-theme-icon="dark" xmlns="http://www.w3.org/2000/svg" class="hidden h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                                    </svg>
                                </span>
                                <span class="hidden sm:inline">Theme</span>
                            </button>

                            <button type="button" data-mobile-menu-toggle="#user-mobile-menu" class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-700 transition hover:border-sky-200 hover:text-sky-700 dark:border-white/10 dark:bg-zinc-900 dark:text-zinc-200 dark:hover:border-sky-500/30 dark:hover:text-sky-300 xl:hidden" aria-controls="user-mobile-menu" aria-expanded="false" aria-label="Open navigation menu">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M4 6h16"></path>
                                    <path d="M4 12h16"></path>
                                    <path d="M4 18h16"></path>
                                </svg>
                            </button>

                            <a href="{{ route('user.profile.setting') }}" class="hidden h-11 items-center rounded-full border border-slate-200 bg-white px-4 text-sm font-medium text-slate-700 transition hover:border-sky-200 hover:text-sky-700 dark:border-white/10 dark:bg-zinc-900 dark:text-zinc-200 dark:hover:border-sky-500/30 dark:hover:text-sky-300 sm:inline-flex">
                                Profile
                            </a>

                            <a href="{{ route('user.logout') }}" class="hidden h-11 items-center rounded-full bg-slate-950 px-4 text-sm font-medium text-white transition hover:bg-slate-800 dark:bg-white dark:text-slate-950 dark:hover:bg-slate-200 sm:inline-flex">
                                Logout
                            </a>
                        </div>
                    </div>
                </div>
            </header>

            <main class="flex-1 px-4 py-6 sm:px-6 lg:px-8">
                <div class="mx-auto w-full max-w-7xl">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <div id="user-mobile-menu" class="app-mobile-menu-overlay" data-mobile-menu-panel data-open="0" aria-hidden="true">
        <button type="button" class="app-mobile-menu-backdrop" data-mobile-menu-close aria-label="Close navigation menu"></button>
        <aside class="app-mobile-menu-panel">
            <div class="flex items-center justify-between gap-3">
                <a href="{{ route('user.home') }}" class="flex items-center gap-3">
                    <img src="{{ getImage(imagePath()['logoIcon']['path'] . '/logo.png') }}" alt="{{ $general->sitename }}" class="h-10 w-10 rounded-2xl border border-slate-200 object-contain dark:border-white/10">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.25em] text-sky-600 dark:text-sky-400">{{ $general->sitename }}</p>
                        <p class="text-sm text-slate-500 dark:text-zinc-400">{{ __($pageTitle ?? 'Dashboard') }}</p>
                    </div>
                </a>

                <button type="button" data-mobile-menu-close class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-700 dark:border-white/10 dark:bg-zinc-900 dark:text-zinc-200">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 6 6 18"></path>
                        <path d="M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-slate-50 p-4 dark:border-white/10 dark:bg-white/5">
                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-500 dark:text-zinc-400">Wallet balance</p>
                <p class="mt-2 text-2xl font-semibold tracking-tight text-slate-950 dark:text-white">{{ $general->cur_sym }}{{ showAmount($currentUser?->balance ?? 0) }}</p>
                <p class="mt-1 text-sm text-slate-500 dark:text-zinc-400">{{ $currentUser?->username ?? 'Account' }}</p>
            </div>

            <nav class="space-y-1">
                @foreach ($navItems as $item)
                    <a href="{{ route($item['route']) }}" class="shell-link {{ request()->routeIs($item['route']) ? 'shell-link-active' : '' }}">
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-slate-100 text-slate-600 dark:bg-white/5 dark:text-zinc-300">
                            @switch($item['icon'])
                                @case('home')
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 11l9-8 9 8"></path><path d="M5 10v10h14V10"></path></svg>
                                    @break
                                @case('wallet')
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="6" width="20" height="12" rx="2"></rect><path d="M16 12h4"></path></svg>
                                    @break
                                @case('card')
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"></rect><path d="M2 10h20"></path></svg>
                                    @break
                                @case('send')
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m22 2-7 20-4-9-9-4Z"></path><path d="M22 2 11 13"></path></svg>
                                    @break
                                @case('shield')
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                                    @break
                                @case('settings')
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.7 1.7 0 0 0 .33 1.86l.05.05a2 2 0 0 1-1.41 3.41l-.05-.05A1.7 1.7 0 0 0 17 20.6a1.7 1.7 0 0 0-1 .3 1.7 1.7 0 0 0-.7 1.18V22a2 2 0 0 1-4 0v-.05a1.7 1.7 0 0 0-.7-1.18 1.7 1.7 0 0 0-1-.3 1.7 1.7 0 0 0-1.35.58l-.05.05A2 2 0 0 1 4.2 17l.05-.05A1.7 1.7 0 0 0 4.6 15a1.7 1.7 0 0 0-.3-1 1.7 1.7 0 0 0-1.18-.7H3a2 2 0 0 1 0-4h.12a1.7 1.7 0 0 0 1.18-.7 1.7 1.7 0 0 0 .3-1 1.7 1.7 0 0 0-.58-1.35l-.05-.05A2 2 0 0 1 6.4 4.6l.05.05A1.7 1.7 0 0 0 8 4.3a1.7 1.7 0 0 0 1-.3 1.7 1.7 0 0 0 .7-1.18V3a2 2 0 0 1 4 0v.12a1.7 1.7 0 0 0 .7 1.18 1.7 1.7 0 0 0 1 .3 1.7 1.7 0 0 0 1.35-.58l.05-.05A2 2 0 0 1 19.8 7l-.05.05A1.7 1.7 0 0 0 19.4 9a1.7 1.7 0 0 0 .3 1 1.7 1.7 0 0 0 1.18.7H21a2 2 0 0 1 0 4h-.12a1.7 1.7 0 0 0-1.18.7 1.7 1.7 0 0 0-.3 1z"></path></svg>
                                    @break
                                @case('wifi')
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12.55a11 11 0 0 1 14 0"></path><path d="M1.5 8.5a16 16 0 0 1 21 0"></path><path d="M8.5 16.5a6 6 0 0 1 7 0"></path><circle cx="12" cy="20" r="1"></circle></svg>
                                    @break
                                @case('phone')
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.8 19.8 0 0 1-8.63-3.07 19.4 19.4 0 0 1-6-6 19.8 19.8 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.07.6.21 1.19.41 1.76a2 2 0 0 1-.45 2.11L8 9.6a16 16 0 0 0 6.4 6.4l2-2a2 2 0 0 1 2.11-.45c.57.2 1.16.34 1.76.41A2 2 0 0 1 22 16.92z"></path></svg>
                                    @break
                                @case('tv')
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="12" rx="2"></rect><path d="M8 21h8"></path><path d="M12 3l-4 4h8z"></path></svg>
                                    @break
                                @case('bolt')
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M13 2 3 14h7l-1 8 10-12h-7z"></path></svg>
                                    @break
                                @case('spark')
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2l1.9 5.9H20l-4.9 3.6L17 17l-5-3.5L7 17l1.9-5.5L4 7.9h6.1z"></path></svg>
                                    @break
                                @default
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16M4 12h16M4 18h16"></path></svg>
                            @endswitch
                        </span>
                        <span>{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </nav>

            <div class="mt-auto grid gap-2 pt-2">
                <a href="{{ route('user.profile.setting') }}" class="shell-link">
                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-slate-100 text-slate-600 dark:bg-white/5 dark:text-zinc-300">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8z"></path><path d="M4 22v-2a8 8 0 0 1 16 0v2"></path></svg>
                    </span>
                    <span>Profile</span>
                </a>
                <a href="{{ route('user.logout') }}" class="shell-link">
                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-slate-100 text-slate-600 dark:bg-white/5 dark:text-zinc-300">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 17l5-5-5-5"></path><path d="M15 12H3"></path><path d="M21 4v16"></path></svg>
                    </span>
                    <span>Logout</span>
                </a>
            </div>
        </aside>
    </div>

    @livewire('invisible-turnstile')

    @livewireScripts
    @stack('script-lib')
    @stack('script')

    @include('partials.notify')
    @include('partials.alertx')
    @include('partials.btnlog')
    @include('partials.btnvox')
    @include('partials.plugins')
</body>
</html>
