@extends('admin.layouts.app')

@section('panel')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bolder text-dark">@lang('User Details') - {{ $user->fullname }}</h2>
                <div>
                    <a href="javascript:void(0)" data-bs-toggle="modal" href="#addvestModal" class="btn btn-sm btn-info">
                        <i class="fas fa-plus"></i> @lang('Add Investment')
                    </a>
                    <a href="javascript:void(0)" data-bs-toggle="modal" href="#addSubModal" class="btn btn-sm btn-success">
                        <i class="fas fa-plus-minus"></i> @lang('Credit/Debit')
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Sidebar - User Info & Actions -->
        <div class="col-lg-3 col-md-4 mb-4">
            <!-- User Info Card -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <h6 class="text-muted text-uppercase fw-bold mb-3">@lang('User Information')</h6>
                    
                    <div class="mb-3">
                        <small class="text-muted d-block">@lang('Username')</small>
                        <span class="fw-bold">{{ $user->username }}</span>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">@lang('Status')</small>
                        @if($user->status == 1)
                            <span class="badge bg-success rounded-pill">@lang('Active')</span>
                        @else
                            <span class="badge bg-danger rounded-pill">@lang('Banned')</span>
                        @endif
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">@lang('Balance')</small>
                        <span class="fw-bold text-primary">{{ $general->cur_sym }}{{ showAmount($user->balance) }}</span>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">@lang('Investment Return')</small>
                        <span class="fw-bold text-success">{{ $general->cur_sym }}{{ showAmount($user->invest_return) }}</span>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">@lang('Compounding')</small>
                        <span class="fw-bold text-info">{{ $general->cur_sym }}{{ showAmount($user->compound) }}</span>
                    </div>

                    <div class="mb-0">
                        <small class="text-muted d-block">@lang('Referral Bonus')</small>
                        <span class="fw-bold text-warning">{{ $general->cur_sym }}{{ showAmount($user->ref_bonus) }}</span>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted text-uppercase fw-bold mb-3">@lang('Actions')</h6>
                    
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.users.login.history.single', $user->id) }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-history"></i> @lang('Login Logs')
                        </a>
                        <a href="{{ route('admin.users.email.single', $user->id) }}" class="btn btn-outline-info btn-sm">
                            <i class="fas fa-envelope"></i> @lang('Send Email')
                        </a>
                        <a href="{{ route('admin.users.login', $user->id) }}" target="_blank" class="btn btn-outline-dark btn-sm">
                            <i class="fas fa-sign-in-alt"></i> @lang('Login as User')
                        </a>
                        <a href="{{ route('admin.users.email.log', $user->id) }}" class="btn btn-outline-warning btn-sm">
                            <i class="fas fa-envelope-open"></i> @lang('Email Log')
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-lg-9 col-md-8 mb-4">
            <!-- Stats Cards -->
            <div class="row mb-4">
                <div class="col-md-6 col-lg-3 mb-3">
                    <div class="card border-0 shadow-sm bg-light">
                        <div class="card-body text-center">
                            <i class="fas fa-wallet text-primary fa-2x mb-2"></i>
                            <h6 class="text-muted small">@lang('Total Deposit')</h6>
                            <h4 class="fw-bold text-primary">{{ $general->cur_sym }}{{ showAmount($totalDeposit) }}</h4>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3 mb-3">
                    <div class="card border-0 shadow-sm bg-light">
                        <div class="card-body text-center">
                            <i class="fas fa-money-bill-wave text-success fa-2x mb-2"></i>
                            <h6 class="text-muted small">@lang('Total Withdrawal')</h6>
                            <h4 class="fw-bold text-success">{{ $general->cur_sym }}{{ showAmount($totalWithdraw) }}</h4>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3 mb-3">
                    <div class="card border-0 shadow-sm bg-light">
                        <div class="card-body text-center">
                            <i class="fas fa-chart-line text-info fa-2x mb-2"></i>
                            <h6 class="text-muted small">@lang('Total Investment')</h6>
                            <h4 class="fw-bold text-info">{{ $general->cur_sym }}{{ showAmount($totalInvest) }}</h4>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3 mb-3">
                    <div class="card border-0 shadow-sm bg-light">
                        <div class="card-body text-center">
                            <i class="fas fa-exchange-alt text-warning fa-2x mb-2"></i>
                            <h6 class="text-muted small">@lang('Total Transaction')</h6>
                            <h4 class="fw-bold text-warning">{{ $totalTransaction }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Details Form -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light border-bottom">
                    <h5 class="fw-bold mb-0">@lang('Edit User Information')</h5>
                </div>

                <div class="card-body">
                    <form action="{{ route('admin.users.update', [$secureId]) }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Name Fields -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">@lang('First Name') <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-lg @error('firstname') is-invalid @enderror" name="firstname" value="{{ $user->firstname }}" required>
                                @error('firstname')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">@lang('Last Name') <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-lg @error('lastname') is-invalid @enderror" name="lastname" value="{{ $user->lastname }}" required>
                                @error('lastname')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Contact Fields -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">@lang('Email') <span class="text-danger">*</span></label>
                                <input type="email" class="form-control form-control-lg @error('email') is-invalid @enderror" name="email" value="{{ $user->email }}" required>
                                @error('email')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">@lang('Mobile Number') <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control form-control-lg @error('mobile') is-invalid @enderror" name="mobile" value="{{ $user->mobile }}" required>
                                @error('mobile')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Address -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <label class="form-label fw-bold">@lang('Address')</label>
                                <input type="text" class="form-control form-control-lg" name="address" value="{{ @$user->address->address }}">
                            </div>
                        </div>

                        <!-- City, State, Zip, Country -->
                        <div class="row mb-4">
                            <div class="col-md-6 col-lg-3">
                                <label class="form-label fw-bold">@lang('City')</label>
                                <input type="text" class="form-control form-control-lg" name="city" value="{{ @$user->address->city }}">
                            </div>

                            <div class="col-md-6 col-lg-3">
                                <label class="form-label fw-bold">@lang('State')</label>
                                <input type="text" class="form-control form-control-lg" name="state" value="{{ @$user->address->state }}">
                            </div>

                            <div class="col-md-6 col-lg-3">
                                <label class="form-label fw-bold">@lang('Zip/Postal')</label>
                                <input type="text" class="form-control form-control-lg" name="zip" value="{{ @$user->address->zip }}">
                            </div>

                            <div class="col-md-6 col-lg-3">
                                <label class="form-label fw-bold">@lang('Country') <span class="text-danger">*</span></label>
                                <select name="country" class="form-control form-control-lg @error('country') is-invalid @enderror" required>
                                    @foreach($countries as $key => $country)
                                        <option value="{{ $key }}" @if(@$user->address->country == $country->country) selected @endif>
                                            {{ $country->country }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('country')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Status Toggles -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="status" id="status" @if($user->status) checked @endif>
                                    <label class="form-check-label fw-bold" for="status">
                                        @lang('Account Status') - <span class="text-muted small">@if($user->status)@lang('Active')@else@lang('Banned')@endif</span>
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="ev" id="ev" @if($user->ev) checked @endif>
                                    <label class="form-check-label fw-bold" for="ev">
                                        @lang('Email Verification')
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-6 mt-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="sv" id="sv" @if($user->sv) checked @endif>
                                    <label class="form-check-label fw-bold" for="sv">
                                        @lang('SMS Verification')
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-6 mt-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="ts" id="ts" @if($user->ts) checked @endif>
                                    <label class="form-check-label fw-bold" for="ts">
                                        @lang('2FA Status')
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-6 mt-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="tv" id="tv" @if($user->tv) checked @endif>
                                    <label class="form-check-label fw-bold" for="tv">
                                        @lang('2FA Verification')
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="row mt-5">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary btn-lg fw-bold">
                                    <i class="fas fa-save"></i> @lang('Save Changes')
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add/Subtract Balance Modal -->
<div id="addSubModal" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-bottom bg-light">
                <h5 class="modal-title fw-bold">@lang('Add / Subtract Balance')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form action="{{ route('admin.users.add.sub.balance', $user->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input type="checkbox" class="form-check-input" name="act" id="actToggle" checked>
                            <label class="form-check-label fw-bold" for="actToggle">
                                <span id="actLabel">@lang('Credit')</span>
                            </label>
                        </div>
                        <small class="text-muted d-block mt-2">@lang('Check to credit, uncheck to debit')</small>
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-bold">@lang('Amount') <span class="text-danger">*</span></label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text">{{ $general->cur_sym }}</span>
                            <input type="number" name="amount" class="form-control form-control-lg" placeholder="0.00" step="0.01" required>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">@lang('Close')</button>
                    <button type="submit" class="btn btn-success">@lang('Submit')</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Investment Modal -->
<div id="addvestModal" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-bottom bg-light">
                <h5 class="modal-title fw-bold">@lang('Add New Investment')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form action="{{ route('admin.users.add.plan', $user->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">@lang('Plan') <span class="text-danger">*</span></label>
                        @php $plans = App\Models\Plan::get(); @endphp
                        <select name="plan" class="form-select form-select-lg" required>
                            <option value="">@lang('Select a plan')</option>
                            @foreach($plans as $plan)
                                <option value="{{ $plan->id }}">{{ $plan->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-bold">@lang('Amount') <span class="text-danger">*</span></label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text">{{ $general->cur_sym }}</span>
                            <input type="number" name="amount" class="form-control form-control-lg" placeholder="0.00" step="0.01" required>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">@lang('Close')</button>
                    <button type="submit" class="btn btn-success">@lang('Submit')</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('actToggle').addEventListener('change', function() {
    const label = document.getElementById('actLabel');
    label.textContent = this.checked ? '@lang("Credit")' : '@lang("Debit")';
});
</script>
@endsection
