@extends($activeTemplate.'layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">@lang('Transfer Preview')</h5>
                </div>
                <div class="card-body">
                    @php
                        $resolved = session('dpay_transfer.resolved', []);
                    @endphp

                    <div class="transfer-details">
                        <div class="detail-item mb-3">
                            <label class="detail-label">@lang('Recipient Name')</label>
                            <p class="detail-value">{{ $resolved['account_name'] ?? 'N/A' }}</p>
                        </div>

                        <div class="detail-item mb-3">
                            <label class="detail-label">@lang('Recipient Contact')</label>
                            <p class="detail-value">{{ session('dpay_transfer.recipient') }}</p>
                        </div>

                        <div class="detail-item mb-3">
                            <label class="detail-label">@lang('Bank')</label>
                            <p class="detail-value">{{ $resolved['bank_name'] ?? 'Internal Transfer' }}</p>
                        </div>

                        <hr>

                        <div class="detail-item mb-3">
                            <label class="detail-label">@lang('Transfer Amount')</label>
                            <p class="detail-value fs-5 fw-bold">{{ $general->cur_sym }}{{ showAmount(session('dpay_transfer.amount')) }}</p>
                        </div>

                        <div class="detail-item mb-3">
                            <label class="detail-label">@lang('Narration')</label>
                            <p class="detail-value">{{ session('dpay_transfer.narration') ?: '(None)' }}</p>
                        </div>

                        @if(session('dpay_transfer.narration'))
                            <div class="alert alert-info" role="alert">
                                <i class="la la-info-circle"></i>
                                @lang('Your transfer includes a description for the recipient.')
                            </div>
                        @endif
                    </div>

                    <form action="{{ route('user.dpay.confirm') }}" method="POST" class="mt-4">
                        @csrf
                        <button type="submit" class="btn btn-success btn-block mb-2">
                            @lang('Confirm Transfer')
                        </button>
                        <a href="{{ route('user.dpay.index') }}" class="btn btn-secondary btn-block">
                            @lang('Cancel')
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.detail-item {
    padding: 10px 0;
    border-bottom: 1px solid #eee;
}

.detail-item:last-child {
    border-bottom: none;
}

.detail-label {
    font-size: 0.85rem;
    color: #666;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.detail-value {
    font-size: 1rem;
    font-weight: 500;
    margin: 5px 0 0 0;
    color: #333;
}
</style>
@endsection
