@extends('admin.layouts.app')

@section('panel')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <form action="{{ route('admin.withdraw.method.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="card-body">
                            <!-- Header Section with Logo and Method Name -->
                            <div class="row mb-4">
                                <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3 mb-lg-0">
                                    <div class="text-center">
                                        <div class="avatar-edit">
                                            <input type="file" name="image" class="form-control profilePicUpload" id="image" accept=".png, .jpg, .jpeg"/>
                                            <label for="image" class="btn btn-sm btn-primary mt-2"><i class="la la-pencil"></i> @lang('Upload')</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-9 col-md-8 col-sm-6 col-12">
                                    <div class="form-group mb-3">
                                        <label class="form-label">@lang('Method Name') <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" placeholder="@lang('Method Name')" name="name" value="{{ old('name') }}"/>
                                    </div>
                                </div>
                            </div>

                            <!-- Basic Settings Row -->
                            <div class="row mb-4">
                                <div class="col-lg-4 col-md-6 col-12 mb-3 mb-lg-0">
                                    <div class="form-group">
                                        <label class="form-label">@lang('Currency') <span class="text-danger">*</span></label>
                                        <input type="text" name="currency" class="form-control" value="{{ old('currency') }}"/>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6 col-12 mb-3 mb-lg-0">
                                    <div class="form-group">
                                        <label class="form-label">@lang('Rate') 1 {{ __($general->cur_text) }} = <span class="currency_symbol"></span> <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" placeholder="0" name="rate" value="{{ old('rate') }}"/>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6 col-12">
                                    <div class="form-group">
                                        <label class="form-label">@lang('Processing Time') <span class="text-danger">*</span></label>
                                        <input type="text" name="delay" class="form-control" value="{{ old('delay') }}"/>
                                    </div>
                                </div>
                            </div>

                            <!-- Range Section -->
                            <div class="row mb-4">
                                <div class="col-lg-6 col-md-6 col-12 mb-3 mb-lg-0">
                                    <div class="card border-primary">
                                        <h5 class="card-header bg-primary text-white">@lang('Range')</h5>
                                        <div class="card-body">
                                            <div class="form-group mb-3">
                                                <label class="form-label">@lang('Minimum Amount') <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" name="min_limit" placeholder="0" value="{{ old('min_limit') }}"/>
                                                    <span class="input-group-text">{{ __($general->cur_text) }}</span>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label">@lang('Maximum Amount') <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" placeholder="0" name="max_limit" value="{{ old('max_limit') }}"/>
                                                    <span class="input-group-text">{{ __($general->cur_text) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Charge Section -->
                                <div class="col-lg-6 col-md-6 col-12">
                                    <div class="card border-primary">
                                        <h5 class="card-header bg-primary text-white">@lang('Charge')</h5>
                                        <div class="card-body">
                                            <div class="form-group mb-3">
                                                <label class="form-label">@lang('Fixed Charge') <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" placeholder="0" name="fixed_charge" value="{{ old('fixed_charge') }}"/>
                                                    <span class="input-group-text">{{ __($general->cur_text) }}</span>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label">@lang('Percent Charge') <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" placeholder="0" name="percent_charge" value="{{ old('percent_charge') }}"/>
                                                    <span class="input-group-text">%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Instruction Section -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <div class="card border-primary">
                                        <h5 class="card-header bg-primary text-white">@lang('Withdraw Instruction')</h5>
                                        <div class="card-body">
                                            <div class="form-group">
                                                <textarea rows="5" class="form-control nicEdit" name="instruction">{{ old('instruction') }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- User Data Section -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <div class="card border-primary">
                                        <h5 class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                            <span>@lang('User data')</span>
                                            <button type="button" class="btn btn-sm btn-outline-light addUserData">
                                                <i class="la la-fw la-plus"></i>@lang('Add New')
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

                        <div class="card-footer">
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">@lang('Save Method')</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection


@push('breadcrumb-plugins')
    <a href="{{ route('admin.withdraw.method.index') }}" class="btn btn-sm btn-primary">
        <i class="la la-fw la-backward"></i> @lang('Go Back')
    </a>
@endpush

@push('script')
    <script>
        (function ($) {
            "use strict";
            $('input[name=currency]').on('input', function () {
                $('.currency_symbol').text($(this).val());
            });
            $('.addUserData').on('click', function () {
                var html = `
                    <div class="col-12 user-data mb-3">
                        <div class="card bg-light">
                            <div class="card-body">
                                <div class="row align-items-end">
                                    <div class="col-lg-4 col-md-6 col-12 mb-2 mb-lg-0">
                                        <label class="form-label">@lang('Field Name')</label>
                                        <input name="field_name[]" class="form-control" type="text" required placeholder="@lang('Field Name')">
                                    </div>
                                    <div class="col-lg-2 col-md-6 col-12 mb-2 mb-lg-0">
                                        <label class="form-label">@lang('Type')</label>
                                        <select name="type[]" class="form-control">
                                            <option value="text" > @lang('Input Text') </option>
                                            <option value="textarea" > @lang('Textarea') </option>
                                            <option value="file"> @lang('File') </option>
                                        </select>
                                    </div>
                                    <div class="col-lg-3 col-md-6 col-12 mb-2 mb-lg-0">
                                        <label class="form-label">@lang('Validation')</label>
                                        <select name="validation[]" class="form-control">
                                            <option value="required"> @lang('Required') </option>
                                            <option value="nullable">  @lang('Optional') </option>
                                        </select>
                                    </div>
                                    <div class="col-lg-3 col-md-6 col-12 mb-2 mb-lg-0">
                                        <button class="btn btn-danger removeBtn w-100" type="button">
                                            <i class="la la-trash"></i> @lang('Remove')
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>`;

                $('.addedField').append(html);
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
