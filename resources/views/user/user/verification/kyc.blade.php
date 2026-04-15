@extends($activeTemplate . 'layouts.dashboard')

@section('content')
<div id="content" class="main-content">
    <div class="layout-px-spacing">
        <div class="row layout-top-spacing">
            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
                <div class="statbox widget box box-shadow">
                    <div class="card">
                        <div class="card-body">
                            <div class="buy-sell-widget">
                                <div class="tab-content tab-content-default">
                                    <form action="{{ route('user.submitkyc') }}" class="list-view product-checkout" id="checkout-address" method="post" enctype="multipart/form-data">
                                        @csrf
                                        @if($user->kyc == 0)
                                            <div>
                                                <div class="alert alert-info mb-3">
                                                    Complete at least ₦{{ number_format((float) $minimumFundingAmount, 2) }} in successful deposits before starting KYC.
                                                    Your current funded total is ₦{{ number_format((float) $fundedAmount, 2) }}.
                                                </div>
                                                <label class="@if(Auth::user()->darkmode != 0) text-white @endif">Enter NIN</label>
                                                <div class="input-group mb-3">
                                                    <input class="form-control" maxlength="16" type="text" name="nin" placeholder="NIN (optional if BVN is provided)">
                                                </div>
                                                <label class="@if(Auth::user()->darkmode != 0) text-white @endif">Enter BVN</label>
                                                <div class="input-group mb-3">
                                                    <input class="form-control" maxlength="11" type="tel" name="bvn" placeholder="BVN (optional if NIN is provided)">
                                                </div>
                                                <br>
                                                <div class="btn-group col-12">
                                                    <button type="submit" class="btn btn-primary text-white" id="buyButton">Submit</button>
                                                </div>
                                            </div>
										@elseif($user->kyc == 1)
											
											<div>
                                               <span>Kyc Verification Submitted</span>
                                            </div>
										
                                        @endif
                                    </form>
                                </div>
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
<script src="https://pixinvent.com/demo/vuexy-html-bootstrap-admin-template/app-assets/js/scripts/forms/pickers/form-pickers.min.js"></script>
@endpush
