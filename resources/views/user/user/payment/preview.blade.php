@extends($activeTemplate.'layouts.dashboard')
@section('content')

 <!-- Checkout Place Order Right starts -->



 <div id="content" class="main-content">
            <div class="layout-px-spacing">

                <div class="row layout-top-spacing">

                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
          <div class="card">
            <div class="card-body">
              <label class="section-label form-label mb-1">{{$pageTitle}}</label>
              <div class="coupons input-group input-group-merge">

                <span class="input-group-text text-primary ps-1" id="input-coupons">

                <img src="{{ $data->gatewayCurrency()->methodImage() }}" alt="@lang('Image')" width="40" />

                </span>
              </div>
              <hr />
              <div class="price-details">
                <h6 class="price-title">{{$pageTitle}}</h6>
                <ul class="list-group text-center">
                            <p class="list-group-items">
                                @lang('Amount'):
                                <strong>{{showAmount($data->amount)}} </strong> {{__($general->cur_text)}}
                            </p>
                            <p class="list-group-items">
                                @lang('Charge'):
                                <strong>{{showAmount($data->charge)}}</strong> {{__($general->cur_text)}}
                            </p>
                            <p class="list-group-items">
                                @lang('Payable'): <strong> {{showAmount($data->amount + $data->charge)}}</strong> {{__($general->cur_text)}}
                            </p>
                            <p class="list-group-items">
                                @lang('Conversion Rate'): <strong>1 {{__($general->cur_text)}} = {{showAmount($data->rate)}}  {{__($data->baseCurrency())}}</strong>
                            </p>



                            @if($data->gateway->crypto==1)
                                <p class="list-group-items">
                                    @lang('Conversion with')
                                    <b> {{ __($data->method_currency) }}</b> @lang('and final value will Show on next step')
                                </p>
                            @endif
                        </ul>

                <hr />
                <ul class="list-unstyled">
                  <li class="price-detail">
                    <div class="detail-title detail-total"> @lang('In') {{$data->baseCurrency()}}:</div>
                    <div class="detail-amt fw-bolder">

                                <strong>{{showAmount($data->final_amo)}}</strong>
                    </div>
                  </li>
                </ul>
                 @if( 1000 >$data->method_code)
                            <a href="{{route('user.deposit.confirm')}}" class="btn btn--primary w-100 btn-next place-order">@lang('Pay Now')</a>
                 @else
                            <a href="{{route('user.deposit.manual.confirm')}}" class="btn btn--primary w-100 btn-next place-order">@lang('Pay Now')</a>
                 @endif
               </div>
            </div>
          </div>
          <!-- Checkout Place Order Right ends -->

          </div>
          </div>
         
@endsection

@push('style')
<style>
    .list-group-item{
        background: transparent;
    }
</style>
@endpush

