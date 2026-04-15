@extends('admin.layouts.app')

@section('panel')
    <div class="space-y-6">
        <div>
            <p class="section-kicker">Bills</p>
            <h2 class="mt-2 section-title">Bill Payment Settings</h2>
            <p class="mt-2 section-copy max-w-3xl">Choose the default bills provider, route each bill category to BudPay or Squad where supported, and keep provider catalogs fresh every eight hours by default.</p>
        </div>

        <div class="panel-card rounded-2xl border border-slate-200 p-6 dark:border-white/10">
            <form action="{{ route('admin.bills.settings.update') }}" method="POST" class="space-y-8">
                @csrf

                <div class="grid gap-6 lg:grid-cols-2">
                    <div class="space-y-4 rounded-2xl border border-slate-200 bg-slate-50 p-5 dark:border-white/10 dark:bg-white/5">
                        <h3 class="text-lg font-semibold text-slate-950 dark:text-white">Feature Control</h3>
                        <label class="flex items-center justify-between gap-4 rounded-2xl border border-slate-200 bg-white px-4 py-3 dark:border-white/10 dark:bg-zinc-900/60">
                            <div>
                                <p class="text-sm font-semibold text-slate-900 dark:text-white">Enable bill payments</p>
                                <p class="mt-1 text-xs text-slate-500 dark:text-zinc-500">Keeps airtime, data, TV, and electricity active on user pages.</p>
                            </div>
                            <input type="checkbox" name="bill_payment_enabled" value="1" class="h-5 w-5 rounded border-slate-300 text-sky-600 focus:ring-sky-500" @checked($settings['enabled'])>
                        </label>
                        <label class="flex items-center justify-between gap-4 rounded-2xl border border-slate-200 bg-white px-4 py-3 dark:border-white/10 dark:bg-zinc-900/60">
                            <div>
                                <p class="text-sm font-semibold text-slate-900 dark:text-white">Auto sync catalog</p>
                                <p class="mt-1 text-xs text-slate-500 dark:text-zinc-500">Refreshes live providers and plans on the schedule below.</p>
                            </div>
                            <input type="checkbox" name="bill_payment_auto_sync_enabled" value="1" class="h-5 w-5 rounded border-slate-300 text-sky-600 focus:ring-sky-500" @checked($settings['auto_sync_enabled'])>
                        </label>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-zinc-300">Default provider</label>
                                <select name="bill_payment_default_provider" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-slate-900 outline-none focus:border-sky-500 dark:border-white/10 dark:bg-zinc-950 dark:text-white">
                                    @foreach ($providers as $key => $label)
                                        <option value="{{ $key }}" @selected($settings['default_provider'] === $key)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-zinc-300">Sync interval (hours)</label>
                                <input type="number" min="1" max="168" name="bill_payment_auto_sync_hours" value="{{ old('bill_payment_auto_sync_hours', $settings['auto_sync_hours']) }}" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-slate-900 outline-none focus:border-sky-500 dark:border-white/10 dark:bg-zinc-950 dark:text-white">
                            </div>
                        </div>
                        <div class="rounded-2xl border border-dashed border-slate-300 px-4 py-3 text-sm text-slate-600 dark:border-white/10 dark:text-zinc-400">
                            Last synced:
                            <span class="font-semibold text-slate-900 dark:text-white">
                                {{ optional($settings['last_synced_at'])->format('M d, Y h:i A') ?: 'Never' }}
                            </span>
                        </div>
                    </div>

                    <div class="space-y-4 rounded-2xl border border-slate-200 bg-slate-50 p-5 dark:border-white/10 dark:bg-white/5">
                        <h3 class="text-lg font-semibold text-slate-950 dark:text-white">Service Routing</h3>
                        @foreach ($services as $key => $label)
                            <div>
                                <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-zinc-300">{{ $label }} provider</label>
                                <select name="bill_payment_service_providers[{{ $key }}]" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-slate-900 outline-none focus:border-sky-500 dark:border-white/10 dark:bg-zinc-950 dark:text-white">
                                    @foreach ($serviceChoices[$key] as $providerKey => $providerLabel)
                                        <option value="{{ $providerKey }}" @selected(data_get($settings, "service_providers.$key") === $providerKey)>{{ $providerLabel }}</option>
                                    @endforeach
                                </select>
                                @if ($key === 'tv')
                                    <p class="mt-1 text-xs text-slate-500 dark:text-zinc-500">TV stays on BudPay because that is the official catalog and purchase path configured here.</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="grid gap-4 xl:grid-cols-2">
                    @foreach ($providers as $key => $label)
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5 dark:border-white/10 dark:bg-white/5">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <h3 class="text-lg font-semibold text-slate-950 dark:text-white">{{ $label }}</h3>
                                    <p class="mt-1 text-xs text-slate-500 dark:text-zinc-500">Credentials used for catalog sync and live bill purchases.</p>
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
                                @if ($key === 'budpay')
                                    <div>
                                        <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-zinc-300">Public key</label>
                                        <input type="text" name="providers[{{ $key }}][public_key]" value="{{ old("providers.$key.public_key", data_get($settings, "providers.$key.public_key")) }}" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-slate-900 outline-none focus:border-sky-500 dark:border-white/10 dark:bg-zinc-950 dark:text-white">
                                    </div>
                                @endif
                                @if ($key === 'squad')
                                    <div>
                                        <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-zinc-300">Merchant ID</label>
                                        <input type="text" name="providers[{{ $key }}][merchant_id]" value="{{ old("providers.$key.merchant_id", data_get($settings, "providers.$key.merchant_id")) }}" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-slate-900 outline-none focus:border-sky-500 dark:border-white/10 dark:bg-zinc-950 dark:text-white">
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="flex flex-wrap justify-end gap-3">
                    <button type="submit" class="inline-flex h-11 items-center rounded-full bg-sky-600 px-6 text-sm font-semibold text-white transition hover:bg-sky-700 dark:bg-sky-700 dark:hover:bg-sky-800">Save Bill Settings</button>
                    <button type="submit" formaction="{{ route('admin.bills.settings.sync') }}" formmethod="POST" class="inline-flex h-11 items-center rounded-full border border-slate-300 px-6 text-sm font-semibold text-slate-700 transition hover:border-sky-500 hover:text-sky-700 dark:border-white/10 dark:text-zinc-200 dark:hover:border-sky-500 dark:hover:text-sky-300">Sync Catalog Now</button>
                </div>
            </form>
        </div>
    </div>
@endsection
