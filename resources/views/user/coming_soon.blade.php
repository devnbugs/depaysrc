@extends('user.layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-lg-8 col-md-10 mx-auto">
            <div class="card">
                <div class="card-body text-center py-5">
                    <h2 class="text-primary mb-3">@lang('Coming Soon')</h2>
                    <p class="text-muted mb-4">@lang('This feature is coming soon. We are working hard to bring you an amazing experience.')</p>
                    <a href="{{ route('user.home') }}" class="btn btn-primary">@lang('Back to Dashboard')</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
