@extends($activeTemplate.'layouts.dashboard')

@section('content')
<section class="space-y-6">
    <!-- Header Section -->
    <div class="hero-surface p-6 sm:p-8">
        <div class="space-y-2">
            <h1 class="text-3xl font-semibold tracking-tight text-slate-950 dark:text-white sm:text-4xl">
                Buy Airtime
            </h1>
            <p class="text-base text-slate-600 dark:text-zinc-400">
                Recharge airtime for any network instantly with your wallet balance.
            </p>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-[1.2fr_0.8fr]">
        <!-- Main Form -->
        <div class="panel-card p-6">
            <form method="POST" action="" id="purchase" class="space-y-6" data-busy-form data-busy-message="Processing your airtime purchase...">
                @csrf

                <!-- Wallet Balance -->
                <div class="rounded-3xl border border-sky-200 bg-sky-50/90 p-4 dark:border-sky-500/20 dark:bg-sky-500/10">
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-sky-700 dark:text-sky-300">Wallet Balance</p>
                    <p class="mt-2 text-3xl font-semibold text-sky-950 dark:text-sky-50">
                        <span class="w-currency">{{ $general->cur_sym }}</span>{{ showAmount($user->balance) }}
                    </p>
                </div>

                <!-- Network Selection -->
                <div class="space-y-2">
                    <label for="network" class="block text-sm font-medium text-slate-700 dark:text-zinc-200">
                        Select Network <span class="text-red-500">*</span>
                    </label>
                    <select name="network" id="network" required class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100 dark:border-white/10 dark:bg-zinc-950 dark:text-white dark:focus:border-sky-500 dark:focus:ring-sky-500/10">
                        <option value="" disabled selected>Choose a network</option>
                        @foreach($network as $data)
                            <option value="{{ $data->symbol }}">{{ $data->name }}</option>
                        @endforeach
                    </select>
                    @error('network')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone Number -->
                <div class="space-y-2">
                    <label for="phone" class="block text-sm font-medium text-slate-700 dark:text-zinc-200">
                        Phone Number <span class="text-red-500">*</span>
                    </label>
                    <div class="flex gap-2">
                        <input type="tel" name="phone" id="phone" placeholder="08123456789" value="{{ old('phone') }}" required class="flex-1 rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-sky-400 focus:ring-4 focus:ring-sky-100 dark:border-white/10 dark:bg-zinc-950 dark:text-white dark:placeholder:text-zinc-500 dark:focus:border-sky-500 dark:focus:ring-sky-500/10">
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

                 <!-- Airtime Type (for resellers) -->
                <!--@if(in_array($user->usertype, [2, 3]))
                    <div class="space-y-2">
                        <label for="airtype" class="block text-sm font-medium text-slate-700 dark:text-zinc-200">
                            Airtime Type <span class="text-red-500">*</span>
                        </label>
                        <select id="airtype" name="airtype" required class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100 dark:border-white/10 dark:bg-zinc-950 dark:text-white dark:focus:border-sky-500 dark:focus:ring-sky-500/10">
                            <option value="" disabled selected>Select Type</option>
                            <option value="001">VTU (3% Discount)</option>
                            <option value="002">Share and Sell (3.5% Discount)</option>
                        </select>
                        @error('airtype')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                @endif-->

                <!-- Amount -->
                <div class="space-y-2">
                    <label for="amount" class="block text-sm font-medium text-slate-700 dark:text-zinc-200">
                        Amount <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm font-medium text-slate-500 dark:text-zinc-400">{{ $general->cur_sym }}</span>
                        <input type="number" name="amount" id="amount" placeholder="0.00" step="0.01" value="{{ old('amount') }}" required class="w-full rounded-2xl border border-slate-200 bg-white pl-8 pr-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-sky-400 focus:ring-4 focus:ring-sky-100 dark:border-white/10 dark:bg-zinc-950 dark:text-white dark:placeholder:text-zinc-500 dark:focus:border-sky-500 dark:focus:ring-sky-500/10">
                    </div>
                    @error('amount')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <!--div>
                    @error('amount')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div-->

                @include('user.partials.security-verification', ['user' => $user])

                @include('user.partials.invisible-recaptcha', ['formId' => 'purchase'])

                <!-- Submit Button -->
                <button type="submit" class="app-submit-button h-12 rounded-full bg-slate-950 font-semibold text-white transition hover:bg-slate-800 dark:bg-white dark:text-slate-950 dark:hover:bg-slate-200">
                    Buy Airtime
                </button>
            </form>
        </div>

        <!-- Sidebar Info -->
        <div class="space-y-4">
            <div class="panel-card p-6">
                <p class="section-kicker">Quick Info</p>
                <h3 class="mt-3 section-title text-base">Successful Purchases</h3>
                <p class="mt-4 text-3xl font-semibold text-slate-950 dark:text-white">{{ $trxcount }}</p>
            </div>

            <div class="panel-card p-6 space-y-3">
                <p class="section-kicker">Balance Codes</p>
                <div class="space-y-2">
                    <a href="tel:*310#" class="flex items-center justify-between rounded-2xl border border-slate-200 bg-slate-50 p-3 text-sm font-medium text-slate-700 hover:bg-slate-100 dark:border-white/10 dark:bg-white/5 dark:text-zinc-200 dark:hover:bg-white/10 transition">
                        <span>Check Balance</span>
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                            <polyline points="12 5 19 12 12 19"></polyline>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('script')
<script>

</script>
@endpush
