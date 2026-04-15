@extends('admin.layouts.app')
@section('panel')

<!-- Basic Tables start -->
<div class="row" id="basic-table">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <p class="card-text">
          @lang('Other Bank Transfer Log').
        </p>
      </div>
      <div class="table-responsive">
        <table class="table">
          <thead>
           <tr>
                <th>@lang('#')</th>
                <th>@lang('Reference')</th>
                <th>@lang('Details')</th>
                <th>@lang('Amount') & Charge</th>
                <th>@lang('Date')</th>
                <th>@lang('Action')</th>
            </tr>
          </thead>
          <tbody>
          @php $i = 1; @endphp
            @foreach($log as $k=>$data)
            <tr>
                <td data-label="#@lang('#')">{{$i++}}</td>
                <td data-label="@lang('Ref')">{{ __($data->trx) }}<br>
                <span>@if($data->status == 1)  <badge class="badge rounded-pill badge-glow bg-success">Successful</badge> @elseif($data->status == 2)  <badge class="badge rounded-pill badge-glow bg-danger badge-sm">Declined</badge>@else  <badge class="badge rounded-pill badge-glow bg-warning">Pending</badge> @endif</span>
                </td>
                <td data-label="@lang('Name')">{!!$data->details !!}</td>
                <td data-label="@lang('Amount')">{{$general->cur_sym}}{{ number_format($data->amount,2) }}<br>
                <small class="text-danger">{{$general->cur_sym}}{{ number_format($data->charge,2) }}</small>
                </td>

                <td data-label="@lang('Time')">
                    {!! date(' D d, M Y', strtotime($data->created_at)) !!}</h6>
              <small>{{date('h:i A', strtotime($data->created_at))}}
                </td>
                 <td data-label="@lang('Time')">
                <a class="btn btn--primary btn-sm text-white" href="{{route('admin.transfer.view',$data->trx)}}"> View</a>
                </td>


            </tr>

            @endforeach

          </tbody>
        </table>
      </div>
       @if(count($log) < 1)
          <div class="demo-spacing-0">
            <div class="alert alert-danger" role="alert">
              <div class="alert-body">There is no fund transfer log at the moment.</div>
            </div>
          </div>
          @endif
       {{$log->links()}}
    </div>
  </div>
</div>
<!-- Basic Tables end -->

@endsection



@push('script')

<script type="text/javascript" src="http://code.jquery.com/jquery.js"></script>
<script type="text/javascript">
$(document).ready(function(){
    $("#first").change(function(){
        $(this).find("option:selected").each(function(){
            if($(this).attr("value")=="1"){
                $(".box").not(".red").hide();
                $(".red").show();
            }
            else if($(this).attr("value")=="2"){
                $(".box").not(".green").hide();
                $(".green").show();
            }
            else if($(this).attr("value")=="3"){
                $(".box").not(".blue").hide();
                $(".blue").show();
            }
            else{
                $(".box").hide();
            }
        });
    }).change();
});
</script>
@endpush

