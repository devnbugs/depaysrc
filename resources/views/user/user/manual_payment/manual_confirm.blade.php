@extends($activeTemplate.'layouts.dashboard')

@section('content')
div id="content" class="main-content">

            <div class="container"> 

                <div class="row layout-top-spacing">
                <div class="col-lg-12 layout-spacing">
                <br><br><br><br><br> 
          <div class="card">
            <div class="card-body">
           
                
            <label class="section-label form-label mb-1">{{$pageTitle}}</label>
                        <form action="{{ route('user.deposit.manual.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-12 text-center">
                                    <p class="text-center mt-2">@lang('You have requested') <b class="text--base">{{ showAmount($data['amount'])  }} {{__($general->cur_text)}}</b> , @lang('Please pay')
                                        <b class="text--base">{{showAmount($data['final_amo']) .' '.$data['method_currency'] }} </b> @lang('for successful payment')
                                    </p>
                                    <h4 class="text-center mb-4">@lang('Please follow the instruction below')</h4>

                            @if( $data->gateway->crypto == 1)
                            <div class="col-md-12">
                            <div class="alert alert-info">
                            <strong>Info!</strong><br> Please send your payment to the {{$data->gateway->name}} wallet address below and fill in your transaction details accordingly. <br><br>Your payment will be approved once your payment has been confirmed on our server
                            </div>
                            <br>

                            <img src="https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl={{$data->gateway->name.':'.$data->gateway->description}}&choe=UTF-8\" style='width:190px;'  />



                            <div class="form-group mb-1">
                           <label>{{$data->gateway->name}} Wallet Address</label><br>
							<div class="input-group" style="margin-top: 5px">

                            <input readonly value="{{$data->gateway->description}}"  id="referralURL" class="form-control margin-top-10" type="text" required placeholder="Enter Referral Earning For Level '+lev+++'">
                            <span class="input-group-btn">
                            <button class="btn btn-info margin-top-10 delete_desc text-white" onclick="myFunction()" type="button">Copy</button></span>
                            </div> </div></div>

                            @else

                                    <p class="my-4 text-center">Deposit Description: @php echo  $data->gateway->description @endphp</p>
                            @endif
                            <hr>

                                </div>

                                @if($method->gateway_parameter)

                                    @foreach(json_decode($method->gateway_parameter) as $k => $v)

                                        @if($v->type == "text")
                                            <div class="col-md-12">
                                                <div class="form-group mb-1">
                                                    <label><strong>{{__(inputTitle($v->field_level))}} @if($v->validation == 'required') <span class="text-danger">*</span>  @endif</strong></label>
                                                    <input type="text" class="form-control" name="{{$k}}" value="{{old($k)}}" placeholder="{{__($v->field_level)}}">
                                                </div>
                                            </div>
                                        @elseif($v->type == "textarea")
                                                <div class="col-md-12">
                                                    <div class="form-group mb-1">
                                                        <label><strong>{{__(inputTitle($v->field_level))}} @if($v->validation == 'required') <span class="text-danger">*</span>  @endif</strong></label>
                                                        <textarea name="{{$k}}" class="form-control" placeholder="{{__($v->field_level)}}" rows="3">{{old($k)}}</textarea>

                                                    </div>
                                                </div>
                                        @elseif($v->type == "file")
                                            <div class="col-md-12">
                                                <div class="form-group mb-1">
                                                    <label><strong>{{__($v->field_level)}} @if($v->validation == 'required') <span class="text-danger">*</span>  @endif</strong></label>
                                                    <br>

                                                    <div class="fileinput fileinput-new " data-provides="fileinput">
                                                        <div class="fileinput-new thumbnail withdraw-thumbnail" data-trigger="fileinput">
                                                            <img src="{{ asset(getImage('/')) }}" alt="@lang('Image')">
                                                        </div>
                                                        <div class="fileinput-preview fileinput-exists thumbnail wh-200-150"></div>

                                                        <div class="img-input-div">
                                                            <span class="btn btn-info btn-file">
                                                                <span class="fileinput-new "> @lang('Select') {{__($v->field_level)}}</span>
                                                                <span class="fileinput-exists"> @lang('Change')</span>
                                                                <input type="file" name="{{$k}}" accept="image/*" >
                                                            </span>
                                                            <a href="#" class="btn btn-danger fileinput-exists"
                                                            data-dismiss="fileinput"> @lang('Remove')</a>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                @endif
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <button type="submit" class="btn btn--base w-100 mt-2 text-center text-white">@lang('Pay Now')</button>
                                    </div>
                                </div>

                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div> 

@endsection
@push('style')
<style>
    .withdraw-thumbnail{
        max-width: 220px;
        max-height: 220px
    }
</style>
@endpush
@push('script-lib')
<script src="{{asset($activeTemplateTrue.'/js/bootstrap-fileinput.js')}}"></script>
@endpush
@push('style-lib')
<link rel="stylesheet" href="{{asset($activeTemplateTrue.'/css/bootstrap-fileinput.css')}}">
@endpush


@push('style')
<style>
    .fileinput .thumbnail{
        max-height: 300px;
        width: 100%;
    }
</style>
@endpush

@push('script')
   <script>
        function myFunction() {
            var copyText = document.getElementById("referralURL");
            copyText.select();
            copyText.setSelectionRange(0, 99999); /*For mobile devices*/
            document.execCommand("copy");
            var alertStatus = "{{$general->alert}}";
            toastr.success("Wallet Address Copied Successfully","👋 Copied!!!");
        }
    </script>
<script>

    (function($){

        "use strict";

            $('.withdraw-thumbnail').hide();

            $('.clickBtn').on('click', function() {

                var classNmae = $('.fileinput').attr('class');

                if(classNmae != 'fileinput fileinput-exists'){
                    $('.withdraw-thumbnail').hide();
                }else{

                    $('.fileinput-preview img').css({"width":"100%", "height":"300px", "object-fit":"contain"});

                    $('.withdraw-thumbnail').show();

                }

            });

    })(jQuery);

</script>
@endpush
