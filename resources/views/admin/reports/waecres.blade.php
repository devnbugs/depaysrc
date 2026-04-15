@extends('admin.layouts.app')

@section('panel')

 <div class="row" id="basic-table">
  <div class="col-12">



                <div class="row">

                <!-- row opened -->
							<div class="col-xl-12">
								<div class="card">
									<div class="card-header">
										<div class="card-title">Recent WAEC Result Cheker Payment Log </div>
										<div class="card-options">
											<a href="#" class="card-options-collapse" data-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a>
											<a href="#" class="card-options-fullscreen" data-toggle="card-fullscreen"><i class="fe fe-maximize"></i></a>
											<a href="#" class="card-options-remove" data-toggle="card-remove"><i class="fe fe-x"></i></a>
										</div>
									</div>
									<div class="card-body">
										<div class="table-responsive">
											<table class="table card-table table-striped text-nowrap table-bordered border-top">
												<thead>
													<tr>
														<th>ID</th>
														<th>Exam</th>
														<th>Type</th>
														<th>Amount</th>
														<th>Details</th>
														<th>Status</th>
														<th>Date</th>
													</tr>
												</thead>
												<tbody>
												@foreach($bills as $data)
													<tr>
														<td>#{{$data->trx}}</td>
														<td class="text-success">
														{!!$data->accountname!!}
														</td>
														<td>{{strtoupper($data->network)}}<br>
														</td>

														<td>{{$general->cur_sym}}{{number_format($data->amount,2)}}</td>


														<td class="text-success">{!!$data->accountnumber!!}</td>

                                                       @if($data->status == 0)
														<td><span class="badge bg-warning badge-pill">Pending</span></td>
														@elseif($data->status == 1)
														<td><span class="badge bg-success badge-pill">Completed</span></td>
														@else
														<td><span class="badge bg-danger badge-pill">Declined</span></td>
														@endif
														<td>{{date(' d M, Y ', strtotime($data->created_at))}} {{date('h:i A', strtotime($data->created_at))}}</td>
													</tr>
											  @endforeach


												</tbody>
											</table>
											 @if(count($bills) < 1)
											 <div class="alert alert-danger alert-dismissible fade show mb-0" role="alert">
			<span class="alert-inner--icon"><i class="fe fe-slash"></i></span>
			<span class="alert-inner--text"><strong>Hey Boss!</strong>   You dont have any transaction log at the moment</span>

		</div>

											  @endif
										</div>
									</div>
								</div>
							</div>



                </div>
            </div>
        </div>



    </div>

@endsection
