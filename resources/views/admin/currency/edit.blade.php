@extends('admin.layouts.app')
@section('panel')
<!-- row opened -->
						<div class="row">
							<div class="col-12">
								<div class="card">
									<div class="card-header">
										<div class="card-title">{{$currency->name}}  Currency Manager</div>
										<div class="card-options ">
											<a href="#" class="card-options-collapse" data-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a>
											<a href="#" class="card-options-fullscreen" data-toggle="card-fullscreen"><i class="fe fe-maximize"></i></a>
											<a href="#" class="card-options-remove" data-toggle="card-remove"><i class="fe fe-x"></i></a>
										</div>
									</div>
									<div class="card-body">
										<div class="row">
											<div class="col-lg-6 col-xl-12 col-md-12 col-sm-12">
												<div class="card  box-shadow-0">
													<div class="card-header">
														<h4 class="card-title">{{$currency->name}} API Credentials</h4>
														<br>
													</div>
													<div class="card-body">
														<form action="{{route('admin.coin.edit',$currency->id)}}" class="form-horizontal" method="POST">
                                                        {{csrf_field()}}
															<div class="form-group mb-1">
															<small>{{$currency->name}} Wallet API Key</small>
																<input type="text" class="form-control" value="{{$currency->apikey}}" name="apikey" placeholder="API Wallet Key">
															</div>
															<div class="form-group  mb-1">
															<small>{{$currency->name}} Wallet Password</small>
																<input type="text" class="form-control" value="{{$currency->apipass}}" name="apipass" placeholder="API Wallet Password">
															</div>

															<div class="form-group mb-0 mt-3 justify-content-end">
																<div>
																	<button type="submit" class="btn text-white btn--primary">Update</button>
																</div>
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
						<!-- row closed -->
					</div>
				</div>
				<!-- App-content closed -->
			</div>
@endsection
