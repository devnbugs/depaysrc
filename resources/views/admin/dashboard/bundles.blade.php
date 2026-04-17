@extends('admin.layouts.app')
    
@section('panel')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5>@lang('Data Bundles Management')</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                <tr>
                                    <th>@lang('Name')</th>
                                    <th class="d-none d-md-table-cell">@lang('Data Type')</th>
                                    <th class="d-none d-lg-table-cell">@lang('Network')</th>
                                    <th class="d-none d-xl-table-cell">@lang('Plan')</th>
                                    <th class="text-end">@lang('Cost')</th>
                                    <th class="text-center">@lang('Status')</th>
                                    <th class="text-center">@lang('Action')</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($bundles as $bundle)
                                    <tr>
                                        <td>
                                            <strong>{{ $bundle->name }}</strong>
                                            <div class="d-md-none">
                                                <small class="d-block text-muted">{{ $bundle->datatype }}</small>
                                                <small class="d-block text-muted">{{ $bundle->network }}</small>
                                            </div>
                                        </td>
                                        <td class="d-none d-md-table-cell">
                                            <span class="badge bg-info">{{ $bundle->datatype }}</span>
                                        </td>
                                        <td class="d-none d-lg-table-cell">
                                            <span class="badge bg-secondary">{{ $bundle->network }}</span>
                                        </td>
                                        <td class="d-none d-xl-table-cell">
                                            <small>{{ $bundle->plan }}</small>
                                        </td>
                                        <td class="text-end">
                                            <strong class="text-success">{{ $general->cur_sym }}{{ $bundle->cost }}</strong>
                                        </td>
                                        <td class="text-center">
                                            @if($bundle->status == 1)
                                                <span class="badge bg-success">@lang('ON')</span>
                                            @else
                                                <span class="badge bg-danger">@lang('OFF')</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('admin.dashboard.edit', ['bundle' => $bundle->id]) }}" class="btn btn-sm btn-primary">@lang('Edit')</a>
                                        </td>
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

@endsection