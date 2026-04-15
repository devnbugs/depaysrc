@extends('admin.layouts.app')
@section('panel')
    <div class="row mb-none-30">
        <div class="col-lg-12">
            <div class="card">
                <form action="" method="post">
                    @csrf
                    <div class="card-body">
                      <div class="row">
                        <div class="col-md-6">
                          <div class="form-group mb-1">
                            <label>@lang('Policy Link')</label>
                              <input type="text" name="link" class="form-control" value="{{ @$cookie->data_values->link }}" placeholder="@lang('Policy Link')">
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-group mb-1">
                            <label>@lang('Status')</label>
                            <div class="form-check form-switch form-check-primary">
                <input type="checkbox" class="form-check-input"  @if(@$cookie->data_values->status) checked @endif name="status" id="customSwitch10" />
                <label class="form-check-label" for="customSwitch10">
                  <span class="switch-icon-left"><i data-feather="plus"></i></span>
                  <span class="switch-icon-right"><i data-feather="x"></i></span>
                </label>
              </div>
                           </div>
                        </div>
                      </div>
                        <div class="form-group mb-1">
                          <label>@lang('Description')</label>
                            <textarea class="form-control nicEdit" rows="10" name="description" placeholder="@lang('Description')">@php echo @$cookie->data_values->description @endphp</textarea>
                        </div>
                    </div>
                    <div class="card-footer">
                    <br>
                        <button type="submit" class="btn btn-primary btn-block">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
