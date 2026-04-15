@extends($activeTemplate.'layouts.dashboard')
@section('content')
<!-- Basic Tables start -->
@push('style')
 <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset($activeTemplateTrue. 'app-assets/vendors/css/tables/datatable/dataTables.bootstrap5.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset($activeTemplateTrue. 'app-assets/vendors/css/tables/datatable/responsive.bootstrap4.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset($activeTemplateTrue. 'app-assets/vendors/css/tables/datatable/buttons.bootstrap5.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset($activeTemplateTrue. 'app-assets/vendors/css/tables/datatable/rowGroup.bootstrap4.min.css')}}">
    <!-- END: Vendor CSS-->
@endpush
<!-- Basic table -->
<div class="row" id="basic-datatable">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">{{$pageTitle}}</h4>
      </div>
      <div class="card-body">
        <p class="card-text">
          @lang('Your Loan Log').
        </p>
      </div>
      <div class="table-responsive">
        <table class="datatables-basic table">
          <thead>
           <tr>
                <th>@lang('Trx')</th>
                <th>@lang('Plan')</th>
                <th>@lang('Amount')</th>
                <th>@lang('Interest')</th>
                <th>@lang('Total')</th>
                <th>@lang('Duration')</th>
                <th>@lang('Status')</th>
                <th>@lang('Action')</th>
            </tr>
          </thead>
          <tbody>
            @forelse($loan as $k=>$data)
            @php $plan = App\Models\LoanPlan::where('id', $data->plan_id)->first(); @endphp
            <tr>
                <td data-label="#@lang('Trx')">{{$data->reference}}<br>
                <small>{{showDateTime($data->created_at)}}</small>
                </td>
                <td data-label="@lang('Gateway')">{{ __(@$plan->name) }}</td>
                <td data-label="@lang('Amount')">
                    <strong>{{showAmount($data->amount)}} {{__($general->cur_text)}}</strong>
                </td>
                <td data-label="@lang('Charge')" class="text-danger">
                    {{showAmount($data->interest)}} {{__($general->cur_text)}}
                </td>
                <td data-label="@lang('Rate')">
                    {{showAmount($data->total)}} {{__($general->cur_text)}}
                </td>
                <td data-label="@lang('Receivable')" class="text--base">
                    <strong>{{_($data->duration)}} Months</strong>
                </td>
                <td data-label="@lang('Status')">
                     @if($data->status == 1)
                    <span class="badge rounded-pill badge-light-primary me-1">@lang('Running')</span>
                    @elseif($data->status == 2)
                        <span class="badge rounded-pill badge-light-success me-1">@lang('Completed')</span>
                    @elseif($data->status == 0)
                        <span class="badge rounded-pill badge-light-warning me-1">@lang('Pending')</span>
                    @elseif($data->status == 3)
                        <span class="badge rounded-pill badge-light-danger me-1">@lang('Rejected')</span>
                    @endif

                </td>
                <td data-label="@lang('Time')">
                <a href="{{route('user.viewloan',$data->reference)}}"><i class="text-primary" data-feather='menu'></i></a>
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
       {{$loan->links()}}
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
    <!-- BEGIN: Page Vendor JS-->
    <script src="{{ asset($activeTemplateTrue. 'app-assets/vendors/js/tables/datatable/jquery.dataTables.min.js')}}"></script>

    <script src="{{ asset($activeTemplateTrue. 'app-assets/js/scripts/tables/table-datatables-basic.min.js')}}"></script>

@endpush

