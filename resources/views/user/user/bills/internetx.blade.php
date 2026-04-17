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
                                        <h5><span class="w-currency">{{ $general->cur_sym }}</span>{{ showAmount($user->balance) }}</h5>
                                    </div>
                                    <div>
                                        <small class="text-muted">Data Purchased: {{ $trxcount }}</small>
                                    </div>
                                    <small class="text-muted"><br></small>
                                    <ul class="navbar-nav flex-row ml-auto">
                                        <li class="nav-item more-dropdown">
                                            <div class="dropdown custom-dropdown-icon">
                                                <a class="dropdown-toggle btn" href="#" role="button" id="customDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <span>Check Balance Codes</span>
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down">
                                                        <polyline points="6 9 12 15 18 9"></polyline>
                                                    </svg>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="customDropdown">
                                                    <a class="dropdown-item" href="tel:*323*4#">MTN</a>
                                                    <a class="dropdown-item" href="tel:*323#">Airtel</a>
                                                    <a class="dropdown-item" href="tel:*323#">GLO</a> 
                                                    <a class="dropdown-item" href="tel:*323#">9Mobile</a>
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
		</div>	
		<div class="row layout-top-spacing">
            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
                <div class="card">
                    <div class="card-body">
                        <div class="buy-sell-widget">
                            <div class="tab-content tab-content-default">
                                <div class="tab-pane fade show active" id="buy" role="tabpanel">
                                    <br>
                                    <form class="contact-form currency_validate" id="purchase" action="" method="post" enctype="multipart/form-data">
                                        @csrf
                                        <div class="row">
                                            <div class="form-group col-12">
                                                <label class="@if(Auth::user()->darkmode != 0) text-white @endif">Select Network</label>
                                                <div class="input-group mb-3">
                                                    <select name="network" id="s1" onChange="populate()" class="form-control" data-placeholder="Network" required>
                                                        <option label="Choose one">Select one</option>
                                                        @foreach($network as $data)
                                                            <option value="{{$data->symbol}}">{{$data->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
											@if(in_array($user->usertype, [2, 3]))
											<div class="form-group col-12">
                                                <label class="@if(Auth::user()->darkmode != 0) text-white @endif">Data Type</label>
                                                <div class="input-group mb-3">
                                                    <select name="datatype" id="s2" onChange="populatex()" class="form-control" data-placeholder="Bundle Type" required>
                                                        <option selected disabled>Data Type</option>
                                                    </select>
                                                </div>
                                            </div>
											@endif
                                            <div class="form-group col-12">
                                                <label class="@if(Auth::user()->darkmode != 0) text-white @endif">Select Bundle</label>
                                                <div class="input-group mb-3">
                                                    <select id="s3" name="plan" class="form-control" required>
                                                        <option selected disabled>Select Plan</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group col-12">
                                                <label class="@if(Auth::user()->darkmode != 0) text-white @endif">Enter Phone</label>
                                                <div class="input-group mb-3">
                                                    <input class="form-control" name="phone" id="phone" type="tel" placeholder="080123456789" maxlength="11" required>
                                                </div>
                                            </div>
                                            @if($user->pin_state == 1)
                                            <div class="form-group col-12">
                                                <label class="@if(Auth::user()->darkmode != 0) text-white @endif">Input PIN</label>
                                                <div class="input-group mb-3">
                                                    <input class="form-control pin-input" maxlength="4" type="password" name="pin_code" placeholder="* * * *" required>
                                                </div>
                                            </div>
                                            @endif
                                            <div class="col-12">
                                                <button type="submit" class="btn btn-primary text-white app-submit-button" id="buyButton">Buy Data</button>  
                                            </div>
                                        </div>
                                    </form>
                                    <br>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
	</div>	
    </div> 
</div>

@push('script')
<script>
function populate() {
    const s1 = document.getElementById('s1');
    const s2 = document.getElementById('s2');
    s2.innerHTML = "";
    var optionArray = [];

    if (s1.value == "01") {
        optionArray = ["|Select"@foreach($mtntype as $data)@if($data['networkcode'] == "mtn" && $data['status'] == "1" ), "{{$data['datatype']}}|{{$data['datatype']}}"@endif @endforeach];
    } else if (s1.value == "02") {
        optionArray = ["|Select"@foreach($glotype as $data)@if($data['networkcode'] == "glo" && $data['status'] == "1" ), "{{$data['datatype']}}|{{$data['datatype']}}"@endif @endforeach];
    } else if (s1.value == "03") {
        optionArray = ["|Select"@foreach($airteltype as $data)@if($data['networkcode'] == "airtel" && $data['status'] == "1" ), "{{$data['datatype']}}|{{$data['datatype']}}"@endif @endforeach];
    } else if (s1.value == "05") {
        optionArray = ["|Select"@foreach($airteltype as $data)@if($data['networkcode'] == "smile" && $data['status'] == "1" ), "{{$data['datatype']}}|{{$data['datatype']}}"@endif @endforeach];
    } else if (s1.value == "04") {
        optionArray = ["|Select"@foreach($ninetype as $data)@if($data['networkcode'] == "etisalat" && $data['status'] == "1" ), "{{$data['datatype']}}|{{$data['datatype']}}"@endif @endforeach];
    }

    // Populate s2 options
    optionArray.forEach(option => {
        const [value, text] = option.split("|");
        const newOption = document.createElement("option");
        newOption.value = value;
        newOption.innerHTML = text;
        s2.options.add(newOption);
    });
}
function populatex() {
    const s1 = document.getElementById('s1');
	const s2 = document.getElementById('s2');
    const s3 = document.getElementById('s3');
    s3.innerHTML = "";
    var optionArray = [];

	//SME
    if (s2.value == "SME" && s1.value == "01") {
        optionArray = ["|Select"@foreach($mtntypes as $data)@if($data['datatype'] == "SME" && $data['status'] == "1" ), "{{$data['plan']}}|{{$data['network']}} - {{$data['name']}} - {{ $general->cur_sym }}{{$data['cost']}} - {{$data['datatype']}}"@endif @endforeach];
    } else if (s2.value == "SME" && s1.value == "02") {
        optionArray = ["|Select"@foreach($glotypes as $data)@if($data['datatype'] == "SME" && $data['status'] == "1" ), "{{$data['plan']}}|{{$data['network']}} - {{$data['name']}} - {{ $general->cur_sym }}{{$data['cost']}} - {{$data['datatype']}}"@endif @endforeach];
    } else if (s2.value == "SME" && s1.value == "03") {
        optionArray = ["|Select"@foreach($airteltypes as $data)@if($data['datatype'] == "SME" && $data['status'] == "1" ), "{{$data['plan']}}|{{$data['network']}} - {{$data['name']}} - {{ $general->cur_sym }}{{$data['cost']}} - {{$data['datatype']}}"@endif @endforeach];
    } else if (s2.value == "SME" && s1.value == "04") {
        optionArray = ["|Select"@foreach($ninetypes as $data)@if($data['datatype'] == "SME" && $data['status'] == "1" ), "{{$data['plan']}}|{{$data['network']}} - {{$data['name']}} - {{ $general->cur_sym }}{{$data['cost']}} - {{$data['datatype']}}"@endif @endforeach];
    }
	
	//CG
    if (s2.value == "CG" && s1.value == "01") {
        optionArray = ["|Select"@foreach($mtntypes as $data)@if($data['datatype'] == "CG" && $data['status'] == "1" ), "{{$data['plan']}}|{{$data['network']}} - {{$data['name']}} - {{ $general->cur_sym }}{{$data['cost']}} - {{$data['datatype']}}"@endif @endforeach];
    } else if (s2.value == "CG" && s1.value == "02") {
        optionArray = ["|Select"@foreach($glotypes as $data)@if($data['datatype'] == "CG" && $data['status'] == "1" ), "{{$data['plan']}}|{{$data['network']}} - {{$data['name']}} - {{ $general->cur_sym }}{{$data['cost']}} - {{$data['datatype']}}"@endif @endforeach];
    } else if (s2.value == "CG" && s1.value == "03") {
        optionArray = ["|Select"@foreach($airteltypes as $data)@if($data['datatype'] == "CG" && $data['status'] == "1" ), "{{$data['plan']}}|{{$data['network']}} - {{$data['name']}} - {{ $general->cur_sym }}{{$data['cost']}} - {{$data['datatype']}}"@endif @endforeach];
    } else if (s2.value == "CG" && s1.value == "04") {
        optionArray = ["|Select"@foreach($ninetypes as $data)@if($data['datatype'] == "CG" && $data['status'] == "1" ), "{{$data['plan']}}|{{$data['network']}} - {{$data['name']}} - {{ $general->cur_sym }}{{$data['cost']}} - {{$data['datatype']}}"@endif @endforeach];
    }
	
	//CG_LITE
    if (s2.value == "CG_LITE" && s1.value == "01") {
        optionArray = ["|Select"@foreach($mtntypes as $data)@if($data['datatype'] == "CG_LITE" && $data['status'] == "1" ), "{{$data['plan']}}|{{$data['network']}} - {{$data['name']}} - {{ $general->cur_sym }}{{$data['cost']}} - {{$data['datatype']}}"@endif @endforeach];
    } else if (s2.value == "CG_LITE" && s1.value == "02") {
        optionArray = ["|Select"@foreach($glotypes as $data)@if($data['datatype'] == "CG_LITE" && $data['status'] == "1" ), "{{$data['plan']}}|{{$data['network']}} - {{$data['name']}} - {{ $general->cur_sym }}{{$data['cost']}} - {{$data['datatype']}}"@endif @endforeach];
    } else if (s2.value == "CG_LITE" && s1.value == "03") {
        optionArray = ["|Select"@foreach($airteltypes as $data)@if($data['datatype'] == "CG_LITE" && $data['status'] == "1" ), "{{$data['plan']}}|{{$data['network']}} - {{$data['name']}} - {{ $general->cur_sym }}{{$data['cost']}} - {{$data['datatype']}}"@endif @endforeach];
    } else if (s2.value == "CG_LITE" && s1.value == "04") {
        optionArray = ["|Select"@foreach($ninetypes as $data)@if($data['datatype'] == "CG_LITE" && $data['status'] == "1" ), "{{$data['plan']}}|{{$data['network']}} - {{$data['name']}} - {{ $general->cur_sym }}{{$data['cost']}} - {{$data['datatype']}}"@endif @endforeach];
    }
	
	//Awoof
    if (s2.value == "Awoof" && s1.value == "01") {
        optionArray = ["|Select"@foreach($mtntypes as $data)@if($data['datatype'] == "Awoof" && $data['status'] == "1" ), "{{$data['plan']}}|{{$data['network']}} - {{$data['name']}} - {{ $general->cur_sym }}{{$data['cost']}} - {{$data['datatype']}}"@endif @endforeach];
    } else if (s2.value == "Awoof" && s1.value == "02") {
        optionArray = ["|Select"@foreach($glotypes as $data)@if($data['datatype'] == "Awoof" && $data['status'] == "1" ), "{{$data['plan']}}|{{$data['network']}} - {{$data['name']}} - {{ $general->cur_sym }}{{$data['cost']}} - {{$data['datatype']}}"@endif @endforeach];
    } else if (s2.value == "Awoof" && s1.value == "03") {
        optionArray = ["|Select"@foreach($airteltypes as $data)@if($data['datatype'] == "Awoof" && $data['status'] == "1" ), "{{$data['plan']}}|{{$data['network']}} - {{$data['name']}} - {{ $general->cur_sym }}{{$data['cost']}} - {{$data['datatype']}}"@endif @endforeach];
    } else if (s2.value == "Awoof" && s1.value == "04") {
        optionArray = ["|Select"@foreach($ninetypes as $data)@if($data['datatype'] == "Awoof" && $data['status'] == "1" ), "{{$data['plan']}}|{{$data['network']}} - {{$data['name']}} - {{ $general->cur_sym }}{{$data['cost']}} - {{$data['datatype']}}"@endif @endforeach];
    }
	
	//Direct
    if (s2.value == "Direct" && s1.value == "01") {
        optionArray = ["|Select"@foreach($mtntypes as $data)@if($data['datatype'] == "Direct" && $data['status'] == "1" ), "{{$data['plan']}}|{{$data['network']}} - {{$data['name']}} - {{ $general->cur_sym }}{{$data['cost']}} - {{$data['datatype']}}"@endif @endforeach];
    } else if (s2.value == "Direct" && s1.value == "02") {
        optionArray = ["|Select"@foreach($glotypes as $data)@if($data['datatype'] == "Direct" && $data['status'] == "1" ), "{{$data['plan']}}|{{$data['network']}} - {{$data['name']}} - {{ $general->cur_sym }}{{$data['cost']}} - {{$data['datatype']}}"@endif @endforeach];
    } else if (s2.value == "Direct" && s1.value == "03") {
        optionArray = ["|Select"@foreach($airteltypes as $data)@if($data['datatype'] == "Direct" && $data['status'] == "1" ), "{{$data['plan']}}|{{$data['network']}} - {{$data['name']}} - {{ $general->cur_sym }}{{$data['cost']}} - {{$data['datatype']}}"@endif @endforeach];
    } else if (s2.value == "Direct" && s1.value == "04") {
        optionArray = ["|Select"@foreach($ninetypes as $data)@if($data['datatype'] == "Direct" && $data['status'] == "1" ), "{{$data['plan']}}|{{$data['network']}} - {{$data['name']}} - {{ $general->cur_sym }}{{$data['cost']}} - {{$data['datatype']}}"@endif @endforeach];
    }
	
	

    // Populate s3 options
    optionArray.forEach(option => {
        const [value, text] = option.split("|");
        const newOption = document.createElement("option");
        newOption.value = value;
        newOption.innerHTML = text;
        s3.options.add(newOption);
    });
}
</script>
<!--script>
function populate() {
    var s1 = document.getElementById('s1');
    var s2 = document.getElementById('s2');
    s2.innerHTML = "";
    var optionArray = [];

    if (s1.value == "01") {
        optionArray = ["|Select"@foreach($bill as $data)@if($data['networkcode'] == "mtn" && $data['status'] == "1" ), "{{$data['plan']}}|{{$data['network']}} - {{$data['name']}} - {{ $general->cur_sym }}{{$data['cost']}} - {{$data['datatype']}}/{{$data['validity']}}"@endif @endforeach];
    } else if (s1.value == "02") {
        optionArray = ["|Select"@foreach($bill as $data)@if($data['networkcode'] == "glo" && $data['status'] == "1" ), "{{$data['plan']}}|{{$data['network']}} - {{$data['name']}} - {{ $general->cur_sym }}{{$data['cost']}} - {{$data['datatype']}}/{{$data['validity']}}"@endif @endforeach];
    } else if (s1.value == "03") {
        optionArray = ["|Select"@foreach($bill as $data)@if($data['networkcode'] == "airtel" && $data['status'] == "1" ), "{{$data['plan']}}|{{$data['network']}} - {{$data['name']}} - {{ $general->cur_sym }}{{$data['cost']}} - {{$data['datatype']}}/{{$data['validity']}}"@endif @endforeach];
    } else if (s1.value == "05") {
        optionArray = ["|Select"@foreach($bill as $data)@if($data['networkcode'] == "smile" && $data['status'] == "1" ), "{{$data['plan']}}|{{$data['network']}} - {{$data['name']}} - {{ $general->cur_sym }}{{$data['cost']}} - {{$data['datatype']}}/{{$data['validity']}}"@endif @endforeach];
    } else if (s1.value == "04") {
        optionArray = ["|Select"@foreach($bill as $data)@if($data['networkcode'] == "etisalat" && $data['status'] == "1" ), "{{$data['plan']}}|{{$data['network']}} - {{$data['name']}} - {{ $general->cur_sym }}{{$data['cost']}} - {{$data['datatype']}}/{{$data['validity']}}"@endif @endforeach];
    }

    for (var i = 0; i < optionArray.length; i++) {
        var pair = optionArray[i].split("|");
        var newOption = document.createElement("option");
        newOption.value = pair[0];
        newOption.innerHTML = pair[1];
        s2.options.add(newOption);
    }
}
</script -->

@endpush

@endsection
