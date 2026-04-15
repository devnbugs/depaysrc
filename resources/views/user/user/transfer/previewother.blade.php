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
          <span class="bs-stepper-subtitle">Preview Tranfer Details Below</span>
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
              <p class="card-text">@lang('Request Amount'): {{showAmount($amount)  }} {{__($general->cur_text)}}</p>
              <p class="card-text">@lang('Transfer Charge'): {{$general->transferfee }}%</p>
              <p class="card-text">@lang('Charge'): {{showAmount($amount * $general->transferfee/100) }} {{__($general->cur_text)}}</p>

            </div>
          </div>
        </div>
        <!-- Checkout Customer Address Right ends -->


    <!-- Checkout Customer Address Starts -->
    <form action="" method="post" enctype="multipart/form-data">
                                    @csrf

<!-- Checkout Customer Address Left starts -->
        <div class="card">
          <div class="card-header flex-column align-items-start">
            <h4 class="card-title">Account Details</h4>
            <p class="card-text text-muted mt-25">@lang('Be sure you enter correct account details. We will not be liable to any loss arising from entering wrong account details')</p>
          </div>
          <div class="card-body">
            <div class="row">
               <div class="col-md-12 col-sm-12">
                <div class="mb-2">
                <label class="form-label" cfor="checkout-name"><strong>Account Number</label>

                  <input type="text"class="form-control" value="{{@$accountnumber}}"  readonly>
                </div>

              </div>

               <div class="col-md-12 col-sm-12">
                <div class="mb-2">
                <label class="form-label" cfor="checkout-name"><strong>Bank Name</label>

                  <input type="text"class="form-control" value="{{@$bankname}}"  readonly>
                </div>

              </div>


             <div class="col-md-12 col-sm-12">
                <div class="mb-2">
                <label class="form-label" cfor="checkout-name"><strong>Beneficiary Name</label>

                  <input type="text"class="form-control" value="{{@$name}}"  readonly>
                </div>

              </div>

              @if($type == 2)


              <div class="col-md-12 col-sm-12">
                <div class="mb-2">
                <label class="form-label" cfor="checkout-name"><strong>Swift Code</label>

                  <input type="text"class="form-control" value="{{@$bankcode}}"  readonly>
                </div>

              </div>
              <div class="col-md-12 col-sm-12">
                <div class="mb-2">
                <label class="form-label" cfor="checkout-name"><strong>Country</label>

                  <input type="text"class="form-control" value="{{@$country}}"  readonly>
                </div>

              </div>


              @endif


            <br>

              <div class="d-flex flex-column">
              <label class="form-check-label mb-50" for="customSwitch3">Select Who Pays Transaction Charge</label>
              <div class="demo-inline-spacing">
            <div class="form-check form-check-inline">
              <input
                class="form-check-input"
                type="radio"
                name="chargepay"
                id="inlineRadio1"
                value="1"

              />
              <label class="form-check-label" for="inlineRadio1">Beneficiary</label>
            </div>
            <div class="form-check form-check-inline">
              <input
                class="form-check-input"
                type="radio"
                name="chargepay"
                id="inlineRadio2"
                value="2" checked
              />
              <label class="form-check-label" for="inlineRadio2">Sender</label>
            </div></div>

            </div>
            <br>

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
