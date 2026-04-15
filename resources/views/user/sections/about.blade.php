@php
    $about = getContent('about.content', true);
    $aboutDatas = getContent('about.element');
@endphp
 	<!-- About section starts -->

 	<section class="how-it-works" id="about">
		<div class="container">
			<div class="row">

				<div class="col-lg-12">
					<!-- How it work entry start -->
					<div class="how-it-work-entry">
						<h2>{{ __(@$about->data_values->heading) }}</h2>
						<p>{{ __(@$about->data_values->sub_heading) }}</p>
					</div>
					<!-- How it work entry end -->
				</div>
			</div>
		</div>
	</section>
	<!-- How it work section ends -->
	<!-- Portfolio section starts -->
	<section class="portfolio" id="dashboard">
		<div class="container">
			<div class="row">
				<!-- Section title start -->
				<div class="col-md-12">
					<div class="section-title">
						<h2>More About Us</h2>
					</div>
				</div>
				<!-- Section title end -->
			</div>
			<div class="row">
				<div class="col-md-5">

				@foreach($aboutDatas as $aboutData)
                   <!-- Portfolio step start -->
					<div class="portfolio-step wow fadeInLeft" data-wow-delay="0.6s">
						<div class="portfolio-step-header">
							<div class="icon-box text-white"  style="background-color: {{$general->secondary_color}}">
								@php echo $aboutData->data_values->icon; @endphp
							</div>
							<h3>{{ __($aboutData->data_values->title) }}</h3>
						</div>
						<p>{{ __($aboutData->data_values->text) }}</p>
					</div>
					<!-- Portfolio step end -->
                @endforeach

				</div>
				<div class="col-md-7">
					<!-- Portfolio image start -->
					<div class="portfolio-img wow fadeInRight">
						<figure>
							<img src="{{ getImage('assets/images/frontend/about/' .@$about->data_values->image, '635x560') }}" alt="" />
						</figure>
					</div>
					<!-- Portfolio image end -->
				</div>
			</div>
		</div>
	</section>



