@extends($activeTemplate.'layouts.dashboard')


@section('content')

@push('style')

    <!-- BEGIN: Page CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset($activeTemplateTrue. 'app-assets/css/core/menu/menu-types/vertical-menu.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset($activeTemplateTrue. 'app-assets/css/plugins/forms/form-validation.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset($activeTemplateTrue. 'app-assets/css/plugins/forms/form-wizard.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset($activeTemplateTrue. 'app-assets/vendors/css/forms/wizard/bs-stepper.min.css')}}">
    <link rel="stylesheet" type="text/css" href=".{{ asset($activeTemplateTrue. 'app-assets/vendors/css/forms/select/select2.min.css')}}">
    <!-- END: Page CSS-->
@endpush

<!-- Deposit Wizard -->
<form action="" method="post">
@csrf
<section class="horizontal-wizard">
  <div class="bs-stepper horizontal-wizard-example">
    <div class="bs-stepper-header" role="tablist">
      <div class="step" data-target="#account-details" role="tab" id="account-details-trigger">
        <button type="button" class="step-trigger">
          <span class="bs-stepper-box"><i data-feather="bar-chart"></i></span>
          <span class="bs-stepper-label">
            <span class="bs-stepper-title">Other Wallet</span>
            <span class="bs-stepper-subtitle">Multi Wallet Withdrawal</span>
          </span>
        </button>
      </div>


    </div>
    <div class="bs-stepper-content">
      <div id="account-details" class="content" role="tabpanel" aria-labelledby="account-details-trigger">
        <div class="content-header">
          <h5 class="mb-0 text-danger">Please Note</h5>
          <small class="text-info">Please note that the fund in your any of your multi wallet will be transfered to your Central Wallet from where you can proceed to withdraw using any of our available withdrawal methods<br>
         <br> Please note that the minimum amount you can withdraw from your compounding wallet at any time is <b>{{$general->cur_sym}} {{number_format($general->min_compound,2)}}</b>
          </small>
        </div>
          <div class="row">
          <p id="image"></p>
            <div class="mb-1 col-md-12">
              <label class="form-label" for="username">Wallet</label>
              <select id="gateway" required name="wallet" class="form-control">
              <option selected disabled> Please select wallet</option>
              <option  value="2"  > Investment Return Wallet ( {{$general->cur_sym}} {{number_format(Auth::user()->invest_return,2)}} )</option>
              <option  value="3"  > Referral Bonus Wallet ( {{$general->cur_sym}} {{number_format(Auth::user()->ref_bonus,2)}} )</option>
              </select>

            </div>


           <div class="row">
            <div class="mb-1 col-md-12">
              <label class="form-label" for="first-name">Amount</label>
              <input type="text" name="amount" id="amount" class="form-control" placeholder="0.00" />
            </div>
          </div>
        <div class="d-flex justify-content-between">

          <button class="btn btn--primary text-white btn-next">
            <span class="align-middle d-sm-inline-block d-none">Proceed</span>
            <i data-feather="arrow-right" class="align-middle ms-sm-25 ms-0"></i>
          </button>
        </div>
      </div>
    </div>
  </div>
</section>
</form>
<!-- /Deposit Wizard -->

@endsection



@push('script')
<script src="{{ asset($activeTemplateTrue. 'app-assets/vendors/js/forms/wizard/bs-stepper.min.js')}}"></script>
<script src="{{ asset($activeTemplateTrue. 'app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
<script src="{{ asset($activeTemplateTrue. 'app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}"></script>
<script src="{{ asset($activeTemplateTrue. 'app-assets/js/scripts/forms/form-wizard.min.js')}}"></script>

@endpush

