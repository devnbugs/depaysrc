@extends($activeTemplate.'layouts.dashboard')

@section('content')
<section class="space-y-6">
    <div class="hero-surface p-6 sm:p-8">
        <div class="space-y-2">
            <p class="section-kicker">Security</p>
            <h1 class="text-3xl font-semibold tracking-tight text-slate-950 dark:text-white sm:text-4xl">
                Two-factor authenticator
            </h1>
            <p class="max-w-2xl text-sm leading-6 text-slate-600 dark:text-zinc-300">
                Add an authenticator app for a stronger sign-in layer and payment confirmation coverage where enabled.
            </p>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-[1.1fr_0.9fr]">
        <div class="panel-card p-6 sm:p-8">
            @if(Auth::user()->ts)
                <div class="space-y-5">
                    <div class="inline-flex items-center rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.22em] text-emerald-700 dark:border-emerald-500/20 dark:bg-emerald-500/10 dark:text-emerald-300">
                        Enabled
                    </div>

                    <div class="space-y-2">
                        <h2 class="section-title text-xl">Authenticator is active</h2>
                        <p class="text-sm leading-6 text-slate-600 dark:text-zinc-300">
                            Your account is already protected with an authenticator app. Keep your recovery method stored somewhere safe before disabling it.
                        </p>
                    </div>

                    <div class="rounded-3xl border border-slate-200 bg-slate-50/80 p-5 text-sm leading-6 text-slate-600 dark:border-white/10 dark:bg-white/5 dark:text-zinc-300">
                        Use Google Authenticator or any compatible TOTP app to continue generating your verification codes.
                        <a class="ml-1 font-semibold text-sky-600 hover:text-sky-700 dark:text-sky-400 dark:hover:text-sky-300" href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2&hl=en" target="_blank" rel="noopener">
                            App link
                        </a>
                    </div>

                    <button type="button" class="inline-flex h-12 items-center justify-center rounded-full border border-rose-200 bg-rose-50 px-6 text-sm font-semibold text-rose-700 transition hover:bg-rose-100 dark:border-rose-500/20 dark:bg-rose-500/10 dark:text-rose-300 dark:hover:bg-rose-500/20" data-bs-toggle="modal" data-bs-target="#disableModal">
                        Disable two-factor authenticator
                    </button>
                </div>
            @else
                <div class="space-y-6">
                    <div class="space-y-2">
                        <div class="inline-flex items-center rounded-full border border-amber-200 bg-amber-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.22em] text-amber-700 dark:border-amber-500/20 dark:bg-amber-500/10 dark:text-amber-300">
                            Setup required
                        </div>
                        <h2 class="section-title text-xl">Scan your QR code</h2>
                        <p class="text-sm leading-6 text-slate-600 dark:text-zinc-300">
                            Scan this QR code with Google Authenticator or another compatible authenticator app, then verify the generated code below.
                        </p>
                    </div>

                    <div class="flex justify-center rounded-3xl border border-slate-200 bg-white p-6 dark:border-white/10 dark:bg-zinc-950">
                        <img class="h-56 w-56 max-w-full rounded-2xl object-contain" src="{{ $qrCodeUrl }}" alt="Two-factor QR code" loading="lazy" referrerpolicy="no-referrer">
                    </div>

                    <div class="space-y-2">
                        <label for="referralURL" class="text-sm font-medium text-slate-700 dark:text-zinc-200">Manual secret key</label>
                        <div class="flex flex-col gap-3 sm:flex-row">
                            <input type="text" name="key" value="{{ $secret }}" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition dark:border-white/10 dark:bg-zinc-950 dark:text-white" id="referralURL" readonly>
                            <button type="button" class="copytext inline-flex items-center justify-center rounded-full border border-slate-200 bg-slate-50 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 dark:border-white/10 dark:bg-white/5 dark:text-zinc-200 dark:hover:bg-white/10">
                                Copy key
                            </button>
                        </div>
                    </div>

                    <button type="button" class="inline-flex h-12 items-center justify-center rounded-full bg-slate-950 px-6 text-sm font-semibold text-white transition hover:bg-slate-800 dark:bg-white dark:text-slate-950 dark:hover:bg-slate-200" data-bs-toggle="modal" data-bs-target="#enableModal">
                        Enable two-factor authenticator
                    </button>
                </div>
            @endif
        </div>

        <div class="space-y-4">
            <div class="panel-card p-6">
                <p class="section-kicker">Recommended app</p>
                <p class="mt-4 text-sm leading-6 text-slate-600 dark:text-zinc-300">
                    Google Authenticator works well, but any standard TOTP app can scan the QR and generate the same time-based code.
                </p>
                <a class="mt-4 inline-flex items-center justify-center rounded-full border border-slate-200 bg-slate-50 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 dark:border-white/10 dark:bg-white/5 dark:text-zinc-200 dark:hover:bg-white/10" href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2&hl=en" target="_blank" rel="noopener">
                    Open authenticator app link
                </a>
            </div>

            <div class="panel-card p-6">
                <p class="section-kicker">Keep safe</p>
                <div class="mt-4 space-y-3 text-sm leading-6 text-slate-600 dark:text-zinc-300">
                    <p>Store your secret or backup method somewhere private before you switch devices.</p>
                    <p>If you disable two-factor protection, sign-in and purchase verification becomes less secure.</p>
                </div>
            </div>
        </div>
    </div>

    <div id="enableModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">@lang('Verify your code')</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal">
                        <i class="fa fa-times"></i>
                    </button>
                </div>
                <form action="{{ route('user.twofactor.enable') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="space-y-2">
                            <input type="hidden" name="key" value="{{ $secret }}">
                            <input type="text" class="form-control" name="code" placeholder="@lang('Enter Google Authenticator Code')">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-warning me-1 mt-1" data-bs-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn--primary text-white me-1 mt-1">@lang('Verify')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="disableModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">@lang('Disable authenticator')</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal">
                        <i class="fa fa-times"></i>
                    </button>
                </div>
                <form action="{{ route('user.twofactor.disable') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="space-y-2">
                            <input type="text" class="form-control" name="code" placeholder="@lang('Enter Google Authenticator Code')">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-warning me-1 mt-1" data-bs-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn text-white btn--primary me-1 mt-1">@lang('Verify')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection

@push('script')
    <script>
        (function($){
            'use strict';

            $('.copytext').on('click', function() {
                var copyText = document.getElementById('referralURL');
                copyText.select();
                copyText.setSelectionRange(0, 99999);
                document.execCommand('copy');
                iziToast.success({message: 'Copied: ' + copyText.value, position: 'topRight'});
            });
        })(jQuery);
    </script>
@endpush
