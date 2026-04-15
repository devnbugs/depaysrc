@php
    $coundown = getContent('overView.element');
@endphp
	<!-- Stat Counter section starts -->
	<div class="stat-counter">
		<div class="container">
			<div class="row">
				<!-- Stat Counter single start -->
				 @foreach($coundown as $overView)

				<div class="col-md-3">
					<div class="counter-box">
					<!--@php echo $overView->data_values->icon; @endphp-->
						<h3 class="countser">{{ $overView->data_values->text }}</h3>
						<p>{{ __($overView->data_values->title) }}</p>
					</div>
				</div>
				 @endforeach
				<!-- Stat Counter single end -->

			</div>
		</div>
	</div>
	<!-- Stat Counter section ends -->
