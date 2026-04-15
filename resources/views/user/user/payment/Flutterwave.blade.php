@extends($activeTemplate.'layouts.dashboard')
@section('content')
<!-- Checkout Place Order Right starts -->




       
<div id="content" class="main-content">
            <div class="layout-px-spacing">

                <div class="row layout-top-spacing">

                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">

          <div class="card">
            <div class="card-body">
              <label class="section-label form-label mb-1">Flutterwave</label>
              <div class="coupons input-group input-group-merge">

                <span class="input-group-text text-primary ps-1" id="input-coupons">

                <img src="{{$deposit->gatewayCurrency()->methodImage()}}" alt="@lang('Image')" width="40" />

                </span>
              </div>
              <hr />
              <div class="price-details">
                <h6 class="price-title">{{$pageTitle}}</h6>
                <ul class="list-group text-center">
                            <p class="list-group-items">
                                @lang('Amount'):
                                <strong>{{showAmount($deposit->amount)}} </strong> {{__($general->cur_text)}}
                            </p>


                        </ul>

                <hr />
                <ul class="list-unstyled">
                  <li class="price-detail">
                    <div class="detail-title detail-total"> @lang('Payable'): {{__($deposit->method_currency)}}:</div>
                    <div class="detail-amt fw-bolder">

                                <strong>{{showAmount($deposit->final_amo)}}</strong>
                    </div>
                  </li>
                </ul>


                 <button type="button" class="btn btn-primary w-100 btn-next place-order" id="btn-confirm" onClick="payWithRave()">@lang('Pay Now')</button>

                 </div>
            </div>
          </div>
               </div>
            </div>
          </div>
          <!-- Checkout Place Order Right ends -->


@endsection
@push('script')
    <script src="https://api.ravepay.co/flwv3-pug/getpaidx/api/flwpbf-inline.js"></script>
    <script>
        "use strict"
        var btn = document.querySelector("#btn-confirm");
        btn.setAttribute("type", "button");
        const API_publicKey = "{{$data->API_publicKey}}";

        function payWithRave() {
            var x = getpaidSetup({
                PBFPubKey: API_publicKey,
                customer_email: "{{$data->customer_email}}",
                amount: "{{$data->amount }}",
                customer_phone: "{{$data->customer_phone}}",
                currency: "{{$data->currency}}",
                txref: "{{$data->txref}}",
                onclose: function () {
                },
                callback: function (response) {
                    var txref = response.tx.txRef;
                    var status = response.tx.status;
                    var chargeResponse = response.tx.chargeResponseCode;
                    if (chargeResponse == "00" || chargeResponse == "0") {
                        window.location = '{{ url('ipn/flutterwave') }}/' + txref + '/' + status;
                    } else {
                        window.location = '{{ url('ipn/flutterwave') }}/' + txref + '/' + status;
                    }
                        // x.close(); // use this to close the modal immediately after payment.
                    }
                });
        }
    </script>
@endpush
