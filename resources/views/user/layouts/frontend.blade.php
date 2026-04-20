<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
    <meta name="description" content="Digital payment and financial services platform for seamless transactions, bill payments, and wealth management">
    <meta name="application-name" content="OneTera">
    
    @include('partials.seo')
    <title>{{ $general->sitename(__($pageTitle ?? 'Home')) }}</title>
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
        $isLoggedIn = auth()->check();
        $desktopNav = [
            ['label' => 'Home', 'route' => 'home'],
            ['label' => 'Contact', 'route' => 'contact'],
            ['label' => $isLoggedIn ? 'Dashboard' : 'Login', 'route' => $isLoggedIn ? 'user.home' : 'user.login'],
            ['label' => $isLoggedIn ? 'Profile' : 'Register', 'route' => $isLoggedIn ? 'user.profile.setting' : 'user.register'],
        ];

        $mobileNav = [
            ['label' => 'Home', 'route' => 'home'],
            ['label' => 'Contact', 'route' => 'contact'],
            ['label' => 'Login', 'route' => 'user.login'],
            ['label' => 'Register', 'route' => 'user.register'],
        ];

        $footerPlatformLinks = [
            ['label' => 'Home', 'href' => route('home')],
            ['label' => 'Contact', 'href' => route('contact')],
            ['label' => $isLoggedIn ? 'Dashboard' : 'Login', 'href' => $isLoggedIn ? route('user.home') : route('user.login')],
            ['label' => $isLoggedIn ? 'Profile settings' : 'Register', 'href' => $isLoggedIn ? route('user.profile.setting') : route('user.register')],
        ];

        $footerServiceLinks = [
            ['label' => 'Airtime', 'href' => $isLoggedIn ? route('user.airtime') : route('user.login')],
            ['label' => 'Data', 'href' => $isLoggedIn ? route('user.internet') : route('user.login')],
            ['label' => 'Deposit', 'href' => $isLoggedIn ? route('user.deposit') : route('user.login')],
            ['label' => 'Transfer', 'href' => $isLoggedIn ? route('user.othertransfer') : route('user.login')],
        ];

        if ($isLoggedIn) {
            $mobileNav[] = ['label' => 'Dashboard', 'route' => 'user.home'];
            $mobileNav[] = ['label' => 'Profile', 'route' => 'user.profile.setting'];
            $mobileNav[] = ['label' => 'Logout', 'route' => 'user.logout'];
        }
    @endphp

    <div class="app-shell flex min-h-screen flex-col">
        <header class="border-b border-slate-200/70 bg-white/90 backdrop-blur dark:border-white/10 dark:bg-zinc-950/85">
            <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <img src="{{ getImage(imagePath()['logoIcon']['path'] . '/logo.png') }}" alt="{{ $general->sitename }}" class="h-11 w-11 rounded-2xl border border-slate-200 object-contain shadow-sm dark:border-white/10">
                    <div class="leading-tight">
                        <p class="text-sm font-semibold uppercase tracking-[0.28em] text-sky-600 dark:text-sky-400">{{ $general->sitename }}</p>
                        <p class="text-sm text-slate-500 dark:text-zinc-400">{{ __($pageTitle ?? 'Home') }}</p>
                    </div>
                </a>

                <div class="flex items-center gap-2">
                    <nav class="hidden items-center gap-2 md:flex">
                        @foreach ($desktopNav as $item)
                            <a href="{{ route($item['route']) }}" class="shell-link {{ request()->routeIs($item['route']) ? 'shell-link-active' : '' }}">{{ $item['label'] }}</a>
                        @endforeach
                    </nav>

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

                    <button type="button" data-mobile-menu-toggle="#frontend-mobile-menu" class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-700 transition hover:border-sky-200 hover:text-sky-700 dark:border-white/10 dark:bg-zinc-900 dark:text-zinc-200 dark:hover:border-sky-500/30 dark:hover:text-sky-300 md:hidden" aria-controls="frontend-mobile-menu" aria-expanded="false" aria-label="Open navigation menu">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 6h16"></path>
                            <path d="M4 12h16"></path>
                            <path d="M4 18h16"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </header>

        <main class="flex-1">
            <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
                @yield('content')
            </div>
        </main>

        <footer class="border-t border-slate-200/70 bg-white/85 py-12 text-sm text-slate-500 backdrop-blur dark:border-white/10 dark:bg-zinc-950/90 dark:text-zinc-400">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="grid gap-8 border-b border-slate-200/70 pb-8 dark:border-white/10 lg:grid-cols-[1.15fr_0.85fr_0.85fr_0.85fr_1fr]">
                    <div class="space-y-4">
                        <a href="{{ route('home') }}" class="flex items-center gap-3">
                            <img src="{{ getImage(imagePath()['logoIcon']['path'] . '/logo.png') }}" alt="{{ $general->sitename }}" class="h-11 w-11 rounded-2xl border border-slate-200 object-contain shadow-sm dark:border-white/10">
                            <div class="leading-tight">
                                <p class="text-sm font-semibold uppercase tracking-[0.28em] text-sky-600 dark:text-sky-400">{{ $general->sitename }}</p>
                                <p class="text-sm text-slate-500 dark:text-zinc-400">Digital payment operations</p>
                            </div>
                        </a>
                        <p class="max-w-sm text-sm leading-7 text-slate-600 dark:text-zinc-300">
                            A cleaner public experience for billing, wallet funding, and transaction operations across every screen size.
                        </p>
                    </div>

                    <div class="space-y-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-500 dark:text-zinc-400">Platform</p>
                        <div class="grid gap-3">
                            @foreach ($footerPlatformLinks as $item)
                                <a href="{{ $item['href'] }}" class="text-sm text-slate-600 transition hover:text-slate-950 dark:text-zinc-300 dark:hover:text-white">
                                    {{ $item['label'] }}
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <div class="space-y-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-500 dark:text-zinc-400">Services</p>
                        <div class="grid gap-3">
                            @foreach ($footerServiceLinks as $item)
                                <a href="{{ $item['href'] }}" class="text-sm text-slate-600 transition hover:text-slate-950 dark:text-zinc-300 dark:hover:text-white">
                                    {{ $item['label'] }}
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <div class="space-y-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-500 dark:text-zinc-400">Legal</p>
                        <div class="grid gap-3">
                            <a href="{{ route('legal.privacy') }}" class="text-sm text-slate-600 transition hover:text-slate-950 dark:text-zinc-300 dark:hover:text-white">
                                Privacy Policy
                            </a>
                            <a href="{{ route('legal.terms') }}" class="text-sm text-slate-600 transition hover:text-slate-950 dark:text-zinc-300 dark:hover:text-white">
                                Terms & Conditions
                            </a>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-500 dark:text-zinc-400">Contact</p>
                        <div class="space-y-3 text-sm leading-7 text-slate-600 dark:text-zinc-300">
                            <p>Email: <a href="mailto:{{ $general->email_from }}" class="font-medium text-slate-950 dark:text-white">{{ $general->email_from }}</a></p>
                            <p>Need help with billing, support review, or a service question? Use the direct contact page.</p>
                            <a href="{{ route('contact') }}" class="inline-flex items-center rounded-full border border-slate-200 bg-slate-50 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 dark:border-white/10 dark:bg-white/5 dark:text-zinc-200 dark:hover:bg-white/10">
                                Open contact page
                            </a>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-3 pt-6 text-sm sm:flex-row sm:items-center sm:justify-between">
                    <p>&copy; {{ date('Y') }} {{ $general->sitename }}. All rights reserved.</p>
                    <p>Built for cleaner billing, stronger controls, and a calmer transaction experience.</p>
                </div>
            </div>
        </footer>
    </div>

    <div id="frontend-mobile-menu" class="app-mobile-menu-overlay" data-mobile-menu-panel data-open="0" aria-hidden="true">
        <button type="button" class="app-mobile-menu-backdrop" data-mobile-menu-close aria-label="Close navigation menu"></button>
        <aside class="app-mobile-menu-panel">
            <div class="flex items-center justify-between gap-3">
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <img src="{{ getImage(imagePath()['logoIcon']['path'] . '/logo.png') }}" alt="{{ $general->sitename }}" class="h-10 w-10 rounded-2xl border border-slate-200 object-contain dark:border-white/10">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.25em] text-sky-600 dark:text-sky-400">{{ $general->sitename }}</p>
                        <p class="text-sm text-slate-500 dark:text-zinc-400">{{ __($pageTitle ?? 'Home') }}</p>
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
                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-500 dark:text-zinc-400">Navigation</p>
                <p class="mt-2 text-2xl font-semibold tracking-tight text-slate-950 dark:text-white">Stay in control</p>
                <p class="mt-1 text-sm text-slate-500 dark:text-zinc-400">Open the pages you need without the desktop nav getting in the way.</p>
            </div>

            <nav class="space-y-1">
                @foreach ($mobileNav as $item)
                    <a href="{{ route($item['route']) }}" class="shell-link {{ request()->routeIs($item['route']) ? 'shell-link-active' : '' }}">
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-slate-100 text-slate-600 dark:bg-white/5 dark:text-zinc-300">
                            @switch($item['label'])
                                @case('Home')
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 11l9-8 9 8"></path><path d="M5 10v10h14V10"></path></svg>
                                    @break
                                @case('Contact')
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16v12H5.17L4 17.17V4Z"></path><path d="m8 9 4 3 4-3"></path></svg>
                                    @break
                                @case('Login')
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 3h4a2 2 0 0 1 2 2v4"></path><path d="M10 14 21 3"></path><path d="M21 15v4a2 2 0 0 1-2 2h-4"></path><path d="M14 10 3 21"></path></svg>
                                    @break
                                @case('Register')
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14"></path><path d="M5 12h14"></path></svg>
                                    @break
                                @case('Dashboard')
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 11l9-8 9 8"></path><path d="M5 10v10h14V10"></path></svg>
                                    @break
                                @case('Profile')
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8z"></path><path d="M4 22v-2a8 8 0 0 1 16 0v2"></path></svg>
                                    @break
                                @case('Logout')
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 17l5-5-5-5"></path><path d="M15 12H3"></path><path d="M21 4v16"></path></svg>
                                    @break
                            @endswitch
                        </span>
                        <span>{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </nav>

            <div class="mt-auto grid gap-2 pt-2">
                @if ($isLoggedIn)
                    <a href="{{ route('user.home') }}" class="shell-link">
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-slate-100 text-slate-600 dark:bg-white/5 dark:text-zinc-300">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 11l9-8 9 8"></path><path d="M5 10v10h14V10"></path></svg>
                        </span>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('user.logout') }}" class="shell-link">
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-slate-100 text-slate-600 dark:bg-white/5 dark:text-zinc-300">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 17l5-5-5-5"></path><path d="M15 12H3"></path><path d="M21 4v16"></path></svg>
                        </span>
                        <span>Logout</span>
                    </a>
                @else
                    <a href="{{ route('user.login') }}" class="shell-link">
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-slate-100 text-slate-600 dark:bg-white/5 dark:text-zinc-300">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 3h4a2 2 0 0 1 2 2v4"></path><path d="M10 14 21 3"></path><path d="M21 15v4a2 2 0 0 1-2 2h-4"></path><path d="M14 10 3 21"></path></svg>
                        </span>
                        <span>Login</span>
                    </a>
                    <a href="{{ route('user.register') }}" class="shell-link">
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-slate-100 text-slate-600 dark:bg-white/5 dark:text-zinc-300">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14"></path><path d="M5 12h14"></path></svg>
                        </span>
                        <span>Register</span>
                    </a>
                @endif
            </div>
        </aside>
    </div>
    @stack('script-lib')
    @stack('script')

    @include('partials.notify')
    @include('partials.alertx')
    @include('partials.plugins')
    @push('script')
        <script async src="https://www.googletagmanager.com/gtag/js?id=G-T524903Y5N"></script>
        <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'G-T524903Y5N');
        </script>
    @endpush
</body>
</html>
