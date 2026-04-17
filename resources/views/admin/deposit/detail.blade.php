@extends('admin.layouts.app')
@section('panel')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="fw-bolder text-dark mb-4">@lang('Deposit Details')</h2>
            </div>
        </div>

        <div class="row g-3">
            <!-- Left Sidebar - Deposit Info -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="text-muted text-uppercase fw-bold mb-3">@lang('Deposit Via') {{ __(@$deposit->gateway->name) }}</h6>
                        
                        <div class="text-center mb-3 p-3 bg-light rounded">
                            <img src="{{ $deposit->gatewayCurrency()->methodImage() }}" alt="@lang('Method Image')" class="img-fluid rounded" style="max-width: 100px;">
                        </div>

                        <div class="mb-3">
                            <small class="text-muted d-block">@lang('Date')</small>
                            <span class="fw-bold">{{ showDateTime($deposit->created_at) }}</span>
                        </div>

                        <div class="mb-3">
                            <small class="text-muted d-block">@lang('Transaction Number')</small>
                            <span class="fw-bold font-monospace text-primary">{{ $deposit->trx }}</span>
                        </div>

                        <div class="mb-3">
                            <small class="text-muted d-block">@lang('Username')</small>
                            <span class="fw-bold">
                                <a href="{{ route('admin.users.detail', $deposit->user_id) }}" class="text-decoration-none">{{ @$deposit->user->username }}</a>
                            </span>
                        </div>

                        <div class="mb-3">
                            <small class="text-muted d-block">@lang('Method')</small>
                            <span class="fw-bold">{{ __(@$deposit->gateway->name) }}</span>
                        </div>

                        <hr class="my-3">

                        <div class="mb-3">
                            <small class="text-muted d-block">@lang('Amount')</small>
                            <span class="fw-bold text-success h6">{{ showAmount($deposit->amount ) }} {{ __($general->cur_text) }}</span>
                        </div>

                        <div class="mb-3">
                            <small class="text-muted d-block">@lang('Charge')</small>
                            <span class="fw-bold text-warning">{{ showAmount($deposit->charge ) }} {{ __($general->cur_text) }}</span>
                        </div>

                        <div class="mb-3">
                            <small class="text-muted d-block">@lang('After Charge')</small>
                            <span class="fw-bold text-info">{{ showAmount($deposit->amount+$deposit->charge ) }} {{ __($general->cur_text) }}</span>
                        </div>

                        <div class="mb-3">
                            <small class="text-muted d-block">@lang('Rate')</small>
                            <span class="fw-bold small">1 {{__($general->cur_text)}} = {{ showAmount($deposit->rate) }} {{__($deposit->baseCurrency())}}</span>
                        </div>

                        <div class="mb-3">
                            <small class="text-muted d-block">@lang('Payable')</small>
                            <span class="fw-bold text-primary h6">{{ showAmount($deposit->final_amo ) }} {{__($deposit->method_currency)}}</span>
                        </div>

                        <div class="mb-0">
                            <small class="text-muted d-block">@lang('Status')</small>
                            @if($deposit->status == 2)
                                <span class="badge bg-warning rounded-pill">@lang('Pending')</span>
                            @elseif($deposit->status == 1)
                                <span class="badge bg-success rounded-pill">@lang('Approved')</span>
                            @elseif($deposit->status == 3)
                                <span class="badge bg-danger rounded-pill">@lang('Rejected')</span>
                            @endif
                        </div>

                        @if($deposit->admin_feedback)
                            <div class="mt-3 p-3 bg-light rounded">
                                <strong class="small">@lang('Admin Response')</strong>
                                <p class="mb-0 mt-2 text-muted small">{{__($deposit->admin_feedback)}}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Content - User Info -->
            <div class="col-lg-8 col-md-6 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light border-bottom">
                        <h5 class="fw-bold mb-0">@lang('User Deposit Information')</h5>
                    </div>
                    <div class="card-body">
                        @if($details != null)
                            @foreach(json_decode($details) as $k => $val)
                                @if($deposit->method_code >= 1000)
                                    @if($val->type == 'file')
                                        <div class="mb-4">
                                            <h6 class="fw-bold text-muted">{{inputTitle($k)}}</h6>
                                            <div class="mt-2">
                                                <img src="{{getImage('assets/images/verify/deposit/'.$val->field_name)}}" alt="@lang('Deposit Image')" class="img-fluid rounded shadow-sm" style="max-width: 100%;">
                                            </div>
                                        </div>
                                        <hr>
                                    @else
                                        <div class="mb-4">
                                            <h6 class="fw-bold text-muted">{{inputTitle($k)}}</h6>
                                            <p class="mb-0 fw-bold">{{__($val->field_name)}}</p>
                                        </div>
                                        <hr>
                                    @endif
                                @endif
                            @endforeach
                            @if($deposit->method_code < 1000)
                                @include('admin.deposit.gateway_data',['details'=>json_decode($details)])
                            @endif
                        @endif

                        @if($deposit->status == 2)
                            <div class="d-grid gap-2 mt-4">
                                <button class="btn btn-success btn-lg approveBtn"
                                        data-id="{{ $deposit->id }}"
                                        data-info="{{$details}}"
                                        data-amount="{{ showAmount($deposit->amount)}} {{ __($general->cur_text) }}"
                                        data-username="{{ @$deposit->user->username }}"
                                        data-toggle="tooltip" data-original-title="@lang('Approve')">
                                    <i class="fas fa-check"></i> @lang('Approve Deposit')
                                </button>

                                <button class="btn btn-danger btn-lg rejectBtn"
                                        data-id="{{ $deposit->id }}"
                                        data-info="{{$details}}"
                                        data-amount="{{ showAmount($deposit->amount)}} {{ __($general->cur_text) }}"
                                        data-username="{{ @$deposit->user->username }}"
                                        data-toggle="tooltip" data-original-title="@lang('Reject')">
                                    <i class="fas fa-ban"></i> @lang('Reject Deposit')
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>


    {{-- APPROVE MODAL --}}
    <div id="approveModal" class="modal fade" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-bottom bg-light">
                    <h5 class="modal-title fw-bold">@lang('Approve Deposit Confirmation')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{route('admin.deposit.approve')}}" method="POST">
                    @csrf
                    <input type="hidden" name="id">
                    <div class="modal-body">
                        <p class="mb-0">@lang('Are you sure to') <span class="fw-bold">@lang('approve')</span> <span class="fw-bold withdraw-amount text-success"></span> @lang('deposit of') <span class="fw-bold withdraw-user"></span>?</p>
                    </div>
                    <div class="modal-footer border-top bg-light">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn-success">@lang('Approve')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- REJECT MODAL --}}
    <div id="rejectModal" class="modal fade" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-bottom bg-light">
                    <h5 class="modal-title fw-bold">@lang('Reject Deposit Confirmation')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.deposit.reject')}}" method="POST">
                    @csrf
                    <input type="hidden" name="id">
                    <div class="modal-body">
                        <p>@lang('Are you sure to') <span class="fw-bold">@lang('reject')</span> <span class="fw-bold withdraw-amount text-success"></span> @lang('deposit of') <span class="fw-bold withdraw-user"></span>?</p>

                        <div class="mt-3">
                            <label class="form-label fw-bold">@lang('Reason for Rejection')</label>
                            <textarea name="message" id="message" placeholder="@lang('Reason for Rejection')" class="form-control form-control-lg" rows="4" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-top bg-light">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn-danger">@lang('Reject')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        (function ($) {
            "use strict";

            $('.approveBtn').on('click', function () {
                var modal = $('#approveModal');
                modal.find('input[name=id]').val($(this).data('id'));
                modal.find('.withdraw-amount').text($(this).data('amount'));
                modal.find('.withdraw-user').text($(this).data('username'));
                modal.modal('show');
            });

            $('.rejectBtn').on('click', function () {
                var modal = $('#rejectModal');
                modal.find('input[name=id]').val($(this).data('id'));
                modal.find('.withdraw-amount').text($(this).data('amount'));
                modal.find('.withdraw-user').text($(this).data('username'));
                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush
