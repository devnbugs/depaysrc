@php
    $footer = getContent('footer.content', true);
    $medias = getContent('footer.element');
    $policy = getContent('policy_pages.element');
@endphp
<!-- Footer Section starts -->
	<footer class="footer">
		<div class="container">
			<div class="row">
				<div class="col-md-9">
					<!-- Footer social link start -->
					<div class="footer-social-link">
					 @foreach($medias as $media)
                    <a href="{{ $media->data_values->soial_media_link }}" target="_blank"> @php echo $media->data_values->icon; @endphp </a>
                     @endforeach
					</div>
					<!-- Footer social link end -->
					<!-- Footer Menu start -->
					<div class="footer-menu">
						<ul>
							@foreach($policy as $singlePolicy)
                             <li><a href="{{ route('privacy.page', ['slug'=> slug($singlePolicy->data_values->title), 'id'=>$singlePolicy->id]) }}">{{ __($singlePolicy->data_values->title) }}</a></li>
                            @endforeach
						</ul>
					</div>
					<!-- Footer Menu end -->

				</div>
				<div class="col-md-3 text-right">
					<div class="copyright">
						<p>&copy; {{ __($footer->data_values->text) }}</p>
					</div>
				</div>
			</div>
		</div>
	</footer>
