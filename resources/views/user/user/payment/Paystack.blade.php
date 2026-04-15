@extends($activeTemplate.'layouts.dashboard')
@section('content')
<!-- Checkout Place Order Right starts -->




<div id="content" class="main-content">
            <div class="layout-px-spacing">

                <div class="row layout-top-spacing">

                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">

          <div class="card">
            <div class="card-body">
              <label class="section-label form-label mb-1">Paystack</label>
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

                  <form action="{{ route('ipn.'.$deposit->gateway->alias) }}" method="POST" class="text-center">
                        @csrf
                 <button type="button" class="btn btn-primary w-100 btn-next place-order" id="btn-confirm">@lang('Pay Now')</button>
                        <script
                            src="//js.paystack.co/v1/inline.js"
                            data-key="{{ $data->key }}"
                            data-email="{{ $data->email }}"
                            data-amount="{{$data->amount}}"
                            data-currency="{{$data->currency}}"
                            data-ref="{{ $data->ref }}"
                            data-custom-button="btn-confirm"
                        >
                        </script>
                </form>
               </div>
            </div>
          </div>

          </div>
            </div>
          </div>
          <!-- Checkout Place Order Right ends -->


@endsection
