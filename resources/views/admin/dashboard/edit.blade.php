@extends('admin.layouts.app')

@section('panel')
<div class="row" id="basic-table">
  <div class="col-12">
      <div class="row">
        <div class="col-xl-12">
            <div class="card card-statistics">
                <div class="card-header">
                    <div class="form-row"> 
						<div class="card mt-50">
							<div class="card-body">					
								<h5 class="card-title border-bottom pb-2">@lang('Data Bundle Update')</h5>
                        <form action="{{ route('admin.dashboard.update', $bundle) }}" method="POST" class="card b-radius--10 overflow-hidden box--shadow1">
                            @method('PUT')
                    
                    <div class="row" bis_skin_checked="1">
							<div class= col-md-12>
                            <div class="form-group col-12 mb-1">
                                <label for="name" class="mb-4"> Plan</label>
                                <input type="text" name="name" value="{{ $bundle->name }}" class="form-control">
                            </div>
                    
                            <div class="form-group col-12 mb-1">
                                <div class="form-group mb-1">
                                <label for="datatype" class="mb-4"> Data Type</label>
                                <input type="text" name="datatype" value="{{ $bundle->datatype }}" class="form-control">
                                </div>
                            </div>
                    
                            <div class="form-group col-12 mb-1">
                                <div class="form-group mb-1">
                                <label for="network" class="mb-4"> Network</label>
                                <input type="text" name="network" value="{{ $bundle->network }}" class="form-control">
                                </div>
                            </div>
                            </div>
                    
                            <div class="form-group col-12 mb-1">
                                <div class="form-group mb-1">
                                <label for="plan" class="mb-4"> Variation Code</label>
                                <input type="text" name="plan" value="{{ $bundle->plan }}" class="form-control">
                                </div>
                            </div>
                    
                            <div class="form-group col-12 mb-1">
                                <div class="form-group mb-1">
                                <label for="cost" class="mb-4"> Cost</label>
                                <input type="text" name="cost" value="{{ $bundle->cost }}" class="form-control">
                            </div>
							
							<!-- Status Toggle Switch -->
							<div class="form-group col-12 mb-1">
								<label class="form-check-label mb-50" for="customSwitch4">@lang('Status')</label>
								<div class="form-check form-check-primary form-switch">
									<input type="checkbox" name="status" class="form-check-input" id="customSwitch4"
									@if($bundle->status) checked @endif />
								</div>
							</div>

                            <br>
                            <input type="hidden" name="_token" value="{{ csrf_token() }}"> <!-- Add this line -->
                            <div>
                                <button type="submit" class="btn btn-primary"> Update</button>
                            </div>
					</div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>    
@push('script')

<script>
    function toggleStatus(toggle) {
        const statusLabel = document.getElementById('statusLabel');
        if (toggle.checked) {
            statusLabel.innerText = 'On';
            // Optionally, update the status in your backend via AJAX or form submission
        } else {
            statusLabel.innerText = 'Off';
            // Optionally, update the status in your backend via AJAX or form submission
        }
    }
</script>


@endpush

@endsection
       