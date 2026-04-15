@php
    $payment = getContent('payment.content', true);
    $paymentDatas = getContent('payment.element');
    $allPayment = App\Models\Gateway::where('status', 1)->get();
@endphp

 <!-- payment brand section start -->
 <!-- Currency Price box starts -->
	<div class="currency-price">
		<div class="container">
			<div class="row">
				<!-- Currency Price box start -->
				@foreach($allPayment as $singleValue)

				<div class="col-md-2 col-6">
					<div class="currency-price-box wow fadeInUp" data-wow-delay="0.2s">
						<figure>
							<img src="{{ getImage(imagePath()['gateway']['path'].'/'. $singleValue->image,imagePath()['gateway']['size']) }}" alt="" />
						</figure>
						<h3>{{$singleValue->name}}</h3>
					</div>
				</div>
                @endforeach
				<!-- Currency Price box end -->

			</div>
		</div>
	</div>
