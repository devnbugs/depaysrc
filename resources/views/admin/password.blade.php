@extends('admin.layouts.app')

@section('panel')
    <div class="row mb-none-30">
        <div class="col-lg-12 col-md-12 mb-30">



        <div class="col-lg-12 col-md-9 mb-30">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-50 border-bottom pb-2">@lang('Change Password')</h5>

                    <form action="{{ route('admin.password.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="form-group row mb-1">
                            <label class="col-lg-3 col-form-label form-control-label">@lang('Password')</label>
                            <div class="col-lg-9">
                                <input class="form-control" type="password" placeholder="@lang('Password')" name="old_password">
                            </div>
                        </div>

                        <div class="form-group row mb-1">
                            <label class="col-lg-3 col-form-label form-control-label">@lang('New Password')</label>
                            <div class="col-lg-9">
                                <input class="form-control" type="password" placeholder="@lang('New Password')" name="password">
                            </div>
                        </div>

                        <div class="form-group row mb-1">
                            <label class="col-lg-3 col-form-label form-control-label">@lang('Confirm Password')</label>
                            <div class="col-lg-9">
                                <input class="form-control" type="password" placeholder="@lang('Confirm Password')" name="password_confirmation">
                            </div>
                        </div>


                        <div class="form-group row mb-1">
                            <label class="col-lg-3 col-form-label form-control-label"></label>
                            <div class="col-lg-9">
                            <button type="submit" class="btn text-white btn--primary btn-block">@lang('Save Changes')</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <a href="{{route('admin.profile')}}" class="btn btn-sm btn--primary box--shadow1 text--small" ><i class="fa fa-user"></i>@lang('Profile Setting')</a>
@endpush
