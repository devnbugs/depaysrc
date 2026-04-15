@php
    $banner = getContent('banner.content', true);
    $image = getContent('breadcumb.content', true);
@endphp
	<!-- Banner Section Starts -->
	<section class="banner" id="home">
		<div class="container">
			<div class="row">
				<div class="col-lg-6">
					<!-- Header content start -->
					<div class="header-content wow fadeInUp">
						<h2>{{ __(@$banner->data_values->heading) }}</h2>
						<p>{{ __(@$banner->data_values->sub_heading) }}</p>
						<div class="download-button">
							<a href="#" class="btn-download btn-apple"><i class="fa fa-apple"></i> <span>Download on the</span>App Store</a>
							<a href="#" class="btn-download btn-android"><i class="fa fa-android"></i> <span>Get in on</span>Google Play</a>
						</div>
					</div>
					<!-- Header content end -->
				</div>
				<div class="col-lg-4 offset-lg-2">
					<div class="registration wow fadeInRight">
						<h3>{{ __($pageTitle) }}</h3>

						<!--
							<form action="#">
								<div class="row">
									<div class="form-group col-md-6">
										<input type="text" class="form-control" placeholder="First Name" required>
									</div>
									<div class="form-group col-md-6">
										<input type="text" class="form-control" placeholder="Last Name" required>
									</div>
									<div class="form-group col-md-12">
										<input type="email" class="form-control" placeholder="Email" required>
									</div>
									<div class="form-group col-md-12">
										<input type="text" class="form-control" placeholder="Phone" required>
									</div>
									<div class="form-group col-md-12">
										<select class="form-control">
											<option>India</option>
											<option>United State</option>
											<option>Australia</option>
										</select>
									</div>
									<div class="form-group col-md-12">
										<button type="submit" class="btn-register">@lang('Get Started')</button>
										<p class="registration-note">Accept <a href="#">Terms & Conditions</a></p>
									</div>
								</div>
							</form>-->
							<img src="{{ getImage( 'assets/images/frontend/breadcumb/' .@$image->data_values->image, '1920x900') }}" class="img-fluid"  alt="...">

					</div>
				</div>
			</div>
		</div>
	</section>
	<!-- Banner Section Ends -->


