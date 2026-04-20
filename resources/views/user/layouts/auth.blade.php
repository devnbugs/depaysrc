<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
    <meta name="description" content="Digital payment and financial services platform for seamless transactions, bill payments, and wealth management">
    <meta name="application-name" content="OneTera">
    
    @include('partials.seo')
    <title>{{ $general->sitename(__($pageTitle ?? '')) }}</title>
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
    <div class="app-shell flex min-h-screen flex-col">
        <header class="border-b border-slate-200/70 bg-white/90 backdrop-blur dark:border-white/10 dark:bg-zinc-950/85">
            <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <img src="{{ getImage(imagePath()['logoIcon']['path'] . '/logo.png') }}" alt="{{ $general->sitename }}" class="h-11 w-11 rounded-2xl border border-slate-200 object-contain shadow-sm dark:border-white/10">
                    <div class="leading-tight">
                        <p class="text-sm font-semibold uppercase tracking-[0.28em] text-sky-600 dark:text-sky-400">{{ $general->sitename }}</p>
                        <p class="text-sm text-slate-500 dark:text-zinc-400">{{ __($pageTitle ?? 'Authentication') }}</p>
                    </div>
                </a>

                <button type="button" data-theme-toggle class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 transition hover:border-sky-200 hover:text-sky-700 dark:border-white/10 dark:bg-zinc-900 dark:text-zinc-200 dark:hover:border-sky-500/30 dark:hover:text-sky-300">
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
                    <span class="hidden sm:inline"></span>
                </button>
            </div>
        </header>

        <main class="flex-1">
            <div class="mx-auto grid min-h-[calc(100vh-5.5rem)] max-w-7xl items-center gap-8 px-4 py-8 sm:px-6 lg:grid-cols-[1.05fr_0.95fr] lg:px-8">
                <section class="hidden rounded-[2rem] border border-slate-200 bg-white p-8 shadow-[0_30px_90px_rgba(15,23,42,0.08)] dark:border-white/10 dark:bg-zinc-900/70 lg:flex lg:flex-col lg:justify-between">
                    <div class="space-y-8">
                        <div class="inline-flex items-center gap-2 rounded-full border border-sky-200 bg-sky-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.25em] text-sky-700 dark:border-sky-500/30 dark:bg-sky-500/10 dark:text-sky-300">
                            Secured by Cloudflare
                        </div>
                        <div class="space-y-4">
                            <h1 class="max-w-xl text-4xl font-semibold tracking-tight text-slate-950 dark:text-white">
                                A cleaner wallet experience with a light-first system and dark mode on demand.
                            </h1>
                            <p class="max-w-xl text-base leading-7 text-slate-600 dark:text-zinc-300">
                                Sign in, register, and recover access in a calm interface built for clarity, with the security-sensitive flows kept on the Rails side.
                            </p>
                        </div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-3">
                        <div class="rounded-3xl border border-slate-200 p-4 dark:border-white/10">
                            <p class="text-sm font-medium text-slate-500 dark:text-zinc-400">Wallet</p>
                            <p class="mt-2 text-2xl font-semibold text-slate-950 dark:text-white">Secure</p>
                        </div>
                        <div class="rounded-3xl border border-slate-200 p-4 dark:border-white/10">
                            <p class="text-sm font-medium text-slate-500 dark:text-zinc-400">Theme</p>
                            <p class="mt-2 text-2xl font-semibold text-slate-950 dark:text-white">Adaptive</p>
                        </div>
                        <div class="rounded-3xl border border-slate-200 p-4 dark:border-white/10">
                            <p class="text-sm font-medium text-slate-500 dark:text-zinc-400">UI</p>
                            <p class="mt-2 text-2xl font-semibold text-slate-950 dark:text-white">Flux</p>
                        </div>
                    </div>
                </section>

                <section class="glass-panel p-6 sm:p-8">
                    @yield('content')
                </section>
            </div>
        </main>
    </div>

    {{-- Countly Debug Console Logging --}}
    @if(config('services.countly.debug'))
        <script>
            window.CountlyDebug = true;
            window.countlyLog = function(...args) {
                if (window.CountlyDebug) {
                    console.log('[Countly]', ...args);
                }
            };
        </script>
    @endif

    @livewireScripts
    @stack('script-lib')
    @stack('script')

    @include('partials.notify')
    @include('partials.alertx')
    @include('partials.btnlog')
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
