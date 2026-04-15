@php
    $feature = getContent('feature.content', true);
    $features = getContent('feature.element');
@endphp

 <!-- feature section start -->
 <section class="why-choose-us" id="feature">
		<div class="container">
			<div class="row">
				<!-- Section title start -->
				<div class="col-md-12">
					<div class="section-title">
						<h2>{{ __(@$feature->data_values->heading) }}</h2>
					</div>
				</div>
				<!-- Section title end -->
			</div>
			<div class="row">
				<!-- Why us box start -->
				@foreach($features as $singleFeature)
              <div class="col-lg-3 col-md-6">
					<div class="why-us-box wow fadeInUp" data-wow-delay="0.2s">
						<h3>{{ __(@$singleFeature->data_values->title) }}</h3>
						<div class="icon-box">
							 @php echo $singleFeature->data_values->icon; @endphp
						</div>
                        <p class="mt-3">{{ __(@$singleFeature->data_values->text) }}</p>
					</div>
				</div>

            @endforeach
				<!-- Why us box end -->

			</div>
		</div>
	</section>


      <!-- feature section end -->
