@extends($activeTemplate.'layouts.dashboard')
@section('content')
<!-- Checkout Place Order Right starts -->




<div id="content" class="main-content">
            <div class="layout-px-spacing">

                <div class="row layout-top-spacing">

                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
          <div class="card">
            <div class="card-body">
              <label class="section-label form-label mb-1">Razor Pay</label>
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

                  <form action="{{$data->url}}" method="{{$data->method}}">
                            <input type="hidden" custom="{{$data->custom}}" name="hidden">
                            <script src="{{$data->checkout_js}}"
                                    @foreach($data->val as $key=>$value)
                                    data-{{$key}}="{{$value}}"
                                @endforeach >
                            </script>
                        </form>
               </div>
            </div>
          </div>  </div>    </div>    </div>
          <!-- Checkout Place Order Right ends -->


@endsection

@push('script')
    <script>
        (function ($) {
            "use strict";
            $('input[type="submit"]').addClass("ml-4 mt-4 btn-custom2 text-center btn-lg");
            $('.razorpay-payment-button').addClass('btn--base');
        })(jQuery);
    </script>
@endpush
