@extends($activeTemplate.'layouts.dashboard')

@section('content')

@push('style')
     
    <link href="{{ asset($activeTemplateTrue. 'user-assets/assets/css/apps/mailing-chat.css')}}" rel="stylesheet" type="text/css" />
@endpush
<!--  BEGIN CONTENT AREA  -->
<div id="content" class="main-content">
            <div class="layout-px-spacing">

                <div class="chat-section layout-top-spacing">
                    <div class="row">

                        <div class="col-xl-12 col-lg-12 col-md-12">

                            <div class="chat-system">
                                <div class="hamburger"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-menu mail-menu d-lg-none"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg></div>
                                <div class="user-list-box">
                                    <div class="search">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                                        <input type="text" class="form-control" placeholder="Search" />
                                    </div>
                                    <div class="people">
 
                                      
                                        <div class="person border-none" data-chat="person12">
                                            <div class="user-info">
                                                <div class="f-head">
                                                    <img src="{{ @getImage(imagePath()['profile']['user']['path'].'/'. $user->image,imagePath()['profile']['user']['size']) }}"" alt="avatar">
                                                </div>
                                                <div class="f-body">
                                                    <div class="meta-info">
                                                        <span class="user-name" data-name="Grace Roberts">@lang('Ticket')#{{ $my_ticket->ticket }}</span>
                                                        <span class="user-meta-time">

                                                        @if($my_ticket->status == 0)
                                                            <a class="badge badge-glsow text-white bg-warning">@lang('Open')</a>
                                                        @elseif($my_ticket->status == 1)
                                                            <a class="badge badge-glsow  text-white bg-primary">@lang('Answered')</a>
                                                        @elseif($my_ticket->status == 2)
                                                            <a class="badge badge-glsow  text-white bg-info">@lang('Replied')</a>
                                                        @elseif($my_ticket->status == 3)
                                                            <a class="badge badge-glsow text-white  bg-success">@lang('Closed')</a>
                                                        @endif
                                                        </span>
                                                    </div>
                                                    <span class="preview">{{$my_ticket->subject}}</span>
                                                </div>
                                            </div>
                                        </div>                                        
                                    </div>
                                </div>
                                <div class="chat-box">

                                    <div class="chat-not-selected">
                                        <p> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-message-square"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg> Click User To Chat</p>
                                    </div>

                                    <div class="chat-box-inner">
                                        <div class="chat-meta-user">
                                            <div class="current-chat-user-name"><span><img src="{{ @getImage(imagePath()['profile']['user']['path'].'/'. $user->image,imagePath()['profile']['user']['size']) }}"" alt="dynamic-image"><span class="name"></span></span></div>

                                            <div class="chat-action-btn align-self-center">
                                            @if($my_ticket->status != 3)
                                            <a class="badge bg-danger badge-sm" type="button" title="@lang('Close Ticket')" data-bs-toggle="modal" data-bs-target="#DelModal">Close Ticket</i>
                                            </a> @endif
                                                
                                            </div>
                                        </div>
                                        <div class="chat-conversation-box">
                                            <div id="schat-conversation-box-scroll" class="chat-conversation-box-scroll">
                                                 
                                                <div class="chat" data-chat="person12">
                                                @foreach($messages as $message)
                                                  @if($message->type == 2)
                                                    
                                                    <div class="bubble you">
                                                    {{$message->message}}<br>
                                                    <small>{{ $message->created_at->format('l, dS F Y @ H:i') }}</small>
                                                      @if($message->attachments()->count() > 0)
                                                 
                                                  @foreach($message->attachments as $k=> $image)
                                                        <a href="{{route('user.ticket.download',encrypt($image->id))}}" class="mb-50">
                                                        <i data-feather="file" class="text-white" ></i>
                                                        <small class="text-muted fw-bolder">@lang('Attachment')</small>
                                                        </a>
                                                  @endforeach
                                                  @endif
                                                    </div>
                                                  
                                              
                                                    @else
                                                    <div class="bubble me">
                                                    {{$message->message}}<br>
                                                    <small>{{ $message->created_at->format('l, dS F Y @ H:i') }}</small>
                                                      @if($message->attachments()->count() > 0)
                                                 
                                                  @foreach($message->attachments as $k=> $image)
                                                        <a href="{{route('user.ticket.download',encrypt($image->id))}}" class="mb-50">
                                                        <i data-feather="file" class="text-white" ></i>
                                                        <small class="text-muted fw-bolder">@lang('Attachment')</small>
                                                        </a>
                                                  @endforeach
                                                  @endif
                                                      
                                                    </div>
                                                   
                                                    @endif
                                                @endforeach
                                                </div>
                                                 
                                                 
                                            </div>
                                        </div>
                                        @if($my_ticket->status != 3)
                                        <div class="chat-footer">
                                            <div class="chat-input">
                                            <form class="chat-form" onsubmit="enterChat();" method="post" action="{{ route('user.ticket.reply', $my_ticket->id) }}"">
                                              @csrf
                                              <input type="hidden" name="replayTicket" value="1">
                                                
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-message-square"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                                                    <input type="text" required name="message"  class="mail-write-boxw form-control" placeholder="Message"/>
                                                    
                                                  </form>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>

    <!-- BEGIN: Content-->
<!-- Main chat area -->




    <div class="modal fade" id="DelModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post" action="{{ route('user.ticket.reply', $my_ticket->id) }}">
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

<script src="{{ asset($activeTemplateTrue. 'user-assets/assets/js/apps/mailbox-chat.js')}}"></script>
@endpush
