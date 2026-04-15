
 <!-- BEGIN: Header-->
    <nav class="header-navbar navbar navbar-expand-lg align-items-center floating-nav navbar-light navbar-shadow container-xxl">
      <div class="navbar-container d-flex content">
        <div class="bookmark-wrapper d-flex align-items-center">
          <ul class="nav navbar-nav d-xl-none">
            <li class="nav-item"><a class="nav-link menu-toggle" href="#"><i class="ficon text-white" data-feather="menu"></i></a></li>
          </ul>

        </div>
        <ul class="nav navbar-nav align-items-center ms-auto">

          <li class="nav-item d-none d-lg-block"><a class="nav-link nav-link-style"><i class="ficon  text-white" data-feather="moon"></i></a></li>
          <li class="nav-item dropdown dropdown-notification me-25"><a class="nav-link" href="#" data-bs-toggle="dropdown"><i class=" text-white ficon" data-feather="bell"></i>
          @if($adminNotifications->count() > 0)
          <span class="badge rounded-pill bg-danger badge-up">{{ $adminNotifications->count() }}</span>
          @endif
          </a>
            <ul class="dropdown-menu dropdown-menu-media dropdown-menu-end">
              <li class="dropdown-menu-header">
                <div class="dropdown-header d-flex">
                  <h4 class="notification-title mb-0 me-auto">Notifications</h4>
                   @if($adminNotifications->count() > 0)
                   <div class="badge rounded-pill badge-light-primary">{{ $adminNotifications->count() }} New</div>

                    @endif

                </div>
              </li>
            @foreach($adminNotifications as $notification)
           <li class="scrollable-container media-list"><a class="d-flex" href="{{ route('admin.notification.read',$notification->id) }}">

                  <div class="list-item d-flex align-items-start">
                    <div class="me-1">
                      <div class="avatar"><img src="{{ getImage(imagePath()['profile']['user']['path'].'/'.@$notification->user->image)}}" alt="avatar" width="32" height="32"></div>
                    </div>
                    <div class="list-item-body flex-grow-1">
                      <p class="media-heading"><span class="fw-bolder">{{ __($notification->title) }}</span></p><small class="notification-text"> {{ $notification->created_at->diffForHumans() }}.</small>
                    </div>
                  </div></a><a class="d-flex" href="{{ route('admin.notification.read',$notification->id) }}">

              </li>
            @endforeach

            </ul>
          </li>
          <li class="nav-item dropdown dropdown-user"><a class="nav-link dropdown-toggle dropdown-user-link" id="dropdown-user" href="index.html#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <div class="user-nav d-sm-flex d-none"><span class="user-name fw-bolder  text-white">{{auth()->guard('admin')->user()->username}}</span><span class="user-status  text-white">Admin</span></div><span class="avatar"><img class="round" src="{{ getImage('assets/admin/images/profile/'. auth()->guard('admin')->user()->image) }}" alt="avatar" height="40" width="40"><span class="avatar-status-online"></span></span></a>
            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdown-user">
            <a class="dropdown-item" href="{{ route('admin.profile') }}"><i class="me-50" data-feather="user"></i> @lang('Profile')</a>
            <a class="dropdown-item" href="{{route('admin.password')}}"><i class="me-50" data-feather="lock"></i> @lang('Password')</a>
            <div class="dropdown-divider"></div><a class="dropdown-item" href="{{ route('admin.logout') }}"><i class="me-50" data-feather="power"></i> @lang('Logout')</a>
            </div>
          </li>
        </ul>
      </div>
    </nav>


    <!-- END: Header-->



