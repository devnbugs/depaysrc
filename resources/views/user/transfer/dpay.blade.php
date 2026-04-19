@extends($activeTemplate.'layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">@lang('Dpay Interbank Transfer')</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('user.dpay.submit') }}" method="POST">
                        @csrf
                        
                        <div class="form-group mb-3">
                            <label class="form-label">@lang('Recipient Type')</label>
                            <select name="type" class="form-control @error('type') is-invalid @enderror" required>
                                <option value="phone" selected>@lang('Phone Number (Default)')</option>
                                <option value="username">@lang('Username')</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label">@lang('Recipient')</label>
                            <input type="text" 
                                   name="recipient" 
                                   class="form-control @error('recipient') is-invalid @enderror" 
                                   placeholder="@lang('Enter phone number or username')" 
                                   required>
                            @error('recipient')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label">@lang('Amount')</label>
                            <div class="input-group">
                                <span class="input-group-text">{{ $general->cur_sym }}</span>
                                <input type="number" 
                                       name="amount" 
                                       class="form-control @error('amount') is-invalid @enderror" 
                                       step="0.01"
                                       placeholder="@lang('Enter amount')" 
                                       required>
                            </div>
                            @error('amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label">@lang('Narration')</label>
                            <textarea name="narration" 
                                      class="form-control @error('narration') is-invalid @enderror"
                                      rows="3"
                                      placeholder="@lang('Optional: Enter transfer description')"></textarea>
                            @error('narration')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-check mb-3">
                            <input type="checkbox" 
                                   name="save_beneficiary" 
                                   value="1" 
                                   class="form-check-input" 
                                   id="saveBeneficiary">
                            <label class="form-check-label" for="saveBeneficiary">
                                @lang('Save recipient for future transfers')
                            </label>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            @lang('Continue Transfer')
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">@lang('Saved Recipients')</h5>
                </div>
                <div class="card-body">
                    @if($beneficiaries->count() > 0)
                        <div class="list-group">
                            @foreach($beneficiaries as $beneficiary)
                                <button type="button" 
                                        class="list-group-item list-group-item-action"
                                        onclick="selectBeneficiary('{{ $beneficiary->account_number }}')">
                                    <strong>{{ $beneficiary->name }}</strong><br>
                                    <small class="text-muted">{{ $beneficiary->account_number }}</small>
                                </button>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted">@lang('No saved recipients yet.')</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Transfer History -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">@lang('Transfer History')</h5>
                </div>
                <div class="card-body table-responsive">
                    @if($transfers->count() > 0)
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>@lang('Date')</th>
                                    <th>@lang('Recipient')</th>
                                    <th>@lang('Amount')</th>
                                    <th>@lang('Status')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transfers as $transfer)
                                    <tr>
                                        <td>{{ showDateTime($transfer->created_at) }}</td>
                                        <td>
                                            @php
                                                $details = json_decode($transfer->details, true);
                                            @endphp
                                            {{ $details['account_name'] ?? 'N/A' }}
                                        </td>
                                        <td>{{ $general->cur_sym }}{{ showAmount($transfer->amount) }}</td>
                                        <td>
                                            @if($transfer->status == 1)
                                                <span class="badge bg-success">@lang('Completed')</span>
                                            @elseif($transfer->status == 0)
                                                <span class="badge bg-warning">@lang('Pending')</span>
                                            @else
                                                <span class="badge bg-danger">@lang('Failed')</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{ $transfers->links() }}
                    @else
                        <p class="text-muted text-center py-4">@lang('No transfer history')</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function selectBeneficiary(accountNumber) {
    document.querySelector('input[name="recipient"]').value = accountNumber;
    document.querySelector('input[name="recipient"]').focus();
}
</script>
@endsection
