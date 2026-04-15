@extends('admin.layouts.app')
@section('panel')
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

      <div class="table-responsive">
        <table class="datatables-basic table">
          <thead>
           <tr>
                <th>@lang('Borrower')</th>
                <th>@lang('Trx')</th>
                <th>@lang('Plan')</th>
                <th>@lang('Amount')</th>
                <th>@lang('Interest')</th>
                <th>@lang('Total')</th>
                <th>@lang('Duration')</th>
                <th>@lang('Status')</th>
                @if(\Route::current()->getName() != 'admin.loan.declined') <th>@lang('Action')</th> @endif

            </tr>
          </thead>
          <tbody>
            @forelse($loan as $k=>$data)
            @php $plan = App\Models\LoanPlan::where('id', $data->plan_id)->first(); @endphp
            @php $user = App\Models\User::where('id', $data->user_id)->first(); @endphp
            <tr>
            <td data-label="@lang('User')">{{ __(@$user->username) }}<br>
                 <a href="{{ route('admin.users.detail', @$user->id) }}" class="btn btn-sm btn-primary" data-toggle="tooltip" title="" data-original-title="@lang('Details')">
                                        View
                 </a>
                </td>
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
                    <span class="badge rounded-pill bg-light-primary me-1">@lang('Running')</span>
                    @elseif($data->status == 2)
                    <span class="badge rounded-pill bg-light-success me-1">@lang('Completed')</span>
                    @elseif($data->status == 0)
                    <span class="badge rounded-pill bg-light-warning me-1">@lang('Pending')</span>
                    @elseif($data->status == 3)
                    <span class="badge rounded-pill bg-light-danger me-1">@lang('Rejected')</span>
                    @endif

                </td>
                @if(\Route::current()->getName() != 'admin.loan.declined')
                <td data-label="@lang('Time')">
                @if($data->status == 0)
                <a data-bs-toggle="modal" data-bs-target="#approve" class="btn btn-sm text-white" >Approve</a>
                <a data-bs-toggle="modal" data-bs-target="#reject" class="btn btn-sm btn-danger" >Reject</a>
                @else
                <a class="btn btn--primary btn-sm text-white" href="{{route('admin.loan.view',$data->reference)}}"> View</a>
                @endif
                </td>
                @endif
            </tr>


              <!-- Modal -->
              <div
                class="modal fade text-start"
                id="approve"
                tabindex="-1"
                aria-labelledby="myModalLabel1"
                aria-hidden="true"
              >
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h4 class="modal-title" id="myModalLabel1">Approve Loan</h4>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                      <h5>Are You Sure</h5>
                      <p>
                        If you approve this loan, a sum of {{$general->cur_sym}}{{showAmount($data->amount)}}  will be credited into user's wallet.<br>
                       <b> Are you sure to proceed?</b>
                      </p>
                    </div>
                    <div class="modal-footer">
                      <a class="btn btn-warning" data-bs-dismiss="modal">Dismiss</a>
                      <a href="{{route('admin.loan.approveloan',$data->reference)}}" class="btn text-white btn--primary" >Approve</a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- Basic trigger modal end -->

             <!-- Modal -->
              <div
                class="modal fade text-start"
                id="reject"
                tabindex="-1"
                aria-labelledby="myModalLabel1"
                aria-hidden="true"
              >
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h4 class="modal-title" id="myModalLabel1">Reject Loan</h4>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                      <h5>Are You Sure</h5>
                      <p>
                        You are about to reject this loan request. This cannot be undone.<br>
                        <b> Are you sure to proceed?</b>
                      </p>
                    </div>
                    <div class="modal-footer">
                      <a class="btn btn-success" data-bs-dismiss="modal">Dismiss</a>
                      <a href="{{route('admin.loan.rejectloan',$data->reference)}}" class="btn btn-danger">Reject</a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- Basic trigger modal end -->
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

