@extends($activeTemplate.'layouts.dashboard')

@section('content')
<section class="space-y-6">
    <div class="hero-surface p-6 sm:p-8">
        <div class="space-y-2">
            <h1 class="text-3xl font-semibold tracking-tight text-slate-950 dark:text-white sm:text-4xl">
                Buy Data
            </h1>
            <p class="text-base text-slate-600 dark:text-zinc-400">
                Pick your network, choose the validity window you want, and complete your bundle purchase instantly.
            </p>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-[1.2fr_0.8fr]">
        <div class="panel-card p-6">
            <form method="POST" action="" id="purchase" class="space-y-6" data-busy-form data-busy-message="Processing your data purchase...">
                @csrf

                <div class="rounded-3xl border border-emerald-200 bg-emerald-50/90 p-4 dark:border-emerald-500/20 dark:bg-emerald-500/10">
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-emerald-700 dark:text-emerald-300">Wallet Balance</p>
                    <p class="mt-2 text-3xl font-semibold text-emerald-950 dark:text-emerald-50">
                        <span class="w-currency">{{ $general->cur_sym }}</span>{{ showAmount($user->balance) }}
                    </p>
                </div>

                <div class="space-y-2">
                    <label for="network" class="block text-sm font-medium text-slate-700 dark:text-zinc-200">
                        Network <span class="text-red-500">*</span>
                    </label>
                    <select name="network" id="network" required class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100 dark:border-white/10 dark:bg-zinc-950 dark:text-white dark:focus:border-sky-500 dark:focus:ring-sky-500/10">
                        <option value="" disabled selected>Choose a network</option>
                        @foreach($dataCatalog as $item)
                            <option value="{{ $item['network_code'] }}" @selected(old('network') === $item['network_code'])>{{ $item['network'] }}</option>
                        @endforeach
                    </select>
                    @error('network')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="validity" class="block text-sm font-medium text-slate-700 dark:text-zinc-200">
                        Validity <span class="text-red-500">*</span>
                    </label>
                    <select id="validity" required class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100 dark:border-white/10 dark:bg-zinc-950 dark:text-white dark:focus:border-sky-500 dark:focus:ring-sky-500/10">
                        <option value="" disabled selected>Select a validity group</option>
                    </select>
                </div>

                <div class="space-y-2">
                    <label for="plan" class="block text-sm font-medium text-slate-700 dark:text-zinc-200">
                        Plan <span class="text-red-500">*</span>
                    </label>
                    <select name="plan" id="plan" required class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100 dark:border-white/10 dark:bg-zinc-950 dark:text-white dark:focus:border-sky-500 dark:focus:ring-sky-500/10">
                        <option value="" disabled selected>Select a bundle</option>
                    </select>
                    <p id="planHint" class="text-xs text-slate-500 dark:text-zinc-400">Plans are grouped by validity automatically from the synced provider catalog.</p>
                    @error('plan')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="phone" class="block text-sm font-medium text-slate-700 dark:text-zinc-200">
                        Phone Number <span class="text-red-500">*</span>
                    </label>
                    <div class="flex gap-2">
                        <input type="tel" name="phone" id="phone" placeholder="08012345678" maxlength="11" value="{{ old('phone') }}" required class="flex-1 rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-sky-400 focus:ring-4 focus:ring-sky-100 dark:border-white/10 dark:bg-zinc-950 dark:text-white dark:placeholder:text-zinc-500 dark:focus:border-sky-500 dark:focus:ring-sky-500/10">
                        <button type="button" id="selectContact" class="shrink-0 px-4 py-3 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-white/10 dark:hover:bg-white/20 dark:text-white transition" title="Select from contacts">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                        </button>
                    </div>
                    @error('phone')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                @include('user.partials.security-verification', ['user' => $user])

                @include('user.partials.invisible-recaptcha', ['formId' => 'purchase'])

                <button type="submit" class="app-submit-button h-12 rounded-full bg-slate-950 font-semibold text-white transition hover:bg-slate-800 dark:bg-white dark:text-slate-950 dark:hover:bg-slate-200">
                    Buy Data
                </button>
            </form>
        </div>

        <div class="space-y-4">
            <div class="panel-card p-6">
                <p class="section-kicker">Quick Info</p>
                <h3 class="mt-3 section-title text-base">Successful Purchases</h3>
                <p class="mt-4 text-3xl font-semibold text-slate-950 dark:text-white">{{ $trxcount }}</p>
            </div>

            <div class="panel-card p-6">
                <p class="section-kicker">Catalog</p>
                <h3 class="mt-3 section-title text-base">Synced Networks</h3>
                <div class="mt-4 space-y-2">
                    @forelse($dataCatalog as $item)
                        <p class="text-sm text-slate-600 dark:text-zinc-300">{{ $item['network'] }}</p>
                    @empty
                        <p class="text-sm text-slate-500 dark:text-zinc-400">No data plans have been synced yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('script')
<script>
    const dataCatalog = @json($dataCatalog);
    const networkSelect = document.getElementById('network');
    const validitySelect = document.getElementById('validity');
    const planSelect = document.getElementById('plan');
    const initialValidity = @json(old('validity'));
    const initialPlan = @json(old('plan'));

    function resetSelect(select, placeholder) {
        select.innerHTML = '';
        const option = document.createElement('option');
        option.value = '';
        option.textContent = placeholder;
        option.disabled = true;
        option.selected = true;
        select.appendChild(option);
    }

    function selectedNetwork() {
        return dataCatalog.find((item) => item.network_code === networkSelect.value) || null;
    }

    function populateValidities() {
        resetSelect(validitySelect, 'Select a validity group');
        resetSelect(planSelect, 'Select a bundle');

        const network = selectedNetwork();
        if (!network) {
            return;
        }

        network.validities.forEach((group) => {
            const option = document.createElement('option');
            option.value = group.label;
            option.textContent = `${group.label} (${group.plans.length} plan${group.plans.length === 1 ? '' : 's'})`;
            option.selected = group.label === initialValidity;
            validitySelect.appendChild(option);
        });

        if (network.validities.length) {
            if (!initialValidity) {
                validitySelect.selectedIndex = 1;
            }
            validitySelect.disabled = false;
            populatePlans();
        }
    }

    function populatePlans() {
        resetSelect(planSelect, 'Select a bundle');

        const network = selectedNetwork();
        if (!network) {
            return;
        }

        const validityGroup = network.validities.find((group) => group.label === validitySelect.value) || null;
        if (!validityGroup) {
            return;
        }

        validityGroup.plans.forEach((plan) => {
            const option = document.createElement('option');
            option.value = plan.id;
            option.textContent = `${plan.name} - {{ $general->cur_sym }}${Number(plan.amount).toFixed(2)}`;
            option.selected = String(plan.id) === String(initialPlan);
            planSelect.appendChild(option);
        });
    }

    networkSelect?.addEventListener('change', populateValidities);
    validitySelect?.addEventListener('change', populatePlans);
    document.addEventListener('DOMContentLoaded', () => {
        if (networkSelect?.value) {
            populateValidities();
        }
    });
</script>
@endpush
