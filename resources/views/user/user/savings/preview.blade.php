@extends($activeTemplate.'layouts.dashboard')

@section('content')

    <!-- BEGIN: Page CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset($activeTemplateTrue. 'app-assets/css/core/menu/menu-types/vertical-menu.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset($activeTemplateTrue. 'app-assets/css/plugins/forms/form-validation.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset($activeTemplateTrue. 'app-assets/css/plugins/forms/form-wizard.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset($activeTemplateTrue. 'app-assets/vendors/css/forms/wizard/bs-stepper.min.css')}}">
    <link rel="stylesheet" type="text/css" href=".{{ asset($activeTemplateTrue. 'app-assets/vendors/css/forms/select/select2.min.css')}}">
    <!-- END: Page CSS-->

  <div class="bs-stepper checkout-tab-steps">
  <!-- Wizard starts -->
  <div class="bs-stepper-header">

    <div class="step" data-target="#step-address" role="tab" id="step-address-trigger">
      <button type="button" class="step-trigger">
        <span class="bs-stepper-box">
          <i data-feather="home" class="font-medium-3"></i>
        </span>
        <span class="bs-stepper-label">
          <span class="bs-stepper-title">{{$pageTitle}}</span>
          <span class="bs-stepper-subtitle">Enter Your Account Details Below</span>
        </span>
      </button>
    </div>

  </div>
  <!-- Wizard ends -->

  <div class="bs-stepper-content">

    <!-- Checkout Customer Address Right starts -->
        <div class="customer-card">
          <div class="card">
            <div class="card-header">
              <h4 class="card-title">@lang('Current Balance'): {{ showAmount(auth()->user()->balance)}}  {{ __($general->cur_text) }}</h4>
            </div>
            <div class="card-body actions">
              <p class="card-text">@lang('Request Amount'): {{showAmount($withdraw->amount)  }} {{__($general->cur_text)}}</p>
              <p class="card-text">@lang('Withdrawal Charge'): {{showAmount($withdraw->charge) }} {{__($general->cur_text)}}</p>
              <p class="card-text">@lang('After Charge'): {{showAmount($withdraw->after_charge) }} {{__($general->cur_text)}}</p>
              <p class="card-text">@lang('Conversion Rate'): 1 {{__($general->cur_text)}} = {{showAmount($withdraw->rate)  }} {{__($withdraw->currency)}}</p>
              <p class="card-text">@lang('You Will Get'): {{showAmount($withdraw->final_amount) }} {{__($withdraw->currency)}}</p>
             <!-- <button type="button" class="btn btn-primary w-100 btn-next delivery-address mt-2">
                Deliver To This Address
              </button>-->
            </div>
          </div>
        </div>
        <!-- Checkout Customer Address Right ends -->


    <!-- Checkout Customer Address Starts -->
    <form action="{{route('user.withdraw.submit')}}" method="post" enctype="multipart/form-data">
                                    @csrf

<!-- Checkout Customer Address Left starts -->
        <div class="card">
          <div class="card-header flex-column align-items-start">
            <h4 class="card-title">Enter Account Details</h4>
            <p class="card-text text-muted mt-25">@lang('Be sure to enter correct account details. We will not be liable to any loss arising from entering wrong account details')</p>
          </div>
          <div class="card-body">
            <div class="row">
             @if($withdraw->method->user_data)
             @foreach($withdraw->method->user_data as $k => $v)
             @if($v->type == "text")
              <div class="col-md-12 col-sm-12">
                <div class="mb-2">
                <label class="form-label" cfor="checkout-name"><strong>{{__($v->field_level)}} @if($v->validation == 'required') <span class="text-danger">*</span>  @endif</strong></label>

                  <input type="text" name="{{$k}}" class="form-control" value="{{old($k)}}" placeholder="{{__($v->field_level)}}" @if($v->validation == "required") required @endif>
                </div>
                @if ($errors->has($k))
                                                    <span class="text-danger">{{ __($errors->first($k)) }}</span>
                @endif
              </div>
               @elseif($v->type == "textarea")
              <div class="col-md-12 col-sm-12">
                <div class="mb-2">
                  <label class="form-label" cfor="checkout-number"><strong>{{__($v->field_level)}} @if($v->validation == 'required') <span class="text-danger">*</span>  @endif</strong>:</label>
                  <textarea name="{{$k}}"  class="form-control"  placeholder="{{__($v->field_level)}}" rows="3" @if($v->validation == "required") required @endif>{{old($k)}}</textarea>
                                                @if ($errors->has($k))
                                                    <span class="text-danger">{{ __($errors->first($k)) }}</span>
                                                @endif
                </div>
              </div>
              @elseif($v->type == "file")
              <div class="col-md-12 col-sm-12">
                <div class="mb-2">
                  <label class="form-label" cfor="checkout-apt-number"><strong>{{__($v->field_level)}} @if($v->validation == 'required') <span class="text-danger">*</span>  @endif</strong></label>
                 <input type="file" class="form-control" name="{{$k}}" accept="image/*" @if($v->validation == "required") required @endif>
                </div>
                 @if ($errors->has($k))
                                                    <br>
                                                    <span class="text-danger">{{ __($errors->first($k)) }}</span>
                 @endif
              </div>
               @endif
               @endforeach
               @endif

              <div class="col-12">
                <button type="submit" class="btn btn-primary btn-next delivery-address">@lang('Confirm')</button>
              </div>
            </div>
          </div>
        </div>
        </form>
        <!-- Checkout Customer Address Left ends -->

    </div>
    <!-- Checkout Customer Address Ends -->

    <!-- </div> -->
@endsection


@push('script-lib')
<script src="{{asset($activeTemplateTrue.'/js/bootstrap-fileinput.js')}}"></script>
@endpush
@push('style-lib')
<link rel="stylesheet" href="{{asset($activeTemplateTrue.'/css/bootstrap-fileinput.css')}}">
@endpush

@push('script')
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

@push('style')
<style>
    .fileinput .thumbnail{
        max-height: 300px;
        width: 100%;
    }
</style>
@endpush
@push('script')
<script src="{{ asset($activeTemplateTrue. 'app-assets/vendors/js/forms/wizard/bs-stepper.min.js')}}"></script>
<script src="{{ asset($activeTemplateTrue. 'app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
<script src="{{ asset($activeTemplateTrue. 'app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}"></script>
<script src="https://pixinvent.com/demo/vuexy-html-bootstrap-admin-template/app-assets/js/scripts/forms/form-wizard.min.js"></script>

@endpush
