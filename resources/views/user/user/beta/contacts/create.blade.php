@extends($activeTemplate.'layouts.dashboard')

@section('content')
<section class="space-y-6">
    <div class="hero-surface p-6 sm:p-8">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div class="space-y-2">
                <p class="section-kicker">Saved Contacts</p>
                <h1 class="text-3xl font-semibold tracking-tight text-slate-950 dark:text-white sm:text-4xl">
                    Add a new contact
                </h1>
                <p class="max-w-2xl text-sm leading-6 text-slate-600 dark:text-zinc-300">
                    Save a frequently used phone number so airtime and data purchases feel quicker next time.
                </p>
            </div>

            <a href="{{ route('user.beta.contacts.index') }}" class="inline-flex items-center justify-center rounded-full border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:border-sky-200 hover:text-sky-700 dark:border-white/10 dark:bg-zinc-900 dark:text-zinc-200 dark:hover:border-sky-500/30 dark:hover:text-sky-300">
                Back to contacts
            </a>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-[1.1fr_0.9fr]">
        <div class="panel-card p-6 sm:p-8">
            <div class="mb-6 space-y-2">
                <h2 class="section-title text-xl">Contact details</h2>
                <p class="text-sm text-slate-500 dark:text-zinc-400">
                    Add the name, phone number, and an optional note for easy recognition later.
                </p>
            </div>

            <form method="POST" action="{{ route('user.beta.contacts.create') }}" class="space-y-5">
                @csrf

                <div class="space-y-2">
                    <label for="name" class="text-sm font-medium text-slate-700 dark:text-zinc-200">Name</label>
                    <input id="name" name="name" type="text" value="{{ old('name') }}" placeholder="John Doe" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-sky-400 focus:ring-4 focus:ring-sky-100 dark:border-white/10 dark:bg-zinc-950 dark:text-white dark:placeholder:text-zinc-500 dark:focus:border-sky-500 dark:focus:ring-sky-500/10">
                </div>

                <div class="space-y-2">
                    <label for="phone" class="text-sm font-medium text-slate-700 dark:text-zinc-200">Phone number</label>
                    <input id="phone" name="phone" type="tel" inputmode="numeric" value="{{ old('phone') }}" placeholder="08012345678" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-sky-400 focus:ring-4 focus:ring-sky-100 dark:border-white/10 dark:bg-zinc-950 dark:text-white dark:placeholder:text-zinc-500 dark:focus:border-sky-500 dark:focus:ring-sky-500/10">
                </div>

                <div class="space-y-2">
                    <label for="remark" class="text-sm font-medium text-slate-700 dark:text-zinc-200">Remark</label>
                    <input id="remark" name="remark" type="text" value="{{ old('remark') }}" placeholder="MTN main line, family line, office number..." class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-sky-400 focus:ring-4 focus:ring-sky-100 dark:border-white/10 dark:bg-zinc-950 dark:text-white dark:placeholder:text-zinc-500 dark:focus:border-sky-500 dark:focus:ring-sky-500/10">
                </div>

                <button type="submit" class="app-submit-button inline-flex h-12 rounded-full bg-slate-950 px-6 text-sm font-semibold text-white transition hover:bg-slate-800 dark:bg-white dark:text-slate-950 dark:hover:bg-slate-200">
                    Save contact
                </button>
            </form>
        </div>

        <div class="space-y-4">
            <div class="panel-card p-6">
                <p class="section-kicker">Why save contacts?</p>
                <div class="mt-4 space-y-4 text-sm leading-6 text-slate-600 dark:text-zinc-300">
                    <p>Reuse phone numbers quickly across airtime and data forms without typing them again.</p>
                    <p>Attach a short remark so you can tell personal, family, and business lines apart at a glance.</p>
                </div>
            </div>

            <div class="panel-card p-6">
                <p class="section-kicker">Tips</p>
                <ul class="mt-4 space-y-3 text-sm text-slate-600 dark:text-zinc-300">
                    <li>Use a clear display name you will recognize during checkout.</li>
                    <li>Keep phone numbers in their standard local format for faster reuse.</li>
                    <li>Short remarks work best for network, role, or owner labels.</li>
                </ul>
            </div>
        </div>
    </div>
</section>
@endsection
