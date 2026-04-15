@extends($activeTemplate.'layouts.frontend')
@section('content')


@php
    $about = getContent('about.content', true);
    $aboutDatas = getContent('about.element');
@endphp

    <!-- About Us In start -->
    <section class="about-section">
        <div class="overlay pt-120 pb-120">
            <div class="container wow fadeInUp">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <div class="text-area">
                            <h5 class="sub-title">{{ __(@$about->data_values->heading) }}</h5>
                            <h2 class="title">{{ __(@$about->data_values->sub_heading) }}</h2>
                            <p>We understand that with global ambition, comes global challenges, and we are here to
                                bridge the gap by offering seamless cross-border financial services. Consider us your
                                friendly digital guide to all things finance, helping you make the most of your money.
                            </p>
                        </div>
                        <div class="row cus-mar">
                            <div class="col-xl-4 col-md-4">
                                <div class="count-content text-center">
                                    <div class="count-number">
                                        <h4 class="counter">98</h4>
                                        <h4 class="static">%</h4>
                                    </div>
                                    <p>Customer satisfaction</p>
                                </div>
                            </div>
                            <div class="col-xl-4 col-md-4">
                                <div class="count-content text-center">
                                    <div class="count-number ">
                                        <h4 class="counter">250</h4>
                                        <h4 class="static">M</h4>
                                    </div>
                                    <p>Monthly active users</p>
                                </div>
                            </div>
                            <div class="col-xl-4 col-md-4">
                                <div class="count-content text-center">
                                    <div class="count-number ">
                                        <h4 class="counter">100</h4>
                                        <h4 class="static">K</h4>
                                    </div>
                                    <p>New Users per week</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 text-end">
                        <div class="img-area">
                            <img class="img-1" src="{{ getImage('assets/images/frontend/about/' .@$about->data_values->image, '635x560') }}" alt="image">
                            <img class="img-2" src="{{url('/')}}/assets/frontend/assets/images/about-img-2.png" alt="image">
                            <img class="img-3" src="{{url('/')}}/assets/frontend/assets/images/about-img-3.png" alt="image">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- About Us In end -->

    <!-- Our core values In start -->
    <section class="our-core-values">
        <div class="overlay pt-120 pb-120">
            <div class="container wow fadeInUp">
                <div class="row justify-content-center">
                    <div class="col-lg-9">
                        <div class="section-header text-center">
                            <h5 class="sub-title">Our core values</h5>
                            @foreach($aboutDatas as $aboutData)
                            <h2 class="title">{{ __($aboutData->data_values->title) }}</h2>
                            <p>{{ __($aboutData->data_values->text) }}</p>
  
                            @endforeach
                            
                            
                        </div>
                    </div>
                </div>
                <div class="row cus-mar">
                    <div class="col-xl-4 col-md-4">
                        <div class="single-box">
                            <div class="icon">
                                <img src="{{url('/')}}/assets/frontend/assets/images/icon/core-values-1.png" alt="icon">
                            </div>
                            <div class="text-area">
                                <h5>Customer First</h5>
                                <p>We aim to provide our customers with top-notch service that helps them grow their
                                    business and put their best foot forward.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-4">
                        <div class="single-box">
                            <div class="icon">
                                <img src="{{url('/')}}/assets/frontend/assets/images/icon/core-values-2.png" alt="icon">
                            </div>
                            <div class="text-area">
                                <h5>Passion over Pedigree</h5>
                                <p>We hire for passion, because passionate people can overcome any obstacle and acquire
                                    any knowledge necessary.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-4">
                        <div class="single-box">
                            <div class="icon">
                                <img src="{{url('/')}}/assets/frontend/assets/images/icon/core-values-3.png" alt="icon">
                            </div>
                            <div class="text-area">
                                <h5>Commitment</h5>
                                <p>Our first priority is to keep your money safe and secure. Every single aspect of our
                                    service is optimized to protect and grow your funds!</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Our core values In end -->

    <!-- Map In start -->
    <section class="map-section">
        <div class="overlay pt-120 pb-120">
            <div class="container wow fadeInUp">
                <div class="row justify-content-center">
                    <div class="col-xl-9 col-lg-10">
                        <div class="section-header text-center">
                            <h5 class="sub-title">Our core values</h5>
                            <h2 class="title">Our values help us set the bar for good banking high.</h2>
                            <p>Our values define us. They guide us in building and improving our product, hiring
                                teammates, and serving our customers.</p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="map-area">
                        <img src="{{url('/')}}/assets/frontend/assets/images/world-map.png" alt="image">
                        <div class="element pos-1">
                            <div class="details">
                                <p>2715 Ash Dr. San Jose, South Dakota 83475</p>
                            </div>
                            <div class="dot-area">
                                <img src="{{url('/')}}/assets/frontend/assets/images/icon/maps-dot.png" alt="image">
                            </div>
                        </div>
                        <div class="element pos-2">
                            <div class="details">
                                <p>2972 Westheimer Rd. Santa Ana, Illinois 85486</p>
                            </div>
                            <div class="dot-area">
                                <img src="{{url('/')}}/assets/frontend/assets/images/icon/maps-dot.png" alt="image">
                            </div>
                        </div>
                        <div class="element pos-3">
                            <div class="details">
                                <p>2715 Ash Dr. San Jose, South Dakota 83475</p>
                            </div>
                            <div class="dot-area">
                                <img src="{{url('/')}}/assets/frontend/assets/images/icon/maps-dot.png" alt="image">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Map In end -->
 
  
    <!-- Get Start In start -->
    <section class="get-start wow fadeInUp">
        <div class="overlay">
            <div class="container">
                <div class="col-12">
                    <div class="get-content">
                        <div class="section-text">
                            <h3 class="title">Ready to get started?</h3>
                            <p>It only takes a few minutes to register your FREE Bankio account.</p>
                        </div>
                        <a href="{{ route('user.register') }}" class="cmn-btn">Open an Account</a>
                        <img src="{{url('/')}}/assets/frontend/assets/images/get-start.png" alt="images">
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Get Start In end -->

 

    <!--====== Gainz FOOTER PART START ======-->




@endsection
