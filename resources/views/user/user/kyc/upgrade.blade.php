@extends($activeTemplate.'layouts.dashboard')

@section('content')
    <section class="space-y-8">
        <div class="hero-surface p-6 sm:p-8">
            <div class="space-y-4">
                <div class="flex flex-wrap gap-2">
                    <span class="section-kicker">Service</span>
                    <span class="section-kicker">Subscription</span>
                </div>

                <h2 class="max-w-3xl text-3xl font-semibold tracking-tight text-slate-950 dark:text-white sm:text-4xl">
                    KYC Services Upgrade
                </h2>
                <p class="max-w-3xl section-copy">
                    Subscribe with Paystack to unlock KYC verification services and keep your access active on the selected billing cycle.
                </p>
            </div>
        </div>

        @if ($activePlan)
            <div class="mx-6 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 dark:border-emerald-800/50 dark:bg-emerald-900/30">
                <div class="flex gap-3">
                    <svg class="h-6 w-6 flex-shrink-0 text-emerald-600 dark:text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    <div>
                        <h3 class="font-semibold text-emerald-900 dark:text-emerald-200">Current subscription</h3>
                        <p class="mt-1 text-sm text-emerald-800 dark:text-emerald-300">
                            You are on the <strong>{{ $activePlan->name }}</strong> plan{{ $user->kyc_subscription_status ? ' with status '.str_replace('-', ' ', $user->kyc_subscription_status) : '' }}.
                        </p>
                    </div>
                </div>
            </div>
        @endif

        @if (! $settings['enabled'] || ! $settings['paystack_secret_configured'])
            <div class="mx-6 rounded-2xl border border-amber-200 bg-amber-50 p-4 dark:border-amber-800/50 dark:bg-amber-900/30">
                <div class="flex gap-3">
                    <svg class="mt-0.5 h-6 w-6 flex-shrink-0 text-amber-600 dark:text-amber-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 9v4"></path>
                        <path d="M12 17h.01"></path>
                        <path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                    </svg>
                    <div>
                        <h3 class="font-semibold text-amber-900 dark:text-amber-200">Subscription checkout is not ready</h3>
                        <p class="mt-1 text-sm text-amber-800 dark:text-amber-300">
                            Admin still needs to enable KYC subscriptions and complete the Paystack configuration before checkout can start.
                        </p>
                    </div>
                </div>
            </div>
        @endif

        @if (! $isFundingEligible)
            <div class="mx-6 rounded-2xl border border-sky-200 bg-sky-50 p-4 dark:border-sky-800/50 dark:bg-sky-900/30">
                <div class="flex gap-3">
                    <svg class="mt-0.5 h-6 w-6 flex-shrink-0 text-sky-600 dark:text-sky-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 8v4"></path>
                        <path d="M12 16h.01"></path>
                        <circle cx="12" cy="12" r="10"></circle>
                    </svg>
                    <div>
                        <h3 class="font-semibold text-sky-900 dark:text-sky-100">Fund your wallet before starting KYC</h3>
                        <p class="mt-1 text-sm text-sky-800 dark:text-sky-200">
                            You have funded ₦{{ number_format((float) $fundedAmount, 2) }} so far. The minimum required amount is ₦{{ number_format((float) $minimumFundingAmount, 2) }}.
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <section class="px-6">
            <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-3">
                @foreach ($plans as $plan)
                    @php
                        $isCurrentPlan = $user->kyc_plan === $plan->key && $user->is_kyc_upgraded;
                    @endphp

                    <article class="panel-card flex flex-col space-y-6 p-6 {{ $plan->badge ? 'ring-2 ring-sky-500 dark:ring-sky-400' : '' }}">
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-xl font-semibold text-slate-950 dark:text-white">{{ $plan->name }}</h3>
                                @if ($plan->badge)
                                    <span class="inline-flex items-center rounded-full bg-sky-100 px-2 py-1 text-xs font-semibold text-sky-700 dark:bg-sky-900/30 dark:text-sky-300">
                                        {{ $plan->badge }}
                                    </span>
                                @endif
                            </div>
                            <p class="mt-2 text-sm text-slate-600 dark:text-zinc-400">{{ $plan->description }}</p>
                        </div>

                        <div class="space-y-2 border-y border-slate-200 py-6 dark:border-white/10">
                            <div class="text-4xl font-bold text-slate-950 dark:text-white">
                                ₦{{ number_format((float) $plan->price) }}
                                <span class="text-lg font-normal text-slate-600 dark:text-zinc-400">/{{ $plan->paystack_interval }}</span>
                            </div>
                            <p class="text-sm text-slate-600 dark:text-zinc-400">
                                {{ (float) $plan->monthly_limit >= 999999999 ? 'Unlimited monthly usage' : 'Monthly limit: ₦'.number_format((float) $plan->monthly_limit) }}
                            </p>
                        </div>

                        <ul class="space-y-3">
                            @foreach (($plan->features ?? []) as $feature)
                                <li class="flex gap-3">
                                    <svg class="mt-0.5 h-5 w-5 flex-shrink-0 text-sky-600 dark:text-sky-400" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M9 16.17 4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"></path>
                                    </svg>
                                    <span class="text-sm text-slate-700 dark:text-zinc-300">{{ $feature }}</span>
                                </li>
                            @endforeach
                        </ul>

                        <button
                            type="button"
                            class="mt-auto inline-flex h-11 items-center justify-center rounded-full px-6 text-sm font-semibold transition {{ $isCurrentPlan ? 'cursor-not-allowed bg-slate-300 text-slate-700 dark:bg-slate-700 dark:text-zinc-300' : 'bg-sky-600 text-white hover:bg-sky-700 dark:bg-sky-500 dark:hover:bg-sky-600' }}"
                            @disabled($isCurrentPlan || ! $settings['enabled'] || ! $settings['paystack_secret_configured'] || ! $isFundingEligible)
                            onclick="openPlanModal('{{ $plan->key }}', '{{ addslashes($plan->name) }}', '{{ number_format((float) $plan->price) }}', '{{ $plan->paystack_interval }}')"
                        >
                            {{ $isCurrentPlan ? 'Current Plan' : ($isFundingEligible ? 'Subscribe With Paystack' : 'Fund Wallet First') }}
                        </button>
                    </article>
                @endforeach
            </div>
        </section>

        <div class="flex justify-end px-6">
            <a href="{{ route('user.kyc.services') }}" class="inline-flex h-11 items-center rounded-full border border-slate-200 bg-white px-6 text-sm font-semibold text-slate-700 transition hover:border-sky-200 hover:text-sky-700 dark:border-white/10 dark:bg-zinc-900 dark:text-zinc-200 dark:hover:border-sky-500/30 dark:hover:text-sky-300">
                Back to KYC Services
            </a>
        </div>
    </section>

    <div class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 px-4 dark:bg-black/80" id="planModal">
        <div class="w-full max-w-md rounded-2xl bg-white p-6 dark:bg-zinc-900">
            <h3 class="text-2xl font-semibold text-slate-950 dark:text-white">Confirm subscription</h3>
            <p class="mt-3 text-sm text-slate-600 dark:text-zinc-400">
                You are about to subscribe to <strong id="planName" class="text-slate-900 dark:text-white"></strong> for
                <strong class="text-slate-900 dark:text-white">₦<span id="planPrice"></span>/<span id="planInterval"></span></strong>.
            </p>

            <form action="{{ route('user.kyc.upgrade.process') }}" method="POST" class="mt-6 space-y-5">
                @csrf
                <input type="hidden" name="plan" id="planIdInput">

                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-600 dark:border-white/10 dark:bg-white/5 dark:text-zinc-400">
                    Paystack will charge the first billing cycle immediately and automatically create the recurring subscription after payment succeeds.
                </div>

                <div class="flex gap-3">
                    <button type="button" onclick="closePlanModal()" class="flex-1 inline-flex h-11 items-center justify-center rounded-full border border-slate-200 bg-white px-6 text-sm font-semibold text-slate-700 transition hover:border-sky-200 hover:text-sky-700 dark:border-white/10 dark:bg-zinc-900 dark:text-zinc-200 dark:hover:border-sky-500/30 dark:hover:text-sky-300">
                        Cancel
                    </button>
                    <button type="submit" class="flex-1 inline-flex h-11 items-center justify-center rounded-full bg-sky-600 px-6 text-sm font-semibold text-white transition hover:bg-sky-700 dark:bg-sky-500 dark:hover:bg-sky-600">
                        Proceed to Paystack
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('script')
    <script>
        function openPlanModal(plan, name, price, interval) {
            document.getElementById('planName').textContent = name;
            document.getElementById('planPrice').textContent = price;
            document.getElementById('planInterval').textContent = interval;
            document.getElementById('planIdInput').value = plan;
            document.getElementById('planModal').classList.remove('hidden');
            document.getElementById('planModal').classList.add('flex');
        }

        function closePlanModal() {
            document.getElementById('planModal').classList.add('hidden');
            document.getElementById('planModal').classList.remove('flex');
        }

        document.getElementById('planModal').addEventListener('click', function (event) {
            if (event.target === this) {
                closePlanModal();
            }
        });
    </script>
@endpush
