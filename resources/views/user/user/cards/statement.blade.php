@extends($activeTemplate.'layouts.dashboard')
@section('content')

@push('style')
<link rel="stylesheet" type="text/css" href="{{ asset($activeTemplateTrue. 'app-assets/css/core/menu/menu-types/vertical-menu.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset($activeTemplateTrue. 'app-assets/css/plugins/forms/pickers/form-flat-pickr.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset($activeTemplateTrue. 'app-assets/css/pages/app-invoice.min.css')}}">

@endpush

<!-- Basic Tables start -->
<div class="row" id="basic-table">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">{{$pageTitle}}</h4>
      </div>
      <div class="card-body">
        <p class="card-text">

        </p>
      </div>
      <div class="table-responsive">
        <table class="table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Narration</th>
              <th>Amount</th>
              <th>Date</th>
              <th>Reference</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
          @php $i = 1; @endphp
          @foreach($log as $data)
            <tr>
              <td><span class="fw-bold">{{@$i++}}</span></td>
              <td><span class="fw-bold">{{@$data['narration']}}</span></td>
              <td>{{@$data['amount']}}{{@$data['currency']}}</td>
              <td>{{@$data['created_at']}}</td>
              <td>{{@$data['reference']}}</td>
              <td>{{@$data['status']}}</td>

            </tr>
         @endforeach
          </tbody>
        </table>
      </div>
      @if(count($log) < 1)
      <div class="card-body">
          <div class="demo-spacing-0">
            <div class="alert alert-danger" role="alert">
              <div class="alert-body d-flex align-items-center">
                <i data-feather="info" class="me-50"></i>
                <span class="text-center"> Sorry, we are unable to find any transaction fot his selected date range</span>
              </div>
            </div>
          </div>
        </div>
      @endif
    </div>
  </div>
</div>
<!-- Basic Tables end -->
@endsection
@push('script')
 <script src="{{ asset($activeTemplateTrue. 'app-assets/js/scripts/pages/app-invoice.min.js')}}"></script>


@endpush
