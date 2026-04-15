@extends('admin.layouts.app')
@section('panel')
<div class="space-y-6">
    <!-- Header with Add Button -->
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold text-slate-950 dark:text-white">@lang('Loan Plans')</h1>
        <button type="button" class="inline-flex items-center gap-2 rounded-full bg-sky-600 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-sky-700 dark:bg-sky-700 dark:hover:bg-sky-800 addBtn" data-bs-toggle="modal" data-bs-target="#addModal">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            @lang('Add New Plan')
        </button>
    </div>

    <!-- Loan Plans Table -->
    <div class="panel-card rounded-2xl border border-slate-200 dark:border-white/10 overflow-hidden">
        <div class="border-b border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 px-6 py-4">
            <h2 class="text-lg font-semibold text-slate-950 dark:text-white">@lang('Available Loan Plans')</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5">
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-zinc-400">@lang('Plan Name')</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-zinc-400">@lang('Loan Amount')</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-zinc-400">@lang('Duration')</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-zinc-400">@lang('Interest')</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-zinc-400">@lang('Penalty')</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-zinc-400">@lang('Status')</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-zinc-400">@lang('Action')</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-white/10">
                    @forelse($plan as $data)
                    <tr class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                        <td class="px-6 py-4">
                            <p class="font-semibold text-slate-950 dark:text-white">{{ __($data->name) }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm">
                                <p class="font-medium text-slate-950 dark:text-white">{{ $general->cur_sym }} {{ showAmount($data->min) }} - {{ $general->cur_sym }} {{ showAmount($data->max) }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-600 dark:text-zinc-400">
                            {{ $data->duration }} @lang('Months')
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-600 dark:text-zinc-400">
                            {{ $data->fee }}%
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-600 dark:text-zinc-400">
                            {{ $data->penalty }}%
                        </td>
                        <td class="px-6 py-4">
                            @if($data->status == 0)
                                <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-700 dark:bg-red-900/30 dark:text-red-400">@lang('Disabled')</span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">@lang('Enabled')</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <button type="button" class="inline-flex items-center gap-2 rounded-full bg-sky-100 px-3 py-1.5 text-xs font-semibold text-sky-700 transition hover:bg-sky-200 dark:bg-sky-900/30 dark:text-sky-400 dark:hover:bg-sky-900/50 editBtn" 
                                data-id='{{ $data->id }}'
                                data-name='{{ $data->name }}'
                                data-status='{{ $data->status }}'
                                data-min='{{ getAmount($data->min) }}'
                                data-max='{{ getAmount($data->max) }}'
                                data-duration='{{ $data->duration }}'
                                data-interest='{{ $data->fee }}'
                                data-penalty='{{ $data->penalty }}'
                                data-bs-toggle="modal" 
                                data-bs-target="#editModal">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                @lang('Edit')
                            </button>
                        </td>
                    </tr>
                    @empty
                        <tr>
                            <td class="px-6 py-8 text-center text-slate-500 dark:text-zinc-400" colspan="7">{{ __($emptyMessage ?? 'No loan plans found') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-200 dark:border-white/10 px-6 py-4">
            {{ paginateLinks($plan) }}
        </div>
    </div>
</div>

{{-- ADD METHOD MODAL --}}
<div id="addModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Add New Loan Plan')</h5>

            </div>
            <form action="{{ route('admin.loan.create') }}" method="POST">
                @csrf

                <div class="modal-body">

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group mb-1">
                                <label for="name">@lang('Name')</label>
                                <input type="text" name="name" class="form-control" id="name" required>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group  mb-1">
                                <label for="min_amount">@lang('Minimum Amount')</label>
                                <div class="input-group">
                                    <input type="text" name="min" onkeyup="this.value = this.value.replace (/^\.|[^\d\.]/g, '')" id="min" class="form-control" required>

                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group mb-1">
                                <label for="max_amount">@lang('Maximum Amount')</label>
                                <div class="input-group">
                                    <input type="text" name="max" onkeyup="this.value = this.value.replace (/^\.|[^\d\.]/g, '')" id="max" class="form-control" required>

                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group mb-1">
                                <label for="total_return">@lang('Loan Duration')  <small>(Months)</small></label>
                                <div class="input-group">
                                    <input type="number" id="total_return" class="form-control" name="duration" required>

                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group mb-1">
                                <label for="interest_type">@lang('Loan Penalty')  <span>%</span></label>
                                <div class="input-group">
                                    <input type="number" id="total_return" class="form-control" name="penalty" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group mb-1">
                                <label for="interest">@lang('Interest Amount') <span id="change_interest_symbol">%</span></label>
                                <div class="input-group">
                                    <input type="text" onkeyup="this.value = this.value.replace (/^\.|[^\d\.]/g, '')" name="interest" id="interest" class="form-control" required>

                                </div>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="form-group mb-1">
                                <label for="status">@lang('Status')</label>
                                <select name="status" id="status" class="form-control" required>
                                    <option value="1">@lang('Enable')</option>
                                    <option value="0">@lang('Disable')</option>
                                </select>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-warning" data-bs-dismiss="modal">@lang('Close')</button>
                    <button type="submit" class="btn btn--primary text-white">@lang('Save')</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- EDIT METHOD MODAL --}}
<div id="editModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Edit Loan Plan')</h5>

            </div>
            <form action="{{ route('admin.loan.edit') }}" method="POST">
                @csrf

                <input type="hidden" name="id" required>

                <div class="modal-body">

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group mb-1">
                                <label for="edit_name">@lang('Name')</label>
                                <input type="text" name="name" class="form-control" id="edit_name" required>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="edit_min_amount">@lang('Minimum Amount')</label>
                                <div class="input-group mb-1">
                                    <input type="text" name="min" onkeyup="this.value = this.value.replace (/^\.|[^\d\.]/g, '')" id="min" class="form-control" required>

                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group mb-1">
                                <label for="edit_max_amount">@lang('Maximum Amount')</label>
                                <div class="input-group">
                                    <input type="text" name="max" onkeyup="this.value = this.value.replace (/^\.|[^\d\.]/g, '')" id="max" class="form-control" required>

                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group mb-1">
                                <label for="edit_total_return">@lang('Duration') <small>(Months)</small></label>
                                <div class="input-group">
                                    <input type="number" id="duration" class="form-control" name="duration" required>

                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group mb-1">
                                <label for="edit_interest_type">@lang('Penalty') %</label>
                                <div class="input-group">
                                    <input type="number" id="penalty" class="form-control" name="penalty" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group mb-1">
                                <label for="edit_interest">@lang('Interest Amount') <span id="update_interest_symbol">%</span></label>
                                <div class="input-group">
                                    <input type="text" onkeyup="this.value = this.value.replace (/^\.|[^\d\.]/g, '')" name="interest" id="edit_interest" class="form-control" required>

                                </div>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="form-group mb-1">
                                <label for="edit_status">@lang('Status')</label>
                                <select name="status" id="edit_status" class="form-control" required>
                                    <option value="1">@lang('Enable')</option>
                                    <option value="0">@lang('Disable')</option>
                                </select>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-warning" data-bs-dismiss="modal">@lang('Close')</button>
                    <button type="submit" class="btn text-white btn--primary">@lang('Update')</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

{{-- ADD PLAN MODAL --}}
<div id="addModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content bg-white dark:bg-slate-900 rounded-2xl">
            <div class="modal-header border-b border-slate-200 dark:border-white/10 px-6 py-4">
                <h5 class="modal-title font-semibold text-slate-950 dark:text-white">@lang('Add New Loan Plan')</h5>
                <button type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300" data-bs-dismiss="modal">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>
            <form action="{{ route('admin.loan.create') }}" method="POST">
                @csrf
                <div class="modal-body space-y-4 px-6 py-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-950 dark:text-white mb-1">@lang('Plan Name')</label>
                        <input type="text" name="name" class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-slate-900 placeholder-slate-500 transition focus:border-sky-500 focus:outline-none dark:border-white/10 dark:bg-white/5 dark:text-white dark:placeholder-zinc-400" required>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-950 dark:text-white mb-1">@lang('Minimum Amount')</label>
                            <input type="text" name="min" onkeyup="this.value = this.value.replace (/^\.|[^\d\.]/g, '')" class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-slate-900 placeholder-slate-500 transition focus:border-sky-500 focus:outline-none dark:border-white/10 dark:bg-white/5 dark:text-white dark:placeholder-zinc-400" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-950 dark:text-white mb-1">@lang('Maximum Amount')</label>
                            <input type="text" name="max" onkeyup="this.value = this.value.replace (/^\.|[^\d\.]/g, '')" class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-slate-900 placeholder-slate-500 transition focus:border-sky-500 focus:outline-none dark:border-white/10 dark:bg-white/5 dark:text-white dark:placeholder-zinc-400" required>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-950 dark:text-white mb-1">@lang('Duration (Months)')</label>
                            <input type="number" name="duration" class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-slate-900 placeholder-slate-500 transition focus:border-sky-500 focus:outline-none dark:border-white/10 dark:bg-white/5 dark:text-white dark:placeholder-zinc-400" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-950 dark:text-white mb-1">@lang('Interest (%)')</label>
                            <input type="text" name="interest" onkeyup="this.value = this.value.replace (/^\.|[^\d\.]/g, '')" class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-slate-900 placeholder-slate-500 transition focus:border-sky-500 focus:outline-none dark:border-white/10 dark:bg-white/5 dark:text-white dark:placeholder-zinc-400" required>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-950 dark:text-white mb-1">@lang('Penalty (%)')</label>
                        <input type="number" name="penalty" class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-slate-900 placeholder-slate-500 transition focus:border-sky-500 focus:outline-none dark:border-white/10 dark:bg-white/5 dark:text-white dark:placeholder-zinc-400" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-950 dark:text-white mb-1">@lang('Status')</label>
                        <select name="status" class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-slate-900 placeholder-slate-500 transition focus:border-sky-500 focus:outline-none dark:border-white/10 dark:bg-white/5 dark:text-white dark:placeholder-zinc-400" required>
                            <option value="1">@lang('Enable')</option>
                            <option value="0">@lang('Disable')</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-t border-slate-200 dark:border-white/10 flex gap-3 justify-end px-6 py-4">
                    <button type="button" class="inline-flex items-center rounded-full border border-slate-300 bg-white px-6 py-2.5 text-sm font-semibold text-slate-700 transition hover:border-slate-400 dark:border-white/10 dark:bg-white/5 dark:text-zinc-200" data-bs-dismiss="modal">@lang('Cancel')</button>
                    <button type="submit" class="inline-flex items-center rounded-full bg-sky-600 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-sky-700 dark:bg-sky-700 dark:hover:bg-sky-800">@lang('Save Plan')</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- EDIT PLAN MODAL --}}
<div id="editModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content bg-white dark:bg-slate-900 rounded-2xl">
            <div class="modal-header border-b border-slate-200 dark:border-white/10 px-6 py-4">
                <h5 class="modal-title font-semibold text-slate-950 dark:text-white">@lang('Edit Loan Plan')</h5>
                <button type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300" data-bs-dismiss="modal">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>
            <form action="{{ route('admin.loan.edit') }}" method="POST">
                @csrf
                <input type="hidden" name="id" required>
                <div class="modal-body space-y-4 px-6 py-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-950 dark:text-white mb-1">@lang('Plan Name')</label>
                        <input type="text" name="name" id="edit_name" class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-slate-900 placeholder-slate-500 transition focus:border-sky-500 focus:outline-none dark:border-white/10 dark:bg-white/5 dark:text-white dark:placeholder-zinc-400" required>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-950 dark:text-white mb-1">@lang('Minimum Amount')</label>
                            <input type="text" name="min" id="edit_min" onkeyup="this.value = this.value.replace (/^\.|[^\d\.]/g, '')" class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-slate-900 placeholder-slate-500 transition focus:border-sky-500 focus:outline-none dark:border-white/10 dark:bg-white/5 dark:text-white dark:placeholder-zinc-400" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-950 dark:text-white mb-1">@lang('Maximum Amount')</label>
                            <input type="text" name="max" id="edit_max" onkeyup="this.value = this.value.replace (/^\.|[^\d\.]/g, '')" class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-slate-900 placeholder-slate-500 transition focus:border-sky-500 focus:outline-none dark:border-white/10 dark:bg-white/5 dark:text-white dark:placeholder-zinc-400" required>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-950 dark:text-white mb-1">@lang('Duration (Months)')</label>
                            <input type="number" name="duration" id="edit_duration" class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-slate-900 placeholder-slate-500 transition focus:border-sky-500 focus:outline-none dark:border-white/10 dark:bg-white/5 dark:text-white dark:placeholder-zinc-400" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-950 dark:text-white mb-1">@lang('Interest (%)')</label>
                            <input type="text" name="interest" id="edit_interest" onkeyup="this.value = this.value.replace (/^\.|[^\d\.]/g, '')" class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-slate-900 placeholder-slate-500 transition focus:border-sky-500 focus:outline-none dark:border-white/10 dark:bg-white/5 dark:text-white dark:placeholder-zinc-400" required>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-950 dark:text-white mb-1">@lang('Penalty (%)')</label>
                        <input type="number" name="penalty" id="edit_penalty" class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-slate-900 placeholder-slate-500 transition focus:border-sky-500 focus:outline-none dark:border-white/10 dark:bg-white/5 dark:text-white dark:placeholder-zinc-400" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-950 dark:text-white mb-1">@lang('Status')</label>
                        <select name="status" id="edit_status" class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-slate-900 placeholder-slate-500 transition focus:border-sky-500 focus:outline-none dark:border-white/10 dark:bg-white/5 dark:text-white dark:placeholder-zinc-400" required>
                            <option value="1">@lang('Enable')</option>
                            <option value="0">@lang('Disable')</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-t border-slate-200 dark:border-white/10 flex gap-3 justify-end px-6 py-4">
                    <button type="button" class="inline-flex items-center rounded-full border border-slate-300 bg-white px-6 py-2.5 text-sm font-semibold text-slate-700 transition hover:border-slate-400 dark:border-white/10 dark:bg-white/5 dark:text-zinc-200" data-bs-dismiss="modal">@lang('Cancel')</button>
                    <button type="submit" class="inline-flex items-center rounded-full bg-sky-600 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-sky-700 dark:bg-sky-700 dark:hover:bg-sky-800">@lang('Update Plan')</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('script')
<script>
    (function ($) {
        "use strict";

        $('.editBtn').on('click', function(e){
            var modal = $('#editModal');
            
            document.querySelector('input[name="id"]').value = $(this).data('id');
            document.getElementById('edit_name').value = $(this).data('name');
            document.getElementById('edit_min').value = $(this).data('min');
            document.getElementById('edit_max').value = $(this).data('max');
            document.getElementById('edit_duration').value = $(this).data('duration');
            document.getElementById('edit_interest').value = $(this).data('interest');
            document.getElementById('edit_penalty').value = $(this).data('penalty');
            document.getElementById('edit_status').value = $(this).data('status');
            
            modal.modal('show');
        });
    })(jQuery);
</script>
@endpush
            }

            $('#update_interest_symbol').text(result);

        });

        $('.editBtn').on('click', (e)=> {
            var $this = $(e.currentTarget);
            var modal = $('#editModal');

            var result = null;

            if($this.data('interest_type') == 0){
                result = '{{ __($general->cur_text) }}';
            }else{
                result = '%';
            }

            $('#update_interest_symbol').text(result);

            modal.find('input[name=id]').val($this.data('id'));
            modal.find('input[name=name]').val($this.data('name'));
            modal.find('input[name=duration]').val($this.data('duration'));
            modal.find('input[name=max]').val($this.data('max'));
            modal.find('input[name=min]').val($this.data('min'));
            modal.find('input[name=interest]').val($this.data('interest'));
            modal.find('input[name=status]').val($this.data('status'));
            modal.find('input[name=penalty]').val($this.data('penalty'));
            modal.modal('show');
        });

    })(jQuery);

</script>
@endpush
