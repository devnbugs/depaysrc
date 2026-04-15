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
 
              </div>
              <hr /> 
                 
              <ul class="list-group text-center">
                <li class="list-group-items d-flex justify-content-between align-items-center">
        @lang('Customer Name'):
        <span><strong>{{$customer}}</strong> </span>
    </li>

    <p class="list-group-items d-flex justify-content-between align-items-center">
        @lang('Customer Address'):
        <span><strong>{{$address}}</strong> </span>
</p>

    <p class="list-group-items d-flex justify-content-between align-items-center">
        @lang('Meter Number'):
        <span><strong>{{$meter}}</strong> </span>
</p>
    <p class="list-group-items d-flex justify-content-between align-items-center">@lang('Status'): <strong>{{$status}}</strong></p>

    <p class="list-group-items d-flex justify-content-between align-items-center">
        @lang('Cost'): <strong>{{$general->cur_sym}}{{showAmount($amount)}} </strong>
</p>

    <p class="list-group-items d-flex justify-content-between align-items-center">
        @lang('Disco'): <strong>{{$disco}}</strong>
</p>
    <p class="list-group-items d-flex justify-content-between align-items-center">
        @lang('Token'): <strong>{{$token}}</strong>
</p>

    <p class="list-group-items d-flex justify-content-between align-items-center">
        @lang('Unit'): <strong>{{$unit}}</strong>
</p>
                        </ul>

                
                  
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

