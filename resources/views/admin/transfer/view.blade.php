@extends('admin.layouts.app')

@section('panel')
    <div class="row mb-none-30">


        <div class="col-lg-4 col-md-4 mb-30">
            <div class="card b-radius--10 overflow-hidden box--shadow1">
                <div class="card-body">
                    <h5 class="mb-20 text-muted">@lang('Fund Transfer')</h5>



                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Date')
                            <span class="font-weight-bold">{{ showDateTime($log->created_at) }}</span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Trx Number')
                            <span class="font-weight-bold">{{ $log->trx }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Sender')
                            <span class="font-weight-bold">
                                <a href="{{ route('admin.users.detail', $log->user_id) }}">{{ @App\Models\User::whereId($log->user_id)->first()->username }}</a>
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Method')
                            <span class="font-weight-bold">Other Bank Transfer</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Amount')
                            <span class="font-weight-bold">{{ showAmount($log->amount ) }} {{ __($general->cur_text) }}</span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Charge')
                            <span class="font-weight-bold">{{ showAmount($log->charge ) }} {{ __($general->cur_text) }}</span>
                        </li>



                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Status')
                            @if($log->status == 0)
                                <span class="badge badge-pill bg-warning">@lang('Pending')</span>
                            @elseif($log->status == 1)
                                <span class="badge badge-pill bg-success">@lang('Approved')</span>
                            @elseif($log->status == 2)
                                <span class="badge badge-pill bg-danger">@lang('Rejected')</span>
                            @endif
                        </li>


                    </ul>
                </div>
            </div>
        </div>
        <div class="col-lg-8 col-md-8 mb-30">

            <div class="card b-radius--10 overflow-hidden box--shadow1">
                <div class="card-body">
                    <h5 class="card-title border-bottom pb-2">@lang('User Withdraw Information')</h5>



                                <div class="row mt-4">
                                    <div class="col-md-12">
                                        <b>Details</b>
                                        <p>{!!$log->details!!}</p>
                                    </div>
                                </div>




                    @if($log->status == 0)
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <button class="btn text-white btn--success ml-1 approveBtn" data-toggle="tooltip" data-original-title="@lang('Approve')"
                                        data-id="{{ $log->id }}" data-amount="{{ showAmount($log->amount) }} {{$general->cur_text}}">
                                    <i class="fas la-check"></i> @lang('Approve')
                                </button>

                                <button class="btn btn-danger ml-1 rejectBtn" data-toggle="tooltip" data-original-title="@lang('Reject')"
                                        data-id="{{ $log->id }}" data-amount="{{ showAmount($log->amount) }} {{$general->cur_text}}">
                                    <i class="fas fa-ban"></i> @lang('Reject')
                                </button>
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>



    {{-- APPROVE MODAL --}}
    <div id="approveModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Approve Withdrawal Confirmation')</h5>

                </div>
                <form action="{{ route('admin.transfer.approve') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id">
                    <div class="modal-body">
                        <p>@lang('Have you sent') <span class="font-weight-bold withdraw-amount text-success"></span>?</p>
                        <p class="withdraw-detail"></p>
                        <textarea name="details" class="form-control pt-3" rows="3" placeholder="@lang('Provide the details. eg: transaction number')" required=""></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-warning" data-bs-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn-success">@lang('Approve')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- REJECT MODAL --}}
    <div id="rejectModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Reject Withdrawal Confirmation')</h5>

                </div>
                <form action="{{route('admin.transfer.reject')}}" method="POST">
                    @csrf
                    <input type="hidden" name="id">
                    <div class="modal-body">
                        <strong>@lang('Reason of Rejection')</strong>
                        <textarea name="details" class="form-control pt-3" rows="3" placeholder="@lang('Provide the Details')" required=""></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-warning" data-bs-dismiss="modal">@lang('Close')</button>
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

            $('.rejectBtn').on('click', function() {
                var modal = $('#rejectModal');
                modal.find('input[name=id]').val($(this).data('id'));
                modal.find('.withdraw-amount').text($(this).data('amount'));
                modal.modal('show');
            });
        })(jQuery);

    </script>
@endpush
