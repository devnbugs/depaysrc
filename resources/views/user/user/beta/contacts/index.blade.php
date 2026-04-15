@extends($activeTemplate.'layouts.dashboard')

@section('content')
<section class="space-y-6">
    <div class="hero-surface p-6 sm:p-8">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div class="space-y-2">
                <p class="section-kicker">Saved Contacts</p>
                <h1 class="text-3xl font-semibold tracking-tight text-slate-950 dark:text-white sm:text-4xl">
                    Contact shortcuts for purchases
                </h1>
                <p class="max-w-2xl text-sm leading-6 text-slate-600 dark:text-zinc-300">
                    Keep your frequent airtime and data recipients in one place so checkout feels faster and cleaner.
                </p>
            </div>

            <a href="{{ $user->pin !== null ? route('user.beta.contacts.create') : '#' }}" class="inline-flex items-center justify-center rounded-full bg-slate-950 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800 dark:bg-white dark:text-slate-950 dark:hover:bg-slate-200">
                Create contact
            </a>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">
        <div class="panel-card overflow-hidden">
            <div class="border-b border-slate-200/80 px-6 py-5 dark:border-white/10">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <p class="section-kicker">Directory</p>
                        <h2 class="mt-2 section-title text-xl">Saved contacts</h2>
                    </div>
                    <span class="inline-flex items-center rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.22em] text-slate-600 dark:border-white/10 dark:bg-white/5 dark:text-zinc-300">
                        {{ $contacts->count() }} {{ \Illuminate\Support\Str::plural('contact', $contacts->count()) }}
                    </span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 dark:divide-white/10">
                    <thead class="bg-slate-50/80 dark:bg-white/5">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.18em] text-slate-500 dark:text-zinc-400">Name</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.18em] text-slate-500 dark:text-zinc-400">Phone</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.18em] text-slate-500 dark:text-zinc-400">Remark</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-[0.18em] text-slate-500 dark:text-zinc-400">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-white/10">
                        @forelse($contacts as $contact)
                            <tr class="bg-white/60 transition hover:bg-slate-50/90 dark:bg-transparent dark:hover:bg-white/5">
                                <td class="px-6 py-4">
                                    <div class="space-y-1">
                                        <p class="font-semibold text-slate-900 dark:text-white">{{ $contact->name }}</p>
                                        <p class="text-xs uppercase tracking-[0.2em] text-slate-400 dark:text-zinc-500">Saved contact</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span id="phone-{{ $contact->id }}" class="font-medium text-slate-900 dark:text-white">{{ $contact->phone }}</span>
                                        <button type="button" class="copy-chip" data-copy-value="{{ $contact->phone }}">
                                            Copy
                                        </button>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600 dark:text-zinc-300">
                                    {{ $contact->remark ?: 'No remark' }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <form action="{{ route('user.user.beta.contacts.destroy', ['id' => $contact->id]) }}" method="POST" data-confirm-form data-confirm-tone="danger" data-confirm-title="Delete contact" data-confirm-message="This contact will be removed from your saved list." data-confirm-accept-text="Delete" class="inline-flex">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center justify-center rounded-full border border-rose-200 bg-rose-50 px-4 py-2 text-sm font-semibold text-rose-700 transition hover:bg-rose-100 dark:border-rose-500/20 dark:bg-rose-500/10 dark:text-rose-300 dark:hover:bg-rose-500/20">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center">
                                    <div class="mx-auto max-w-md space-y-3">
                                        <p class="text-base font-semibold text-slate-900 dark:text-white">No saved contacts yet</p>
                                        <p class="text-sm text-slate-500 dark:text-zinc-400">
                                            Start by saving the phone numbers you use often for airtime and data purchases.
                                        </p>
                                        <a href="{{ $user->pin !== null ? route('user.beta.contacts.create') : '#' }}" class="inline-flex items-center justify-center rounded-full bg-slate-950 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800 dark:bg-white dark:text-slate-950 dark:hover:bg-slate-200">
                                            Add your first contact
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="space-y-4">
            <div class="panel-card p-6">
                <p class="section-kicker">Summary</p>
                <div class="mt-4 space-y-4">
                    <div class="rounded-2xl border border-slate-200 bg-slate-50/80 p-4 dark:border-white/10 dark:bg-white/5">
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-500 dark:text-zinc-400">Total saved</p>
                        <p class="mt-2 text-3xl font-semibold text-slate-950 dark:text-white">{{ $contacts->count() }}</p>
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-slate-50/80 p-4 text-sm leading-6 text-slate-600 dark:border-white/10 dark:bg-white/5 dark:text-zinc-300">
                        Use the copy button to quickly reuse a number elsewhere in the app, or remove stale entries whenever they are no longer needed.
                    </div>
                </div>
            </div>

            <div class="panel-card p-6">
                <p class="section-kicker">Quick actions</p>
                <div class="mt-4 space-y-3">
                    <a href="{{ route('user.airtime') }}" class="flex items-center justify-between rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-700 transition hover:bg-slate-100 dark:border-white/10 dark:bg-white/5 dark:text-zinc-200 dark:hover:bg-white/10">
                        <span>Buy airtime</span>
                        <span>&rarr;</span>
                    </a>
                    <a href="{{ route('user.internet') }}" class="flex items-center justify-between rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-700 transition hover:bg-slate-100 dark:border-white/10 dark:bg-white/5 dark:text-zinc-200 dark:hover:bg-white/10">
                        <span>Buy data</span>
                        <span>&rarr;</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
