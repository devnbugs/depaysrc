@extends($activeTemplate.'layouts.dashboard')


@section('content')

@push('style')

@endpush

<!-- Basic multiple Column Form section start -->
<section id="multiple-column-form">
  <div class="row">

  <div class="content-header">
          <h5 class="mb-0">{{$pageTitle}}</h5>
          <small class="text-muted">Please fill the form below to proceed with fund transfer</small>
   </div>



    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h4 class="card-title">Request Form</h4>
        </div>
        <div class="card-body">
          <form action="" method="post">
@csrf
            <div class="content-header">
          <small class="text-muted">Please Select Type</small>
        </div>
          <div class="row">

          <div class="mb-1 col-md-12">
        <select id="first" name="type" class="form-control select-2">
            <option>Select Recipient Option</option>
            <option value="1">Saved Beneficiary</option>
            <option value="2">Enter Beneficiary's Details</option>
        </select>
       </div>

            <div class="mb-1 col-md-12 red box">
              <label class="form-label" for="username">Saved Beneficiaries</label>
              <select class="form-control" name="beneficiary" >
            <option disabled selected >Select Beneficiary</option>
            @foreach($benefit as $data)
            <option value="{{$data->details}}">{{$data->details}}</option>
            @endforeach
              </select>
            </div>


            <div class="mb-1 col-md-12 green box">
              <label class="form-label" for="username">Beneficiary's Account Number</label>
              <input
                type="number"
                name="username"
                id="username"
                class="form-control"
                placeholder="Enter Account Number"
              />
            </div>



          </div>


           <div class="row">
            <div class="mb-1 col-md-12">
              <label class="form-label" for="first-name">Amount</label>
              <input type="text" name="amount" id="amount" class="form-control" placeholder="0.00" />
            </div>
          </div>



        <div class="d-flex justify-content-between">


          <button class="btn btn--primary text-white btn-next">
            <span class="align-middle d-sm-inline-block d-none">Transfer</span>
            <i data-feather="arrow-right" class="align-middle ms-sm-25 ms-0"></i>
          </button>
        </div>
      </div>
        </div>
      </div>
    </div>
  </div>
</section>
<!-- Basic Floating Label Form section end -->


<!-- Basic Tables start -->
<div class="row" id="basic-table">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <p class="card-text">
          @lang('Your Account Beneficiary Log').
        </p>
      </div>
      <div class="table-responsive">
        <table class="table">
          <thead>
           <tr>
                <th>@lang('#')</th>
                <th>@lang('Name')</th>
                <th>@lang('Date Added')</th>
                <th>@lang('Delete')</th>
            </tr>
          </thead>
          <tbody>
          @php $i = 1; @endphp
            @foreach($benefit as $k=>$data)
            <tr>
                <td data-label="#@lang('#')">{{$i++}}</td>
                <td data-label="@lang('Name')">{{ __($data->details) }}</td>
                <td data-label="@lang('Time')">
                    {!! date(' D d, M Y', strtotime($data->created_at)) !!}</h6>
              <small>{{date('h:i A', strtotime($data->created_at))}}
                </td>

                <td data-label="@lang('Delete')">
                    <a
                        href="{{ route('user.deletebeneficiary', $data->id) }}"
                        class="btn btn-danger btn-sm"
                        data-confirm-link
                        data-confirm-tone="danger"
                        data-confirm-title="Delete beneficiary"
                        data-confirm-message="This beneficiary will be removed from your saved list."
                        data-confirm-accept-text="Delete"
                    >
                        Delete
                    </a>
                </td>
            </tr>

            @endforeach

          </tbody>
        </table>
      </div>
       @if(count($benefit) < 1)
          <div class="demo-spacing-0">
            <div class="alert alert-danger" role="alert">
              <div class="alert-body"><strong>Hello {{Auth::user()->username}}!</strong> You dont have any account beneficiary at the moment.</div>
            </div>
          </div>
       @endif
    </div>
  </div>
</div>
<!-- Basic Tables end -->




<!-- Basic Tables start -->
<div class="row" id="basic-table">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <p class="card-text">
          @lang('Your Fund Transfer Log').
        </p>
      </div>
      <div class="table-responsive">
        <table class="table">
          <thead>
           <tr>
                <th>@lang('#')</th>
                <th>@lang('Reference')</th>
                <th>@lang('Beneficiary')</th>
                <th>@lang('Amount')</th>
                <th>@lang('Date')</th>
            </tr>
          </thead>
          <tbody>
          @php $i = 1; @endphp
            @foreach($log as $k=>$data)
            <tr>
                <td data-label="#@lang('#')">{{$i++}}</td>
                <td data-label="@lang('Ref')">{{ __($data->trx) }}</td>
                <td data-label="@lang('Name')">{{ __($data->details) }}</td>
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
              <div class="alert-body"><strong>Hello {{Auth::user()->username}}!</strong> You dont have any fund transfer log at the moment.</div>
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
