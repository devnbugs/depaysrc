@extends($activeTemplate.'layouts.dashboard')

@section('content')

<div id="content" class="main-content">
    <div class="layout-px-spacing">
        <div class="row layout-top-spacing">
            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
                <div class="card">
                    <div class="card-body">
                        <div class="buy-sell-widget">
                            <div class="tab-content tab-content-default">
                                <div class="tab-pane fade show active" id="buy" role="tabpanel">
                                    <form action="{{ route('user.beta.trx.verifyMonnify') }}" method="get">
                                        @csrf
                                        <div class="row">
                                            <div class="form-group col-12">
                                                    <label class="@if(Auth::user()->darkmode != 0) text-white @endif">Enter Transaction ID</label><br>
                                                        <small class="text-muted">Transaction ID Sent to Your Email</small>
                                                        <div class="input-group mb-3">
                                                            <input class="form-control" name="trx" type="text" placeholder=MNFY|00|11223344556677|123456>
                                                        </div>
                                                        <div></div>
                                            <button type="submit" class="btn btn-primary" id="buyButton">Check Transaction</button>
                                        </div>
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
