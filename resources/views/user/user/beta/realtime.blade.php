@extends($activeTemplate.'layouts.dashboard')

@section('content')

<div id="content" class="main-content">
    <div class="layout-px-spacing">
        <div class="row layout-top-spacing">
            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
                <div class="card">
                    <div class="card-header">
                            <div class="card-body">
                                <!--div class="table-responsive">
                                   <table class="table card-table table-striped text-nowrap table-bordered border-top">
                                      <thead>
                                        <tr>
                                          <th>Network</th>
                                          <th>Data Purchase</th>
                                          <th>Airtime</th>
                                          <th>Price</th>
                                          <th>API Server</th>
                                          <th>Most State</th>
                                        </tr>
                                      </thead>
                                      <tbody>
                                        <tr>
                                          <td>Airtel NG</td>
                                          <td><span class="badge bg-success badge-pill"> Good - 98%</span></</td>
                                          <td><span class="badge bg-success badge-pill"> Good - 99%</span></</td>
                                          <td class="text-success">{{$general->cur_sym}} +0.92 </td>
                                          <td>$584,038</td>
                                          <td>$63,980,869,420.81</td>
                                          <td>19,037</td>
                                        </tr>
                                        <tr>
                                          <td>MTN NG</td>
                                          <td><span class="badge bg-success badge-pill"> Good - 99%</span></</td>
                                          <td><span class="badge bg-success badge-pill"> Good - 100%</span></</td>
                                          <td class="text-danger">{{$general->cur_sym}} -0.77 </td>
                                          <td>$270,548</td>
                                          <td>$42,608,450,438.17</td>
                                          <td>120,736</td>
                                        </tr>
                                        <tr>
                                          <td>GLO NG</td>
                                          <td><span class="badge bg-success badge-pill"> Good - 89%</span></</td>
                                          <td><span class="badge bg-success badge-pill"> Good - 90%</span></</td>
                                          <td class="text-danger">{{$general->cur_sym}} -0.54 </td>
                                          <td>$83,058</td>
                                          <td>$145,586,754,045.55</td>
                                          <td>83,147,694</td>
                                        </tr>
                                        <tr>
                                          <td>9Mobile NG</td>
                                          <td><span class="badge bg-success badge-pill"> Good - 97%</span></</td>
                                          <td><span class="badge bg-success badge-pill"> Good - 99%</span></</td>
                                          <td class="text-danger">{{$general->cur_sym}} -0.12 </td>
                                          <td>$48,488</td>
                                          <td>$12,110,978,045.95</td>
                                          <td>48,466,313</td>
                                        </tr>
                                        <tr>
                                          <td>SMILE NG</td>
                                          <td><span class="badge bg-success badge-pill"> Good - 99%</span></</td>
                                          <td><span class="badge bg-success badge-pill"> Good - 100%</span></</td>
                                          <td class="text-success">{{$general->cur_sym}} -0.92 </td>
                                          <td>$17,188</td>
                                          <td>$12,675,224,217.65</td>
                                          <td>17,149,724</td>
                                        </tr>
                                      </tbody>
                                    </table>
                              </div -->
								<div>
									<i class="icon__signal-strength signal-0">
										<span class="bar-1"></span>
										<span class="bar-2"></span>
										<span class="bar-3"></span>
										<span class="bar-4"></span>
									</i>
									<i class="icon__signal-strength signal-1">
										<span class="bar-1"></span>
										<span class="bar-2"></span>
										<span class="bar-3"></span>
										<span class="bar-4"></span>
									</i>
									<i class="icon__signal-strength signal-2">
										<span class="bar-1"></span>
										<span class="bar-2"></span>
										<span class="bar-3"></span>
										<span class="bar-4"></span>
									</i>
									<i class="icon__signal-strength signal-3">
										<span class="bar-1"></span>
										<span class="bar-2"></span>
										<span class="bar-3"></span>
										<span class="bar-4"></span>
									</i>
									<i class="icon__signal-strength signal-4">
										<span class="bar-1"></span>
										<span class="bar-2"></span>
										<span class="bar-3"></span>
										<span class="bar-4"></span>
									</i>
								</div>
                        </div>
                    </div>    
                </div>
            </div>
        </div>
    </div>
</div>
@endsection