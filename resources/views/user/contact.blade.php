@extends($activeTemplate.'layouts.frontend')

@section('content')

@php
    $contact = getContent('contact_us.content', true);
    $contacts = getContent('contact_us.element');
@endphp



    <!--====== Gainz HERO PART START ======-->
    <!-- banner-section start -->
    <section class="banner-section inner-banner contact">
        <div class="overlay">
            <div class="banner-content d-flex align-items-center">
                <div class="container">
                    <div class="row justify-content-start">
                        <div class="col-lg-7 col-md-10">
                            <div class="main-content">
                                <h1>Contact Us</h1>
                                <div class="breadcrumb-area">
                                    <nav aria-label="breadcrumb">
                                        <ol class="breadcrumb d-flex align-items-center">
                                            <li class="breadcrumb-item"><a href="{{url('/')}}">Home</a></li>
                                            <li class="breadcrumb-item active" aria-current="page">Contact Us</li>
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

    <!-- Apply for a loan In start -->
    <section class="apply-for-loan contact">
        <div class="overlay pt-120 pb-120">
            <div class="container wow fadeInUp">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="section-header text-center">
                            <h2 class="title">{{ __(@$contact->data_values->heading) }}</h2>
                            <p>{{ __(@$contact->data_values->sub_heading) }}</p>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <div class="form-content">
                        <form class="contact-form" method="post" action="">
                @csrf
                                <div class="row">
                                    <div class="col-6">
                                        <div class="single-input">
                                            <label for="name">Name</label>
                                            <input type="text" name="name" id="name" placeholder="What's your name?">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="single-input">
                                            <label for="email">Email</label>
                                            <input type="text" name="email" id="email" placeholder="What's your email?">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    
                                    <div class="col-12">
                                        <div class="single-input">
                                            <label for="loan">Subject</label>
                                            <input type="text" name="subject" id="subject" placeholder="Ex. Auto Loan, Home Loan">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="single-input">
                                            <label for="message">Message</label>
                                            <textarea id="message" name="message" placeholder="I would like to get in touch with you..." cols="30" rows="10"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="btn-area text-center">
                                    <button class="cmn-btn">Send Message</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Apply for a loan In end -->

    <!-- Need more help In start -->
    <section class="account-feature loan-feature need-more-help">
        <div class="overlay pt-120 pb-120">
            <div class="container wow fadeInUp">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="section-header text-center">
                            <h5 class="sub-title">You can reach out to us for all your</h5>
                            <h2 class="title">Need More Help?</h2>
                            <p>Queries, complaints and feedback. We will be happy to serve you</p>
                        </div>
                    </div>
                </div>
                <div class="row cus-mar">
                @foreach($contacts as $singleContact)
                <div class="col-md-4">
                        <div class="single-box">
                            <div class="icon-box">
                                <img src="{{url('/')}}/assets/frontend/assets/images/icon/need-help-2.png" alt="icon">
                            </div>
                            <h5>{{ __($singleContact->data_values->address_type) }}</h5>
                            <p><a href="#" class="__cf_email__" >{{ __($singleContact->data_values->address) }}</a></p>
                        </div>
                    </div> 
                         @endforeach
                     
                </div>
            </div>
        </div>
    </section>
    <!-- Need more help In end -->
 
 

 

@endsection
