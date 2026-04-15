@extends('admin.layouts.app')
@section('panel')

<div class="row justify-content-center">
    <div class="row">
    <div class="col-lg-4 col-sm-6 col-12">
      <div class="card">
        <div class="card-header">
          <div>
            <h2 class="fw-bolder mb-0"> {{ __($general->cur_sym) }}{{ showAmount($successful) }}</h2>
            <p class="card-text">@lang('Successful Deposit')</p>
          </div>
          <div class="avatar bg-light-primary p-50 m-0">
            <div class="avatar-content">
              <i data-feather="dollar-sign" class="font-medium-5"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-4 col-sm-6 col-12">
      <div class="card">
        <div class="card-header">
          <div>
            <h2 class="fw-bolder mb-0">{{ __($general->cur_sym) }}{{ showAmount($pending) }}</h2>
            <p class="card-text">@lang('Pending Deposit')</p>
          </div>
          <div class="avatar bg-light-warning p-50 m-0">
            <div class="avatar-content">
              <i data-feather="clock" class="font-medium-5"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-4 col-sm-6 col-12">
      <div class="card">
        <div class="card-header">
          <div>
            <h2 class="fw-bolder mb-0">{{ __($general->cur_sym) }}{{ showAmount($rejected) }}</h2>
            <p class="card-text">@lang('Rejected Deposit')</p>
          </div>
          <div class="avatar bg-light-danger p-50 m-0">
            <div class="avatar-content">
              <i data-feather="alert-octagon" class="font-medium-5"></i>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>

@endsection