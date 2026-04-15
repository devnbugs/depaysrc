@extends('admin.layouts.app')
@section('panel')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <p class="section-kicker">@lang('KYC Management')</p>
            <h2 class="mt-2 section-title">@lang('Document Types')</h2>
        </div>
        <button type="button" class="inline-flex h-11 items-center rounded-full bg-sky-600 px-6 text-sm font-semibold text-white transition hover:bg-sky-700 dark:bg-sky-700 dark:hover:bg-sky-800 addBtn">
            <svg class="mr-2 h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            @lang('Add New Document Type')
        </button>
    </div>

    <!-- Document Types Table -->
    <div class="panel-card rounded-2xl border border-slate-200 dark:border-white/10 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5">
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-zinc-400">@lang('ID')</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-zinc-400">@lang('Document Type')</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-zinc-400">@lang('Status')</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-zinc-400">@lang('Action')</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-white/10">
                    @php $i = 1; @endphp
                    @forelse($kyc as $data)
                        <tr class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                            <td class="px-6 py-4">
                                <span class="font-semibold text-slate-950 dark:text-white">{{ __($i++) }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-semibold text-slate-950 dark:text-white">{{ __($data->type) }}</span>
                            </td>
                            <td class="px-6 py-4">
                                @if($data->status == 0)
                                    <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700 dark:bg-slate-900/30 dark:text-slate-400">
                                        <svg class="mr-1 h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                        @lang('Disabled')
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">
                                        <svg class="mr-1 h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                        @lang('Enabled')
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <button type="button" class="inline-flex items-center rounded-full bg-sky-600 px-3 py-2 text-sm font-semibold text-white transition hover:bg-sky-700 dark:bg-sky-700 dark:hover:bg-sky-800 editBtn"
                                        data-id='{{ $data->id }}'
                                        data-name='{{ $data->type }}'
                                        data-status='{{ $data->status }}'>
                                    <svg class="mr-2 h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg>
                                    @lang('Edit')
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="px-6 py-8 text-center text-slate-500 dark:text-zinc-400" colspan="4">{{ __($empty_message ?? 'No document types found') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div id="addModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/50 p-4" tabindex="-1" role="dialog">
    <div class="w-full max-w-md rounded-2xl bg-white dark:bg-slate-900">
        <div class="border-b border-slate-200 dark:border-white/10 px-6 py-4">
            <h2 class="text-lg font-semibold text-slate-950 dark:text-white">@lang('Add New KYC Document Type')</h2>
        </div>
        <form action="" method="POST">
            @csrf
            <div class="space-y-4 px-6 py-5">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-zinc-300 mb-2">@lang('Document Type Name')</label>
                    <input type="text" name="type" class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-slate-900 placeholder-slate-500 transition focus:border-sky-500 focus:outline-none dark:border-white/10 dark:bg-white/5 dark:text-white dark:placeholder-zinc-400" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-zinc-300 mb-2">@lang('Status')</label>
                    <select name="status" class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-slate-900 transition focus:border-sky-500 focus:outline-none dark:border-white/10 dark:bg-white/5 dark:text-white" required>
                        <option value="1">@lang('Enable')</option>
                        <option value="0">@lang('Disable')</option>
                    </select>
                </div>
            </div>
            <div class="flex gap-3 border-t border-slate-200 dark:border-white/10 px-6 py-4">
                <button type="button" class="flex-1 rounded-full border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 dark:border-white/10 dark:bg-slate-800 dark:text-zinc-200 dark:hover:bg-slate-700" onclick="closeAddModal()">
                    @lang('Close')
                </button>
                <button type="submit" class="flex-1 rounded-full bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-sky-700 dark:bg-sky-700 dark:hover:bg-sky-800">
                    @lang('Save')
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/50 p-4" tabindex="-1" role="dialog">
    <div class="w-full max-w-md rounded-2xl bg-white dark:bg-slate-900">
        <div class="border-b border-slate-200 dark:border-white/10 px-6 py-4">
            <h2 class="text-lg font-semibold text-slate-950 dark:text-white">@lang('Edit Document Type')</h2>
        </div>
        <form action="{{ route('admin.users.kyc.editsettings') }}" method="POST">
            @csrf
            <input type="hidden" name="id" required>
            <div class="space-y-4 px-6 py-5">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-zinc-300 mb-2">@lang('Document Type Name')</label>
                    <input type="text" name="type" class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-slate-900 placeholder-slate-500 transition focus:border-sky-500 focus:outline-none dark:border-white/10 dark:bg-white/5 dark:text-white dark:placeholder-zinc-400" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-zinc-300 mb-2">@lang('Status')</label>
                    <select name="status" class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-slate-900 transition focus:border-sky-500 focus:outline-none dark:border-white/10 dark:bg-white/5 dark:text-white" required>
                        <option value="1">@lang('Enable')</option>
                        <option value="0">@lang('Disable')</option>
                    </select>
                </div>
            </div>
            <div class="flex gap-3 border-t border-slate-200 dark:border-white/10 px-6 py-4">
                <button type="button" class="flex-1 rounded-full border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 dark:border-white/10 dark:bg-slate-800 dark:text-zinc-200 dark:hover:bg-slate-700" onclick="closeEditModal()">
                    @lang('Close')
                </button>
                <button type="submit" class="flex-1 rounded-full bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-sky-700 dark:bg-sky-700 dark:hover:bg-sky-800">
                    @lang('Update')
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('script')
<script>
    (function () {
        "use strict";

        // Add Modal
        document.querySelector('.addBtn').addEventListener('click', function() {
            document.getElementById('addModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        });

        // Edit Modal
        document.querySelectorAll('.editBtn').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');
                const status = this.getAttribute('data-status');

                document.querySelector('#editModal input[name=id]').value = id;
                document.querySelector('#editModal input[name=type]').value = name;
                document.querySelector('#editModal select[name=status]').value = status;

                document.getElementById('editModal').classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            });
        });

        // Close functions
        window.closeAddModal = function() {
            document.getElementById('addModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        };

        window.closeEditModal = function() {
            document.getElementById('editModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        };

        // Close on outside click
        document.getElementById('addModal').addEventListener('click', function(e) {
            if (e.target === this) closeAddModal();
        });

        document.getElementById('editModal').addEventListener('click', function(e) {
            if (e.target === this) closeEditModal();
        });
    })();
</script>
@endpush
