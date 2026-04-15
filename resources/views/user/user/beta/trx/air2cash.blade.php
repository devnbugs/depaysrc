@extends($activeTemplate.'layouts.dashboard')

@section('content')

<div id="content" class="main-content">
    <div class="layout-px-spacing">
        <div class="row layout-top-spacing">
            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
                <div class="card">
                    <div class="card-body">
                        <div class="card">
                <div class="card-header">
                    <div class="card-title">Airtime Sell History</div>
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
                                    <th>Phone</th>
                                    <th>Network</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Receipt</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bills as $data)
                                    <tr>
                                        <td>#{{$data->trx}}</td>
                                        <td class="text-success">{{$data->phone}}</td>
                                        <td>{{strtoupper($data->network)}}<br></td>
                                        <td>{{$general->cur_sym}}{{number_format($data->amount,2)}}</td>

                                        @if($data->status == 0)
                                            <td><span class="badge bg-warning badge-pill">Pending</span></td>
                                        @elseif($data->status == 1)
                                            <td><span class="badge bg-success badge-pill">Completed</span></td>
                                        @else
                                            <td><span class="badge bg-danger badge-pill">Declined</span></td>
                                        @endif

                                        <td>{{date(' d M, Y ', strtotime($data->created_at))}} {{date('h:i A', strtotime($data->created_at))}}</td>
                                        <td>
                                            <a href="{{ route('user.beta.receipt', ['billId' => $data->id]) }}" class="btn btn-outline-secondary" type="button" target="_blank">
                                                View Receipt
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @if(count($bills) < 1)
                            <div class="alert alert-danger alert-dismissible fade show mb-0" role="alert">
                                <span class="alert-inner--icon"><i class="fe fe-slash"></i></span>
                                <span class="alert-inner--text"><strong>Hey {{$user->username}}</strong>   You don't have any transaction History at the moment</span>
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
    </div>
</div>

@endsection
