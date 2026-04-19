@extends('user.layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-lg-8 col-md-10 mx-auto">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">@lang('Create New Savings Plan')</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">@lang('Create a new savings plan to achieve your financial goals.')</p>
                    
                    <form method="POST" action="{{ route('user.savings.store') }}" class="mt-4">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label">@lang('Plan Name')</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">@lang('Target Amount')</label>
                            <input type="number" class="form-control @error('target_amount') is-invalid @enderror" name="target_amount" step="0.01" value="{{ old('target_amount') }}" required>
                            @error('target_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">@lang('Description')</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" name="description" rows="4">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">@lang('Create Savings Plan')</button>
                        <a href="{{ route('user.home') }}" class="btn btn-secondary">@lang('Cancel')</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
