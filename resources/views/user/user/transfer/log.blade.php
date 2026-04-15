@extends($activeTemplate.'layouts.dashboard')
@section('content')
<!-- Basic Tables start -->
<div class="row" id="basic-table">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">{{$pageTitle}}</h4>
      </div>
      <div class="card-body">
        <p class="card-text">
          @lang('Your Withdrawal Log').
        </p>
      </div>
      <div class="table-responsive">
        <table class="table">
          <thead>
           <tr>
                <th>@lang('Trx')</th>
                <th>@lang('Gateway')</th>
                <th>@lang('Amount')</th>
                <th>@lang('Charge')</th>
                <th>@lang('Rate')</th>
                <th>@lang('Receivable')</th>
                <th>@lang('Status')</th>
                <th>@lang('Time')</th>
            </tr>
          </thead>
          <tbody>
            @forelse($withdraws as $k=>$data)
            <tr>
                <td data-label="#@lang('Trx')">{{$data->trx}}</td>
                <td data-label="@lang('Gateway')">{{ __($data->method->name) }}</td>
                <td data-label="@lang('Amount')">
                    <strong>{{showAmount($data->amount)}} {{__($general->cur_text)}}</strong>
                </td>
                <td data-label="@lang('Charge')" class="text-danger">
                    {{showAmount($data->charge)}} {{__($general->cur_text)}}
                </td>
                <td data-label="@lang('Rate')">
                    {{showAmount($data->rate)}} {{__($data->currency)}}
                </td>
                <td data-label="@lang('Receivable')" class="text--base">
                    <strong>{{showAmount($data->final_amount)}} {{__($data->currency)}}</strong>
                </td>
                <td data-label="@lang('Status')">
                     @if($data->status == 1)
                    <span class="badge rounded-pill badge-light-primary me-1">@lang('Complete')</span>
                    @elseif($data->status == 2)
                        <span class="badge rounded-pill badge-light-warning me-1">@lang('Pending')</span>
                    @elseif($data->status == 3)
                        <span class="badge rounded-pill badge-light-danger me-1">@lang('Rejected')</span>
                    @endif

                </td>
                <td data-label="@lang('Time')">
                    {{showDateTime($data->created_at)}}
                </td>
            </tr>
            @empty
                <tr>
                    <td colspan="100%">{{ __($emptyMessage) }}</td>
                </tr>
            @endforelse

          </tbody>
        </table>
      </div>
       {{$withdraws->links()}}
    </div>
  </div>
</div>
<!-- Basic Tables end -->

  {{-- Detail MODAL --}}
    <div id="detailModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Details')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fa fa-times"></i>
                    </button>
                </div>
                <div class="modal-body">

                    <div class="withdraw-detail"></div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">@lang('Close')</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('script')
    <script>
        (function($){
            "use strict";
            $('.approveBtn').on('click', function() {
                var modal = $('#detailModal');
                var feedback = $(this).data('admin_feedback');
                feedback = feedback ? feedback : 'Data Not Found';
                modal.find('.withdraw-detail').html(`<p> ${feedback} </p>`);
                modal.modal('show');
            });
        })(jQuery);

    </script>
@endpush
