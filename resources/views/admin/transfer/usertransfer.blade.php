@extends('admin.layouts.app')
@section('panel')





<!-- Basic Tables start -->
<div class="row" id="basic-table">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <p class="card-text">
          @lang('User To User Fund Transfer Log').
        </p>
      </div>
      <div class="table-responsive">
        <table class="table">
          <thead>
           <tr>
                <th>@lang('#')</th>
                <th>@lang('Sender')</th>
                <th>@lang('Beneficiary')</th>
                <th>@lang('Reference')</th>
                <th>@lang('Amount')</th>
                <th>@lang('Date')</th>
            </tr>
          </thead>
          <tbody>
          @php $i = 1; @endphp
            @foreach($log as $k=>$data)
            @php $user = App\Models\User::where('id', $data->user_id)->first(); @endphp
            @php $ben = App\Models\User::where('account_number', $data->details)->first(); @endphp

            <tr>
                <td data-label="#@lang('#')">{{$i++}}</td>
                 <td data-label="@lang('User')">{{ __(@$user->username) }}<br>
                 <a href="{{ route('admin.users.detail', $user->id) }}" class="btn btn-sm btn-primary" data-toggle="tooltip" title="" data-original-title="@lang('Details')">
                 View
                 </a>
                </td>
                <td data-label="@lang('Name')">{{ __($data->details) }}<br>
                 <a href="{{ route('admin.users.detail', @$ben->id ?? "0") }}" class="btn btn-sm btn--primary text-white" data-toggle="tooltip" title="" data-original-title="@lang('Details')">
                 View
                 </a>
                 </td>
                <td data-label="@lang('Ref')">{{ __($data->trx) }}</td>
                <td data-label="@lang('Amount')">{{$general->cur_sym}}{{ number_format($data->amount,2) }}</td>
                <td data-label="@lang('Time')">
                    {!! date(' D d, M Y', strtotime($data->created_at)) !!}</h6>
              <small>{{date('h:i A', strtotime($data->created_at))}}
                </td>


            </tr>

            @endforeach

          </tbody>
        </table>
      </div>
       @if(count($log) < 1)
          <div class="demo-spacing-0">
            <div class="alert alert-danger" role="alert">
              <div class="alert-body"><strong>Hello!</strong> You dont have any fund transfer log at the moment.</div>
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

