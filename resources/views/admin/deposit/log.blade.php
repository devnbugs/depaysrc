@extends('admin.layouts.app')

@section('panel')
    <div class="container-fluid">
        @if(request()->routeIs('admin.deposit.list') || request()->routeIs('admin.deposit.method') || request()->routeIs('admin.users.deposits') || request()->routeIs('admin.users.deposits.method'))
            <!-- Stats Cards -->
            <div class="row mb-4">
                <div class="col-lg-4 col-md-6 col-12 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="text-muted small">@lang('Successful Deposit')</p>
                                    <h4 class="text-success mt-2">{{ __($general->cur_sym) }}{{ showAmount($successful) }}</h4>
                                </div>
                                <span class="badge bg-success">
                                    <i class="la la-check-circle la-2x"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-12 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="text-muted small">@lang('Pending Deposit')</p>
                                    <h4 class="text-warning mt-2">{{ __($general->cur_sym) }}{{ showAmount($pending) }}</h4>
                                </div>
                                <span class="badge bg-warning">
                                    <i class="la la-clock la-2x"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-12 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="text-muted small">@lang('Rejected Deposit')</p>
                                    <h4 class="text-danger mt-2">{{ __($general->cur_sym) }}{{ showAmount($rejected) }}</h4>
                                </div>
                                <span class="badge bg-danger">
                                    <i class="la la-times-circle la-2x"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Deposits Table -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">@lang('Deposits')</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>@lang('Gateway | Trx')</th>
                                        <th class="d-none d-md-table-cell">@lang('Initiated')</th>
                                        <th>@lang('User')</th>
                                        <th class="d-none d-lg-table-cell">@lang('Amount')</th>
                                        <th class="d-none d-xl-table-cell">@lang('Conversion')</th>
                                        <th class="text-center">@lang('Status')</th>
                                        <th class="text-center">@lang('Action')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($deposits as $deposit)
                                        @php
                                            $details = $deposit->detail ? json_encode($deposit->detail) : null;
                                        @endphp
                                        <tr>
                                            <td>
                                                <div>
                                                    <a href="{{ route('admin.deposit.method', [$deposit->gateway?->alias ?? 'all']) }}" class="text-primary fw-bold">{{ __($deposit->gateway?->name ?? 'Unknown Gateway') }}</a>
                                                    <p class="mt-1 mb-0 small text-muted">{{ $deposit->trx }}</p>
                                                </div>
                                            </td>
                                            <td class="d-none d-md-table-cell">
                                                <small>{{ showDateTime($deposit->created_at) }}</small>
                                                <br>
                                                <small class="text-muted">{{ diffForHumans($deposit->created_at) }}</small>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong>{{ $deposit->user?->fullname ?? 'Deleted User' }}</strong>
                                                    @if($deposit->user)
                                                        <br>
                                                        <a href="{{ route('admin.users.detail', $deposit->user_id) }}" class="small text-primary">@{{ $deposit->user->username }}</a>
                                                    @else
                                                        <br>
                                                        <small class="text-muted">ID: {{ $deposit->user_id }}</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="d-none d-lg-table-cell">
                                                <div>
                                                    <strong class="text-success">{{ __($general->cur_sym) }}{{ showAmount($deposit->amount) }}</strong>
                                                    <br>
                                                    <small class="text-danger">+ {{ showAmount($deposit->charge) }} charge</small>
                                                    <br>
                                                    <small><strong>{{ showAmount($deposit->amount+$deposit->charge) }} {{ __($general->cur_text) }}</strong></small>
                                                </div>
                                            </td>
                                            <td class="d-none d-xl-table-cell">
                                                <small>1 {{ __($general->cur_text) }} = {{ showAmount($deposit->rate) }} {{__($deposit->method_currency)}}</small>
                                                <br>
                                                <strong>{{ showAmount($deposit->final_amo) }} {{__($deposit->method_currency)}}</strong>
                                            </td>
                                            <td class="text-center">
                                                @if($deposit->status == 2)
                                                    <span class="badge bg-warning">@lang('Pending')</span>
                                                @elseif($deposit->status == 1)
                                                    <span class="badge bg-success">@lang('Approved')</span>
                                                    <br>
                                                    <small class="text-muted">{{ diffForHumans($deposit->updated_at) }}</small>
                                                @elseif($deposit->status == 3)
                                                    <span class="badge bg-danger">@lang('Rejected')</span>
                                                    <br>
                                                    <small class="text-muted">{{ diffForHumans($deposit->updated_at) }}</small>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('admin.deposit.details', $deposit->id) }}" class="btn btn-sm btn-info">@lang('View')</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td class="text-center text-muted py-4" colspan="7">{{ __($emptyMessage) }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        {{ paginateLinks($deposits) }}
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('breadcrumb-plugins')
    @if(!request()->routeIs('admin.users.deposits') && !request()->routeIs('admin.users.deposits.method'))
        <form action="{{ route('admin.deposit.search', $scope ?? str_replace('admin.deposit.', '', request()->route()->getName())) }}" method="GET" class="d-flex gap-2 mb-2">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="@lang('Trx number/Username')" value="{{ $search ?? '' }}">
            <button type="submit" class="btn btn-sm btn-primary">
                <i class="la la-search"></i>
            </button>
        </form>

        <form action="{{ route('admin.deposit.dateSearch', $scope ?? str_replace('admin.deposit.', '', request()->route()->getName())) }}" method="GET" class="d-flex gap-2">
            <input name="date" type="text" data-range="true" data-multiple-dates-separator=" - " data-language="en" class="form-control form-control-sm datepicker-here" data-position='bottom right' placeholder="@lang('Min date - Max date')" autocomplete="off" value="{{ @$dateSearch }}">
            <input type="hidden" name="method" value="{{ @$methodAlias }}">
            <button type="submit" class="inline-flex items-center rounded-full bg-sky-600 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-sky-700 dark:bg-sky-700 dark:hover:bg-sky-800">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><path d="m21 21-4.35-4.35"></path></svg>
            </button>
        </form>
    @endif
@endpush

@push('script-lib')
  <script src="{{ asset('assets/admin/js/vendor/datepicker.min.js') }}"></script>
  <script src="{{ asset('assets/admin/js/vendor/datepicker.en.js') }}"></script>
@endpush
@push('script')
  <script>
    (function($){
        "use strict";
        if(!$('.datepicker-here').val()){
            $('.datepicker-here').datepicker();
        }
    })(jQuery)
  </script>
@endpush
