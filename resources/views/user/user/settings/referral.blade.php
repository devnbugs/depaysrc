@extends($activeTemplate.'layouts.dashboard')
@section('content')
  <!-- account setting page -->
  <div id="content" class="main-content">
            <div class="layout-px-spacing">

                <div class="row layout-top-spacing">

                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
                            <div class="statbox widget box box-shadow">
                                  
									<div class="card">

                           <div class="card-header">
                           <label>Referral Link</label><br>
							<div class="input-group" style="margin-top: 5px">

                            <input readonly value="{{url('/')}}/register/{{$user->username}}"  id="referralURL" class="form-control margin-top-10" type="text" required placeholder="Enter Referral Earning For Level '+lev+++'">
                            <span class="input-group-btn">
                            <button class="btn btn-info margin-top-10 delete_desc text-white" onclick="myFunction()" type="button">Copy</button></span>
                            </div> </div>


										<div class="card-header">
											<h3 class="card-title">Recently Referred</h3>
										</div>

										 <div class="table-responsive--md  table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                            <tr>
                                <th>@lang('Image')</th>
                                <th>@lang('User')</th>
                                <th>@lang('Country')</th>
                                <th>@lang('Joined At')</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($ref as $data)
                            <tr>
                              <td data-label="@lang('User')">
                                    <span class="font-weight-bold"><img src="{{ @getImage(imagePath()['profile']['user']['path'].'/'. $data->image,imagePath()['profile']['user']['size']) }}" width="60" alt=""></span>

                                </td>


                                <td data-label="@lang('User')">
                                    <span class="font-weight-bold">{{$data->fullname}}</span>

                                </td>

                                <td data-label="@lang('Country')">
                                    <span class="font-weight-bold" data-toggle="tooltip" data-original-title="{{ @$data->address->country }}">{{ $data->address->country ?? "Unknown" }}</span>
                                </td>



                                <td data-label="@lang('Joined At')">
                                    {{ showDateTime($data->created_at) }} <br> {{ diffForHumans($data->created_at) }}
                                </td>

                            </tr>
                            @empty
                                <tr>
                                    <td class="text-muted text-center" colspan="100%">You dont have any referred user at the moment. Keep sharing your referral link to invite more users</td>
                                </tr>
                            @endforelse

                            </tbody>
                        </table><!-- table end -->
                    </div>

									</div>
								</div>
							</div>
							<!-- row closed -->
 
							<div class="col-md-12 col-lg-12">
								<div class="card">
									 
									<div class="card-body">
										<div class="table-responsive">
											<table class="table table-bordered ">
												<thead>
													 <tr>
                                <th scope="col">@lang('Date')</th>
                                <th scope="col">@lang('Commission Via')</th>
                                <th scope="col">@lang('Description')</th>

                                <th scope="col">@lang('Level Commission')</th>
                                <th scope="col">@lang('Amount')</th>
                                <th scope="col">@lang('After Balance')</th>
                            </tr>
												</thead>
												<tbody>
                                                @foreach($trans as $k=>$data)
                                                @php $bywho = App\Models\User::whereId($data->bywho)->first(); @endphp
								<tr @if($data->amount < 0)  @endif>
                                    <td data-label="@lang('Date')">{{date('d M, Y h:i:s A', strtotime($data->created_at))}}</td>
                                    <td data-label="@lang('Commission Via')"><strong>{{$bywho->username ?? "Unknown"}}</strong></td>
                                    <td data-label="@lang('Description')">{{__($data->details)}}</td>
                                    <td data-label="@lang('Level Commission')">{{__($data->level)}}</td>
                                    <td data-label="@lang('Amount')">{{__($general->cur_sym)}} {{number_format($data->amount)}}</td>
                                    <td data-label="@lang('After Balance')">{{__($general->cur_sym)}} {{number_format($data->main_amo)}}</td>
                                </tr>
												  @endforeach

												</tbody>
											</table>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<!-- App-content closed -->
			</div>

@stop

@push('script')
    <script>
        function myFunction() {
            var copyText = document.getElementById("referralURL");
            copyText.select();
            copyText.setSelectionRange(0, 99999); /*For mobile devices*/
            document.execCommand("copy");
            var alertStatus = "{{$general->alert}}";
            toastr.success("Referral Link Copied Successfully","👋 Copied!!!");
        }
    </script>
@endpush

  </div>
</section>
<!-- / account setting page -->


