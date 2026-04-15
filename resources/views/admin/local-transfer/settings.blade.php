@extends('admin.layouts.app')

@section('panel')
    <div class="space-y-6">
        <div>
            <p class="section-kicker">Transfers</p>
            <h2 class="mt-2 section-title">Local Transfer Settings</h2>
            <p class="mt-2 section-copy max-w-3xl">Enable wallet-to-bank transfers, control the provider order for account resolution and payout, and manage API credentials in one place.</p>
        </div>

        <div class="panel-card rounded-2xl border border-slate-200 p-6 dark:border-white/10">
            <form action="{{ route('admin.local-transfer.settings.update') }}" method="POST" class="space-y-8">
                @csrf

                <div class="grid gap-6 lg:grid-cols-2">
                    <div class="space-y-4 rounded-2xl border border-slate-200 bg-slate-50 p-5 dark:border-white/10 dark:bg-white/5">
                        <h3 class="text-lg font-semibold text-slate-950 dark:text-white">Feature Control</h3>
                        <label class="flex items-center justify-between gap-4 rounded-2xl border border-slate-200 bg-white px-4 py-3 dark:border-white/10 dark:bg-zinc-900/60">
                            <div>
                                <p class="text-sm font-semibold text-slate-900 dark:text-white">Enable local transfer</p>
                                <p class="mt-1 text-xs text-slate-500 dark:text-zinc-500">Show the transfer flow to users.</p>
                            </div>
                            <input type="checkbox" name="local_transfer_enabled" value="1" class="h-5 w-5 rounded border-slate-300 text-sky-600 focus:ring-sky-500" @checked($settings['enabled'])>
                        </label>
                        <label class="flex items-center justify-between gap-4 rounded-2xl border border-slate-200 bg-white px-4 py-3 dark:border-white/10 dark:bg-zinc-900/60">
                            <div>
                                <p class="text-sm font-semibold text-slate-900 dark:text-white">Require PIN when enabled on user account</p>
                                <p class="mt-1 text-xs text-slate-500 dark:text-zinc-500">Uses the user security PIN in the authorization step.</p>
                            </div>
                            <input type="checkbox" name="local_transfer_require_pin" value="1" class="h-5 w-5 rounded border-slate-300 text-sky-600 focus:ring-sky-500" @checked($settings['require_pin'])>
                        </label>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-zinc-300">Minimum amount</label>
                                <input type="number" step="0.01" min="0" name="local_transfer_min" value="{{ old('local_transfer_min', $settings['minimum']) }}" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-slate-900 outline-none focus:border-sky-500 dark:border-white/10 dark:bg-zinc-950 dark:text-white">
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-zinc-300">Maximum amount</label>
                                <input type="number" step="0.01" min="0" name="local_transfer_max" value="{{ old('local_transfer_max', $settings['maximum']) }}" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-slate-900 outline-none focus:border-sky-500 dark:border-white/10 dark:bg-zinc-950 dark:text-white">
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4 rounded-2xl border border-slate-200 bg-slate-50 p-5 dark:border-white/10 dark:bg-white/5">
                        <h3 class="text-lg font-semibold text-slate-950 dark:text-white">Routing Order</h3>
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-zinc-300">Bank directory provider</label>
                            <select name="local_transfer_directory_provider" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-slate-900 outline-none focus:border-sky-500 dark:border-white/10 dark:bg-zinc-950 dark:text-white">
                                @foreach ($providers as $key => $label)
                                    <option value="{{ $key }}" @selected($settings['directory_provider'] === $key)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <p class="mb-2 text-sm font-semibold text-slate-700 dark:text-zinc-300">Resolve order</p>
                                @foreach ($settings['resolve_order'] as $index => $provider)
                                    <select name="local_transfer_resolve_order[]" class="mb-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-slate-900 outline-none focus:border-sky-500 dark:border-white/10 dark:bg-zinc-950 dark:text-white">
                                        @foreach ($providers as $key => $label)
                                            <option value="{{ $key }}" @selected($provider === $key)>{{ ($index + 1).'. '.$label }}</option>
                                        @endforeach
                                    </select>
                                @endforeach
                            </div>
                            <div>
                                <p class="mb-2 text-sm font-semibold text-slate-700 dark:text-zinc-300">Transfer order</p>
                                @foreach ($settings['transfer_order'] as $index => $provider)
                                    <select name="local_transfer_transfer_order[]" class="mb-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-slate-900 outline-none focus:border-sky-500 dark:border-white/10 dark:bg-zinc-950 dark:text-white">
                                        @foreach ($providers as $key => $label)
                                            <option value="{{ $key }}" @selected($provider === $key)>{{ ($index + 1).'. '.$label }}</option>
                                        @endforeach
                                    </select>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid gap-4 xl:grid-cols-2">
                    @foreach ($providers as $key => $label)
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5 dark:border-white/10 dark:bg-white/5">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <h3 class="text-lg font-semibold text-slate-950 dark:text-white">{{ $label }}</h3>
                                    <p class="mt-1 text-xs text-slate-500 dark:text-zinc-500">Credentials used for bank lookup and payout fallback.</p>
                                </div>
                                <input type="checkbox" name="providers[{{ $key }}][enabled]" value="1" class="h-5 w-5 rounded border-slate-300 text-sky-600 focus:ring-sky-500" @checked(data_get($settings, "providers.$key.enabled"))>
                            </div>

                            <div class="mt-4 space-y-3">
                                <div>
                                    <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-zinc-300">Base URL</label>
                                    <input type="text" name="providers[{{ $key }}][base_url]" value="{{ old("providers.$key.base_url", data_get($settings, "providers.$key.base_url")) }}" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-slate-900 outline-none focus:border-sky-500 dark:border-white/10 dark:bg-zinc-950 dark:text-white">
                                </div>
                                <div>
                                    <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-zinc-300">Secret key</label>
                                    <input type="text" name="providers[{{ $key }}][secret_key]" value="{{ old("providers.$key.secret_key", data_get($settings, "providers.$key.secret_key")) }}" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-slate-900 outline-none focus:border-sky-500 dark:border-white/10 dark:bg-zinc-950 dark:text-white">
                                </div>
                                @if ($key === 'squad')
                                    <div>
                                        <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-zinc-300">Merchant ID</label>
                                        <input type="text" name="providers[{{ $key }}][merchant_id]" value="{{ old("providers.$key.merchant_id", data_get($settings, "providers.$key.merchant_id")) }}" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-slate-900 outline-none focus:border-sky-500 dark:border-white/10 dark:bg-zinc-950 dark:text-white">
                                    </div>
                                @endif
                                @if ($key === 'budpay')
                                    <div>
                                        <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-zinc-300">Public key</label>
                                        <input type="text" name="providers[{{ $key }}][public_key]" value="{{ old("providers.$key.public_key", data_get($settings, "providers.$key.public_key")) }}" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-slate-900 outline-none focus:border-sky-500 dark:border-white/10 dark:bg-zinc-950 dark:text-white">
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="inline-flex h-11 items-center rounded-full bg-sky-600 px-6 text-sm font-semibold text-white transition hover:bg-sky-700 dark:bg-sky-700 dark:hover:bg-sky-800">Save Local Transfer Settings</button>
                </div>
            </form>
        </div>
    </div>
@endsection
