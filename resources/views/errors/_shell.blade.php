@php
    $appName = config('app.name', 'DePay');
    $pageCode = trim((string) ($code ?? 'Error'));
    $pageTitle = trim((string) ($title ?? 'Something went wrong'));
    $pageMessage = trim((string) ($message ?? 'Please try again in a moment.'));
    $homeUrl = url('/');
    $backUrl = url()->previous() ?: $homeUrl;
    $primaryLabel = trim((string) ($primaryLabel ?? 'Go Home'));
    $primaryUrl = trim((string) ($primaryUrl ?? $homeUrl));
    $secondaryLabel = trim((string) ($secondaryLabel ?? 'Go Back'));
    $secondaryUrl = trim((string) ($secondaryUrl ?? $backUrl));
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $pageCode }} | {{ $pageTitle }} | {{ $appName }}</title>
    <link rel="shortcut icon" type="image/png" href="{{ getImage(imagePath()['logoIcon']['path'] . '/favicon.png') }}">
    <style>
        :root {
            color-scheme: light dark;
            --bg: #f4f7fb;
            --bg-accent: radial-gradient(circle at top left, rgba(14, 165, 233, 0.18), transparent 28%), radial-gradient(circle at bottom right, rgba(15, 23, 42, 0.12), transparent 30%), linear-gradient(180deg, #f8fbff 0%, #eef4fb 100%);
            --panel: rgba(255, 255, 255, 0.84);
            --panel-border: rgba(148, 163, 184, 0.2);
            --text: #0f172a;
            --muted: #475569;
            --soft: #64748b;
            --accent: #0ea5e9;
            --accent-strong: #0369a1;
            --ring: rgba(14, 165, 233, 0.16);
            --shadow: 0 30px 90px rgba(15, 23, 42, 0.12);
        }

        @media (prefers-color-scheme: dark) {
            :root {
                --bg: #030712;
                --bg-accent: radial-gradient(circle at top left, rgba(14, 165, 233, 0.16), transparent 30%), radial-gradient(circle at bottom right, rgba(59, 130, 246, 0.12), transparent 28%), linear-gradient(180deg, #050816 0%, #090d18 100%);
                --panel: rgba(10, 15, 28, 0.82);
                --panel-border: rgba(255, 255, 255, 0.08);
                --text: #f8fafc;
                --muted: #cbd5e1;
                --soft: #94a3b8;
                --accent: #38bdf8;
                --accent-strong: #7dd3fc;
                --ring: rgba(56, 189, 248, 0.18);
                --shadow: 0 30px 90px rgba(0, 0, 0, 0.42);
            }
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: var(--bg-accent);
            color: var(--text);
        }

        .page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .shell {
            width: min(100%, 1080px);
            display: grid;
            gap: 24px;
            grid-template-columns: 1.1fr 0.9fr;
        }

        .card,
        .aside {
            border: 1px solid var(--panel-border);
            background: var(--panel);
            backdrop-filter: blur(18px);
            border-radius: 32px;
            box-shadow: var(--shadow);
        }

        .card {
            padding: 28px;
        }

        .aside {
            padding: 28px;
            position: relative;
            overflow: hidden;
        }

        .brand {
            display: inline-flex;
            align-items: center;
            gap: 14px;
            text-decoration: none;
            color: inherit;
        }

        .brand img {
            width: 52px;
            height: 52px;
            border-radius: 16px;
            object-fit: contain;
            border: 1px solid var(--panel-border);
            background: rgba(255, 255, 255, 0.7);
            padding: 6px;
        }

        .brand-copy {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 999px;
            background: var(--ring);
            color: var(--accent-strong);
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.18em;
            text-transform: uppercase;
        }

        .hero {
            margin-top: 32px;
            display: grid;
            gap: 18px;
        }

        .code {
            font-size: clamp(52px, 12vw, 108px);
            line-height: 0.9;
            font-weight: 800;
            letter-spacing: -0.06em;
            margin: 0;
        }

        .title {
            margin: 0;
            font-size: clamp(28px, 4vw, 48px);
            line-height: 1.05;
            letter-spacing: -0.04em;
        }

        .message {
            margin: 0;
            max-width: 46ch;
            font-size: 16px;
            line-height: 1.75;
            color: var(--muted);
        }

        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 8px;
        }

        .button {
            min-height: 48px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0 20px;
            border-radius: 999px;
            text-decoration: none;
            font-weight: 700;
            transition: transform 0.15s ease, box-shadow 0.15s ease, background-color 0.15s ease, color 0.15s ease;
        }

        .button:hover {
            transform: translateY(-1px);
        }

        .button-primary {
            background: linear-gradient(135deg, var(--accent) 0%, #2563eb 100%);
            color: #fff;
            box-shadow: 0 18px 40px rgba(14, 165, 233, 0.24);
        }

        .button-secondary {
            border: 1px solid var(--panel-border);
            color: var(--text);
            background: rgba(255, 255, 255, 0.45);
        }

        @media (prefers-color-scheme: dark) {
            .button-secondary {
                background: rgba(255, 255, 255, 0.03);
            }
        }

        .meta {
            margin-top: 28px;
            display: grid;
            gap: 14px;
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .meta-card {
            padding: 16px;
            border-radius: 22px;
            border: 1px solid var(--panel-border);
            background: rgba(255, 255, 255, 0.34);
        }

        @media (prefers-color-scheme: dark) {
            .meta-card {
                background: rgba(255, 255, 255, 0.03);
            }
        }

        .meta-card span {
            display: block;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--soft);
        }

        .meta-card strong {
            display: block;
            margin-top: 8px;
            font-size: 16px;
        }

        .aside::before,
        .aside::after {
            content: "";
            position: absolute;
            border-radius: 999px;
            filter: blur(2px);
        }

        .aside::before {
            width: 180px;
            height: 180px;
            top: -70px;
            right: -40px;
            background: rgba(14, 165, 233, 0.14);
        }

        .aside::after {
            width: 150px;
            height: 150px;
            bottom: -60px;
            left: -35px;
            background: rgba(37, 99, 235, 0.12);
        }

        .aside-inner {
            position: relative;
            z-index: 1;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 28px;
        }

        .signal {
            width: 100%;
            min-height: 240px;
            border-radius: 28px;
            border: 1px solid var(--panel-border);
            background: linear-gradient(180deg, rgba(15, 23, 42, 0.04) 0%, rgba(14, 165, 233, 0.08) 100%);
            display: flex;
            align-items: end;
            justify-content: center;
            gap: 14px;
            padding: 28px;
        }

        @media (prefers-color-scheme: dark) {
            .signal {
                background: linear-gradient(180deg, rgba(255, 255, 255, 0.02) 0%, rgba(56, 189, 248, 0.09) 100%);
            }
        }

        .signal-bar {
            width: 44px;
            border-radius: 18px 18px 10px 10px;
            background: linear-gradient(180deg, rgba(14, 165, 233, 0.35), rgba(37, 99, 235, 0.95));
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.28);
        }

        .signal-bar:nth-child(1) { height: 72px; opacity: 0.55; }
        .signal-bar:nth-child(2) { height: 112px; opacity: 0.72; }
        .signal-bar:nth-child(3) { height: 156px; opacity: 0.86; }
        .signal-bar:nth-child(4) { height: 196px; }

        .aside-copy h2 {
            margin: 0;
            font-size: 24px;
            letter-spacing: -0.03em;
        }

        .aside-copy p {
            margin: 12px 0 0;
            color: var(--muted);
            line-height: 1.75;
        }

        @media (max-width: 900px) {
            .shell {
                grid-template-columns: 1fr;
            }

            .meta {
                grid-template-columns: 1fr;
            }

            .aside {
                order: -1;
            }
        }
    </style>
</head>
<body>
    <main class="page">
        <div class="shell">
            <section class="card">
                <a class="brand" href="{{ $homeUrl }}">
                    <img src="{{ getImage(imagePath()['logoIcon']['path'] . '/logo.png') }}" alt="{{ $appName }}">
                    <span class="brand-copy">
                        <strong>{{ $appName }}</strong>
                        <span style="color: var(--soft); font-size: 14px;">Service experience</span>
                    </span>
                </a>

                <div class="hero">
                    <span class="eyebrow">System Response</span>
                    <p class="code">{{ $pageCode }}</p>
                    <h1 class="title">{{ $pageTitle }}</h1>
                    <p class="message">{{ $pageMessage }}</p>

                    <div class="actions">
                        <a class="button button-primary" href="{{ $primaryUrl }}">{{ $primaryLabel }}</a>
                        <a class="button button-secondary" href="{{ $secondaryUrl }}">{{ $secondaryLabel }}</a>
                    </div>
                </div>

                <div class="meta">
                    <div class="meta-card">
                        <span>Status</span>
                        <strong>{{ $pageCode }}</strong>
                    </div>
                    <div class="meta-card">
                        <span>Route</span>
                        <strong>{{ request()->path() === '/' ? '/' : '/' . ltrim(request()->path(), '/') }}</strong>
                    </div>
                    <div class="meta-card">
                        <span>Next Step</span>
                        <strong>{{ $primaryLabel }}</strong>
                    </div>
                </div>
            </section>

            <aside class="aside">
                <div class="aside-inner">
                    <div class="signal" aria-hidden="true">
                        <div class="signal-bar"></div>
                        <div class="signal-bar"></div>
                        <div class="signal-bar"></div>
                        <div class="signal-bar"></div>
                    </div>

                    <div class="aside-copy">
                        <h2>We kept the recovery path simple.</h2>
                        <p>
                            Use the quick actions to return home, retry the last page, or continue once the request session is refreshed.
                        </p>
                    </div>
                </div>
            </aside>
        </div>
    </main>
</body>
</html>
