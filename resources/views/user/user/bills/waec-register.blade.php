@extends($activeTemplate.'layouts.dashboard')

@section('content')
@push('style')
    <link rel="stylesheet" type="text/css" href="{{ asset($activeTemplateTrue. 'user-assets/plugins/table/datatable/datatables.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset($activeTemplateTrue. 'user-assets/plugins/table/datatable/custom_dt_html5.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset($activeTemplateTrue. 'user-assets/plugins/table/datatable/dt-global_style.css')}}">
@endpush
<div id="content" class="main-content">
            <div class="layout-px-spacing">

                <div class="row layout-top-spacing">

                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
                    <div class="card">
            <div class="card-body">
 
   
                                <div class="buy-sell-widget">
                                     
                                    <div class="tab-content tab-content-default">
                                        <div class="tab-pane fade show active" id="buy" role="tabpanel">

                                            <form class="contact-form" action="{{route('user.registerwaec',$network['serviceID'])}}" class="currency_validate" action="" method="post" enctype="multipart/form-data">
                                            @csrf

                                                <div class="row">
                                                <div class="form-group col-12" >
                                                    <label class="@if(Auth::user()->darkmode != 0) text-white @endif">{{$network['ServiceName']}}</label>
                                                    <div class="input-group mb-3">
                                                    <select name="variant"  id="variant" onchange="myFunction()"   class="form-control" data-placeholder="Network">
                                                    <option selected disabled>Please Select Form</option>
                                                    @foreach($forms as $data)
													<option value="{{$data['variation_code']}}" data-name="{{$data['name']}} " data-amount="{{$general->cur_sym}}{{$data['variation_amount']}}" data-cost="{{$data['variation_amount']}}">{{$data['name']}} </option>
													 @endforeach
												</select>
                                                @push('script')
                                                <script>
                                                function myFunction() {
                                                var amount = $("#variant option:selected").attr('data-amount');
                                                var cost = $("#variant option:selected").attr('data-cost');
                                                var name = $("#variant option:selected").attr('data-name');
                                                var charge = {{$charge}} 
                                                var total = +cost + +charge;

                                                document.getElementById("amount").innerHTML = "Amount: {{$general->cur_sym}}"+amount;
                                                document.getElementById("total").innerHTML = "<br>Total: {{$general->cur_sym}}"+total;
                                                document.getElementById("pay").value = total;
                                                document.getElementById("name").value = name;
                                                document.getElementById("note").innerHTML = "<br>Please note that a transaction fee of {{$general->cur_sym}}{{number_format($charge,2)}} will be added to this transaction";


                                                };
                                                </script>
                                                @endpush
                                                <input id="pay" hidden name="amount">
                                                <input id="name" hidden name="name">
                                                    </div> 
                                                </div>


                                                <div class="form-group col-12">
                                                     <b id="amount"></b>
                                                    <small id="note"></small>
                                                    <b id="total"></b>
                                                     
                                                    
                                                </div>
 

                                                <div class="form-group col-12">
                                                    <label class="@if(Auth::user()->darkmode != 0) text-white @endif">Enter Phone Number</label>
                                                    <div class="input-group mb-3">
                                                         <input class="form-control" name="phone" type="number" placeholder="080123456789">

                                                    </div> 
                                                </div>

                                                <div class="form-group col-12">
                                                   
                                                    <button type="submit" class="btn btn-porimary text-white">Proceed</button>
 
                                                </div>


                                        </div>
                                        </form>

                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>

                </div>

               
                <!-- row opened -->
							<div class="col-xl-12">
								<div class="card">
									<div class="card-header">
										<div class="card-title">Recent WAEC Registration Token Purchase Log  </div>
										<div class="card-options">
											<a href="#" class="card-options-collapse" data-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a>
											<a href="#" class="card-options-fullscreen" data-toggle="card-fullscreen"><i class="fe fe-maximize"></i></a>
											<a href="#" class="card-options-remove" data-toggle="card-remove"><i class="fe fe-x"></i></a>
										</div>
									</div>
									<div class="card-body">
										<div class="table-responsive">
                                        <table id="html5-extension" class="table table-hover non-hover" style="width:100%">
												<thead>
													<tr>
														<th>ID</th>
														<th>TRX</th>
														<th>Phone</th>
														<th>Examination</th>
														<th>Amount</th>
														<th>PIN</th>
														<th>Status</th>
														<th>Date</th>
													</tr>
												</thead>
												<tbody>
												@foreach($bills as $data)
													<tr>
                                                    <td>{{$loop->iteration}}</td>
														<td>#{{$data->trx}}</td>
														<td class="text-success">{{$data->phone}}</td>
														<td>{{strtoupper($data->accountname)}}<br><small>{{strtoupper($data->network)}}</small><br>
														</td>

														<td>{{$general->cur_sym}}{{number_format($data->amount,2)}}</td>
                                                        <td>{{$data->accountnumber}}</td>

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
 

@endsection
@push('script')
<!-- BEGIN PAGE LEVEL CUSTOM SCRIPTS -->
<script src="{{ asset($activeTemplateTrue. 'user-assets/plugins/table/datatable/datatables.js')}}"></script>
    <!-- NOTE TO Use Copy CSV Excel PDF Print Options You Must Include These Files  -->
    <script src="{{ asset($activeTemplateTrue. 'user-assets/plugins/table/datatable/button-ext/dataTables.buttons.min.js')}}"></script>
    <script src="{{ asset($activeTemplateTrue. 'user-assets/plugins/table/datatable/button-ext/jszip.min.js')}}"></script>    
    <script src="{{ asset($activeTemplateTrue. 'user-assets/plugins/table/datatable/button-ext/buttons.html5.min.js')}}"></script>
    <script src="{{ asset($activeTemplateTrue. 'user-assets/plugins/table/datatable/button-ext/buttons.print.min.js')}}"></script>
    <script>
        $('#html5-extension').DataTable( {
            "dom": "<'dt--top-section'<'row'<'col-sm-12 col-md-6 d-flex justify-content-md-start justify-content-center'B><'col-sm-12 col-md-6 d-flex justify-content-md-end justify-content-center mt-md-0 mt-3'f>>>" +
        "<'table-responsive'tr>" +
        "<'dt--bottom-section d-sm-flex justify-content-sm-between text-center'<'dt--pages-count  mb-sm-0 mb-3'i><'dt--pagination'p>>",
            buttons: {
                buttons: [
                    { extend: 'copy', className: 'btn btn-sm' },
                    { extend: 'csv', className: 'btn btn-sm' },
                    { extend: 'excel', className: 'btn btn-sm' },
                    { extend: 'print', className: 'btn btn-sm' }
                ]
            },
            "oLanguage": {
                "oPaginate": { "sPrevious": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>', "sNext": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>' },
                "sInfo": "Showing page _PAGE_ of _PAGES_",
                "sSearch": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>',
                "sSearchPlaceholder": "Search...",
               "sLengthMenu": "Results :  _MENU_",
            },
            "stripeClasses": [],
            "lengthMenu": [7, 10, 20, 50],
            "pageLength": 7 
        } );
    </script>
    <!-- END PAGE LEVEL CUSTOM SCRIPTS -->
@endpush
