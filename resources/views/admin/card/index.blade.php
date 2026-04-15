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
                <th>@lang('Name On Crd')</th>
                <th>@lang('Type')</th>
                <th>@lang('Number')</th>
                <th>@lang('Date Created')</th>
                @if(\Route::current()->getName() != 'admin.card.inactive')
                <th>@lang('Action')</th>
                @endif
            </tr>
          </thead>
          <tbody>
            @forelse($card as $k=>$data)
             @php $user = App\Models\User::where('id', $data->user_id)->first(); @endphp

            <tr>
            <td data-label="@lang('Gateway')">{{ __(@$user->username) }}<br>
                 <a href="{{ route('admin.users.detail', @$user->id ?? 0) }}" class="btn btn-sm btn-primary" data-toggle="tooltip" title="" data-original-title="@lang('Details')">
                                        View
                 </a>
                </td>
                <td data-label="#@lang('Trx')">{{$data->name_on_card}}
                </td>
                <td data-label="@lang('Type')">{{$data->card_type}}</td>
                <td data-label="@lang('Number')">
                    <strong>{{$data->masked_pan}}</strong><br>

                </td>
                <td data-label="@lang('Date')" class="text-primary">
                    {{showDateTime($data->created_at)}}
                </td>


                @if(\Route::current()->getName() != 'admin.card.inactive')
                <td data-label="@lang('Saved')">
                <a class="btn btn--primary btn-sm text-white" href="{{route('admin.card.view',$data->reference)}}">View</a>
                </td>
                @endif
            </tr>
            @empty
                <tr>
                    <td colspan="100%">{{ __($emptyMessage) }}</td>
                </tr>
            @endforelse

          </tbody>
        </table>
      </div>
       {{$card->links()}}
    </div>
  </div>
</div>
<!-- Basic Tables end -->



@endsection


