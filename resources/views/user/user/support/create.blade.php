@extends($activeTemplate.'layouts.dashboard')

@section('content')
<section class="space-y-6">
    <div class="hero-surface p-6 sm:p-8">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div class="space-y-2">
                <p class="section-kicker">Support Desk</p>
                <h1 class="text-3xl font-semibold tracking-tight text-slate-950 dark:text-white sm:text-4xl">
                    Open a support ticket
                </h1>
                <p class="max-w-2xl text-sm leading-6 text-slate-600 dark:text-zinc-300">
                    Share the issue clearly, attach helpful files when needed, and the team can review everything from a single thread.
                </p>
            </div>

            <a href="{{ route('user.support') }}" class="inline-flex items-center justify-center rounded-full border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:border-sky-200 hover:text-sky-700 dark:border-white/10 dark:bg-zinc-900 dark:text-zinc-200 dark:hover:border-sky-500/30 dark:hover:text-sky-300">
                Back to tickets
            </a>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-[1.15fr_0.85fr]">
        <div class="panel-card p-6 sm:p-8">
            <div class="mb-6 space-y-2">
                <h2 class="section-title text-xl">Ticket details</h2>
                <p class="text-sm text-slate-500 dark:text-zinc-400">
                    The more specific the subject and message are, the easier it is to resolve the request quickly.
                </p>
            </div>

            <form action="{{ route('user.ticket.create') }}" method="post" enctype="multipart/form-data" class="space-y-5" onsubmit="return submitUserForm();">
                @csrf

                <div class="grid gap-5 md:grid-cols-2">
                    <div class="space-y-2">
                        <label for="department" class="text-sm font-medium text-slate-700 dark:text-zinc-200">Desk</label>
                        <select name="department" id="department" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100 dark:border-white/10 dark:bg-zinc-950 dark:text-white dark:focus:border-sky-500 dark:focus:ring-sky-500/10">
                            @foreach($topics as $data)
                                <option value="{{ $data->id }}">{{ $data->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label for="priority" class="text-sm font-medium text-slate-700 dark:text-zinc-200">Priority</label>
                        <select name="priority" id="priority" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100 dark:border-white/10 dark:bg-zinc-950 dark:text-white dark:focus:border-sky-500 dark:focus:ring-sky-500/10">
                            <option value="High">High</option>
                            <option value="Medium">Medium</option>
                            <option value="Low">Low</option>
                        </select>
                    </div>
                </div>

                <div class="space-y-2">
                    <label for="subject" class="text-sm font-medium text-slate-700 dark:text-zinc-200">Subject</label>
                    <input id="subject" type="text" name="subject" value="{{ old('subject') }}" placeholder="Tell us what you need help with" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-sky-400 focus:ring-4 focus:ring-sky-100 dark:border-white/10 dark:bg-zinc-950 dark:text-white dark:placeholder:text-zinc-500 dark:focus:border-sky-500 dark:focus:ring-sky-500/10">
                </div>

                <div class="space-y-2">
                    <label for="textarea-counter" class="text-sm font-medium text-slate-700 dark:text-zinc-200">Message</label>
                    <textarea
                        data-length="100"
                        class="char-textarea min-h-[140px] w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-sky-400 focus:ring-4 focus:ring-sky-100 dark:border-white/10 dark:bg-zinc-950 dark:text-white dark:placeholder:text-zinc-500 dark:focus:border-sky-500 dark:focus:ring-sky-500/10"
                        id="textarea-counter"
                        rows="5"
                        placeholder="Enter message here"
                        name="message"
                    >{{ old('message') }}</textarea>
                    <p class="text-right text-xs text-slate-500 dark:text-zinc-400"><span class="char-count">0</span> / 100</p>
                </div>

                <div class="space-y-3">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <label for="inputAttachments" class="text-sm font-medium text-slate-700 dark:text-zinc-200">Attachments</label>
                        <button class="addFile inline-flex items-center justify-center rounded-full border border-slate-200 bg-slate-50 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 dark:border-white/10 dark:bg-white/5 dark:text-zinc-200 dark:hover:bg-white/10" type="button">
                            Add another file
                        </button>
                    </div>

                    <div class="rounded-3xl border border-dashed border-slate-200 bg-slate-50/80 p-4 dark:border-white/10 dark:bg-white/5">
                        <input type="file" name="attachments[]" id="inputAttachments" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 outline-none transition file:mr-4 file:rounded-full file:border-0 file:bg-slate-950 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-slate-800 dark:border-white/10 dark:bg-zinc-950 dark:text-zinc-200 dark:file:bg-white dark:file:text-slate-950 dark:hover:file:bg-slate-200">
                        <div id="fileUploadsContainer" class="mt-4 space-y-3"></div>
                        <p class="mt-3 text-xs text-slate-500 dark:text-zinc-400">
                            Allowed file types: JPG, JPEG, PNG, PDF, DOC, DOCX
                        </p>
                    </div>
                </div>

                <button class="app-submit-button inline-flex h-12 rounded-full bg-slate-950 px-6 text-sm font-semibold text-white transition hover:bg-slate-800 dark:bg-white dark:text-slate-950 dark:hover:bg-slate-200" type="submit" id="recaptcha">
                    Submit ticket
                </button>
            </form>
        </div>

        <div class="space-y-4">
            <div class="panel-card p-6">
                <p class="section-kicker">Best results</p>
                <div class="mt-4 space-y-3 text-sm leading-6 text-slate-600 dark:text-zinc-300">
                    <p>Include transaction IDs, phone numbers, or account details that are directly related to the issue.</p>
                    <p>Use attachments for screenshots or payment evidence that helps the support team verify faster.</p>
                    <p>Choose the correct desk and priority so the request lands with the right team quickly.</p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('script')
    <script>
        (function ($) {
            'use strict';

            $('.addFile').on('click', function () {
                $('#fileUploadsContainer').append(`
                    <div class="flex flex-col gap-3 sm:flex-row">
                        <input type="file" name="attachments[]" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 outline-none transition file:mr-4 file:rounded-full file:border-0 file:bg-slate-950 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-slate-800 dark:border-white/10 dark:bg-zinc-950 dark:text-zinc-200 dark:file:bg-white dark:file:text-slate-950 dark:hover:file:bg-slate-200" required />
                        <button class="remove-btn inline-flex items-center justify-center rounded-full border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-700 transition hover:bg-rose-100 dark:border-rose-500/20 dark:bg-rose-500/10 dark:text-rose-300 dark:hover:bg-rose-500/20" type="button">Remove</button>
                    </div>
                `);
            });

            $(document).on('click', '.remove-btn', function () {
                $(this).closest('.flex').remove();
            });
        })(jQuery);
    </script>
@endpush
