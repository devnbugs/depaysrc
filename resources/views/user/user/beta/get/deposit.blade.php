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
                                <div>
                                    <div class="wallet-balance">
                                        <p></p>
                                        <h5 class="">Auto Account FUNDING</h5>
                                    </div>
									@if($user->kyc == 0)
                                    <div>
                                        <small class="text-muted">KYC Level : PARTIAL</small>
                                    </div>
									@elseif($user->kyc == 1)
									<div>
                                        <small class="text-muted">KYC Level : TIER ~</small>
                                    </div>
									@endif
                                    <small class="text-muted"><br></small>
                                    <!--ul class="navbar-nav flex-row ml-auto ">
                                        <li class="nav-item more-dropdown">
                                            <div class="dropdown  custom-dropdown-icon">
                                                <a class="dropdown-toggle btn" href="#" role="button" id="customDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span>Check Balance Codes</span> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down"><polyline points="6 9 12 15 18 9"></polyline></svg></a>
                                                <!--div class="dropdown-menu dropdown-menu-right" aria-labelledby="customDropdown">
                                                    <a class="dropdown-item" data-value="*310#" href="tel:*310#">Check Balance</a>
                                                </div>
                                            </div>
                                        </li>
                                    </ul-->
                                </div>
                            </div>
                        </div>
					</div>
				</div>
			</div>
		</div>
		<div class="row layout-top-spacing">
            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
                <div class="card">
                    <div class="card-body">
                        <div class="buy-sell-widget">
                            <div class="tab-content tab-content-default">	
								<div>
									<div class="tab-content tab-content-default">
										<div class="tab-pane fade show active" id="buy" role="tabpanel">
											<form action="{{ route('user.beta.get.customer') }}" method="POST">
												@csrf
												<div class="row">
													@if($user->pslinked == 0)
													<div class="btn-group col-12">
														<button type="submit" class="btn btn-primary text-white" id="buyButton" >Activate Account</button>
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
		@if($user->pslinked = 1 && $user->psverified == 0)
		<div class="row layout-top-spacing">
            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
                <div class="card">
                    <div class="card-body">
                        <div class="buy-sell-widget">
                            <div class="tab-content tab-content-default">	
								<div>
									<div class="tab-content tab-content-default">
										<div class="tab-pane fade show active" id="buy" role="tabpanel">
											<form action="{{ route('user.beta.get.inject') }}" method="POST">
											@csrf
													<div class="btn-group col-12">
														<button type="submit" class="btn btn-primary text-white" id="buyButton" >Generate Account</button>  
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
		@endif
		<div class="row layout-top-spacing">
            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
                <div class="card">
                    <div class="card-body">
                        <div class="buy-sell-widget">
                            <div class="tab-content tab-content-default">	
								<div>
									<div class="tab-content tab-content-default">
										<label>Assigned Account Details</label><br>
										<span><label>{{$user->bN1}}</label><br></span>
										<div class="input-group" style="margin-top: 5px">
										<small class="text-muted">{{$user->aN1}}</small>
										<input readonly value="{{$user->aNo1}}"  id="referralURL" class="form-control margin-top-10" type="text" required placeholder="Account Number">
										<span class="input-group-btn">
										<button class="btn btn-info margin-top-10 delete_desc text-white" onclick="myFunction()" type="button">Copy</button></span>
                            
									</div>
								</div>
							</div>
						</div>
					</div>
                </div>
            </div>
        </div>
@endsection