@extends($activeTemplate.'layouts.dashboard')
@section('content')
<div id="content" class="main-content">
            <div class="layout-px-spacing">

                <div class="row layout-top-spacing">

                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 card">

                <div class="custom--card card-deposit">
                    <div class="card-header">
                        <h5 class="card-title text-center">@lang('Stripe Payment')</h5>
                    </div>
                    <div class="card-body card-body-deposit">


                        <div class="card-wrapper"></div>
                        <br><br>

                        <form role="form" id="payment-form" method="{{$data->method}}" action="{{$data->url}}">
                            @csrf
                            <input type="hidden" value="{{$data->track}}" name="track">
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="name">@lang('Name on Card')</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control custom-input" name="name" placeholder="@lang('Name on Card')" autocomplete="off" autofocus/>
                                        <span class="input-group-text bg--base"><i data-feather='user'></i></span>
                                    </div>

                                </div>
                                <div class="col-md-6">
                                    <label for="cardNumber">@lang('Card Number')</label>
                                    <div class="input-group">
                                        <input type="tel" class="form-control custom-input" name="cardNumber" placeholder="@lang('Valid Card Number')" autocomplete="off" required autofocus/>
                                        <span class="input-group-text bg--base"><i data-feather='credit-card'></i></span>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <label for="cardExpiry">@lang('Expiration Date')</label>
                                    <div class="input-group">
                                    <input type="tel" class="form-control input-sz custom-input" name="cardExpiry" placeholder="@lang('MM / YYYY')" autocomplete="off" required/>
                                    <span class="input-group-text bg--base"><i data-feather='calendar'></i></span>
                                </div> </div>
                                <div class="col-md-6 ">
                                    <label for="cardCVC">@lang('CVC Code')</label>
                                    <div class="input-group">
                                    <input type="tel" class="form-control input-sz custom-input" name="cardCVC" placeholder="@lang('CVC')" autocomplete="off" required/>
                                     <span class="input-group-text bg--base"><i data-feather='shield'></i></span>
                                </div> </div>
                            </div>
                            <br>
                            <button class="btn btn-primary w-100 btn-next place-order text-center" type="submit"> @lang('PAY NOW')
                            </button>

                        </form>


                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
            </div>
        </div> 
@endsection


@push('script')
    <script src="{{ asset('assets/global/js/card.js') }}"></script>

    <script>
        (function ($) {
            "use strict";
            var card = new Card({
                form: '#payment-form',
                container: '.card-wrapper',
                formSelectors: {
                    numberInput: 'input[name="cardNumber"]',
                    expiryInput: 'input[name="cardExpiry"]',
                    cvcInput: 'input[name="cardCVC"]',
                    nameInput: 'input[name="name"]'
                }
            });
        })(jQuery);
    </script>
@endpush
