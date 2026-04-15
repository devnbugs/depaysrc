<!-- Header Section Starts-->
	<header>
		<nav class="navbar navbar-toggleable-md navbar-light fixed-top" id="navigation">
			<div class="container">
				<a class="navbar-brand" href="{{url('/')}}"><img src="{{getImage(imagePath()['logoIcon']['path'] .'/logo.png')}}" width="60" alt="" /></a>
				<ul class="navbar-nav ml-auto" id="main-menu">
					<li class="nav-item"><a class="nav-link active" href="{{url('/')}}#home">Home</a></li>
					<li class="nav-item"><a class="nav-link" href="{{url('/')}}#about">About</a></li>
					<li class="nav-item"><a class="nav-link" href="{{url('/')}}#feature">Features</a></li>
					<li class="nav-item"><a class="nav-link" href="{{url('/')}}#testimonial">Testimonial</a></li>
					<li class="nav-item"><a class="nav-link" href="{{url('/')}}#faq">Faq</a></li>
					<li class="nav-item"><a class="nav-link" href="{{ route('contact') }}">contact us</a></li>

					@auth
					<li class="nav-item"><a class="nav-link" href="{{ route('user.home') }}">@lang('Dashboard')</a></li>
                   @else
                	<li class="nav-item"><a class="nav-link" href="{{ route('user.login') }}">@lang('Login')</a></li>
                  	<li class="nav-item"><a class="nav-link" href="{{ route('user.register') }}">@lang('Register')</a></li>
                   @endauth
				</ul>
				<div class="navbar-toggle"></div>
				<div id="responsive-menu"></div>
			</div>
		</nav>
	</header>
	<!-- Header Section ends -->
