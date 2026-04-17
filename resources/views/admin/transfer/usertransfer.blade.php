@extends('admin.layouts.app')
@section('panel')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5>@lang('User To User Fund Transfer Log')</h5>
                    </div>
                    <div class="card-body">
                        @if(count($log) > 0)
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                    <tr>
                                        <th class="text-center" style="width: 50px;">#</th>
                                        <th>@lang('Sender')</th>
                                        <th class="d-none d-md-table-cell">@lang('Beneficiary')</th>
                                        <th class="d-none d-lg-table-cell">@lang('Reference')</th>
                                        <th class="text-end">@lang('Amount')</th>
                                        <th class="text-end">@lang('Date')</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @php $i = 1; @endphp
                                    @foreach($log as $k=>$data)
                                        @php $user = App\Models\User::where('id', $data->user_id)->first(); @endphp
                                        @php $ben = App\Models\User::where('account_number', $data->details)->first(); @endphp

                                        <tr>
                                            <td class="text-center">
                                                <small>{{ $i++ }}</small>
                                            </td>
                                            <td>
                                                <div class="font-weight-bold">{{ __(@$user->username ?? 'N/A') }}</div>
                                                <a href="{{ route('admin.users.detail', $user->id ?? 0) }}" class="btn btn-sm btn-outline-primary mt-1">View</a>
                                                
                                                <div class="d-md-none mt-2">
                                                    <small class="d-block text-muted">@lang('Recipient'): {{ __($data->details) }}</small>
                                                    <a href="{{ route('admin.users.detail', @$ben->id ?? 0) }}" class="btn btn-sm btn-outline-secondary mt-1">View Recipient</a>
                                                </div>
                                            </td>
                                            <td class="d-none d-md-table-cell">
                                                <div>{{ __($data->details) }}</div>
                                                @if($ben)
                                                    <a href="{{ route('admin.users.detail', $ben->id) }}" class="btn btn-sm btn-outline-secondary">View</a>
                                                @else
                                                    <span class="badge bg-warning">Unknown</span>
                                                @endif
                                            </td>
                                            <td class="d-none d-lg-table-cell">
                                                <code>{{ __($data->trx) }}</code>
                                            </td>
                                            <td class="text-end">
                                                <strong class="text-success">{{ $general->cur_sym }}{{ number_format($data->amount, 2) }}</strong>
                                                <div class="d-lg-none">
                                                    <small class="d-block text-muted mt-1">{{ __($data->trx) }}</small>
                                                </div>
                                            </td>
                                            <td class="text-end">
                                                <small>{{ date('D d, M Y', strtotime($data->created_at)) }}</small>
                                                <br>
                                                <small class="text-muted">{{ date('h:i A', strtotime($data->created_at)) }}</small>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-warning" role="alert">
                                <strong>@lang('No Data')</strong> @lang('You don\'t have any fund transfer log at the moment.')
                            </div>
                        @endif
                    </div>
                    @if(count($log) > 0)
                        <div class="card-footer py-3">
                            {{ $log->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection

@push('script')
    <script type="text/javascript">
        $(document).ready(function(){
            $("#first").change(function(){
                $(this).find("option:selected").each(function(){
                    if($(this).attr("value")=="1"){
                        $(".box").not(".red").hide();
                        $(".red").show();
                    }
                    else if($(this).attr("value")=="2"){
                        $(".box").not(".green").hide();
                        $(".green").show();
                    }
                    else if($(this).attr("value")=="3"){
                        $(".box").not(".blue").hide();
                        $(".blue").show();
                    }
                    else{
                        $(".box").hide();
                    }
                });
            }).change();
        });
    </script>
@endpush

