@extends('admin.layouts.master')

@section('content')
    <div class="space-y-8">
        <div class="flex flex-col gap-4 rounded-3xl border border-slate-200 bg-white p-8 shadow-sm dark:border-white/10 dark:bg-zinc-950/40 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-slate-950 dark:text-white">Google Analytics</h1>
                <p class="mt-2 text-sm text-slate-600 dark:text-zinc-300">
                    Property: <span class="font-medium">{{ $propertyId ?? 'Not configured' }}</span>
                </p>
            </div>

            <form method="GET" class="flex items-center gap-3">
                <label class="text-sm text-slate-600 dark:text-zinc-300">
                    Days
                    <input name="days" type="number" min="1" max="365" value="{{ $periodDays }}" class="mt-1 w-24 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm dark:border-white/10 dark:bg-zinc-900 dark:text-white">
                </label>
                <button type="submit" class="mt-6 inline-flex items-center justify-center rounded-xl bg-sky-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-700">
                    Refresh
                </button>
            </form>
        </div>

        @if (! $configured)
            <div class="rounded-3xl border border-amber-200 bg-amber-50 p-6 text-sm text-amber-900 dark:border-amber-900/40 dark:bg-amber-950/20 dark:text-amber-200">
                <p class="font-semibold">Analytics not configured</p>
                <p class="mt-2 leading-6">
                    Set <code class="rounded bg-amber-100 px-1.5 py-0.5 dark:bg-amber-900/30">ANALYTICS_PROPERTY_ID</code> and provide service account credentials via
                    <code class="rounded bg-amber-100 px-1.5 py-0.5 dark:bg-amber-900/30">ANALYTICS_CREDENTIALS_PATH</code>
                    (or <code class="rounded bg-amber-100 px-1.5 py-0.5 dark:bg-amber-900/30">ANALYTICS_CREDENTIALS_JSON</code>) in your <code class="rounded bg-amber-100 px-1.5 py-0.5 dark:bg-amber-900/30">.env</code>.
                </p>
            </div>
        @else
            <div class="grid gap-6 lg:grid-cols-2">
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-zinc-950/40">
                    <p class="text-sm font-semibold text-slate-900 dark:text-white">Top pages ({{ $periodDays }} days)</p>
                    <div class="mt-4 overflow-hidden rounded-2xl border border-slate-200 dark:border-white/10">
                        <table class="min-w-full text-left text-sm">
                            <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500 dark:bg-white/5 dark:text-zinc-400">
                                <tr>
                                    <th class="px-4 py-3">Page</th>
                                    <th class="px-4 py-3 text-right">Views</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 dark:divide-white/10">
                                @forelse ($topPages as $row)
                                    <tr>
                                        <td class="px-4 py-3">
                                            <p class="font-medium text-slate-900 dark:text-white">{{ $row['pageTitle'] ?? '-' }}</p>
                                            <p class="mt-1 break-all text-xs text-slate-500 dark:text-zinc-400">{{ $row['fullPageUrl'] ?? '' }}</p>
                                        </td>
                                        <td class="px-4 py-3 text-right font-semibold text-slate-900 dark:text-white">{{ $row['screenPageViews'] ?? 0 }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="px-4 py-6 text-center text-slate-500 dark:text-zinc-400">No data</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-zinc-950/40">
                    <p class="text-sm font-semibold text-slate-900 dark:text-white">Top referrers ({{ $periodDays }} days)</p>
                    <div class="mt-4 overflow-hidden rounded-2xl border border-slate-200 dark:border-white/10">
                        <table class="min-w-full text-left text-sm">
                            <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500 dark:bg-white/5 dark:text-zinc-400">
                                <tr>
                                    <th class="px-4 py-3">Referrer</th>
                                    <th class="px-4 py-3 text-right">Views</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 dark:divide-white/10">
                                @forelse ($topReferrers as $row)
                                    <tr>
                                        <td class="px-4 py-3 text-slate-700 dark:text-zinc-200">{{ $row['pageReferrer'] ?? '-' }}</td>
                                        <td class="px-4 py-3 text-right font-semibold text-slate-900 dark:text-white">{{ $row['screenPageViews'] ?? 0 }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="px-4 py-6 text-center text-slate-500 dark:text-zinc-400">No data</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-3">
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-zinc-950/40">
                    <p class="text-sm font-semibold text-slate-900 dark:text-white">Top browsers</p>
                    <ul class="mt-4 space-y-2 text-sm">
                        @forelse ($topBrowsers as $row)
                            <li class="flex items-center justify-between gap-3 rounded-2xl bg-slate-50 px-4 py-2 dark:bg-white/5">
                                <span class="text-slate-700 dark:text-zinc-200">{{ $row['browser'] ?? '-' }}</span>
                                <span class="font-semibold text-slate-900 dark:text-white">{{ $row['screenPageViews'] ?? 0 }}</span>
                            </li>
                        @empty
                            <li class="text-slate-500 dark:text-zinc-400">No data</li>
                        @endforelse
                    </ul>
                </div>

                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-zinc-950/40">
                    <p class="text-sm font-semibold text-slate-900 dark:text-white">Top countries</p>
                    <ul class="mt-4 space-y-2 text-sm">
                        @forelse ($topCountries as $row)
                            <li class="flex items-center justify-between gap-3 rounded-2xl bg-slate-50 px-4 py-2 dark:bg-white/5">
                                <span class="text-slate-700 dark:text-zinc-200">{{ $row['country'] ?? '-' }}</span>
                                <span class="font-semibold text-slate-900 dark:text-white">{{ $row['screenPageViews'] ?? 0 }}</span>
                            </li>
                        @empty
                            <li class="text-slate-500 dark:text-zinc-400">No data</li>
                        @endforelse
                    </ul>
                </div>

                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-zinc-950/40">
                    <p class="text-sm font-semibold text-slate-900 dark:text-white">Top operating systems</p>
                    <ul class="mt-4 space-y-2 text-sm">
                        @forelse ($topOperatingSystems as $row)
                            <li class="flex items-center justify-between gap-3 rounded-2xl bg-slate-50 px-4 py-2 dark:bg-white/5">
                                <span class="text-slate-700 dark:text-zinc-200">{{ $row['operatingSystem'] ?? '-' }}</span>
                                <span class="font-semibold text-slate-900 dark:text-white">{{ $row['screenPageViews'] ?? 0 }}</span>
                            </li>
                        @empty
                            <li class="text-slate-500 dark:text-zinc-400">No data</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        @endif
    </div>
@endsection

