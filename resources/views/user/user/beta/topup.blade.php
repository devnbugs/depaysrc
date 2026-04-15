@extends($activeTemplate.'layouts.dashboard')
@section('content')

<div id="content" class="main-content">
            <div class="layout-px-spacing">
                <div class="row layout-top-spacing">
                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
                        <div class="card">
                            <div class="card-body">
                                <div class="buy-sell-widget">
                                    <div class="tab-content tab-content-default">
                                        <div>
                                            <div class="wallet-balance">
                                                <p>Balance</p>
                                                <h5 class=""><span class="w-currency"> Under Processing</h5>
                                            </div>
                                            <div>
                                                <small class="text-muted">Under Maintainance</small>
                                            </div>
                                            <small class="text-muted"><br></small>
                                            <ul class="navbar-nav flex-row ml-auto ">
                                                <li class="nav-item more-dropdown">
                                                    <div class="dropdown  custom-dropdown-icon">
                                                        <a class="dropdown-toggle btn" href="#" role="button" id="customDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span>Check Balance Codes</span> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down"><polyline points="6 9 12 15 18 9"></polyline></svg></a>
                                
                                                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="customDropdown">
                                                            <a class="dropdown-item" data-value="*323*4#" href="tel:*343*4#">MTN</a>
                                                            <a class="dropdown-item" data-value="*323#" href="tel:*343#">Airtel</a>
                                                            <a class="dropdown-item" data-value="*323#" href="tel:*343#">GLO</a> 
                                                            <a class="dropdown-item" data-value="*323#" href="tel:*343#">9Mobile</a>
                                                        </div>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div> 
                            </div>
                        </div>
                    </div>


@endsection
