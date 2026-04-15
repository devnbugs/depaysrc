@extends('admin.layouts.app')

@section('panel')
    <div class="space-y-6">
        <div>
            <p class="section-kicker">KYC Management</p>
            <h2 class="mt-2 section-title">KYC Subscription & Service Settings</h2>
            <p class="mt-2 section-copy max-w-3xl">
                Manage Paystack-backed KYC subscriptions, plan pricing, and the service access level each plan unlocks.
            </p>
        </div>

        <div class="panel-card rounded-2xl border border-slate-200 p-6 dark:border-white/10">
            <form action="{{ route('admin.kyc.services.update') }}" method="POST" class="space-y-8">
                @csrf

                <div class="grid gap-6 xl:grid-cols-2">
                    <div class="space-y-4 rounded-2xl border border-slate-200 bg-slate-50 p-5 dark:border-white/10 dark:bg-white/5">
                        <div>
                            <h3 class="text-lg font-semibold text-slate-950 dark:text-white">Subscription Control</h3>
                            <p class="mt-1 text-sm text-slate-500 dark:text-zinc-500">
                                Uses the global Paystack keys already configured for the application.
                            </p>
                        </div>

                        <label class="flex items-center justify-between gap-4 rounded-2xl border border-slate-200 bg-white px-4 py-3 dark:border-white/10 dark:bg-zinc-900/60">
                            <div>
                                <p class="text-sm font-semibold text-slate-900 dark:text-white">Enable KYC subscriptions</p>
                                <p class="mt-1 text-xs text-slate-500 dark:text-zinc-500">Users can only subscribe when this is on.</p>
                            </div>
                            <input type="checkbox" name="kyc_subscription_enabled" value="1" class="h-5 w-5 rounded border-slate-300 text-sky-600 focus:ring-sky-500" @checked($settings['enabled'])>
                        </label>

                        <label class="flex items-center justify-between gap-4 rounded-2xl border border-slate-200 bg-white px-4 py-3 dark:border-white/10 dark:bg-zinc-900/60">
                            <div>
                                <p class="text-sm font-semibold text-slate-900 dark:text-white">Auto-sync plans to Paystack</p>
                                <p class="mt-1 text-xs text-slate-500 dark:text-zinc-500">Create or update Paystack plans whenever you save.</p>
                            </div>
                            <input type="checkbox" name="kyc_subscription_sync_plans" value="1" class="h-5 w-5 rounded border-slate-300 text-sky-600 focus:ring-sky-500" @checked($settings['sync_plans'])>
                        </label>

                    </div>

                    <div class="space-y-4 rounded-2xl border border-slate-200 bg-slate-50 p-5 dark:border-white/10 dark:bg-white/5">
                        <div>
                            <h3 class="text-lg font-semibold text-slate-950 dark:text-white">Default Paystack Settings</h3>
                            <p class="mt-1 text-sm text-slate-500 dark:text-zinc-500">
                                Current Paystack status:
                                <span class="{{ $settings['paystack_secret_configured'] ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-600 dark:text-amber-400' }}">
                                    {{ $settings['paystack_secret_configured'] ? 'secret key detected' : 'secret key missing' }}
                                </span>
                            </p>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-zinc-300">Currency</label>
                                <input type="text" name="kyc_subscription_currency" value="{{ old('kyc_subscription_currency', $settings['currency']) }}" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-slate-900 outline-none focus:border-sky-500 dark:border-white/10 dark:bg-zinc-950 dark:text-white">
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-zinc-300">Default interval</label>
                                <select name="kyc_subscription_interval" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-slate-900 outline-none focus:border-sky-500 dark:border-white/10 dark:bg-zinc-950 dark:text-white">
                                    @foreach (['hourly', 'daily', 'weekly', 'monthly', 'quarterly', 'biannually', 'annually'] as $interval)
                                        <option value="{{ $interval }}" @selected(old('kyc_subscription_interval', $settings['interval']) === $interval)>{{ ucfirst($interval) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-zinc-300">Reference prefix</label>
                            <input type="text" name="kyc_subscription_reference_prefix" value="{{ old('kyc_subscription_reference_prefix', $settings['reference_prefix']) }}" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-slate-900 outline-none focus:border-sky-500 dark:border-white/10 dark:bg-zinc-950 dark:text-white">
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-zinc-300">Minimum funded amount before KYC</label>
                            <input type="number" step="0.01" min="0" name="kyc_minimum_funded_amount" value="{{ old('kyc_minimum_funded_amount', $settings['minimum_funded_amount']) }}" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-slate-900 outline-none focus:border-sky-500 dark:border-white/10 dark:bg-zinc-950 dark:text-white">
                            <p class="mt-1 text-xs text-slate-500 dark:text-zinc-500">Users must complete at least this amount in successful deposits before they can start identity verification or subscribe to a KYC plan.</p>
                        </div>

                        <div class="rounded-2xl border border-dashed border-slate-300 px-4 py-4 text-sm text-slate-500 dark:border-white/10 dark:text-zinc-500">
                            User callback: <span class="font-medium text-slate-700 dark:text-zinc-300">{{ route('user.kyc.upgrade.callback') }}</span><br>
                            Webhook endpoint: <span class="font-medium text-slate-700 dark:text-zinc-300">{{ url('/paystack/webhook') }}</span>
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-950 dark:text-white">Subscription Plans</h3>
                        <p class="mt-1 text-sm text-slate-500 dark:text-zinc-500">These prices and Paystack plan codes power the user checkout flow.</p>
                    </div>

                    <div class="grid gap-4 xl:grid-cols-3">
                        @foreach ($plans as $plan)
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5 dark:border-white/10 dark:bg-white/5">
                                <div class="flex items-center justify-between gap-3">
                                    <div>
                                        <h4 class="text-lg font-semibold text-slate-950 dark:text-white">{{ $plan->key }}</h4>
                                        <p class="mt-1 text-xs text-slate-500 dark:text-zinc-500">Plan code stored locally and synced to Paystack when enabled.</p>
                                    </div>
                                    <input type="checkbox" name="plans[{{ $plan->id }}][enabled]" value="1" class="h-5 w-5 rounded border-slate-300 text-sky-600 focus:ring-sky-500" @checked(old("plans.$plan->id.enabled", $plan->enabled))>
                                </div>

                                <div class="mt-4 space-y-3">
                                    <div>
                                        <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-zinc-300">Name</label>
                                        <input type="text" name="plans[{{ $plan->id }}][name]" value="{{ old("plans.$plan->id.name", $plan->name) }}" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-slate-900 outline-none focus:border-sky-500 dark:border-white/10 dark:bg-zinc-950 dark:text-white">
                                    </div>
                                    <div>
                                        <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-zinc-300">Description</label>
                                        <textarea name="plans[{{ $plan->id }}][description]" rows="2" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-slate-900 outline-none focus:border-sky-500 dark:border-white/10 dark:bg-zinc-950 dark:text-white">{{ old("plans.$plan->id.description", $plan->description) }}</textarea>
                                    </div>
                                    <div class="grid gap-3 sm:grid-cols-2">
                                        <div>
                                            <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-zinc-300">Price</label>
                                            <input type="number" step="0.01" min="0" name="plans[{{ $plan->id }}][price]" value="{{ old("plans.$plan->id.price", $plan->price) }}" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-slate-900 outline-none focus:border-sky-500 dark:border-white/10 dark:bg-zinc-950 dark:text-white">
                                        </div>
                                        <div>
                                            <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-zinc-300">Monthly limit</label>
                                            <input type="number" step="0.01" min="0" name="plans[{{ $plan->id }}][monthly_limit]" value="{{ old("plans.$plan->id.monthly_limit", $plan->monthly_limit) }}" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-slate-900 outline-none focus:border-sky-500 dark:border-white/10 dark:bg-zinc-950 dark:text-white">
                                        </div>
                                    </div>
                                    <div class="grid gap-3 sm:grid-cols-2">
                                        <div>
                                            <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-zinc-300">Sort order</label>
                                            <input type="number" min="0" name="plans[{{ $plan->id }}][sort_order]" value="{{ old("plans.$plan->id.sort_order", $plan->sort_order) }}" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-slate-900 outline-none focus:border-sky-500 dark:border-white/10 dark:bg-zinc-950 dark:text-white">
                                        </div>
                                        <div>
                                            <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-zinc-300">Badge</label>
                                            <input type="text" name="plans[{{ $plan->id }}][badge]" value="{{ old("plans.$plan->id.badge", $plan->badge) }}" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-slate-900 outline-none focus:border-sky-500 dark:border-white/10 dark:bg-zinc-950 dark:text-white">
                                        </div>
                                    </div>
                                    <div class="grid gap-3 sm:grid-cols-2">
                                        <div>
                                            <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-zinc-300">Paystack interval</label>
                                            <select name="plans[{{ $plan->id }}][paystack_interval]" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-slate-900 outline-none focus:border-sky-500 dark:border-white/10 dark:bg-zinc-950 dark:text-white">
                                                @foreach (['hourly', 'daily', 'weekly', 'monthly', 'quarterly', 'biannually', 'annually'] as $interval)
                                                    <option value="{{ $interval }}" @selected(old("plans.$plan->id.paystack_interval", $plan->paystack_interval) === $interval)>{{ ucfirst($interval) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-zinc-300">Paystack currency</label>
                                            <input type="text" name="plans[{{ $plan->id }}][paystack_currency]" value="{{ old("plans.$plan->id.paystack_currency", $plan->paystack_currency) }}" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-slate-900 outline-none focus:border-sky-500 dark:border-white/10 dark:bg-zinc-950 dark:text-white">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-zinc-300">Invoice limit</label>
                                        <input type="number" min="0" name="plans[{{ $plan->id }}][invoice_limit]" value="{{ old("plans.$plan->id.invoice_limit", $plan->invoice_limit) }}" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-slate-900 outline-none focus:border-sky-500 dark:border-white/10 dark:bg-zinc-950 dark:text-white">
                                    </div>
                                    <div>
                                        <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-zinc-300">Paystack plan code</label>
                                        <input type="text" name="plans[{{ $plan->id }}][paystack_plan_code]" value="{{ old("plans.$plan->id.paystack_plan_code", $plan->paystack_plan_code) }}" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-slate-900 outline-none focus:border-sky-500 dark:border-white/10 dark:bg-zinc-950 dark:text-white">
                                    </div>
                                    <div>
                                        <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-zinc-300">Paystack plan name override</label>
                                        <input type="text" name="plans[{{ $plan->id }}][paystack_plan_name]" value="{{ old("plans.$plan->id.paystack_plan_name", $plan->paystack_plan_name) }}" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-slate-900 outline-none focus:border-sky-500 dark:border-white/10 dark:bg-zinc-950 dark:text-white">
                                    </div>
                                    <div>
                                        <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-zinc-300">Plan features</label>
                                        <textarea name="plans[{{ $plan->id }}][features]" rows="4" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-slate-900 outline-none focus:border-sky-500 dark:border-white/10 dark:bg-zinc-950 dark:text-white">{{ old("plans.$plan->id.features", collect($plan->features ?? [])->implode(PHP_EOL)) }}</textarea>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-950 dark:text-white">Service Access Matrix</h3>
                        <p class="mt-1 text-sm text-slate-500 dark:text-zinc-500">Each service can be enabled or disabled and assigned to the minimum plan required to access it.</p>
                    </div>

                    @foreach ($groupedServices as $provider => $services)
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5 dark:border-white/10 dark:bg-white/5">
                            <div class="mb-4 flex items-center justify-between gap-3">
                                <div>
                                    <h4 class="text-lg font-semibold text-slate-950 dark:text-white">{{ ucfirst($provider) }} Services</h4>
                                    <p class="mt-1 text-xs text-slate-500 dark:text-zinc-500">{{ $services->count() }} configured services</p>
                                </div>
                            </div>

                            <div class="space-y-3">
                                @foreach ($services as $service)
                                    <div class="rounded-2xl border border-slate-200 bg-white p-4 dark:border-white/10 dark:bg-zinc-900/60">
                                        <div class="flex flex-col gap-4 xl:flex-row xl:items-center">
                                            <div class="min-w-0 flex-1">
                                                <div class="flex items-center gap-3">
                                                    <p class="font-semibold text-slate-950 dark:text-white">{{ $service->name }}</p>
                                                    <span class="rounded-full bg-slate-100 px-2 py-1 text-[11px] font-semibold uppercase tracking-wide text-slate-600 dark:bg-white/5 dark:text-zinc-400">{{ $service->service_id }}</span>
                                                </div>
                                                <p class="mt-1 text-sm text-slate-500 dark:text-zinc-500">{{ $service->description }}</p>
                                            </div>

                                            <div class="grid gap-3 sm:grid-cols-3 xl:w-[520px]">
                                                <div>
                                                    <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-zinc-300">Price</label>
                                                    <input type="number" step="0.01" min="0" name="services[{{ $service->id }}][price]" value="{{ old("services.$service->id.price", $service->price) }}" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-slate-900 outline-none focus:border-sky-500 dark:border-white/10 dark:bg-zinc-950 dark:text-white">
                                                </div>
                                                <div>
                                                    <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-zinc-300">Minimum plan</label>
                                                    <select name="services[{{ $service->id }}][minimum_plan]" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-slate-900 outline-none focus:border-sky-500 dark:border-white/10 dark:bg-zinc-950 dark:text-white">
                                                        @foreach ($planLevels as $key => $label)
                                                            <option value="{{ $key }}" @selected(old("services.$service->id.minimum_plan", $service->minimum_plan) === $key)>{{ $label }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="flex items-end">
                                                    <label class="flex w-full items-center justify-between rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 dark:border-white/10 dark:bg-white/5">
                                                        <span class="text-sm font-semibold text-slate-700 dark:text-zinc-300">Enabled</span>
                                                        <input type="checkbox" name="services[{{ $service->id }}][enabled]" value="1" class="h-5 w-5 rounded border-slate-300 text-sky-600 focus:ring-sky-500" @checked(old("services.$service->id.enabled", $service->enabled))>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="inline-flex h-11 items-center rounded-full bg-sky-600 px-6 text-sm font-semibold text-white transition hover:bg-sky-700 dark:bg-sky-700 dark:hover:bg-sky-800">
                        Save KYC Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
