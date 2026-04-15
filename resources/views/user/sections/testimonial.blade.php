@php
    $testimonial = getContent('testimonial.content', true);
    $testimonials = getContent('testimonial.element');
@endphp

<!-- Testimonial section starts -->
	<section class="testimonial" id="testimonial">
		<div class="container">
			<div class="row">
				<!-- Section title start -->
				<div class="col-md-12">
					<div class="section-title">
						<h2>{{ __(@$testimonial->data_values->heading) }}</h2>
                <p class="mt-3">{{ __(@$testimonial->data_values->sub_heading) }}</p>
					</div>
				</div>
				<!-- Section title end -->
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="testimonial-slider-wrapper">
						<div class="swiper-container testimonial-slider">
							<div class="swiper-wrapper">
								<!-- Testimonial slide start -->
								@foreach($testimonials as $singleData)

								<div class="swiper-slide">
									<div class="testimonial-slide">
										<div class="testimonial-quote">
											<p>{{ __($singleData->data_values->say) }}</p>
										</div>
										<div class="testimonial-author">
											<i class="fa fa-quote-left"></i>
											<h3>{{ __($singleData->data_values->name) }}</h3>
											<p>{{ __($singleData->data_values->designation) }}</p>
										</div>
									</div>
								</div>
								<!-- Testimonial slide end -->
								<!--<img src="{{ getImage( 'assets/images/frontend/testimonial/' .@$singleData->data_values->image, '300x250') }}" alt="image">-->

								 @endforeach

							</div>
							<!-- Testimonial Pagination start -->
							<div class="testimonial-pagination"></div>
							<!-- Testimonial Pagination end -->
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
	<!-- Testimonial section ends -->

