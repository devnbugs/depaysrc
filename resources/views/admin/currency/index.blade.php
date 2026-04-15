@extends('admin.layouts.app')
@section('panel')
     <!-- drives area starts-->
    <div class="drives">
      <div class="row">
        <div class="col-12">
          <h6 class="files-section-title mb-75">Available  Currencies</h6>
        </div>
        @foreach($currency as $data)
        <div class="col-lg-3 col-md-6 col-12">
          <div class="card shadow-none border cursor-pointer">
            <div class="card-body">
              <div class="d-flex justify-content-between">
                 <img src="{{url('/')}}/assets/images/coins/{{$data->image}}"  width="50" alt="logo"></span>
                <div class="dropdown-items-wrapper">
                  <i
                    data-feather="more-vertical"
                    id="dropdownMenuLink1"
                    role="button"
                    data-bs-toggle="dropdown"
                    aria-expanded="false"
                  ></i>

                  <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuLink1">
                    <a class="dropdown-item" href="{{ route('admin.coin.edit',$data->id)}}">
                      <i data-feather="settings" class="me-25"></i>
                      <span class="align-middle">Settings</span>
                    </a>
                    @if($data->status == 1)
                    <a class="dropdown-item" href="{{ route('admin.coin.deactivate',$data->id) }}">
                      <i data-feather="lock" class="me-25"></i>
                      <span class="align-middle">Deactivate</span>
                    </a>
                    @else
                    <a class="dropdown-item" href="{{ route('admin.coin.activate',$data->id) }}">
                      <i data-feather="unlock" class="me-25"></i>
                      <span class="align-middle">Activate</span>
                    </a>
                    @endif
                  </div>
                </div>
              </div>
              <div class="my-1">
                <h5>{{$data->name}} <small>{{$data->symbol}}</small></h5>
              </div>
               <p class="text-muted text-center mt-1 col-6">
										@if($data->status == 1)
										<a class="badge bg-success text-white">Status :Active</a>
										@else
										<a class="badge bg-danger text-white">Status: Inactive</a>
										@endif
										</p>


            </div>
          </div>
        </div>
        @endforeach
      </div>
    </div>
    <!-- drives area ends-->





@endsection
