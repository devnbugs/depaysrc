<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
    @include('partials.seo')
    <title>{{ $general->sitename(__($pageTitle ?? 'Admin')) }}</title>
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
    @stack('style-lib')
    @stack('style')
</head>
<body class="min-h-full bg-white text-slate-900 antialiased dark:bg-zinc-950 dark:text-zinc-100">
    @php
        $adminUser = auth('admin')->user();
        $menuItems = [
            ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'icon' => 'home'],
            ['label' => 'Users', 'route' => 'admin.users.all', 'icon' => 'users'],
            ['label' => 'Deposits', 'route' => 'admin.deposit.list', 'icon' => 'wallet'],
            ['label' => 'Withdrawals', 'route' => 'admin.withdraw.log', 'icon' => 'cash'],
            ['label' => 'Local Transfer', 'route' => 'admin.local-transfer.settings', 'icon' => 'send'],
            ['label' => 'Bill Payments', 'route' => 'admin.bills.settings', 'icon' => 'wallet'],
            ['label' => 'KYC Services', 'route' => 'admin.kyc.services.index', 'icon' => 'shield'],
            ['label' => 'Gateway', 'route' => 'admin.gateway.automatic.index', 'icon' => 'lock'],
            ['label' => 'Reports', 'route' => 'admin.report.transaction', 'icon' => 'chart'],
            ['label' => 'Settings', 'route' => 'admin.setting.index', 'icon' => 'settings'],
            ['label' => 'Support', 'route' => 'admin.users.open.ticket', 'icon' => 'chat'],
            ['label' => 'Security', 'route' => 'admin.setting.index', 'icon' => 'shield'],
        ];
    @endphp

    <div class="app-shell flex min-h-screen">
        <aside class="hidden w-80 flex-col border-r border-slate-200/80 bg-white/90 p-6 backdrop-blur dark:border-white/10 dark:bg-zinc-950/80 xl:flex xl:sticky xl:top-0 xl:h-screen xl:overflow-y-auto">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
                <img src="{{ getImage(imagePath()['logoIcon']['path'] . '/logo.png') }}" alt="{{ $general->sitename }}" class="h-11 w-11 rounded-2xl border border-slate-200 object-contain shadow-sm dark:border-white/10">
                <div class="leading-tight">
                    <p class="text-sm font-semibold uppercase tracking-[0.28em] text-sky-600 dark:text-sky-400">{{ $general->sitename }}</p>
                    <p class="text-sm text-slate-500 dark:text-zinc-400">{{ __($pageTitle ?? 'Admin') }}</p>
                </div>
            </a>

            <div class="mt-6 rounded-3xl border border-slate-200 bg-slate-50 p-5 dark:border-white/10 dark:bg-white/5">
                <p class="text-sm font-medium text-slate-500 dark:text-zinc-400">Administrator</p>
                <div class="mt-3">
                    <p class="text-xl font-semibold text-slate-950 dark:text-white">{{ $adminUser->name ?? $adminUser->username ?? 'Admin' }}</p>
                    <p class="mt-2 text-sm text-slate-500 dark:text-zinc-400">{{ $adminUser->email ?? '' }}</p>
                </div>
            </div>

            <nav class="mt-8 flex-1 space-y-1">
                @foreach ($menuItems as $item)
                    <a href="{{ route($item['route']) }}" class="shell-link {{ request()->routeIs($item['route']) ? 'shell-link-active' : '' }}">
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-slate-100 text-slate-600 dark:bg-white/5 dark:text-zinc-300">
                            @switch($item['icon'])
                                @case('home')
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 11l9-8 9 8"></path><path d="M5 10v10h14V10"></path></svg>
                                    @break
                                @case('users')
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                                    @break
                                @case('wallet')
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="6" width="20" height="12" rx="2"></rect><path d="M16 12h4"></path></svg>
                                    @break
                                @case('cash')
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 1v22"></path><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7H14a3 3 0 0 1 0 6H6"></path></svg>
                                    @break
                                @case('send')
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m22 2-7 20-4-9-9-4Z"></path><path d="M22 2 11 13"></path></svg>
                                    @break
                                @case('shield')
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                                    @break
                                @case('lock')
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                                    @break
                                @case('chart')
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19V5"></path><path d="M8 19v-8"></path><path d="M12 19v-5"></path><path d="M16 19v-11"></path><path d="M20 19V9"></path></svg>
                                    @break
                                @case('settings')
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.7 1.7 0 0 0 .33 1.86l.05.05a2 2 0 0 1-1.41 3.41l-.05-.05A1.7 1.7 0 0 0 17 20.6a1.7 1.7 0 0 0-1 .3 1.7 1.7 0 0 0-.7 1.18V22a2 2 0 0 1-4 0v-.05a1.7 1.7 0 0 0-.7-1.18 1.7 1.7 0 0 0-1-.3 1.7 1.7 0 0 0-1.35.58l-.05.05A2 2 0 0 1 4.2 17l.05-.05A1.7 1.7 0 0 0 4.6 15a1.7 1.7 0 0 0-.3-1 1.7 1.7 0 0 0-1.18-.7H3a2 2 0 0 1 0-4h.12a1.7 1.7 0 0 0 1.18-.7 1.7 1.7 0 0 0 .3-1 1.7 1.7 0 0 0-.58-1.35l-.05-.05A2 2 0 0 1 6.4 4.6l.05.05A1.7 1.7 0 0 0 8 4.3a1.7 1.7 0 0 0 1-.3 1.7 1.7 0 0 0 .7-1.18V3a2 2 0 0 1 4 0v.12a1.7 1.7 0 0 0 .7 1.18 1.7 1.7 0 0 0 1 .3 1.7 1.7 0 0 0 1.35-.58l.05-.05A2 2 0 0 1 19.8 7l-.05.05A1.7 1.7 0 0 0 19.4 9a1.7 1.7 0 0 0 .3 1 1.7 1.7 0 0 0 1.18.7H21a2 2 0 0 1 0 4h-.12a1.7 1.7 0 0 0-1.18.7 1.7 1.7 0 0 0-.3 1z"></path></svg>
                                    @break
                                @case('chat')
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4z"></path></svg>
                                    @break
                            @endswitch
                        </span>
                        <span>{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </nav>

            <div class="mt-6 rounded-3xl border border-slate-200 p-5 text-sm text-slate-600 dark:border-white/10 dark:text-zinc-400">
                <p class="font-semibold text-slate-900 dark:text-white">Secure ops</p>
                <p class="mt-2 leading-6">All chargeable flows stay auditable, with the dashboard now using calmer card surfaces and smaller icons.</p>
            </div>
        </aside>

        <div class="flex min-w-0 flex-1 flex-col">
            <header class="sticky top-0 z-40 border-b border-slate-200/70 bg-white/90 backdrop-blur dark:border-white/10 dark:bg-zinc-950/85">
                <div class="mx-auto flex max-w-7xl flex-col gap-4 px-4 py-4 sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-sky-600 dark:text-sky-400">Administration</p>
                            <h1 class="mt-1 truncate text-2xl font-semibold tracking-tight text-slate-950 dark:text-white">{{ __($pageTitle ?? 'Admin') }}</h1>
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

                            <button type="button" data-mobile-menu-toggle="#admin-mobile-menu" class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-700 transition hover:border-sky-200 hover:text-sky-700 dark:border-white/10 dark:bg-zinc-900 dark:text-zinc-200 dark:hover:border-sky-500/30 dark:hover:text-sky-300 xl:hidden" aria-controls="admin-mobile-menu" aria-expanded="false" aria-label="Open navigation menu">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M4 6h16"></path>
                                    <path d="M4 12h16"></path>
                                    <path d="M4 18h16"></path>
                                </svg>
                            </button>

                            <a href="{{ route('admin.profile') }}" class="hidden h-11 items-center rounded-full border border-slate-200 bg-white px-4 text-sm font-medium text-slate-700 transition hover:border-sky-200 hover:text-sky-700 dark:border-white/10 dark:bg-zinc-900 dark:text-zinc-200 dark:hover:border-sky-500/30 dark:hover:text-sky-300 sm:inline-flex">
                                Profile
                            </a>

                            <a href="{{ route('admin.logout') }}" class="hidden h-11 items-center rounded-full bg-slate-950 px-4 text-sm font-medium text-white transition hover:bg-slate-800 dark:bg-white dark:text-slate-950 dark:hover:bg-slate-200 sm:inline-flex">
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

    <div id="admin-mobile-menu" class="app-mobile-menu-overlay" data-mobile-menu-panel data-open="0" aria-hidden="true">
        <button type="button" class="app-mobile-menu-backdrop" data-mobile-menu-close aria-label="Close navigation menu"></button>
        <aside class="app-mobile-menu-panel">
            <div class="flex items-center justify-between gap-3">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
                    <img src="{{ getImage(imagePath()['logoIcon']['path'] . '/logo.png') }}" alt="{{ $general->sitename }}" class="h-10 w-10 rounded-2xl border border-slate-200 object-contain dark:border-white/10">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.25em] text-sky-600 dark:text-sky-400">{{ $general->sitename }}</p>
                        <p class="text-sm text-slate-500 dark:text-zinc-400">{{ __($pageTitle ?? 'Admin') }}</p>
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
                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-500 dark:text-zinc-400">Administrator</p>
                <p class="mt-2 text-2xl font-semibold tracking-tight text-slate-950 dark:text-white">{{ $adminUser->name ?? $adminUser->username ?? 'Admin' }}</p>
                <p class="mt-1 text-sm text-slate-500 dark:text-zinc-400">{{ $adminUser->email ?? '' }}</p>
            </div>

            <nav class="space-y-1">
                @foreach ($menuItems as $item)
                    <a href="{{ route($item['route']) }}" class="shell-link {{ request()->routeIs($item['route']) ? 'shell-link-active' : '' }}">
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-slate-100 text-slate-600 dark:bg-white/5 dark:text-zinc-300">
                            @switch($item['icon'])
                                @case('home')
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 11l9-8 9 8"></path><path d="M5 10v10h14V10"></path></svg>
                                    @break
                                @case('users')
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                                    @break
                                @case('wallet')
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="6" width="20" height="12" rx="2"></rect><path d="M16 12h4"></path></svg>
                                    @break
                                @case('cash')
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 1v22"></path><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7H14a3 3 0 0 1 0 6H6"></path></svg>
                                    @break
                                @case('send')
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m22 2-7 20-4-9-9-4Z"></path><path d="M22 2 11 13"></path></svg>
                                    @break
                                @case('shield')
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                                    @break
                                @case('lock')
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                                    @break
                                @case('chart')
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19V5"></path><path d="M8 19v-8"></path><path d="M12 19v-5"></path><path d="M16 19v-11"></path><path d="M20 19V9"></path></svg>
                                    @break
                                @case('settings')
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.7 1.7 0 0 0 .33 1.86l.05.05a2 2 0 0 1-1.41 3.41l-.05-.05A1.7 1.7 0 0 0 17 20.6a1.7 1.7 0 0 0-1 .3 1.7 1.7 0 0 0-.7 1.18V22a2 2 0 0 1-4 0v-.05a1.7 1.7 0 0 0-.7-1.18 1.7 1.7 0 0 0-1-.3 1.7 1.7 0 0 0-1.35.58l-.05.05A2 2 0 0 1 4.2 17l.05-.05A1.7 1.7 0 0 0 4.6 15a1.7 1.7 0 0 0-.3-1 1.7 1.7 0 0 0-1.18-.7H3a2 2 0 0 1 0-4h.12a1.7 1.7 0 0 0 1.18-.7 1.7 1.7 0 0 0 .3-1 1.7 1.7 0 0 0-.58-1.35l-.05-.05A2 2 0 0 1 6.4 4.6l.05.05A1.7 1.7 0 0 0 8 4.3a1.7 1.7 0 0 0 1-.3 1.7 1.7 0 0 0 .7-1.18V3a2 2 0 0 1 4 0v.12a1.7 1.7 0 0 0 .7 1.18 1.7 1.7 0 0 0 1 .3 1.7 1.7 0 0 0 1.35-.58l.05-.05A2 2 0 0 1 19.8 7l-.05.05A1.7 1.7 0 0 0 19.4 9a1.7 1.7 0 0 0 .3 1 1.7 1.7 0 0 0 1.18.7H21a2 2 0 0 1 0 4h-.12a1.7 1.7 0 0 0-1.18.7 1.7 1.7 0 0 0-.3 1z"></path></svg>
                                    @break
                                @case('chat')
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4z"></path></svg>
                                    @break
                            @endswitch
                        </span>
                        <span>{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </nav>

            <div class="mt-auto grid gap-2 pt-2">
                <a href="{{ route('admin.profile') }}" class="shell-link">
                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-slate-100 text-slate-600 dark:bg-white/5 dark:text-zinc-300">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8z"></path><path d="M4 22v-2a8 8 0 0 1 16 0v2"></path></svg>
                    </span>
                    <span>Profile</span>
                </a>
                <a href="{{ route('admin.logout') }}" class="shell-link">
                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-slate-100 text-slate-600 dark:bg-white/5 dark:text-zinc-300">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 17l5-5-5-5"></path><path d="M15 12H3"></path><path d="M21 4v16"></path></svg>
                    </span>
                    <span>Logout</span>
                </a>
            </div>
        </aside>
    </div>

    @stack('script-lib')
    @stack('script')

    @include('partials.notify')
    @include('partials.alertx')
    @include('partials.plugins')
</body>
</html>
