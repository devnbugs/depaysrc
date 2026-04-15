@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class=" table align-items-center table--light">
                            <thead>
                            <tr>
                                <th>@lang('Short Code')</th>
                                <th>@lang('Description')</th>
                            </tr>
                            </thead>
                            <tbody class="list">
                            @forelse($sms_template->shortcodes as $shortcode => $key)
                                <tr>
                                    <th>@php echo "{{". $shortcode ."}}"  @endphp</th>
                                    <td>{{ __($key) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="100%" class="text-muted text-center">{{ __($emptyMessage) }}</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div><!-- card end -->
        </div>


        <div class="col-lg-12">

            <div class="card mt-5">
                <div class="card-header bg--dark">
                    <h5 class="card-title text-white">{{ __($pageTitle) }}</h5>
                </div>
                <form action="{{ route('admin.sms.template.update', $sms_template->id) }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group col-md-4 mb-1">
                                <label class="font-weight-bold">@lang('Status') <span class="text-danger">*</span></label>
                                 <div class="form-check form-switch form-check-primary">
                <input type="checkbox" class="form-check-input"   name="sms_status" @if($sms_template->sms_status) checked @endif id="customSwitch10" />
                <label class="form-check-label" for="customSwitch10">
                  <span class="switch-icon-left"><i data-feather="plus"></i></span>
                  <span class="switch-icon-right"><i data-feather="x"></i></span>
                </label>
              </div><br>

                           </div>

                            <div class="form-group col-md-12 mb-1">
                                <label class="font-weight-bold">@lang('Message') <span class="text-danger">*</span></label>
                                <textarea name="sms_body" rows="5" class="form-control" placeholder="@lang('Your message using shortcodes')">{{ $sms_template->sms_body }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-block btn-primary mr-2">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('admin.sms.template.index') }}" class="btn btn-sm btn--primary box--shadow1 text--small"><i class="fa fa-fw fa-backward"></i> @lang('Go Back') </a>
@endpush
