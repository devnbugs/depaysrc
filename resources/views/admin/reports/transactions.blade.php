@extends('admin.layouts.app')

@section('panel')
    <div class="container-fluid">
        <!-- Transactions Table -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">@lang('Transaction Report')</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>@lang('User')</th>
                                        <th class="d-none d-md-table-cell">@lang('Trx')</th>
                                        <th class="d-none d-lg-table-cell">@lang('Transacted')</th>
                                        <th>@lang('Amount')</th>
                                        <th class="d-none d-xl-table-cell">@lang('Post Balance')</th>
                                        <th class="d-none d-xl-table-cell">@lang('Detail')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($transactions as $trx)
                                        <tr>
                                            <td>
                                                <div>
                                                    <strong>{{ $trx->user->fullname }}</strong>
                                                    <br>
                                                    <a href="{{ route('admin.users.detail', $trx->user_id) }}" class="small text-primary">@{{ $trx->user->username }}</a>
                                                </div>
                                            </td>
                                            <td class="d-none d-md-table-cell">
                                                <strong>{{ $trx->trx }}</strong>
                                            </td>
                                            <td class="d-none d-lg-table-cell">
                                                <small>{{ showDateTime($trx->created_at) }}</small>
                                                <br>
                                                <small class="text-muted">{{ diffForHumans($trx->created_at) }}</small>
                                            </td>
                                            <td>
                                                <strong class="@if($trx->trx_type == '+')text-success @else text-danger @endif">
                                                    {{ $trx->trx_type }} {{showAmount($trx->amount)}} {{ $general->cur_text }}
                                                </strong>
                                            </td>
                                            <td class="d-none d-xl-table-cell">
                                                <strong>{{ showAmount($trx->post_balance) }} {{ __($general->cur_text) }}</strong>
                                            </td>
                                            <td class="d-none d-xl-table-cell">
                                                <small>{{ __($trx->details) }}</small>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td class="text-center text-muted py-4" colspan="6">{{ __($emptyMessage) }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        {{ paginateLinks($transactions) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    @if(request()->routeIs('admin.users.transactions'))
        <form action="" method="GET" class="d-flex gap-2">
            <div class="flex-grow-1">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="@lang('TRX / Username')" value="{{ $search ?? '' }}">
            </div>
            <button type="submit" class="btn btn-sm btn-primary">
                <i class="la la-search"></i>
            </button>
        </form>
    @else
        <form action="{{ route('admin.report.transaction.search') }}" method="GET" class="d-flex gap-2">
            <div class="flex-grow-1">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="@lang('TRX / Username')" value="{{ $search ?? '' }}">
            </div>
            <button type="submit" class="btn btn-sm btn-primary">
                <i class="la la-search"></i>
            </button>
        </form>
    @endif
@endpush