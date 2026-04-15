@php
    $faq = getContent('faq.content', true);
    $faqDatas = getContent('faq.element');
@endphp
<!-- FAQ Section starts -->
	<section class="faq" id="faq">
		<div class="container">
			<div class="row">
				<!-- Section title start -->
				<div class="col-md-12">
					<div class="section-title">
						<h2>{{ __(@$faq->data_values->heading) }}</h2>
					</div>
				</div>
				<!-- Section title end -->
			</div>
			<div class="row">
				<div class="col-md-12">
					<!-- FAQ Accordion start -->
					<div id="accordion" class="faq-accordion">
						<!-- Faq Panel start -->
						 @foreach($faqDatas as $singleFaq)
						<div class="faqs card wow fadeInUp" data-wow-delay="0.2s">
							<div class="card-header" id="headingOne{{ $singleFaq->id }}">
								<h5 class="mb-0">
									<a data-toggle="collapse" data-target="#collapseOne{{ $singleFaq->id }}" aria-expanded="false" aria-controls="collapseOne{{ $singleFaq->id }}">{{ __($singleFaq->data_values->question) }}</a>
								</h5>
							</div>
							<div id="collapseOne{{ $singleFaq->id }}" class="collapse close" aria-labelledby="headingOne{{ $singleFaq->id }}" data-parent="#accordion">
								<div class="card-body">
									<p>{{ __($singleFaq->data_values->answer) }}</p>
								</div>
							</div>
						</div>
						<!-- Faq Panel end -->
						 @endforeach

				</div>
			</div>
		</div>
	</section>
	<!-- FAQ Section ends -->

