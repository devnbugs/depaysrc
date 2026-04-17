@extends('admin.layouts.app')

@section('panel')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-lg-4 col-md-6 col-12 mb-3 mb-lg-0">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">@lang('Withdraw Via') {{__(@$withdrawal->method->name)}}</h5>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <img src="{{$methodImage}}" width="100" class="rounded" alt="@lang('Image')">
                        </div>

                        <ul class="list-group">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>@lang('Date')</span>
                                <span class="badge bg-info">{{ showDateTime($withdrawal->created_at) }}</span>
                            </li>

                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>@lang('Trx')</span>
                                <code>{{ $withdrawal->trx }}</code>
                            </li>
                            <li class="list-group-item">
                                <span>@lang('Username')</span>
                                <br>
                                <a href="{{ route('admin.users.detail', $withdrawal->user_id) }}" class="btn btn-sm btn-primary mt-2">{{ @$withdrawal->user->username }}</a>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>@lang('Method')</span>
                                <span class="badge bg-secondary">{{__($withdrawal->method->name)}}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>@lang('Amount')</span>
                                <strong class="text-success">{{ showAmount($withdrawal->amount ) }} {{ __($general->cur_text) }}</strong>
                            </li>

                            <li class="list-group-item d-flex justify-content-between">
                                <span>@lang('Charge')</span>
                                <strong class="text-warning">{{ showAmount($withdrawal->charge ) }} {{ __($general->cur_text) }}</strong>
                            </li>

                            <li class="list-group-item d-flex justify-content-between">
                                <span>@lang('After Charge')</span>
                                <strong>{{ showAmount($withdrawal->after_charge ) }} {{ __($general->cur_text) }}</strong>
                            </li>
                            <li class="list-group-item">
                                <span>@lang('Rate')</span>
                                <br>
                                <small>1 {{__($general->cur_text)}} = {{ showAmount($withdrawal->rate ) }} {{__($withdrawal->currency)}}</small>
                            </li>

                            <li class="list-group-item d-flex justify-content-between">
                                <span>@lang('Payable')</span>
                                <strong class="text-success">{{ showAmount($withdrawal->final_amount) }} {{__($withdrawal->currency)}}</strong>
                            </li>

                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>@lang('Status')</span>
                                @if($withdrawal->status == 2)
                                    <span class="badge bg-warning">@lang('Pending')</span>
                                @elseif($withdrawal->status == 1)
                                    <span class="badge bg-success">@lang('Approved')</span>
                                @elseif($withdrawal->status == 3)
                                    <span class="badge bg-danger">@lang('Rejected')</span>
                                @endif
                            </li>

                            @if($withdrawal->admin_feedback)
                            <li class="list-group-item">
                                <strong>@lang('Admin Response')</strong>
                                <p class="mt-2 mb-0 small">{{$withdrawal->admin_feedback}}</p>
                            </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-lg-8 col-md-6 col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">@lang('User Withdraw Information')</h5>
                    </div>
                    <div class="card-body">
                        @if($details != null)
                            @foreach(\GuzzleHttp\json_decode($details) as $k => $val)
                                @if($val->type == 'file')
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <h6>{{__(inputTitle($k))}}</h6>
                                            <div class="mt-2">
                                                <img src="{{getImage('assets/images/verify/withdraw/'.$val->field_name)}}" class="img-fluid rounded" alt="@lang('Image')" style="max-width: 300px;">
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="row mb-3">
                                        <div class="col-12">
                                            <h6>{{__(inputTitle($k))}}</h6>
                                            <p class="mb-0 text-muted">{{$val->field_name}}</p>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        @endif

                        @if($withdrawal->status == 2)
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                        <button class="btn btn-success approveBtn" data-id="{{ $withdrawal->id }}" data-amount="{{ showAmount($withdrawal->final_amount) }} {{$withdrawal->currency}}">
                                            <i class="fas la-check"></i> @lang('Approve')
                                        </button>

                                        <button class="btn btn-danger rejectBtn" data-id="{{ $withdrawal->id }}" data-amount="{{ showAmount($withdrawal->final_amount) }} {{__($withdrawal->currency)}}">
                                            <i class="fas fa-ban"></i> @lang('Reject')
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- APPROVE MODAL --}}
    <div id="approveModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Approve Withdrawal Confirmation')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="@lang('Close')"></button>
                </div>
                <form action="{{ route('admin.withdraw.approve') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id">
                    <div class="modal-body">
                        <p>@lang('Have you sent') <span class="fw-bold text-success withdraw-amount"></span>?</p>
                        <p class="withdraw-detail"></p>
                        <label class="form-label">@lang('Transaction Details')</label>
                        <textarea name="details" class="form-control" rows="3" placeholder="@lang('e.g., transaction number')" required=""></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn-success">@lang('Approve')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- REJECT MODAL --}}
    <div id="rejectModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Reject Withdrawal Confirmation')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="@lang('Close')"></button>
                </div>
                <form action="{{route('admin.withdraw.reject')}}" method="POST">
                    @csrf
                    <input type="hidden" name="id">
                    <div class="modal-body">
                        <label class="form-label fw-bold">@lang('Reason of Rejection')</label>
                        <textarea name="details" class="form-control" rows="3" placeholder="@lang('Provide the reason')" required=""></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('Close')</button>
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
            $('.approveBtn').on('click', function() {
                var modal = $('#approveModal');
                modal.find('input[name=id]').val($(this).data('id'));
                modal.find('.withdraw-amount').text($(this).data('amount'));
                modal.modal('show');
            });
            </div>
            <div class="rejectBtn').on('click', function() {
                var modal = $('#rejectModal');
                modal.find('input[name=id]').val($(this).data('id'));
                modal.find('.withdraw-amount').text($(this).data('amount'));
                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush

            $('.rejectBtn').on('click', function() {
                var modal = $('#rejectModal');
                modal.find('input[name=id]').val($(this).data('id'));
                modal.find('.withdraw-amount').text($(this).data('amount'));
                modal.modal('show');
            });
        })(jQuery);

    </script>
@endpush
