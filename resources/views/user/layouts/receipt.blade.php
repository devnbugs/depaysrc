<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
    @include('partials.seo')
    <title>{{ $general->sitename(__($pageTitle ?? 'Receipt')) }}</title>
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
    <style>
        @media print {
            .no-print {
                display: none !important;
            }

            body {
                background: #fff !important;
            }
        }
    </style>
</head>
<body class="min-h-full bg-white text-slate-900 antialiased dark:bg-zinc-950 dark:text-zinc-100">
    <div class="app-shell min-h-screen">
        <main class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
            @yield('content')
        </main>
    </div>

    @stack('script-lib')
    @stack('script')

    @include('partials.notify')
    @include('partials.alertx')
</body>
</html>
