


    <!-- BEGIN: Main Menu-->
    <div class="main-menu menu-fixed menu-light menu-accordion menu-shadow" data-scroll-to-active="true">
      <div class="navbar-header">
        <ul class="nav navbar-nav flex-row">
          <li class="nav-item me-auto"><a class="navbar-brand" href="{{route('admin.dashboard')}}"><span class="brand-logo">
               <img src="{{getImage(imagePath()['logoIcon']['path'] .'/logo.png')}}" width="150" alt="logo"></span>
              <!--<h2 class="brand-text">{{$general->sitename}}</h2></a></li>-->
          <li class="nav-item nav-toggle"><a class="nav-link modern-nav-toggle pe-0" data-bs-toggle="collapse"><i class="d-block d-xl-none text-primary toggle-icon font-medium-4" data-feather="x"></i><i class="d-none d-xl-block collapse-toggle-icon font-medium-4  text-primary" data-feather="disc" data-ticon="disc"></i></a></li>
        </ul>
      </div>
      <div class="shadow-bottom"></div>
      <div class="main-menu-content">
        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
          <li class="@if(\Route::current()->getName() == 'admin.dashboard') active @endif"><a class="d-flex align-items-center" href="{{route('admin.dashboard')}}"><i data-feather="home"></i><span class="menu-title text-truncate" data-i18n="Dashboards">@lang('Dashboard')</span></a>

          </li>
          <li class=" navigation-header"><span data-i18n="Apps &amp; Pages">Finance &amp; Fund</span><i data-feather="more-horizontal"></i>
          </li>
              <li class="nav-item"><a class="d-flex align-items-center" href="#"><i data-feather="dollar-sign"></i><span class="menu-title text-truncate" data-i18n="Invoice">@lang('Deposit')</span>
              @if($pending_deposits_count)
              <span class="badge badge-light-warning rounded-pill ms-auto me-2">New</span>
              @endif
              </a>
            <ul class="menu-content">
              <li class="@if(\Route::current()->getName() == 'admin.gateway.automatic.index') active @endif"><a class="d-flex align-items-center" href="{{route('admin.gateway.automatic.index')}}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="List">@lang('Automatic Gateways')</span></a>
              </li>
              <li class="@if(\Route::current()->getName() == 'admin.gateway.manual.index') active @endif"><a class="d-flex align-items-center" href="{{route('admin.gateway.manual.index')}}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="List">@lang('Manual Gateways')</span></a>
              </li>
              <li class="@if(\Route::current()->getName() == 'admin.deposit.pending') active @endif"><a class="d-flex align-items-center" href="{{route('admin.deposit.pending')}}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="List">@lang('Pending Deposits')</span>
               @if($pending_deposits_count)
              <span class="badge badge-light-warning rounded-pill ms-auto me-2">{{$pending_deposits_count}}</span>
              @endif
              </a>
              </li>
              <li class="@if(\Route::current()->getName() == 'admin.deposit.approved') active @endif"><a class="d-flex align-items-center" href="{{route('admin.deposit.approved')}}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="Preview">@lang('Approved Deposits')</span></a>
              </li>
               <li class="@if(\Route::current()->getName() == 'admin.deposit.successful') active @endif"><a class="d-flex align-items-center" href="{{route('admin.deposit.successful')}}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="Preview">@lang('Successful Deposits')</span></a>
              </li>
               <li class="@if(\Route::current()->getName() == 'admin.deposit.rejected') active @endif"><a class="d-flex align-items-center" href="{{route('admin.deposit.rejected')}}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="Preview">@lang('Rejected Deposits')</span></a>
              </li>
               <li class="@if(\Route::current()->getName() == 'admin.deposit.list') active @endif"><a class="d-flex align-items-center" href="{{route('admin.deposit.list')}}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="Preview">@lang('All Deposits')</span></a>
              </li>
            </ul>
          </li>

            
          <li class="nav-item"><a class="d-flex align-items-center" href="#"><i data-feather="credit-card"></i><span class="menu-title text-truncate" data-i18n="Invest">@lang('Manage Card')</span></a>
            <ul class="menu-content">
              <li class="@if(\Route::current()->getName() == 'admin.card.active') active @endif"><a class="d-flex align-items-center" href="{{route('admin.card.active')}}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="@lang('Active Card')">@lang('Valid Cards')</span></a>
              </li>
              <li class="@if(\Route::current()->getName() == 'admin.card.inactive') active @endif"><a class="d-flex align-items-center" href="{{route('admin.card.inactive')}}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="@lang('Inactive Card')">@lang('Terminated Cards')</span></a>
              </li>
            </ul>
          </li>

          <li class=" navigation-header"><span data-i18n="Misc">Bill Payment</span><i data-feather="more-horizontal"></i>
          </li>
          <li class=" nav-item @if(\Route::current()->getName() == 'admin.report.airtime') active @endif"><a class="d-flex align-items-center" href="{{route('admin.report.airtime')}}"><i data-feather="smartphone"></i><span class="menu-title text-truncate" data-i18n="Raise Support">@lang('Airtime Transaction')</span></a>
           </li>
          <li class=" nav-item @if(\Route::current()->getName() == 'admin.report.internet') active @endif"><a class="d-flex align-items-center" href="{{route('admin.report.internet')}}"><i data-feather="wifi"></i><span class="menu-title text-truncate" data-i18n="Raise Support">@lang('Internet Transaction')</span></a>
           </li>
          <li class=" nav-item @if(\Route::current()->getName() == 'admin.report.cabletv') active @endif"><a class="d-flex align-items-center" href="{{route('admin.report.cabletv')}}"><i data-feather="tv"></i><span class="menu-title text-truncate" data-i18n="Raise Support">@lang('Cable TV Transaction')</span></a>
           </li>
          <li class=" nav-item @if(\Route::current()->getName() == 'admin.report.utility') active @endif"><a class="d-flex align-items-center" href="{{route('admin.report.utility')}}"><i data-feather="power"></i><span class="menu-title text-truncate" data-i18n="API Doc">@lang('Utility Bill')</span></a>

         </li>
          <li class=" nav-item @if(\Route::current()->getName() == 'admin.report.waecreg') active @endif"><a class="d-flex align-items-center" href="{{route('admin.report.waecreg')}}"><i data-feather="credit-card"></i><span class="menu-title text-truncate" data-i18n="API Doc">@lang('WAEC REG.')</span></a>

         </li>
          <li class=" nav-item @if(\Route::current()->getName() == 'admin.report.waecres') active @endif"><a class="d-flex align-items-center" href="{{route('admin.report.waecres')}}"><i data-feather="credit-card"></i><span class="menu-title text-truncate" data-i18n="API Doc">@lang('WAEC Result.')</span></a>

         </li>
         
       </li>
        <li class=" navigation-header"><span data-i18n="Apps &amp; Pages">Finance &amp; Fund</span><i data-feather="more-horizontal"></i>
       </li>     
         
         <li class=" nav-item @if(\Route::current()->getName() == 'admin.dashboard.bundles') active @endif"><a class="d-flex align-items-center" href="{{route('admin.dashboard.bundles')}}"><i data-feather="wifi"></i><span class="menu-title text-truncate" data-i18n="API Doc">@lang('Manage Bundles.')</span></a>

         </li>
         </li>
         <li class=" nav-item @if(\Route::current()->getName() == 'admin.dashboard.c') active @endif"><a class="d-flex align-items-center" href="{{route('admin.dashboard')}}"><i data-feather="dollar-sign"></i><span class="menu-title text-truncate" data-i18n="API Doc">@lang('Check Balance.')</span></a>

         </li>

          <!--##########USER MANAGER##########!-->
          <li class=" navigation-header"><span data-i18n="Apps &amp; Pages">@lang('Manage Users')</span><i data-feather="more-horizontal"></i>
          </li>
              <li class="nav-item"><a class="d-flex align-items-center" href="#"><i data-feather="users"></i><span class="menu-title text-truncate" data-i18n="Invoice">@lang('Manage Users')</span>

              </a>
            <ul class="menu-content">
              <li class="@if(\Route::current()->getName() == 'admin.users.create') active @endif"><a class="d-flex align-items-center" href="{{route('admin.users.create')}}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="User">@lang('Create User')</span></a>
              </li>
              <li class="@if(\Route::current()->getName() == 'admin.users.all') active @endif"><a class="d-flex align-items-center" href="{{route('admin.users.all')}}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="User">@lang('All Users')</span></a>
              </li>
              <li class="@if(\Route::current()->getName() == 'admin.users.active') active @endif"><a class="d-flex align-items-center" href="{{route('admin.users.active')}}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="User">@lang('Active Users')</span></a>
              </li>
              <li class="@if(\Route::current()->getName() == 'admin.users.banned') active @endif"><a class="d-flex align-items-center" href="{{route('admin.users.banned')}}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="User">@lang('Banned Users')</span></a>
              </li>
              <li class="@if(\Route::current()->getName() == 'admin.users.email.verified') active @endif"><a class="d-flex align-items-center" href="{{route('admin.users.email.verified')}}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="User">@lang('Email Verified')</span></a>
              </li>
              <li class="@if(\Route::current()->getName() == 'admin.users.sms.verified') active @endif"><a class="d-flex align-items-center" href="{{route('admin.users.sms.verified')}}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="User">@lang('SMS Verified')</span></a>
              </li>
              <li class="@if(\Route::current()->getName() == 'admin.users.email.unverified') active @endif"><a class="d-flex align-items-center" href="{{route('admin.users.email.unverified')}}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="User">@lang('Email Unverified')</span></a>
              </li>
              <li class="@if(\Route::current()->getName() == 'admin.users.sms.unverified') active @endif"><a class="d-flex align-items-center" href="{{route('admin.users.sms.unverified')}}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="User">@lang('SMS Unverified')</span></a>
              </li>
              <li class="@if(\Route::current()->getName() == 'admin.users.email.all') active @endif"><a class="d-flex align-items-center" href="{{route('admin.users.email.all')}}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="User">@lang('Email to All')</span></a>
              </li>
              <li class="@if(\Route::current()->getName() == 'admin.users.with.balance') active @endif"><a class="d-flex align-items-center" href="{{route('admin.users.with.balance')}}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="User">@lang('Users With Balance')</span></a>
              </li>
            </ul>
          </li>

          <li class="@if(\Route::current()->getName() == 'admin.referral.index') active @endif"><a class="d-flex align-items-center" href="{{route('admin.referral.index')}}"><i data-feather="wifi"></i><span class="menu-title text-truncate" data-i18n="Referral">@lang('Referral')</span></a>

          </li>

          <li class="nav-item"><a class="d-flex align-items-center" href="#"><i data-feather="camera"></i><span class="menu-title text-truncate" data-i18n="Invoice">@lang('Users Verification')</span></a>
            <ul class="menu-content">
              <li class="@if(\Route::current()->getName() == 'admin.users.kyc.settings') active @endif"><a class="d-flex align-items-center" href="{{route('admin.users.kyc.settings')}}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="KYC">@lang('KYC Settings')</span></a>
              </li>
              <li class="@if(\Route::current()->getName() == 'admin.users.kyc.unverified') active @endif"><a class="d-flex align-items-center" href="{{route('admin.users.kyc.unverified')}}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="KYC">@lang('KYC Requests')</span></a>
              </li>
              <li class="@if(\Route::current()->getName() == 'admin.users.kyc.verified') active @endif"><a class="d-flex align-items-center" href="{{route('admin.users.kyc.verified')}}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="KYC">@lang('KYC Verified')</span></a>
              </li>

            </ul>
          </li>

          <li class="nav-item"><a class="d-flex align-items-center" href="#"><i data-feather="life-buoy"></i><span class="menu-title text-truncate" data-i18n="Ticket">@lang('Support Ticket')</span></a>
            <ul class="menu-content">
              <li class="@if(\Route::current()->getName() == 'admin.users.open.ticket') active @endif"><a class="d-flex align-items-center" href="{{route('admin.users.open.ticket')}}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="Ticket">@lang('Open Ticket')</span></a>
              </li>
              <li class="@if(\Route::current()->getName() == 'admin.users.replied.ticket') active @endif"><a class="d-flex align-items-center" href="{{route('admin.users.replied.ticket')}}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="Ticket">@lang('Replied Ticket')</span></a>
              </li>
              <li class="@if(\Route::current()->getName() == 'admin.users.closed.ticket') active @endif"><a class="d-flex align-items-center" href="{{route('admin.users.closed.ticket')}}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="Ticket">@lang('Closed Ticket')</span></a>
              </li>

            </ul>
          </li>

          <!--##########SETTINGS MANAGER##########!-->
          <li class=" navigation-header"><span data-i18n="Apps &amp; Pages">@lang('System Settings')</span><i data-feather="more-horizontal"></i>
          </li>
              <li class="nav-item"><a class="d-flex align-items-center" href="#"><i data-feather="settings"></i><span class="menu-title text-truncate" data-i18n="Invoice">@lang('Settings')</span>

              </a>
            <ul class="menu-content">
              <li class="@if(\Route::current()->getName() == 'admin.setting.index') active @endif"><a class="d-flex align-items-center" href="{{route('admin.setting.index')}}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="List">@lang('General Setting')</span></a>
              </li>
              <li class="@if(\Route::current()->getName() == 'admin.setting.logo.icon') active @endif"><a class="d-flex align-items-center" href="{{route('admin.setting.logo.icon')}}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="List">@lang('Logo & Favicon')</span></a>
              </li>
              <li class="@if(\Route::current()->getName() == 'admin.extensions.index') active @endif"><a class="d-flex align-items-center" href="{{route('admin.extensions.index')}}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="List">@lang('Live Chat Setup')</span></a>
              </li>
              <li class="@if(\Route::current()->getName() == 'admin.setting.cookie') active @endif"><a class="d-flex align-items-center" href="{{route('admin.setting.cookie')}}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="List">@lang('Site Cookies')</span></a>
              </li>
              <li class="@if(\Route::current()->getName() == 'admin.language.manage') active @endif"><a class="d-flex align-items-center" href="{{route('admin.language.manage')}}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="List">@lang('Language')</span></a>
              </li>
              <li class="@if(\Route::current()->getName() == 'admin.auth.stats' || \Route::current()->getName() == 'admin.auth.logs') active @endif"><a class="d-flex align-items-center" href="{{route('admin.auth.stats')}}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="List">@lang('Authentication')</span></a>
              </li>
              <li class="@if(\Route::current()->getName() == 'admin.seo') active @endif"><a class="d-flex align-items-center" href="{{route('admin.seo')}}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="List">@lang('SEO Manager')</span></a>
              </li>
            </ul>
          </li>

          <li class="nav-item"><a class="d-flex align-items-center" href="#"><i data-feather="mail"></i><span class="menu-title text-truncate" data-i18n="Invoice">@lang('Email Manager')</span></a>
            <ul class="menu-content">
              <li class="@if(\Route::current()->getName() == 'admin.email.template.global') active @endif"><a class="d-flex align-items-center" href="{{route('admin.email.template.global')}}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="List">@lang('Global Template')</span></a>
              </li>
              <li class="@if(\Route::current()->getName() == 'admin.email.template.index') active @endif"><a class="d-flex align-items-center" href="{{ route('admin.email.template.index') }}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="List">@lang('Email Templates')</span></a>
              </li>
                <li class="@if(\Route::current()->getName() == 'admin.email.template.setting') active @endif"><a class="d-flex align-items-center" href="{{route('admin.email.template.setting')}}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="List">@lang('Email Configure')</span></a>
              </li>
            </ul>
          </li>


          <li class="nav-item"><a class="d-flex align-items-center" href="#"><i data-feather="tablet"></i><span class="menu-title text-truncate" data-i18n="Invoice">@lang('SMS Manager')</span></a>
            <ul class="menu-content">
              <li class="@if(\Route::current()->getName() == 'admin.sms.template.global') active @endif"><a class="d-flex align-items-center" href="{{route('admin.sms.template.global')}}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="List">@lang('Global Template')</span></a>
              </li>
              <li class="@if(\Route::current()->getName() == 'admin.sms.templates.setting') active @endif"><a class="d-flex align-items-center" href="{{route('admin.sms.templates.setting')}}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="List">@lang('SMS Gateways')</span></a>
              </li>
                <li class="@if(\Route::current()->getName() == 'admin.sms.template.index') active @endif"><a class="d-flex align-items-center" href="{{ route('admin.sms.template.index') }}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="List">@lang('SMS Template')</span></a>
              </li>
            </ul>
          </li>

          <li class="nav-item"><a class="d-flex align-items-center" href="#"><i data-feather="globe"></i><span class="menu-title text-truncate" data-i18n="Invoice">@lang('Manage Section')</span></a>
            <ul class="menu-content">
            @php
            $lastSegment =  collect(request()->segments())->last();
            @endphp
            @foreach(getPageSections(true) as $k => $secs)
            @if($secs['builder'])
              <li class="@if(\Route::current()->getName() == 'user.withdraw') active @endif"><a class="d-flex align-items-center" href="{{ route('admin.frontend.sections',$k) }}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="List">{{__($secs['name'])}}</span></a>
              </li>
           @endif
           @endforeach

            </ul>
          </li>


          <li class=" navigation-header"><span data-i18n="Misc">Report</span><i data-feather="more-horizontal"></i>
          </li>
          <li class=" nav-item @if(\Route::current()->getName() == 'admin.report.transaction') active @endif"><a class="d-flex align-items-center" href="{{route('admin.report.transaction')}}"><i data-feather="printer"></i><span class="menu-title text-truncate" data-i18n="Raise Support">@lang('Transaction Report')</span></a>
           </li>
          <li class=" nav-item @if(\Route::current()->getName() == 'admin.report.login.history') active @endif"><a class="d-flex align-items-center" href="{{route('admin.report.login.history')}}"><i data-feather="life-buoy"></i><span class="menu-title text-truncate" data-i18n="Raise Support">@lang('Login Report')</span></a>
           </li>
          <li class=" nav-item @if(\Route::current()->getName() == 'admin.report.email.history') active @endif"><a class="d-flex align-items-center" href="{{route('admin.report.email.history')}}"><i data-feather="menu"></i><span class="menu-title text-truncate" data-i18n="API Doc">@lang('Email Report')</span></a>

         </li>
        </ul>
      </div>
    </div>
    <!-- END: Main Menu-->

<!-- sidebar end -->
