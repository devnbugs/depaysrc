@extends('admin.layouts.app')
@section('panel')
<!-- row opened -->
						<div class="row">
							<div class="col-12">

							<div class="card-body card">
							<div class="card-header">
										<div class="card-title">Current Settings</div>
										<div class="card-options ">
											<a href="#" class="card-options-collapse" data-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a>
											<a href="#" class="card-options-fullscreen" data-toggle="card-fullscreen"><i class="fe fe-maximize"></i></a>
											<a href="#" class="card-options-remove" data-toggle="card-remove"><i class="fe fe-x"></i></a>
										</div>
									</div>
                        <div class="table-responsive">
                            <table class="table">

                                <thead>
                                <tr>

                                    <th>Level</th>
                                    <th>Commision</th>
                                </tr>
                                </thead>
                                <tbody>

                                @foreach($trans as $key => $p)
                                    <tr>
                                        <td>LEVEL# {{ $p->level }}</td>
                                        <td>{{ $p->percent }} %</td>
                                    </tr>

                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

								<div class="card">
									<div class="card-header">
										<div class="card-title">Referral System</div>
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
														<h4 class="card-title">Active Referral Features</h4>
													</div>
													<div class="card-body">
														<form action="{{ route('admin.store.feature') }}" class="form-horizontal" method="POST">
                            @csrf
                                <div class="form-row">

                            <div class="form-group col-4">
                                <label class="form-control-label font-weight-bold">@lang('Deposit Commission')</label>
                                <div class="form-check form-switch form-check-primary">
                <input type="checkbox" class="form-check-input"  name="deposit_commission" @if($general->deposit_commission) checked @endif   id="customSwitch5" />
                <label class="form-check-label" for="customSwitch5">
                  <span class="switch-icon-left"><i data-feather="plus"></i></span>
                  <span class="switch-icon-right"><i data-feather="x"></i></span>
                </label>
              </div>

                            </div>




                            <div class="form-group col-4">
                                <label class="form-control-label font-weight-bold">@lang('Invest Commission')</label>
                                <div class="form-check form-switch form-check-primary">
                <input type="checkbox" class="form-check-input" name="invest_commission" @if($general->invest_commission) checked @endif  id="customSwitch6" />
                <label class="form-check-label" for="customSwitch6">
                  <span class="switch-icon-left"><i data-feather="plus"></i></span>
                  <span class="switch-icon-right"><i data-feather="x"></i></span>
                </label>
              </div>

                            </div>



                            <div class="form-group col-4">
                                <label class="form-control-label font-weight-bold">@lang('Interest return commission')</label>
                                <div class="form-check form-switch form-check-primary">
                <input type="checkbox" class="form-check-input"  name="invest_return_commission" @if($general->invest_return_commission) checked @endif  id="customSwitch7"  />
                <label class="form-check-label" for="customSwitch7">
                  <span class="switch-icon-left"><i data-feather="plus"></i></span>
                  <span class="switch-icon-right"><i data-feather="x"></i></span>
                </label>
              </div>

                            </div>


                                </div>
                                <br>
                            <div class="form-group row">
                                <div class="form-group col">
                                    <button type="submit" class="btn btn-block btn-primary mr-2">Submit</button>
                                </div>
                            </div>


                        </form>
													</div>
												</div>
											</div>
											<hr>

											<div class="col-lg-12 col-xl-12 col-md-12 col-sm-12">
												<div class="card  box-shadow-0 mb-0">
													<div class="card-header">
														<h4 class="card-title">Referral Level</h4>
													</div>
													<div class="card-body">
														<form class="form-horizontal" >
															<div class="form-group row">
																<label for="inputName1" class="col-md-3 col-form-label">Enter Referral Level</label>
																<div class="col-md-9">
																	<input  class="form-control" type="number" name="level" id="levelGenerate"  placeholder="Levels">
																</div>
															</div>

															<div class="form-group mb-0 mt-3 row justify-content-end">
																<div class="col-md-9">
																	<button type="button"  style="background-color: {{$general->bclr}}" id="generate" class="btn btn-primary">Generate</button>
																</div>
															</div>
														</form>

														 <form action="{{route('admin.store.refer')}}" id="prantoForm" style="display: none" method="post">
                           {{csrf_field()}}
                           <div class="form-group">
                               <label class="text-success"> Referral Earning : <small></small> </label>
                               <div class="row">
                                   <div class="col-md-12">
                                       <div class="description" style="width: 100%;border: 1px solid #ddd;padding: 10px;border-radius: 5px" >
                                           <div class="row">
                                               <div class="col-md-12" id="planDescriptionContainer">

                                               </div>
                                           </div>


                                       </div>
                                   </div>
                               </div>
                           </div>
                           <hr>
                           <button type="submit"  class="btn btn-primary btn-block">Submit</button>
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


@push('script')
    <script>
        var max = 1;
        $(document).ready(function () {
            $("#generate").on('click', function () {

                var da = $('#levelGenerate').val();
                var a = 0;
                var val = 1;
                var lev = 1;
                var guu = '';
                if (da !== '' && da >0)
                {
                    $('#prantoForm').css('display','block');

                    for (a; a < parseInt(da);a++){

                        console.log()

                        guu += '<div class="input-group" style="margin-top: 5px">\n' +
                            '<input name="level[]" hidden class="form-control margin-top-10" type="number" readonly value="'+val+++'" required placeholder="Level">\n' +
                            '<input name="percent[]" class="form-control margin-top-10" type="text" required placeholder="Enter Referral Earning For Level '+lev+++'">\n' +
                            '<span class="input-group-btn">\n' +
                            '<button class="btn btn-danger margin-top-10 delete_desc text-white" type="button">Remove</button></span>\n' +
                            '</div><br>'
                    }
                    $('#planDescriptionContainer').html(guu);

                }else {
                    alert('Level Field Is Required')
                }

            });

            $(document).on('click', '.delete_desc', function () {
                $(this).closest('.input-group').remove();
            });
        });

    </script>
@endpush
