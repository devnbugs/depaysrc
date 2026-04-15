@extends($activeTemplate.'layouts.dashboard')

@section('content')
<section class="space-y-6">
    <div class="hero-surface p-6 sm:p-8">
        <div class="space-y-2">
            <h1 class="text-3xl font-semibold tracking-tight text-slate-950 dark:text-white sm:text-4xl">
                Subscribe to Cable TV
            </h1>
            <p class="text-base text-slate-600 dark:text-zinc-400">
                Select a decoder provider, pick a package from the live catalog, and renew instantly.
            </p>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-[1.2fr_0.8fr]">
        <div class="panel-card p-6">
            <form method="POST" action="" id="purchase" class="space-y-6" data-busy-form data-busy-message="Processing your cable TV subscription...">
                @csrf

                <div class="rounded-3xl border border-rose-200 bg-rose-50/90 p-4 dark:border-rose-500/20 dark:bg-rose-500/10">
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-rose-700 dark:text-rose-300">Wallet Balance</p>
                    <p class="mt-2 text-3xl font-semibold text-rose-950 dark:text-rose-50">
                        <span class="w-currency">{{ $general->cur_sym }}</span>{{ showAmount($user->balance) }}
                    </p>
                </div>

                <div class="space-y-2">
                    <label for="decoder" class="block text-sm font-medium text-slate-700 dark:text-zinc-200">
                        Decoder Provider <span class="text-red-500">*</span>
                    </label>
                    <select name="decoder" id="decoder" required class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100 dark:border-white/10 dark:bg-zinc-950 dark:text-white dark:focus:border-sky-500 dark:focus:ring-sky-500/10">
                        <option value="" disabled selected>Choose a decoder</option>
                        @foreach($network as $data)
                            <option value="{{ $data->code }}" @selected(old('decoder') === $data->code)>{{ $data->name }}</option>
                        @endforeach
                    </select>
                    @error('decoder')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="plan" class="block text-sm font-medium text-slate-700 dark:text-zinc-200">
                        Package <span class="text-red-500">*</span>
                    </label>
                    <select name="plan" id="plan" required class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100 dark:border-white/10 dark:bg-zinc-950 dark:text-white dark:focus:border-sky-500 dark:focus:ring-sky-500/10">
                        <option value="" disabled selected>Select subscription plan</option>
                    </select>
                    @error('plan')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="number" class="block text-sm font-medium text-slate-700 dark:text-zinc-200">
                        Decoder Number <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="number" id="number" placeholder="1234567890" value="{{ old('number') }}" required class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-sky-400 focus:ring-4 focus:ring-sky-100 dark:border-white/10 dark:bg-zinc-950 dark:text-white dark:placeholder:text-zinc-500 dark:focus:border-sky-500 dark:focus:ring-sky-500/10">
                    @error('number')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>



                @include('user.partials.invisible-recaptcha', ['formId' => 'purchase'])

                <button type="submit" class="w-full h-12 rounded-full bg-slate-950 font-semibold text-white transition hover:bg-slate-800 dark:bg-white dark:text-slate-950 dark:hover:bg-slate-200">
                    Continue to Validation
                </button>
            </form>
        </div>

        <div class="space-y-4">
            <div class="panel-card p-6">
                <p class="section-kicker">Available Decoders</p>
                <h3 class="mt-3 section-title text-base">Live Providers</h3>
                <div class="mt-4 space-y-2">
                    @forelse($network as $provider)
                        <p class="text-sm text-slate-600 dark:text-zinc-300">{{ $provider->name }}</p>
                    @empty
                        <p class="text-sm text-slate-500 dark:text-zinc-400">No cable plans have been synced yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('script')
<script>
    const cableBundles = @json($bill->groupBy('networkcode')->map(fn ($items) => $items->map(fn ($bundle) => ['id' => $bundle->plan, 'name' => $bundle->name, 'amount' => (float) $bundle->cost])->values())->all());
    const decoderSelect = document.getElementById('decoder');
    const planSelect = document.getElementById('plan');
    const initialPlan = @json(old('plan'));


    function populatePackages() {
        planSelect.innerHTML = '';
        const placeholder = document.createElement('option');
        placeholder.value = '';
        placeholder.textContent = 'Select subscription plan';
        placeholder.disabled = true;
        placeholder.selected = true;
        planSelect.appendChild(placeholder);

        const packages = cableBundles[decoderSelect.value] || [];
        packages.forEach((item) => {
            const option = document.createElement('option');
            option.value = item.id;
            option.textContent = `${item.name} - {{ $general->cur_sym }}${Number(item.amount).toFixed(2)}`;
            option.selected = String(item.id) === String(initialPlan);
            planSelect.appendChild(option);
        });
    }

    decoderSelect?.addEventListener('change', populatePackages);
    document.addEventListener('DOMContentLoaded', () => {
        if (decoderSelect?.value) {
            populatePackages();
        }
    });
</script>
@endpush
