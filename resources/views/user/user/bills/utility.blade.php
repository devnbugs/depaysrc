@extends($activeTemplate.'layouts.dashboard')

@section('content')
<section class="space-y-6">
    <!-- Header Section -->
    <div class="hero-surface p-6 sm:p-8">
        <div class="space-y-2">
            <h1 class="text-3xl font-semibold tracking-tight text-slate-950 dark:text-white sm:text-4xl">
                Pay Utilities
            </h1>
            <p class="text-base text-slate-600 dark:text-zinc-400">
                Pay your electricity bills and other utility payments instantly.
            </p>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-[1.2fr_0.8fr]">
        <!-- Main Form -->
        <div class="panel-card p-6">
            <form method="POST" action="" id="purchase" class="space-y-6" data-busy-form data-busy-message="Processing your utility payment...">
                @csrf

                <!-- Wallet Balance -->
                <div class="rounded-3xl border border-amber-200 bg-amber-50/90 p-4 dark:border-amber-500/20 dark:bg-amber-500/10">
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-amber-700 dark:text-amber-300">Wallet Balance</p>
                    <p class="mt-2 text-3xl font-semibold text-amber-950 dark:text-amber-50">
                        <span class="w-currency">{{ $general->cur_sym }}</span>{{ showAmount($user->balance) }}
                    </p>
                </div>

                <!-- Company Selection -->
                <div class="space-y-2">
                    <label for="company" class="block text-sm font-medium text-slate-700 dark:text-zinc-200">
                        Select Company <span class="text-red-500">*</span>
                    </label>
                    <select name="company" id="company" required class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100 dark:border-white/10 dark:bg-zinc-950 dark:text-white dark:focus:border-sky-500 dark:focus:ring-sky-500/10">
                        <option value="" disabled selected>Choose a company</option>
                        @foreach($network as $data)
                            <option value="{{ $data->billercode }}">{{ $data->name }}</option>
                        @endforeach
                    </select>
                    @error('company')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Meter Type -->
                <div class="space-y-2">
                    <label for="type" class="block text-sm font-medium text-slate-700 dark:text-zinc-200">
                        Meter Type <span class="text-red-500">*</span>
                    </label>
                    <select name="type" id="type" required class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100 dark:border-white/10 dark:bg-zinc-950 dark:text-white dark:focus:border-sky-500 dark:focus:ring-sky-500/10">
                        <option value="" disabled selected>Select meter type</option>
                        <option value="prepaid">Prepaid</option>
                        <option value="postpaid">Postpaid</option>
                    </select>
                    @error('type')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Meter Number -->
                <div class="space-y-2">
                    <label for="number" class="block text-sm font-medium text-slate-700 dark:text-zinc-200">
                        Meter Number <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="number" id="number" placeholder="1234567890" value="{{ old('number') }}" required class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-sky-400 focus:ring-4 focus:ring-sky-100 dark:border-white/10 dark:bg-zinc-950 dark:text-white dark:placeholder:text-zinc-500 dark:focus:border-sky-500 dark:focus:ring-sky-500/10">
                    @error('number')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

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

                @include('user.partials.invisible-recaptcha', ['formId' => 'purchase'])

                <!-- Submit Button -->
                <button type="submit" class="w-full h-12 rounded-full bg-slate-950 font-semibold text-white transition hover:bg-slate-800 dark:bg-white dark:text-slate-950 dark:hover:bg-slate-200">
                    Pay Utility
                </button>
            </form>
        </div>

        <!-- Sidebar Info -->
        <div class="space-y-4">
            <div class="panel-card p-6">
                <p class="section-kicker">Quick Info</p>
                <h3 class="mt-3 section-title text-base">Supported Companies</h3>
                <div class="mt-4 space-y-2">
                    @foreach($network->take(3) as $company)
                        <p class="text-sm text-slate-600 dark:text-zinc-300">• {{ $company->name }}</p>
                    @endforeach
                    @if($network->count() > 3)
                        <p class="text-xs text-slate-500 dark:text-zinc-400 italic">+{{ $network->count() - 3 }} more</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

