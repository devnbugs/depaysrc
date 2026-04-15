@extends('admin.layouts.app')
    
@section('panel')
<div class="row" id="basic-table">
  <div class="col-12">
      <div class="row">
        <div class="col-xl-12">
            <div class="card card-statistics">
                <div class="card-header">
                <table class="card b-radius--10 overflow-hidden box--shadow1">
            <tr>
                <th class="card-body">Name</th>
                <th class="card-body">Data Type</th>
                <th class="card-body">Network</th>
                <th class="card-body">Plan</th>
                <th class="card-body">Cost</th>
				<th class="card-body">Status</th>
                <th class="card-body">Actions</th>
            </tr>
            @foreach($bundles as $bundle)
                <tr>
                    <td class="card-body">{{ $bundle->name }}</td>
                    <td class="card-body">{{ $bundle->datatype }}</td>
                    <td class="card-body">{{ $bundle->network }}</td>
                    <td class="card-body">{{ $bundle->plan }}</td>
                    <td class="card-body">{{$general->cur_sym}}{{ $bundle->cost }}</td>
					@if($bundle->status == 1)
					<td class="card-body">ON</td>
					@elseif($bundle->status == 0)
					<td class="card-body">OFF</td>
					@endif
                    <td class="card-body">
                        <a href="{{ route('admin.dashboard.edit', ['bundle' => $bundle->id]) }}" class="btn btn-primary">Edit</a>
                    </td>
                </tr>
            @endforeach
                </table>
            </div>    
        </div>        
    </div>
</div>

    @endsection