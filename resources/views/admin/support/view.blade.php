@extends('admin.layouts.app')

@section('panel')

@push('style')
    <!-- BEGIN: Page CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset($activeTemplateTrue. 'app-assets/css/core/menu/menu-types/vertical-menu.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset($activeTemplateTrue. 'app-assets/css/pages/app-chat.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset($activeTemplateTrue. 'app-assets/css/pages/app-chat-list.min.css')}}">
    <!-- END: Page CSS-->
@endpush
    <!-- BEGIN: Content-->
<!-- Main chat area -->

<section class="chat-app-window">

  <!-- Active Chat -->
  <div class="active-chat">
    <!-- Chat Header -->
    <div class="chat-navbar">
      <header class="chat-header">
        <div class="d-flex align-items-center">


          <h6 class="mb-0">@lang('Ticket')#{{ $my_ticket->ticket }}</h6>
            @if($my_ticket->status != 3)
           <a class="badge bg-danger badge-sm" type="button" title="@lang('Close Ticket')" data-bs-toggle="modal" data-bs-target="#DelModal">Close Ticket</i>
           </a> @endif
        </div>

       <div class="d-flex align-items-center">

        @if($my_ticket->status == 0)
                                <a class="badge badge-glsow text-white bg-warning">@lang('Open')</a>
                            @elseif($my_ticket->status == 1)
                                <a class="badge badge-glsow  text-white bg-primary">@lang('Answered')</a>
                            @elseif($my_ticket->status == 2)
                                <a class="badge badge-glsow  text-white bg-info">@lang('Replied')</a>
                            @elseif($my_ticket->status == 3)
                                <a class="badge badge-glsow text-white  bg-success">@lang('Closed')</a>
                            @endif
        </div>

      </header>
    </div>
    <!--/ Chat Header -->

    <!-- User Chat messages -->
    <div class="user-chats">
      <div class="chats">
       @foreach($messages as $message)
         @if($message->type == 1)
        <div class="chat">

          <div class="chat-body">
            <div class="chat-content">
              <p>{{$message->message}}</p><br>
              <small>{{ $message->created_at->format('l, dS F Y @ H:i') }}</small>
               @if($message->attachments()->count() > 0)
         <div class="d-flex flex-column">
          @foreach($message->attachments as $k=> $image)
                <a href="{{route('admin.user.ticket.download',encrypt($image->id))}}" class="mb-50">
                <i data-feather="file" class="text-white" ></i>
                 <small class="text-muted fw-bolder">@lang('Attachment')</small>
                </a>
          @endforeach

              </div>
         @endif
            </div>

          </div>

        </div>

        @else
        <div class="chat chat-left">

          <div class="chat-body">
            <div class="chat-content">
              <p>{{$message->message}}</p><br>
              <small>{{ $message->created_at->format('l, dS F Y @ H:i') }}</small>
               @if($message->attachments()->count() > 0)
         <div class="d-flex flex-column">
          @foreach($message->attachments as $k=> $image)
                <a href="{{route('admin.user.ticket.download',encrypt($image->id))}}" class="mb-50">
                <i data-feather="file" class="text-white" ></i>
                 <small class="text-muted fw-bolder">@lang('Attachment')</small>
                </a>
          @endforeach

              </div>
         @endif
            </div>

          </div>
        </div>

        @endif
        @endforeach
      </div>
    </div>
    <!-- User Chat messages -->
    @if($my_ticket->status != 3)
    <!-- Submit Chat form -->
    <form class="chat-app-form" method="post" action="{{ route('admin.user.ticket.reply', $my_ticket->id) }}"">
    @csrf
      <div class="input-group input-group-merge me-1 form-send-message">

        <input type="hidden" name="replayTicket" value="1">


              <input type="text" required name="message" class="form-control" placeholder="Type your reply" />

        </div>
      <button type="submit" class="btn btn-primary send" onclick="enterChat()f;">
        <i data-feather="send" class="d-lg-none"></i>
        <span class="d-none d-lg-block">Reply</span>
      </button>
    </form>
    <!-- Submit Chat form -->
    @endif
  </div>
  <!--/ Active Chat -->
</section>
<!--/ Main chat area -->
  </div>  </div>  </div>
<!-- END: Content-->



    <div class="modal fade" id="DelModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post" action="{{ route('admin.user.ticket.reply', $my_ticket->id) }}">
                    @csrf
                    <input type="hidden" name="replayTicket" value="2">
                    <div class="modal-header">
                        <h5 class="modal-title"> @lang('Confirmation')!</h5>

                    </div>
                    <div class="modal-body">
                        <strong class="text-dark">@lang('Are you sure you want to close this support ticket')?</strong>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal"><i class="fa fa-times"></i>
                            @lang('Close')
                        </button>
                        <button type="submit" class="btn text-white btn--success btn-sm"><i class="fa fa-check"></i> @lang("Confirm")
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')
<script src="{{ asset($activeTemplateTrue. 'app-assets/js/scripts/pages/app-chat.min.js')}}"></script>
    <script>
        (function ($) {
            "use strict";
            $('.delete-message').on('click', function (e) {
                $('.message_id').val($(this).data('id'));
            });
            $('.addFile').on('click',function(){
                $("#fileUploadsContainer").append(
                    `<div class="input-group">
                        <input type="file" name="attachments[]" class="form-control my-3" required />
                        <div class="input-group-append support-input-group">
                            <span class="input-group-text btn btn-danger support-btn remove-btn">x</span>
                        </div>
                    </div>`
                )
            });
            $(document).on('click','.remove-btn',function(){
                $(this).closest('.input-group').remove();
            });
        })(jQuery);

    </script>
@endpush
