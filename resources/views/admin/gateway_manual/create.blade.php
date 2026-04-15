@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <form action="{{ route('admin.gateway.manual.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="payment-method-item">
                            <div class="payment-method-header">
                                <div class="thumb">
                                    <div class="avatar-preview">
                                        <div class="profilePicPreview" style="background-image: url('{{getImage(imagePath()['gateway']['path'],imagePath()['gateway']['size'])}}')"></div>
                                    </div>
                                    <div class="avatar-edit">
                                        <input type="file" name="image" class="form-control profilePicUpload" id="image" accept=".png, .jpg, .jpeg"/>
                                        <label for="image" class="bg-primary"><i class="fa fa-edit"></i></label>
                                    </div>
                                </div>

                                    <div class="row mt-4 mb-none-15">
                                        <div class="col-sm-12 col-md-6 col-lg-6 col-xl-4 mb-15">
                                            <div class="input-group mb-1">
                                                <label class="w-100 font-weight-bold">@lang('Gateway Name') <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control " placeholder="@lang('Method Name')" name="name" value="{{ old('name') }}"/>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-md-6 col-lg-6 col-xl-4 mb-15">

                                            <div class="input-group  mb-1">
                                                <label class="w-100 font-weight-bold">@lang('Currency') <span class="text-danger">*</span></label>
                                                <input type="text" name="currency" placeholder="@lang('Currency')" class="form-control border-radius-5" value="{{ old('currency') }}"/>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-md-6 col-lg-6 col-xl-4 mb-15">
                                            <label class="w-100 font-weight-bold">@lang('Rate') 1 {{ __($general->cur_text )}} = <span class="currency_symbol"></span> <span class="text-danger">*</span></label>


                                                <input type="text" class="form-control" placeholder="0" name="rate" value="{{ old('rate') }}"/>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="payment-method-body">
                                <div class="row">

                                    <div class="col-sm-12 col-md-6 col-lg-6 col-xl-6">
                                        <div class="card border-primary mt-3">
                                            <h5 class="card-header bg-primary text-white">@lang('Range')</h5>
                                            <div class="card-body">
                                                <div class="input-group mb-3">
                                                    <label class="w-100 font-weight-bold">@lang('Minimum Amount') <span class="text-danger">*</span></label>
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">{{ __($general->cur_sym) }}</div>
                                                    </div>
                                                    <input type="text" class="form-control" name="min_limit" placeholder="0" value="{{ old('min_limit') }}"/>
                                                </div>
                                                <div class="input-group">
                                                    <label class="w-100 font-weight-bold">@lang('Maximum Amount') <span class="text-danger">*</span></label>
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">{{ __($general->cur_sym) }}</div>
                                                    </div>
                                                    <input type="text" class="form-control" placeholder="0" name="max_limit" value="{{ old('max_limit') }}"/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-6 col-lg-6 col-xl-6">
                                        <div class="card border-primary mt-3">
                                            <h5 class="card-header bg-primary  text-white">@lang('Charge')</h5>
                                            <div class="card-body">
                                                <div class="input-group mb-3">
                                                    <label class="w-100 font-weight-bold">@lang('Fixed Charge') <span class="text-danger">*</span></label>
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">{{ __($general->cur_sym) }}</div>
                                                    </div>
                                                    <input type="text" class="form-control" placeholder="0" name="fixed_charge" value="{{ old('fixed_charge') }}"/>
                                                </div>
                                                <div class="input-group">
                                                    <label class="w-100 font-weight-bold">@lang('Percent Charge') <span class="text-danger">*</span></label>
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">@lang('%')</div>
                                                    </div>
                                                    <input type="text" class="form-control" placeholder="0" name="percent_charge" value="{{ old('percent_charge') }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                     <div class="col-lg-12">
                                    <h5 class="text-danger">For Cryptocurrency or Coin, just enter the respective coin wallet address in the <b>Deposit Instruction</b> field. The wallet address will be converted to its respective QR code for such coin</h5>
                                        <div class="card border-primary mt-3">

                                            <h5 class="card-header  text-white bg-primary">@lang('Currency Type')</h5>
                                            <div class="card-body">
                                                <div class="form-group">
                                                <br>
                                                    <select class="form-control" name="crypto">
                                                    <option selected disabled>Select Currency Type</option>
                                                    <option value="0">Fiat</option>
                                                    <option value="1">Cryptocurrency</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                        <div class="card border-primary mt-3">

                                            <h5 class="card-header bg-primary text-white">@lang('Deposit Instruction')</h5>
                                            <div class="card-body">
                                                <div class="form-group">
                                                    <textarea rows="8" class="form-control border-radius-5 nicEdit" name="instruction">{{ old('instruction') }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-12">
                                        <div class="card border-primary mt-3">
                                            <h5 class="card-header bg-primary  text-white">@lang('User data')
                                                <button type="button" class="btn btn-sm btn-outline-light float-right addUserData"><i class="la la-fw la-plus"></i>@lang('Add New')
                                                </button>
                                            </h5>

                                            <div class="card-body">
                                                <div class="row addedField">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn--primary btn-block">@lang('Save Method')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection



@push('script')
    <script>

        (function ($) {
            "use strict";
            $('input[name=currency]').on('input', function () {
                $('.currency_symbol').text($(this).val());
            });
            $('.addUserData').on('click', function () {
                var html = `
                    <div class="col-md-12 user-data">
                    <br>
                        <div class="form-group">
                            <div class="input-group mb-md-0 mb-1">
                                <div class="col-md-4">
                                    <input name="field_name[]" class="form-control" type="text" required placeholder="@lang('Field Name')">
                                </div>
                                <div class="col-md-3 mt-md-0 mt-2 mb-1">
                                    <select name="type[]" class="form-control">
                                        <option value="text" > @lang('Input Text') </option>
                                        <option value="textarea" > @lang('Textarea') </option>
                                        <option value="file"> @lang('File') </option>
                                    </select>
                                </div>
                                <div class="col-md-3 mt-md-0 mt-1">
                                    <select name="validation[]"
                                            class="form-control">
                                        <option value="required"> @lang('Required') </option>
                                        <option value="nullable">  @lang('Optional') </option>
                                    </select>
                                </div>
                                <div class="col-md-2 mt-md-0 mt-2 text-right">
                                    <span class="input-group-btn">
                                        <button class="btn btn-danger btn-lg removeBtn w-100" type="button">
                                          Remove
                                        </button>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>`;
                $('.addedField').append(html)
            });

            $(document).on('click', '.removeBtn', function () {
                $(this).closest('.user-data').remove();
            });

            @if(old('currency'))
            $('input[name=currency]').trigger('input');
            @endif

        })(jQuery);
    </script>
@endpush
