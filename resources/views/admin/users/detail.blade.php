@extends('admin.layouts.app')

@section('panel')
    <div class="row mb-none-30">
        <div class="col-xl-3 col-lg-5 col-md-5 mb-30">

            <div class="card b-radius--10 overflow-hidden box--shadow1">
                <div class="card-body">
                    <h5 class="mb-20 text-muted">@lang('User information')</h5>
                    <ul class="list-group">

                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Username')
                            <span class="font-weight-bold">{{$user->username}}</span>
                        </li>


                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Status')
                            @if($user->status == 1)
                                <span class="badge badge-pill bg-success">@lang('Active')</span>
                            @elseif($user->status == 0)
                                <span class="badge badge-pill bg-danger">@lang('Banned')</span>
                            @endif
                        </li>

                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Balance')
                            <span class="font-weight-bold">{{showAmount($user->balance)}}  {{__($general->cur_text)}}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Investment Return')
                            <span class="font-weight-bold">{{showAmount($user->invest_return)}}  {{__($general->cur_text)}}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Compounding')
                            <span class="font-weight-bold">{{showAmount($user->compound)}}  {{__($general->cur_text)}}</span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Referral Bonus')
                            <span class="font-weight-bold">{{showAmount($user->ref_bonus)}}  {{__($general->cur_text)}}</span>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="card b-radius--10 overflow-hidden mt-30 box--shadow1">
                <div class="card-body">
                    <h5 class="mb-20 text-muted">@lang('User action')</h5>
					<br>
                    <a data-bs-toggle="modal" href="#addvestModal" class="btn btn-info btn-sm btn-block btn-lg">
                        @lang('Add Investment')
                    </a>
					<br>
                   <a data-bs-toggle="modal" href="#addSubModal" class="btn btn-success btn-sm btn-block btn-lg">
                        @lang('Credit/Debit Balance')
                    </a>
					<br>
                    <a href="{{ route('admin.users.login.history.single', $user->id) }}"
                       class="btn btn-primary btn-sm btn-block btn-lg">
                        @lang('Login Logs')
                    </a>
					<br>
                    <a href="{{route('admin.users.email.single',$user->id)}}"
                       class="btn btn-info btn-sm btn-block btn-lg">
                        @lang('Send Email')
                    </a>
					<br>
                    <a href="{{route('admin.users.login',$user->id)}}" target="_blank" class="btn btn-dark btn-sm btn-block btn-lg">
                        @lang('Login as User')
                    </a>
					<br>
                    <a href="{{route('admin.users.email.log',$user->id)}}" class="btn btn-warning btn-sm btn-block btn-lg">
                        @lang('Email Log')
                    </a>


                </div>
            </div>
        </div>

        <div class="col-xl-9 col-lg-7 col-md-7 mb-30">


         <!-- Stats Horizontal Card -->
  <div class="row">
    <div class="col-lg-6 col-sm-6 col-12">
      <div class="card">
        <div class="card-header">
          <div>
            <h2 class="fw-bolder mb-0"> {{$general->cur_sym}}{{showAmount($totalDeposit)}}</h2>
            <p class="card-text">@lang('Total Deposit')</p>
          </div>
          <div class="avatar bg-light-primary p-50 m-0">
            <div class="avatar-content">
              <i data-feather="dollar-sign" class="font-medium-5"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-6 col-sm-6 col-12">
      <div class="card">
        <div class="card-header">
          <div>
            <h2 class="fw-bolder mb-0">{{$general->cur_sym}}{{showAmount($totalWithdraw)}}</h2>
            <p class="card-text">@lang('Total Withdrawal')</p>
          </div>
          <div class="avatar bg-light-success p-50 m-0">
            <div class="avatar-content">
              <i data-feather="shopping-cart" class="font-medium-5"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-6 col-sm-6 col-12">
      <div class="card">
        <div class="card-header">
          <div>
            <h2 class="fw-bolder mb-0">{{$general->cur_sym}}{{ showAmount($totalInvest) }}</h2>
            <p class="card-text">@lang('Total Investment')</p>
          </div>
          <div class="avatar bg-light-success p-50 m-0">
            <div class="avatar-content">
              <i data-feather="bar-chart" class="font-medium-5"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-6 col-sm-6 col-12">
      <div class="card">
        <div class="card-header">
          <div>
            <h2 class="fw-bolder mb-0">{{$totalTransaction}}</h2>
            <p class="card-text">@lang('Total Transaction')</p>
          </div>
          <div class="avatar bg-light-warning p-50 m-0">
            <div class="avatar-content">
              <i data-feather="printer" class="font-medium-5"></i>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
  <!--/ Stats Horizontal Card -->




            <div class="card mt-50">
                <div class="card-body">
                    <h5 class="card-title border-bottom pb-2">@lang('Information of') {{$user->fullname}}</h5>

                    <form action="{{route('admin.users.update',[$user->id])}}" method="POST"
                          enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-1">
                                    <label class="form-control-label font-weight-bold">@lang('First Name')<span class="text-danger">*</span></label>
                                    <input class="form-control" type="text" name="firstname" value="{{$user->firstname}}">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-1">
                                    <label class="form-control-label  font-weight-bold">@lang('Last Name') <span class="text-danger">*</span></label>
                                    <input class="form-control" type="text" name="lastname" value="{{$user->lastname}}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group  mb-1">
                                    <label class="form-control-label font-weight-bold">@lang('Email') <span class="text-danger">*</span></label>
                                    <input class="form-control" type="email" name="email" value="{{$user->email}}">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-1">
                                    <label class="form-control-label  font-weight-bold">@lang('Mobile Number') <span class="text-danger">*</span></label>
                                    <input class="form-control" type="text" name="mobile" value="{{$user->mobile}}">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group  mb-1">
                                    <label class="form-control-label font-weight-bold">@lang('Address') </label>
                                    <input class="form-control" type="text" name="address" value="{{@$user->address->address}}">
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6">
                                <div class="form-group mb-1">
                                    <label class="form-control-label font-weight-bold">@lang('City') </label>
                                    <input class="form-control" type="text" name="city" value="{{@$user->address->city}}">
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6">
                                <div class="form-group mb-1 ">
                                    <label class="form-control-label font-weight-bold">@lang('State') </label>
                                    <input class="form-control" type="text" name="state" value="{{@$user->address->state}}">
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6">
                                <div class="form-group  mb-1">
                                    <label class="form-control-label font-weight-bold">@lang('Zip/Postal') </label>
                                    <input class="form-control" type="text" name="zip" value="{{@$user->address->zip}}">
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6">
                                <div class="form-group  mb-1">
                                    <label class="form-control-label font-weight-bold">@lang('Country') </label>
                                    <select name="country" class="form-control">
                                        @foreach($countries as $key => $country)
                                            <option value="{{ $key }}" @if($country->country == @$user->address->country ) selected @endif>{{ __($country->country) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>


                        <div class="row">
                            <div class="form-group col-xl-4 col-md-6  col-sm-3 col-12 mb-1">

                            <div class="d-flex flex-column">
              <label class="form-check-label mb-50" for="customSwitch3">@lang('Status')</label>
              <div class="form-check form-check-primary form-switch">
                <input type="checkbox"  name="status"
                                       @if($user->status) checked @endif class="form-check-input" id="customSwitch3" />
              </div>
            </div>


                            </div>

                            <div class="form-group  col-xl-4 col-md-6  col-sm-3 col-12 mb-1">
                             <div class="d-flex flex-column">
              <label class="form-check-label mb-50" for="customSwitch4">@lang('Email Verification')</label>
              <div class="form-check form-check-primary form-switch">
                <input type="checkbox"  name="ev"
                                       @if($user->ev) checked @endif class="form-check-input" id="customSwitch4" />
              </div>
            </div>



                            </div>

                            <div class="form-group  col-xl-4 col-md-6  col-sm-3 col-12 mb-1">
                             <div class="d-flex flex-column">
              <label class="form-check-label mb-50" for="customSwitch5">@lang('SMS Verification')</label>
              <div class="form-check form-check-primary form-switch">
                <input type="checkbox"  name="sv"
                                       @if($user->sv) checked @endif class="form-check-input" id="customSwitch5" />
              </div>
            </div>





                            </div>
                            <div class="form-group  col-md-6  col-sm-3 col-12">
                               <div class="d-flex flex-column">
              <label class="form-check-label mb-50" for="customSwitch6">@lang('2FA Status')</label>
              <div class="form-check form-check-primary form-switch">
                <input type="checkbox"  name="ts"
                                       @if($user->ts) checked @endif class="form-check-input" id="customSwitch6" />
              </div>
            </div>

                            </div>

                            <div class="form-group  col-md-6  col-sm-3 col-12">
                               <div class="d-flex flex-column">
              <label class="form-check-label mb-50" for="customSwitch7">@lang('2FA Verification')</label>
              <div class="form-check form-check-primary form-switch">
                <input type="checkbox"  name="tv"
                                       @if($user->tv) checked @endif class="form-check-input" id="customSwitch7" />
              </div>
            </div>

                            </div>
                        </div>


                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary btn-sm">@lang('Save Changes')
                                    </button>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>



    {{-- Add Sub Balance MODAL --}}
    <div id="addSubModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Add / Subtract Balance')</h5>

                </div>
                <form action="{{route('admin.users.add.sub.balance', $user->id)}}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-row">
                            <div class="form-group col-md-12">
                            <div class="form-check form-switch form-check-primary">
                <input type="checkbox" class="form-check-input" name="act" id="customSwitch10" checked />
                <label class="form-check-label" for="customSwitch10">
                  <span class="switch-icon-left"><i data-feather="plus"></i></span>
                  <span class="switch-icon-right"><i data-feather="minus"></i></span>
                </label>
              </div>
              <small>Check toggle to credit and uncheck toggle to debit</small><br>


                            </div>


                            <div class="form-group col-md-12">
                                <label>@lang('Amount')<span class="text-danger">*</span></label>
                                <div class="input-group has_append">
                                    <input type="text" name="amount" class="form-control" placeholder="@lang('Please provide positive amount')">

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-warning" data-bs-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn-success">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>



    {{-- Add Invest MODAL --}}
    <div id="addvestModal" class="modal fade" tabindex="-1" role="dialog">
     <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Add New Investment')</h5>

                </div>
                <form action="{{route('admin.users.add.plan', $user->id)}}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-row">

                             <div class="form-group col-md-12">
                                <label>@lang('Plan')<span class="text-danger">*</span></label>
                                    @php $plan = App\Models\Plan::get(); @endphp
                                <div class="input-group">
                                    <select name="plan" class="form-control">
                                    @foreach($plan as $data)
                                    <option value="{{$data->id}}">{{$data->name}}</option>
                                    @endforeach
                                    </select>
                                </div>
                                </div>




                            <div class="form-group col-md-12">
                                <label>@lang('Amount')<span class="text-danger">*</span></label>
                                <div class="input-group has_append">
                                    <input type="text" name="amount" class="form-control" placeholder="@lang('Please provide positive amount')">

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-warning" data-bs-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn-success">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>




    </div>



@endsection
