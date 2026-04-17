@extends('admin.layouts.app')
@section('panel')
    @if(@$section->content)
        <div class="container-fluid">
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5>@lang('Content Settings')</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.frontend.sections.content', $key)}}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="type" value="content">
                                <div class="row">
                                    @php
                                        $imgCount = 0;
                                    @endphp
                                    @foreach($section->content as $k => $item)

                                        @if($k == 'images')
                                            @php
                                                $imgCount = collect($item)->count();
                                            @endphp
                                            @foreach($item as $imgKey => $image)
                                                <div class="col-lg-{{ $imgCount > 1 ? 6 : 12 }} col-md-6 col-12 mb-3">
                                                    <input type="hidden" name="has_image" value="1">
                                                    <div class="form-group">
                                                        <label class="form-label">{{__(inputTitle(@$imgKey))}}</label>
                                                        <div class="image-upload">
                                                            <div class="text-center mb-3">
                                                                <img src="{{getImage('assets/images/frontend/' . $key .'/'. @$content->data_values->$imgKey,@$section->content->images->$imgKey->size) }}" class="img-fluid rounded" style="max-width: 200px;" alt="img-placeholder" />
                                                            </div>
                                                            <div class="avatar-edit">
                                                                <input type="file" class="form-control profilePicUpload" name="image_input[{{ @$imgKey }}]" id="profilePicUpload{{ $loop->index }}" accept=".png, .jpg, .jpeg">
                                                                <label for="profilePicUpload{{ $loop->index }}" class="btn btn-primary w-100"><i class="la la-upload"></i> {{__(inputTitle(@$imgKey))}}</label>
                                                                <small class="d-block mt-2 text-muted">@lang('Supported files'): <b>@lang('jpeg'), @lang('jpg'), @lang('png')</b>.
                                                                    @if(@$section->content->images->$imgKey->size)
                                                                        | @lang('Will be resized to'): <b>{{@$section->content->images->$imgKey->size}}</b> @lang('px').
                                                                    @endif
                                                                </small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
                                            @if($k != 'images')
                                                @if($item == 'icon')
                                                    <div class="col-12 mb-3">
                                                        <div class="form-group">
                                                            <label class="form-label">{{__(inputTitle($k))}}</label>
                                                            <input type="text" class="form-control icon" name="{{ $k }}" value="{{ @$content->data_values->$k }}" required>
                                                            <small class="d-block mt-1 text-muted">@lang('Enter icon class name')</small>
                                                        </div>
                                                    </div>
                                                @elseif($item == 'textarea')
                                                    <div class="col-12 mb-3">
                                                        <div class="form-group">
                                                            <label class="form-label">{{__(inputTitle($k))}}</label>
                                                            <textarea rows="5" class="form-control" placeholder="{{__(inputTitle($k))}}" name="{{$k}}" required>{{ @$content->data_values->$k}}</textarea>
                                                        </div>
                                                    </div>

                                                @elseif($item == 'textarea-nic')
                                                    <div class="col-12 mb-3">
                                                        <div class="form-group">
                                                            <label class="form-label">{{__(inputTitle($k))}}</label>
                                                            <textarea rows="8" class="form-control nicEdit" placeholder="{{__(inputTitle($k))}}" name="{{$k}}" >{{ @$content->data_values->$k}}</textarea>
                                                        </div>
                                                    </div>
                                                @elseif($k == 'select')
                                                    @php
                                                        $selectName = $item->name;
                                                    @endphp
                                                    <div class="col-lg-6 col-md-6 col-12 mb-3">
                                                        <div class="form-group">
                                                            <label class="form-label">{{__(inputTitle(@$selectName))}}</label>
                                                            <select class="form-control" name="{{ @$selectName }}">
                                                                @foreach($item->options as $selectItemKey => $selectOption)
                                                                    <option value="{{ $selectItemKey }}" @if(@$content->data_values->$selectName == $selectItemKey) selected @endif>{{ $selectOption }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="col-lg-6 col-md-6 col-12 mb-3">
                                                        <div class="form-group">
                                                            <label class="form-label">{{__(inputTitle($k))}}</label>
                                                            <input type="text" class="form-control" placeholder="{{__(inputTitle($k))}}" name="{{$k}}" value="{{@$content->data_values->$k }}" required/>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endif
                                        @endif
                                    @endforeach
                                </div>

                                <div class="form-group mt-3">
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary btn-lg">@lang('Submit')</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif


    @if(@$section->element)
        <div class="container-fluid">
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">@lang('Elements')</h5>
                            @if($section->element->modal)
                                <a href="javascript:void(0)" class="btn btn-sm btn-primary addBtn"><i class="fa fa-fw fa-plus"></i> @lang('Add New')</a>
                            @else
                                <a href="{{route('admin.frontend.sections.element',$key)}}" class="btn btn-sm btn-success"><i class="fa fa-fw fa-plus"></i> @lang('Add New')</a>
                            @endif
                        </div>

                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                    <tr>
                                        <th style="width: 50px;">#</th>
                                        @if(@$section->element->images)
                                            <th class="d-none d-md-table-cell">@lang('Image')</th>
                                        @endif
                                        @foreach($section->element as $k => $type)
                                            @if($k !='modal')
                                                @if($type=='text' || $type=='icon')
                                                    <th class="d-none d-lg-table-cell">{{ __(inputTitle($k)) }}</th>
                                                @elseif($k == 'select')
                                                    <th class="d-none d-lg-table-cell">{{inputTitle(@$section->element->$k->name)}}</th>
                                                @endif
                                            @endif
                                        @endforeach
                                        <th class="text-center">@lang('Action')</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($elements as $data)
                                        <tr>
                                            <td>{{$loop->iteration}}</td>
                                            @if(@$section->element->images)
                                            @php $firstKey = collect($section->element->images)->keys()[0]; @endphp
                                                <td class="d-none d-md-table-cell">
                                                    <img src="{{ getImage('assets/images/frontend/' . $key .'/'. @$data->data_values->$firstKey,@$section->element->images->$firstKey->size) }}" width="50" class="rounded" alt="@lang('image')">
                                                </td>
                                            @endif
                                            @foreach($section->element as $k => $type)
                                                @if($k !='modal')
                                                    @if($type == 'text' || $type == 'icon')
                                                        <td class="d-none d-lg-table-cell">
                                                            @if($type == 'icon')
                                                                <code>@php echo @$data->data_values->$k; @endphp</code>
                                                            @else
                                                                <small>{{__(@$data->data_values->$k)}}</small>
                                                            @endif
                                                        </td>
                                                    @elseif($k == 'select')
                                                        @php
                                                            $dataVal = @$section->element->$k->name;
                                                        @endphp
                                                        <td class="d-none d-lg-table-cell"><small>{{@$data->data_values->$dataVal}}</small></td>
                                                    @endif
                                                @endif
                                            @endforeach
                                            <td class="text-center">
                                                @if($section->element->modal)
                                                @php
                                                    $images = [];
                                                    if(@$section->element->images){
                                                        foreach($section->element->images as $imgKey => $imgs){
                                                            $images[] = getImage('assets/images/frontend/' . $key .'/'. @$data->data_values->$imgKey,@$section->element->images->$imgKey->size);
                                                        }
                                                    }
                                                @endphp
                                                    <button class="btn btn-sm btn-info updateBtn"
                                                        data-id="{{$data->id}}"
                                                        data-all="{{json_encode($data->data_values)}}"
                                                        @if(@$section->element->images)
                                                            data-images="{{ json_encode($images) }}"
                                                        @endif>
                                                        @lang('Edit')
                                                    </button>
                                                @else
                                                    <a href="{{route('admin.frontend.sections.element',[$key,$data->id])}}" class="btn btn-sm btn-info">@lang('Edit')</a>
                                                @endif
                                                <button class="btn btn-sm btn-danger removeBtn" data-id="{{ $data->id }}">@lang('Delete')</button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td class="text-muted text-center py-4" colspan="100%">{{ __($emptyMessage) }}</td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Add METHOD MODAL --}}
        <div id="addModal" class="modal fade" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"> @lang('Add New') {{__(inputTitle($key))}} @lang('Item')</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="@lang('Close')"></button>
                    </div>
                    <form action="{{ route('admin.frontend.sections.content', $key) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="type" value="element">
                        <div class="modal-body">
                            @foreach($section->element as $k => $type)
                                @if($k != 'modal')
                                    @if($type == 'icon')
                                        <div class="form-group mb-3">
                                            <label class="form-label">{{__(inputTitle($k))}}</label>
                                            <input type="text" class="form-control icon" name="{{ $k }}" required>
                                        </div>
                                    @elseif($k == 'select')
                                        <div class="form-group mb-3">
                                            <label class="form-label">{{inputTitle(@$section->element->$k->name)}}</label>
                                            <select class="form-control" name="{{ @$section->element->$k->name }}">
                                                @foreach($section->element->$k->options as $selectKey => $options)
                                                    <option value="{{ $selectKey }}">{{ $options }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @elseif($k == 'images')
                                        @foreach($type as $imgKey => $image)
                                        <input type="hidden" name="has_image" value="1">
                                        <div class="form-group mb-3">
                                            <label class="form-label">{{__(inputTitle($k)) }}</label>
                                            <div class="image-upload">
                                                <input type="file" class="form-control profilePicUpload" name="image_input[{{ $imgKey }}]" id="addImage{{ $loop->index }}" accept=".png, .jpg, .jpeg">
                                                <label for="addImage{{ $loop->index }}" class="btn btn-success w-100 mt-2"><i class="la la-upload"></i> {{ __(inputTitle($imgKey)) }}</label>
                                                <small class="d-block mt-2 text-muted">@lang('Supported files'): <b>@lang('jpeg'), @lang('jpg'), @lang('png')</b>.
                                                    @if(@$section->element->images->$imgKey->size)
                                                        | @lang('Will be resized to'): <b>{{@$section->element->images->$imgKey->size}}</b> @lang('px').
                                                    @endif
                                                </small>
                                            </div>
                                        </div>
                                        @endforeach
                                    @elseif($type == 'textarea')
                                        <div class="form-group mb-3">
                                            <label class="form-label">{{__(inputTitle($k))}}</label>
                                            <textarea rows="4" class="form-control" placeholder="{{__(inputTitle($k))}}" name="{{$k}}" required></textarea>
                                        </div>

                                    @elseif($type == 'textarea-nic')
                                        <div class="form-group mb-3">
                                            <label class="form-label">{{__(inputTitle($k))}}</label>
                                            <textarea rows="4" class="form-control nicEdit" placeholder="{{__(inputTitle($k))}}" name="{{$k}}"></textarea>
                                        </div>

                                    @else
                                        <div class="form-group mb-3">
                                            <label class="form-label">{{__(inputTitle($k))}}</label>
                                            <input type="text" class="form-control" placeholder="{{__(inputTitle($k))}}" name="{{$k}}" required/>
                                        </div>

                                    @endif
                                @endif
                            @endforeach
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('Close')</button>
                            <button type="submit" class="btn btn-primary">@lang('Save')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>




        {{-- Update METHOD MODAL --}}
        <div id="updateBtn" class="modal fade" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"> @lang('Update')  {{__(inputTitle($key))}} @lang('Item')</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="@lang('Close')"></button>
                    </div>
                    <form action="{{ route('admin.frontend.sections.content', $key) }}" class="edit-route" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="type" value="element">
                        <input type="hidden" name="id">
                        <div class="modal-body">
                            @foreach($section->element as $k => $type)
                                @if($k != 'modal')
                                    @if($type == 'icon')
                                        <div class="form-group mb-3">
                                            <label class="form-label">{{inputTitle($k)}}</label>
                                            <input type="text" class="form-control icon" name="{{ $k }}" required>
                                        </div>

                                    @elseif($k == 'select')
                                    <div class="form-group mb-3">
                                        <label class="form-label">{{inputTitle(@$section->element->$k->name)}}</label>
                                        <select class="form-control" name="{{ @$section->element->$k->name }}">
                                            @foreach($section->element->$k->options as $selectKey => $options)
                                                <option value="{{ $selectKey }}">{{ $options }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    @elseif($k == 'images')
                                        @foreach($type as $imgKey => $image)
                                        <input type="hidden" name="has_image" value="1">
                                        <div class="form-group mb-3">
                                            <label class="form-label">{{__(inputTitle($k))}}</label>
                                            <div class="image-upload">
                                                <input type="file" class="form-control profilePicUpload" name="image_input[{{ $imgKey }}]" id="uploadImage{{ $loop->index }}" accept=".png, .jpg, .jpeg">
                                                <label for="uploadImage{{ $loop->index }}" class="btn btn-success w-100 mt-2"><i class="la la-upload"></i> {{ __(inputTitle($imgKey)) }}</label>
                                                <small class="d-block mt-2 text-muted">@lang('Supported files'): <b>@lang('jpeg'), @lang('jpg'), @lang('png')</b>.
                                                    @if(@$section->element->images->$imgKey->size)
                                                        | @lang('Will be resized to'): <b>{{@$section->element->images->$imgKey->size}}</b> @lang('px').
                                                    @endif
                                                </small>
                                            </div>
                                        </div>
                                        @endforeach
                                    @elseif($type == 'textarea')
                                        <div class="form-group mb-3">
                                            <label class="form-label">{{inputTitle($k)}}</label>
                                            <textarea rows="4" class="form-control" placeholder="{{__(inputTitle($k))}}" name="{{$k}}" required></textarea>
                                        </div>

                                    @elseif($type == 'textarea-nic')
                                        <div class="form-group mb-3">
                                            <label class="form-label">{{inputTitle($k)}}</label>
                                            <textarea rows="4" class="form-control nicEdit" placeholder="{{__(inputTitle($k))}}" name="{{$k}}"></textarea>
                                        </div>

                                    @else
                                        <div class="form-group mb-3">
                                            <label class="form-label">{{inputTitle($k)}}</label>
                                            <input type="text" class="form-control" placeholder="{{__(inputTitle($k))}}" name="{{$k}}" required/>
                                        </div>

                                    @endif
                                @endif
                            @endforeach
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('Close')</button>
                            <button type="submit" class="btn btn-primary">@lang('Update')</button>
                        </div>
                    </form>
                </div>
            </div>
                                                    @endif>
                                                    Edit
                                                </button>
                                            @else
                                                <a href="{{route('admin.frontend.sections.element',[$key,$data->id])}}" class="btn btn-sm text-white ">Edit</a>
                                            @endif
                                            <button class="btn btn-danger btn-sm removeBtn" data-id="{{ $data->id }}">Delete</button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Add METHOD MODAL --}}
        <div id="addModal" class="modal fade" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"> @lang('Add New') {{__(inputTitle($key))}} @lang('Item')</h5>

                    </div>
                    <form action="{{ route('admin.frontend.sections.content', $key) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="type" value="element">
                        <div class="modal-body">
                            @foreach($section->element as $k => $type)
                                @if($k != 'modal')
                                    @if($type == 'icon')

                                        <div class="form-group mb-1">
                                            <label>{{__(inputTitle($k))}}</label>
                                            <div class="input-group has_append">
                                                <input type="text" class="form-control icon" name="{{ $k }}" required>

                                            </div>
                                        </div>
                                    @elseif($k == 'select')
                                    <div class="form-group mb-1">
                                        <label>{{inputTitle(@$section->element->$k->name)}}</label>
                                        <select class="form-control" name="{{ @$section->element->$k->name }}">
                                            @foreach($section->element->$k->options as $selectKey => $options)
                                                <option value="{{ $selectKey }}">{{ $options }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @elseif($k == 'images')
                                        @foreach($type as $imgKey => $image)
                                        <input type="hidden" name="has_image" value="1">
                                        <div class="form-group mb-1">
                                            <label>{{__(inputTitle($k)) }}</label>
                                            <div class="image-upload">
                                                <div class="thumb">

                                                    <div class="avatar-edit">
                                                        <input type="file" class="profilePicUpload" name="image_input[{{ $imgKey }}]" id="addImage{{ $loop->index }}" accept=".png, .jpg, .jpeg">
                                                        <label for="addImage{{ $loop->index }}" class="bg--success">{{ __(inputTitle($imgKey)) }}</label>
                                                        <small class="mt-2 text-facebook">@lang('Supported files'): <b>@lang('jpeg'), @lang('jpg'), @lang('png')</b>.
                                                            @if(@$section->element->images->$imgKey->size)
                                                                | @lang('Will be resized to'): <b>{{@$section->element->images->$imgKey->size}}</b> @lang('px').
                                                            @endif
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    @elseif($type == 'textarea')

                                        <div class="form-group mb-1">
                                            <label>{{__(inputTitle($k))}}</label>
                                            <textarea rows="4" class="form-control" placeholder="{{__(inputTitle($k))}}" name="{{$k}}" required></textarea>
                                        </div>

                                    @elseif($type == 'textarea-nic')

                                        <div class="form-group mb-1">
                                            <label>{{__(inputTitle($k))}}</label>
                                            <textarea rows="4" class="form-control nicEdit" placeholder="{{__(inputTitle($k))}}" name="{{$k}}"></textarea>
                                        </div>

                                    @else

                                        <div class="form-group mb-1">
                                            <label>{{__(inputTitle($k))}}</label>
                                            <input type="text" class="form-control" placeholder="{{__(inputTitle($k))}}" name="{{$k}}" required/>
                                        </div>

                                    @endif
                                @endif
                            @endforeach
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-warning" data-dismiss="modal">@lang('Close')</button>
                            <button type="submit" class="btn btn--primary text-white">@lang('Save')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>




        {{-- Update METHOD MODAL --}}
        <div id="updateBtn" class="modal fade" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"> @lang('Update')  {{__(inputTitle($key))}} @lang('Item')</h5>

                    </div>
                    <form action="{{ route('admin.frontend.sections.content', $key) }}" class="edit-route" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="type" value="element">
                        <input type="hidden" name="id">
                        <div class="modal-body">
                            @foreach($section->element as $k => $type)
                                @if($k != 'modal')
                                    @if($type == 'icon')

                                        <div class="form-group">
                                            <label>{{inputTitle($k)}}</label>
                                            <div class="input-group has_append">
                                                <input type="text" class="form-control icon" name="{{ $k }}" required>

                                            </div>
                                        </div>

                                    @elseif($k == 'select')
                                    <div class="form-group">
                                        <label>{{inputTitle(@$section->element->$k->name)}}</label>
                                        <select class="form-control" name="{{ @$section->element->$k->name }}">
                                            @foreach($section->element->$k->options as $selectKey => $options)
                                                <option value="{{ $selectKey }}">{{ $options }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    @elseif($k == 'images')
                                        @foreach($type as $imgKey => $image)
                                        <input type="hidden" name="has_image" value="1">
                                        <div class="form-group mb-1">
                                            <label>{{__(inputTitle($k))}}</label>
                                            <div class="image-upload">
                                                <div class="thumb">

                                                    <div class="avatar-edit">
                                                        <input type="file" class="profilePicUpload form-control" name="image_input[{{ $imgKey }}]" id="uploadImage{{ $loop->index }}" accept=".png, .jpg, .jpeg">
                                                        <label for="uploadImage{{ $loop->index }}" class="bg--success">{{ __(inputTitle($imgKey)) }}</label>
                                                        <small class="mt-2 text-facebook">@lang('Supported files'): <b>@lang('jpeg'), @lang('jpg'), @lang('png')</b>.
                                                            @if(@$section->element->images->$imgKey->size)
                                                                | @lang('Will be resized to'): <b>{{@$section->element->images->$imgKey->size}}</b> @lang('px').
                                                            @endif
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    @elseif($type == 'textarea')

                                        <div class="form-group mb-1">
                                            <label>{{inputTitle($k)}}</label>
                                            <textarea rows="4" class="form-control" placeholder="{{__(inputTitle($k))}}" name="{{$k}}" required></textarea>
                                        </div>

                                    @elseif($type == 'textarea-nic')

                                        <div class="form-group mb-1">
                                            <label>{{inputTitle($k)}}</label>
                                            <textarea rows="4" class="form-control nicEdit" placeholder="{{__(inputTitle($k))}}" name="{{$k}}"></textarea>
                                        </div>

                                    @else
                                        <div class="form-group mb-1">
                                            <label>{{inputTitle($k)}}</label>
                                            <input type="text" class="form-control" placeholder="{{__(inputTitle($k))}}" name="{{$k}}" required/>
                                        </div>

                                    @endif
                                @endif
                            @endforeach
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-warning" data-dismiss="modal">@lang('Close')</button>
                            <button type="submit" class="btn text-white btn--primary">@lang('Update')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>



        {{-- REMOVE METHOD MODAL --}}
        <div id="removeModal" class="modal fade" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">@lang('Confirmation')</h5>

                    </div>
                    <form action="{{ route('admin.frontend.remove') }}" method="POST">
                        @csrf
                        <input type="hidden" name="id">
                        <div class="modal-body">
                            <p class="font-weight-bold">@lang('Are you sure to delete this item? Once deleted can not be undone.')</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-success" data-bs-dismiss="modal">@lang('Close')</button>
                            <button type="submit" class="btn btn-danger">@lang('Remove')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>



    @endif
    {{-- if section element end --}}


@endsection

@push('script-lib')
    <script src="{{ asset('assets/admin/js/bootstrap-iconpicker.bundle.min.js') }}"></script>
@endpush


@push('script')

    <script>
        (function ($) {
            "use strict";
            $('.removeBtn').on('click', function () {
                var modal = $('#removeModal');
                modal.find('input[name=id]').val($(this).data('id'))
                modal.modal('show');
            });

            $('.addBtn').on('click', function () {
                var modal = $('#addModal');
                modal.modal('show');
            });

            $('.updateBtn').on('click', function () {
                var modal = $('#updateBtn');
                modal.find('input[name=id]').val($(this).data('id'));

                var obj = $(this).data('all');
                var images = $(this).data('images');
                if (images) {
                    for (var i = 0; i < images.length; i++) {
                        var imgloc = images[i];
                        $(`.imageModalUpdate${i}`).css("background-image", "url(" + imgloc + ")");
                    }
                }
                $.each(obj, function (index, value) {
                    modal.find('[name=' + index + ']').val(value);
                });

                modal.modal('show');
            });

            $('#updateBtn').on('shown.bs.modal', function (e) {
                $(document).off('focusin.modal');
            });
            $('#addModal').on('shown.bs.modal', function (e) {
                $(document).off('focusin.modal');
            });

            $('.iconPicker').iconpicker().on('change', function (e) {
                $(this).parent().siblings('.icon').val(`<i class="${e.icon}"></i>`);
            });
        })(jQuery);
    </script>

@endpush
