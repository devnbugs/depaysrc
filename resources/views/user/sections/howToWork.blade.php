@php
    $work = getContent('howToWork.content', true);
    $works = getContent('howToWork.element', false, null, true);
@endphp
<!--How section starts -->
	<div class="buy-step">
		<div class="container">
			<div class="row">
				<!-- Section title start -->
				<div class="col-md-12">
					<div class="section-title">
						<h2>{{ __(@$work->data_values->heading) }}</h2><br>
						<p>{{ __(@$work->data_values->sub_heading) }}</p>
					</div>
				</div>
				<!-- Section title end -->
			</div>
			<div class="row">
			 @foreach($works as $singleWork)
			 <div class="col-lg-4">
					<div class="buy-step-single right-arrow wow fadeInUp" data-wow-delay="0.2s">
						<span>{{ $loop->index + 1 }}</span>
						<p><a href="#">{{ __($singleWork->data_values->title) }}</a> your <br />{{ __($singleWork->data_values->text) }}.</p>
					</div>
				</div>

            @endforeach

			</div>
		</div>
	</div>

      <!-- how work section end -->
