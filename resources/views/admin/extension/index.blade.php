@extends('admin.layouts.app')

@section('panel')


<div class="col-md-12 mb-30">

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.extensions.update', $extension->id) }}" method="POST">
                @csrf
                <div class="row">
                    <div class="form-group col-xl-6">
                        <div class="image-upload">
                            <div class="thumb">
                                <div class="avatar-preview">
                                   <img src="{{ getImage(imagePath()['extensions']['path'] .'/'. 'twak.png') }}" class="img-fluid" alt="img-placeholder" />


                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-xl-6">
                        <h3 class="text-center">{{ __($extension->name) }}</h3>
                        <label for="app_key">@lang('API Key')</label>
                        <input type="text" name="app_key" id="app_key" value="{{@$extension->shortcode->app_key->value}}" class="form-control">

                        <label for="status" class="mt-3">@lang('Status')</label>
                        <select name="status" id="status" class="form-control">
                            <option value="1" {{ $extension->status == 1 ? 'selected' : null }} >@lang('Enable')</option>
                            <option value="2" {{ $extension->status == 2 ? 'selected' : null }} >@lang('Disable')</option>
                        </select>

                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-block">@lang('Update')</button>
            </form>
        </div>
    </div>
</div>



    {{-- EDIT METHOD MODAL --}}
    <div id="editModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Update Extension'): <span class="extension-name"></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="col-md-12 control-label font-weight-bold">@lang('Script') <span class="text-danger">*</span></label>
                            <div class="col-md-12">
                                <textarea name="script" class="form-control" rows="8" placeholder="@lang('Paste your script with proper key')"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark" data-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn--primary" id="editBtn">@lang('Update')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

