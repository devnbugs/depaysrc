<div class="space-y-6">
    @if (session('passkey-status'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-200">
            {{ session('passkey-status') }}
        </div>
    @endif

    <div class="grid gap-6 lg:grid-cols-[0.95fr_1.05fr]">
        <div class="space-y-4 rounded-3xl border border-slate-200 bg-slate-50/70 p-5 dark:border-white/10 dark:bg-white/5">
            <div>
                <p class="section-kicker">Create Passkey</p>
                <h3 class="mt-3 text-xl font-semibold text-slate-950 dark:text-white">Add a new device</h3>
                <p class="mt-2 text-sm leading-6 text-slate-600 dark:text-zinc-400">
                    Give the passkey a label you will recognize later, then approve the browser prompt on this device.
                </p>
            </div>

            <form id="passkeyForm" wire:submit="validatePasskeyProperties" class="space-y-4">
                <div class="space-y-2">
                    <label for="passkey-name" class="text-sm font-medium text-slate-700 dark:text-zinc-200">Passkey label</label>
                    <input
                        id="passkey-name"
                        type="text"
                        wire:model="name"
                        autocomplete="off"
                        placeholder="e.g. Rabiu's iPhone"
                        class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-sky-400 focus:ring-4 focus:ring-sky-100 dark:border-white/10 dark:bg-zinc-950 dark:text-white dark:placeholder:text-zinc-500 dark:focus:border-sky-500 dark:focus:ring-sky-500/10"
                    >
                    @error('name', 'passkeyForm')
                        <p class="text-sm text-rose-600 dark:text-rose-300">{{ $message }}</p>
                    @enderror
                    @error('name')
                        <p class="text-sm text-rose-600 dark:text-rose-300">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="inline-flex h-11 items-center rounded-full bg-sky-600 px-6 text-sm font-semibold text-white transition hover:bg-sky-700 dark:bg-sky-500 dark:hover:bg-sky-600">
                    Create passkey
                </button>
            </form>

            <div class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-600 dark:border-white/10 dark:bg-zinc-950 dark:text-zinc-400">
                Keep at least one backup sign-in method active before removing every passkey from your account.
            </div>
        </div>

        <div class="space-y-4">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="section-kicker">Your Devices</p>
                    <h3 class="mt-3 text-xl font-semibold text-slate-950 dark:text-white">Registered passkeys</h3>
                </div>

                @if ($passkeys->isNotEmpty())
                    <form method="POST" action="{{ route('user.passkey.disable') }}" onsubmit="return confirm('Remove every registered passkey from your account?')">
                        @csrf
                        <button type="submit" class="inline-flex h-10 items-center rounded-full border border-rose-200 bg-white px-4 text-sm font-semibold text-rose-700 transition hover:bg-rose-50 dark:border-rose-500/30 dark:bg-zinc-950 dark:text-rose-300 dark:hover:bg-rose-500/10">
                            Remove all
                        </button>
                    </form>
                @endif
            </div>

            <div class="space-y-3">
                @forelse ($passkeys as $passkey)
                    <article class="rounded-2xl border border-slate-200 bg-white p-4 dark:border-white/10 dark:bg-zinc-950/70">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                            <div class="space-y-2">
                                <h4 class="text-sm font-semibold text-slate-950 dark:text-white">{{ $passkey->name }}</h4>
                                <div class="flex flex-wrap gap-2 text-xs text-slate-500 dark:text-zinc-400">
                                    <span>Added {{ $passkey->created_at?->format('d M Y, h:i A') }}</span>
                                    <span>Last used {{ $passkey->last_used_at?->diffForHumans() ?? 'Not used yet' }}</span>
                                </div>
                            </div>

                            <button
                                type="button"
                                wire:click="deletePasskey({{ $passkey->id }})"
                                wire:confirm="Remove this passkey from your account?"
                                class="inline-flex h-10 items-center rounded-full border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700 transition hover:border-rose-200 hover:text-rose-700 dark:border-white/10 dark:bg-zinc-950 dark:text-zinc-200 dark:hover:border-rose-500/30 dark:hover:text-rose-300"
                            >
                                Remove
                            </button>
                        </div>
                    </article>
                @empty
                    <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-4 py-8 text-center text-sm text-slate-600 dark:border-white/15 dark:bg-white/5 dark:text-zinc-400">
                        No passkeys registered yet. Create your first one on this page.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    @include('passkeys::livewire.partials.createScript')
</div>
