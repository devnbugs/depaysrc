@extends('admin.layouts.app')

@section('panel')
    <div class="row mb-none-30">
        <div class="col-xl-3 col-lg-5 col-md-5 mb-30">
            <div class="card b-radius--10 overflow-hidden box--shadow1">
                <div class="card-body">
                    <h5 class="mb-20 text-muted">@lang('Website News Panel')</h5>
                    <div class="card mt-50">
                        <div class="card-body">
                            <h5 class="card-title border-bottom pb-2">@lang('User News Update') </h5>

                            <form action="{{ route('admin.beta.notify.update' ['id' = $webnotify->description]) }}" method="POST"
                                  enctype="multipart/form-data">
                                @csrf

                                @foreach($webnotify as $data)
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-control-label font-weight-bold">@lang('Latest News')<span class="text-danger">*</span></label>
                                            <input class="form-control form-control-lg" type="text" name="latest" value="{{ $data->description }}">
                                        </div>
                                    </div>
                                </div>
                                @endforeach

                                <!-- Add other form fields or controls here -->

                                <div class="form-group mt-3">
                                    <button type="submit" class="btn btn-primary">@lang('Publish')</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@include('partials.alertx')