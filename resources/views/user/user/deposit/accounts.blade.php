@extends($activeTemplate.'layouts.dashboard')
@section('content')
<!-- Basic Tables start -->

<div class="container">
        <h1>User Account Details</h1>

            <div class="card mb-3">
                <div class="card-header">Monnify</div>
                <div class="card-body">
                    <h5 class="card-title">Deposit</h5>
                    <p class="card-text">Account Number:</p>
                    <p class="card-text">Description:</p>
                </div>
            </div>
    </div>

@push('style')
<style>
    .list-group-item{
        background: transparent;
    }
</style>
@endpush
