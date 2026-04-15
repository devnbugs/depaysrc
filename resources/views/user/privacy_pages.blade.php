@extends($activeTemplate.'layouts.frontend')

@section('content')
 <!-- banner-section start -->
 <section class="banner-section inner-banner contact">
        <div class="overlay">
            <div class="banner-content d-flex align-items-center">
                <div class="container">
                    <div class="row justify-content-start">
                        <div class="col-lg-7 col-md-10">
                            <div class="main-content">
                                <h1>{{$pageTitle}}</h1>
                                <div class="breadcrumb-area">
                                    <nav aria-label="breadcrumb">
                                        <ol class="breadcrumb d-flex align-items-center">
                                            <li class="breadcrumb-item"><a href="{{url('/')}}">Home</a></li>
                                            <li class="breadcrumb-item active" aria-current="page">{{$pageTitle}}</li>
                                        </ol>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- banner-section end -->
 <section class="appie-hero-area">
        <div class="container">

		<section class="appie-pricing-2-area pb-100">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="appie-section-title text-center"> 
						<p style="color:#000000;"> @php echo $content->data_values->details; @endphp					</p>
                    </div>
                </div>
        </div>
		</section>
</div>
</section>


@endsection


