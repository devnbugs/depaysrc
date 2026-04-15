@extends('admin.layouts.app')
@section('panel')
@php
    $mobile_code = is_array($info ?? '') ? implode(',', $info ?? '') : $info ?? '';
    $countries = json_decode(file_get_contents(resource_path('views/partials/country.json')));
    $policy = getContent('policy_pages.element');
@endphp
     <div class="row mb-none-30">
        <div class="col-xl-12 col-lg-12 col-md-12 mb-30">

            <div class="card b-radius--10 overflow-hidden box--shadow1">
                <div class="card-body">
                    <h5 class="mb-20 text-muted">@lang('Create New User')</h5>
                        <form class="auth-register-form mt-2" action="{{ url('/admin/create/user') }}" method="post">
            @csrf

             @isset($reference)
                                    <input type="text" hidden  name="referBy"  class="form-control" id="referenceBy" value="{{$reference}}" placeholder="@lang('Reference BY')" readonly/>

                                @endisset
         <div class="row">
         <div class="mb-1 col-6">
            <label for="firstname" class="form-label">@lang('First Name')</label>
            <input
              type="text"
              class="form-control"
              id="firstname"
              name="firstname"
              value="{{ old('firstname') }}"
              placeholder="Enter First Name"
              aria-describedby="firstname"
              tabindex="1"
              autofocus
            />
          </div>
             <div class="mb-1 col-6">
            <label for="lastname" class="form-label">@lang('Last Name')</label>
            <input
              type="text"
              class="form-control"
              id="lastname"
              name="lastname"
              value="{{ old('lastname') }}"
              placeholder="Enter Last Name"
              aria-describedby="lastname"
              tabindex="2"
            />
          </div>

          </div>

          <div class="row">
           <div class="mb-1 col-6">
            <label for="currency" class="form-label">{{ __('Country') }}</label>
           <select name="country" id="currency" onchange="myFunction()" required  class="form-control">
                        @foreach($countries as $key => $country)
                            <option data-mobile_code="{{ $country->dial_code }}" value="{{ $country->country }}" data-code="{{ $key }}">{{ __($country->country) }}</option>
                        @endforeach
            </select>
          </div>
          @push('script')
<script>
function myFunction() {
var mobile_code = $("#currency option:selected").attr('data-mobile_code');
var code = $("#currency option:selected").attr('data-code');
document.getElementById("code").value = code;
document.getElementById("mobile_code").value = mobile_code;
document.getElementById("22").innerHTML = "+"+mobile_code;
 };
</script>
@endpush
         <div class="mb-1 col-6">
            <label for="firstname" class="form-label">@lang('Mobile')</label>
            <input
              name="mobile"
              id="mobile"
              class="form-control"
              id="mobile"
              value="{{ old('mobile') }}"
              placeholder="Phone Number"
              aria-describedby="mobile"
              tabindex="1"
              autofocus
            />
          </div>

                                <input type="hidden" id="mobile_code" name="mobile_codes">
                                <input type="hidden" id="code" name="country_codes">

          </div>

          <div class="mb-1">
            <label for="register-username" class="form-label">{{ __('Username') }}</label>
            <input
              type="text"
              class="form-control"
              id="username"
              name="username"
              placeholder="Enter Username"
              aria-describedby="username"
              tabindex="1"
              autofocus
            />
          </div>
          <div class="mb-1">
            <label for="register-email" class="form-label">@lang('E-Mail Address')</label>
            <input
              type="email"
              class="form-control"
              id="email"
              name="email"
              placeholder="example@email.com"
              aria-describedby="email"
              tabindex="2"
            />
          </div>

          <div class="row">
         <div class="mb-1 col-12">
            <label for="register-password" class="form-label">@lang('Password')</label>

            <div class="input-group input-group-merge form-password-toggle">
              <input
                type="text" value="1234567890" readonly
                class="form-control form-control-merge"
                id="password"
                name="password"
                placeholder="Enter Password"
                aria-describedby="password"
                tabindex="3"
              />
              <span class="input-group-text cursor-pointer bg-light text-black"><i data-feather="eye"></i></span>
            </div>
          </div>
                <input hidden
                type="text" value="1234567890" readonly
                class="form-control form-control-merge"
                id="password_confirmation"
                name="password_confirmation"
                placeholder="Confirm Password"
                aria-describedby="password_confirmation"
                tabindex="3"
              />

          <div class="mb-1">

            <br><br>
          <button class="btn btn--primary text-white w-100" type="submit" tabindex="5">@lang('Create User')</button>
        </form>

                </div>

            </div>
        </div>


    </div>
@endsection

