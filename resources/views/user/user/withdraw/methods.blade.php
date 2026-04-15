@extends($activeTemplate.'layouts.dashboard')


@section('content')

@push('style')

<div id="content" class="main-content">
            <div class="layout-px-spacing">

                <div class="row layout-top-spacing">

                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
                    <div class="card">
            <div class="card-body">
<form action="{{route('user.withdraw.money')}}" method="post">
@csrf
<section class="horizontal-wizard">
  <div class="bs-stepper horizontal-wizard-example">
    
    <div class="bs-stepper-content">
      <div id="account-details" class="content" role="tabpanel" aria-labelledby="account-details-trigger">
        <div class="content-header">
          <h5 class="mb-0">Withdrawal Gateway</h5>
          <small class="text-muted">Please Select A Payment Method</small>
        </div>
          <div class="row">
          <p id="image"></p>
            <div class="mb-1 col-md-12">
              <label class="form-label" for="username">Method</label>
              <select id="gateway" onchange="myFunction()" name="" class="form-control">
              <option selected disabled>Select An Option</option>
              @foreach($withdrawMethod as $data)
              <option
                                data-id="{{$data->id}}"
                               data-resource="{{$data}}"
                               data-min_amount="{{showAmount($data->min_limit)}}"
                               data-max_amount="{{showAmount($data->max_limit)}}"
                               data-fix_charge="{{showAmount($data->fixed_charge)}}"
                               data-currency="{{$data->currency}}"
                               data-percent_charge="{{showAmount($data->percent_charge)}}"
                               data-base_symbol="{{__($general->cur_text)}}"
                               data-image="<img class='round' height='40' width='40' src='{{getImage(imagePath()['withdraw']['method']['path'].'/'. $data->image,imagePath()['withdraw']['method']['size'])}}' width='100'>"

              > {{__($data->name)}}</option>
              @endforeach
              </select>
              @push('script')
<script>
function myFunction() {
var name = $("#gateway option:selected").attr('data-name');
var currency = $("#gateway option:selected").attr('data-currency');
var fixed_charge = $("#gateway option:selected").attr('data-fix_charge');
var percent_charge = $("#gateway option:selected").attr('data-percent_charge');
var method_code = $("#gateway option:selected").attr('data-id');
var min_amount = $("#gateway option:selected").attr('data-min_amount');
var max_amount = $("#gateway option:selected").attr('data-max_amount');
var image = $("#gateway option:selected").attr('data-image');
document.getElementById("fcharge").value = fixed_charge+"{{$general->cur_text}}";
document.getElementById("pcharge").value = percent_charge+"%";
document.getElementById("min").value = min_amount;
document.getElementById("max").value = max_amount;
document.getElementById("currency").value = currency;
document.getElementById("method_code").value = method_code;
document.getElementById("image").innerHTML = image;
document.getElementById("name").value = name;

 };
</script>
@endpush
            </div>
            <div class="mb-1 col-md-6">
              <label class="form-label" for="email"><br>Fixed Charge</label>
              <input
                type="text"
                name="currency"
                id="fcharge" readonly
                class="form-control"
                placeholder="{{$general->cur_text}}"
                aria-label="{{$general->cur_text}}"
              />
            </div>

             <div class="mb-1 col-md-6">
              <label class="form-label" for="email"><br>Percentage Charge</label>
              <input
                type="text"
                name="currency"
                id="pcharge" readonly
                class="form-control"
                placeholder="{{$general->cur_text}}"
                aria-label="{{$general->cur_text}}"
              />
            </div>

                <input type="hidden" id="currency" name="currency" >
                <input type="hidden" id="method_code" name="method_code">
          </div>

          <div class="row">
            <div class="mb-1 form-password-toggle col-md-6">
              <label class="form-label" for="Min"><br>Min Amount</label>
              <input
                type="text" readonly
                name="min"
                id="min"
                class="form-control"
                placeholder="0.00"
              />
            </div>
            <div class="mb-1 form-password-toggle col-md-6">
              <label class="form-label" for="Max"><br>Max Amount</label>
              <input
                type="text" readonly
                name="max"
                id="max"
                class="form-control"
                placeholder="0.00"
              />
            </div>
          </div>
          <br>
           <div class="row">
            <div class="mb-1 col-md-12">
              <label class="form-label" for="first-name">Amount</label>
              <input type="text" name="amount" id="amount" class="form-control" placeholder="0.00" />
            </div>
          </div>
          <br>
        <div class="d-flex justify-content-between">

          <button class="btn btn--primary text-white btn-next">
            <span class="align-middle d-sm-inline-block d-none">Proceed</span>
            <i data-feather="arrow-right" class="align-middle ms-sm-25 ms-0"></i>
          </button>
          </div>
      </div>
    </div>
  </div>    </div>
      </div>
    </div>
  </div>    </div> 
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

