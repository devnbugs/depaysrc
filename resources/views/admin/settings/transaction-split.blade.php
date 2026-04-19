@extends('admin.layouts.app')

@section('panel')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">@lang('Transaction Split Settings')</h5>
            </div>
            <div class="card-body">
                <p class="text-muted mb-4">
                    @lang('Configure how large transfers are automatically split into smaller chunks for compliance and operational purposes.')
                </p>

                <form action="{{ route('admin.settings.transaction-split.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group mb-4">
                        <label class="form-label">@lang('Enable Transaction Split')</label>
                        <div class="form-check form-switch">
                            <input type="hidden" name="transaction_split_enabled" value="0">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   name="transaction_split_enabled" 
                                   value="1" 
                                   {{ $settings['enabled'] ? 'checked' : '' }} 
                                   id="enableSplit">
                            <label class="form-check-label" for="enableSplit">
                                @lang('Enable automatic transaction splitting for large transfers')
                            </label>
                        </div>
                        <small class="text-muted d-block mt-2">
                            @lang('When enabled, transfers exceeding the threshold amount will be split into multiple smaller transfers.')
                        </small>
                    </div>

                    <div class="form-group mb-4">
                        <label class="form-label" for="threshold">@lang('Split Threshold Amount')</label>
                        <div class="input-group">
                            <span class="input-group-text">{{ $general->cur_sym }}</span>
                            <input type="number" 
                                   id="threshold"
                                   name="transaction_split_threshold" 
                                   class="form-control @error('transaction_split_threshold') is-invalid @enderror" 
                                   value="{{ $settings['threshold'] }}"
                                   step="0.01"
                                   min="100"
                                   max="9999999"
                                   required>
                        </div>
                        <small class="text-muted d-block mt-2">
                            @lang('Amounts exceeding this value will be split into chunks of this size.')
                        </small>
                        @error('transaction_split_threshold')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-4">
                        <label class="form-label" for="description">@lang('Description')</label>
                        <textarea id="description"
                                  name="transaction_split_description" 
                                  class="form-control @error('transaction_split_description') is-invalid @enderror"
                                  rows="3"
                                  placeholder="@lang('Describe what the transaction split feature does')">{{ $settings['description'] }}</textarea>
                        @error('transaction_split_description')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="la la-save"></i> @lang('Save Settings')
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">@lang('Split Examples')</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h6>@lang('Example 1')</h6>
                    <p class="mb-2"><strong>@lang('Amount'): </strong>{{ $general->cur_sym }}15,000</p>
                    <p class="mb-2"><strong>@lang('Threshold'): </strong>{{ $general->cur_sym }}{{ number_format($settings['threshold'], 2) }}</p>
                    <p class="text-muted">@lang('Will be split into'): </p>
                    <ul class="list-unstyled">
                        <li>{{ $general->cur_sym }}{{ number_format($settings['threshold'], 2) }}</li>
                        <li>{{ $general->cur_sym }}{{ number_format(15000 - $settings['threshold'], 2) }}</li>
                    </ul>
                </div>

                <hr>

                <div class="mb-3">
                    <h6>@lang('Example 2')</h6>
                    <p class="mb-2"><strong>@lang('Amount'): </strong>{{ $general->cur_sym }}35,000</p>
                    <p class="mb-2"><strong>@lang('Threshold'): </strong>{{ $general->cur_sym }}{{ number_format($settings['threshold'], 2) }}</p>
                    <p class="text-muted">@lang('Will be split into'):</p>
                    <ul class="list-unstyled">
                        <li>{{ $general->cur_sym }}{{ number_format($settings['threshold'], 2) }}</li>
                        <li>{{ $general->cur_sym }}{{ number_format($settings['threshold'], 2) }}</li>
                        <li>{{ $general->cur_sym }}{{ number_format(35000 - ($settings['threshold'] * 2), 2) }}</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">@lang('Test Split')</h5>
            </div>
            <div class="card-body">
                <div class="form-group mb-3">
                    <label class="form-label">@lang('Test Amount')</label>
                    <div class="input-group">
                        <span class="input-group-text">{{ $general->cur_sym }}</span>
                        <input type="number" 
                               id="testAmount"
                               class="form-control" 
                               value="25000"
                               step="0.01"
                               min="1">
                    </div>
                </div>
                <button type="button" class="btn btn-warning btn-sm w-100" onclick="testSplit()">
                    <i class="la la-calculator"></i> @lang('Calculate Split')
                </button>
                <div id="testResult" class="mt-3"></div>
            </div>
        </div>
    </div>
</div>

<script>
function testSplit() {
    const amount = parseFloat(document.getElementById('testAmount').value);
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    fetch('{{ route("admin.settings.transaction-split.test") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
        },
        body: JSON.stringify({ amount: amount }),
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const splitData = data.data;
            let html = '<div class="alert alert-info"><strong>Split Result:</strong><br>';
            
            if (splitData.requires_split) {
                html += `<p>Amount will be split into <strong>${splitData.chunk_count}</strong> transfers:</p>`;
                html += '<ul class="mb-0">';
                splitData.chunks.forEach(chunk => {
                    html += `<li>{{ $general->cur_sym }}${chunk.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</li>`;
                });
                html += '</ul>';
            } else {
                html += `<p>No split required - Amount is below threshold.</p>`;
            }
            html += '</div>';
            document.getElementById('testResult').innerHTML = html;
        }
    });
}
</script>
@endsection
