@extends('admin.layouts.app')
@section('panel')
<!-- Basic Tables start -->

<!-- Basic table -->
<div class="row" id="basic-datatable">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">{{$pageTitle}}</h4>
      </div>

      <div class="table-responsive">
        <table class="datatables-basic table">
          <thead>
           <tr>
                <th>@lang('User')</th>
                <th>@lang('Trx')</th>
                <th>@lang('Type')</th>
                <th>@lang('Amount')</th>
                <th>@lang('Balance')</th>
                <th>@lang('Status')</th>
                <th>@lang('Action')</th>
            </tr>
          </thead>
          <tbody>
            @forelse($saved as $k=>$data)
             @php $user = App\Models\User::where('id', $data->user_id)->first(); @endphp

            <tr>
            <td data-label="@lang('Gateway')">{{ __(@$user->username) }}<br>
                 <a href="{{ route('admin.users.detail', @$user->id) }}" class="btn btn-sm btn-primary" data-toggle="tooltip" title="" data-original-title="@lang('Details')">
                                        View
                 </a>
                </td>
                <td data-label="#@lang('Trx')">{{$data->reference}}<br>
                <small>{{showDateTime($data->created_at)}}</small>
                </td>
                <td data-label="@lang('Gateway')">@if($data->type == 1) Recurrent Savings @elseif($data->type == 2) Target Savings @endif</td>
                <td data-label="@lang('Amount')">
                    <strong>{{showAmount($data->amount)}} {{__($general->cur_text)}}</strong><br>
                    <small>@if($data->type == 1) Recurrent Amount @elseif($data->type == 2) Targeted Amount @endif</small>
                </td>
                <td data-label="@lang('Balance')" class="text-primary">
                    {{showAmount($data->balance)}} {{__($general->cur_text)}}
                </td>


                <td data-label="@lang('Status')">
                     @if($data->status == 1)
                    <span class="badge rounded-pill badge-light-primary me-1">@lang('Running')</span>
                    @elseif($data->status == 0)
                        <span class="badge rounded-pill badge-light-success me-1">@lang('Completed')</span>

                    @endif

                </td>
                <td data-label="@lang('Saved')">
                <a class="btn btn--primary btn-sm text-white" href="{{route('admin.savings.view',$data->reference)}}">View</a>
                </td>
            </tr>
            @empty
                <tr>
                    <td colspan="100%">{{ __($emptyMessage) }}</td>
                </tr>
            @endforelse

          </tbody>
        </table>
      </div>
       {{$saved->links()}}
    </div>
  </div>
</div>
<!-- Basic Tables end -->



@endsection


