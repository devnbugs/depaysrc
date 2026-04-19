@extends('layouts.app')

@section('title', 'Complete Your Profile - Onboarding')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Complete Your Profile</h1>
            <p class="text-gray-600">Help us verify your identity to unlock all features</p>
        </div>

        <!-- Progress Bar -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <div class="mb-6">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700">Progress</span>
                    <span class="text-sm font-bold text-indigo-600">{{ $progress['progress_percentage'] }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-indigo-600 h-2 rounded-full transition-all duration-300"
                         style="width: {{ $progress['progress_percentage'] }}%"></div>
                </div>
            </div>

            <!-- Step Indicators -->
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                @php
                    $steps = [
                        'personal_info' => 'Personal Info',
                        'identity_verification' => 'Identity',
                        'liveness_check' => 'Liveness',
                        'completed' => 'Complete'
                    ];
                @endphp

                @foreach($steps as $stepKey => $stepLabel)
                    @php
                        $isCompleted = in_array($stepKey, $progress['completed_steps']);
                        $isCurrent = $currentStep === $stepKey;
                    @endphp
                    <div class="text-center">
                        <div class="flex justify-center mb-2">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center font-semibold text-sm
                                @if($isCompleted)
                                    bg-green-500 text-white
                                @elseif($isCurrent)
                                    bg-indigo-600 text-white
                                @else
                                    bg-gray-300 text-gray-600
                                @endif">
                                @if($isCompleted)
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                @else
                                    {{ $loop->iteration }}
                                @endif
                            </div>
                        </div>
                        <p class="text-xs font-medium text-gray-700">{{ $stepLabel }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Main Content -->
        <div class="bg-white rounded-lg shadow-md p-8">
            @if($progress['is_complete'])
                <!-- Completion Message -->
                <div class="text-center py-8">
                    <div class="mb-6">
                        <svg class="w-16 h-16 mx-auto text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Profile Complete!</h2>
                    <p class="text-gray-600 mb-6">Your verification level: <span class="font-bold text-indigo-600">{{ $limits['level_name'] }}</span></p>

                    <!-- Limits Summary -->
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div class="bg-blue-50 p-4 rounded">
                            <p class="text-sm text-gray-600">Transfer Limit</p>
                            <p class="text-lg font-bold text-gray-900">₦{{ number_format($limits['transfer_limit']) }}</p>
                        </div>
                        <div class="bg-blue-50 p-4 rounded">
                            <p class="text-sm text-gray-600">Accounts</p>
                            <p class="text-lg font-bold text-gray-900">{{ $limits['account_creation_limit'] }}</p>
                        </div>
                    </div>

                    <a href="{{ route('dashboard') }}" class="inline-block bg-indigo-600 text-white px-6 py-2 rounded-lg font-medium hover:bg-indigo-700 transition">
                        Go to Dashboard
                    </a>
                </div>
            @elseif($currentStep === 'personal_info')
                <!-- Personal Info Form -->
                <div id="personal-info-form">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Personal Information</h2>
                    <form id="personalInfoForm" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">First Name *</label>
                                <input type="text" name="firstname" value="{{ $user->firstname }}" required
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Last Name *</label>
                                <input type="text" name="lastname" value="{{ $user->lastname }}" required
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number *</label>
                                <input type="tel" name="mobile" value="{{ $user->mobile }}" required
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">WhatsApp Number *</label>
                                <input type="tel" name="whatsapp_phone" value="{{ $user->whatsapp_phone }}" required
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Street Address *</label>
                            <input type="text" name="address[address]" 
                                   value="{{ data_get($user->address, 'address', '') }}" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">State *</label>
                                <input type="text" name="address[state]" 
                                       value="{{ data_get($user->address, 'state', '') }}" required
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">City *</label>
                                <input type="text" name="address[city]" 
                                       value="{{ data_get($user->address, 'city', '') }}" required
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">ZIP Code</label>
                                <input type="text" name="address[zip]" 
                                       value="{{ data_get($user->address, 'zip', '') }}"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Country *</label>
                                <input type="text" name="address[country]" 
                                       value="{{ data_get($user->address, 'country', 'Nigeria') }}" required
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            </div>
                        </div>

                        <div class="pt-4">
                            <button type="submit" class="w-full bg-indigo-600 text-white px-6 py-2 rounded-lg font-medium hover:bg-indigo-700 transition">
                                Continue to Identity Verification
                            </button>
                        </div>
                    </form>
                </div>

                <script>
                document.getElementById('personalInfoForm').addEventListener('submit', function(e) {
                    e.preventDefault();
                    const formData = new FormData(this);
                    
                    fetch("{{ route('user.onboarding.submit-personal-info') }}", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('[name="_token"]').value,
                        },
                        body: formData
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            window.location.href = data.redirect_url;
                        } else {
                            alert(data.message);
                        }
                    })
                    .catch(e => alert('Error: ' + e.message));
                });
                </script>
            @elseif($currentStep === 'identity_verification')
                <div id="identity-form">
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Identity Verification</h2>
                    <p class="text-gray-600 mb-6">Verify your identity using your BVN (Bank Verification Number) or NIN (National Identification Number)</p>

                    <form id="identityVerificationForm" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Identification Type *</label>
                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input type="radio" name="identification_type" value="bvn" checked
                                           class="mr-3 w-4 h-4">
                                    <span class="text-gray-700">BVN (Bank Verification Number)</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="identification_type" value="nin"
                                           class="mr-3 w-4 h-4">
                                    <span class="text-gray-700">NIN (National Identification Number)</span>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Identification Number *</label>
                            <input type="text" name="identification_number" required placeholder="Enter your BVN or NIN"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        </div>

                        <div class="bg-blue-50 p-4 rounded-lg mb-4">
                            <p class="text-sm text-gray-700">
                                <strong>Note:</strong> Your identity information will be verified through Kora Identity. We'll auto-fill your name and address after verification.
                            </p>
                        </div>

                        <div class="pt-4 flex gap-4">
                            <button type="button" class="flex-1 border border-gray-300 text-gray-700 px-6 py-2 rounded-lg font-medium hover:bg-gray-50 transition"
                                    onclick="window.history.back()">
                                Back
                            </button>
                            <button type="submit" class="flex-1 bg-indigo-600 text-white px-6 py-2 rounded-lg font-medium hover:bg-indigo-700 transition">
                                Verify Identity
                            </button>
                        </div>
                    </form>
                </div>

                <script>
                document.getElementById('identityVerificationForm').addEventListener('submit', function(e) {
                    e.preventDefault();
                    const formData = new FormData(this);
                    
                    fetch("{{ route('user.onboarding.submit-identity-verification') }}", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('[name="_token"]').value,
                        },
                        body: formData
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            window.location.href = data.redirect_url;
                        } else {
                            alert(data.message || 'Identity verification failed');
                        }
                    })
                    .catch(e => alert('Error: ' + e.message));
                });
                </script>
            @elseif($currentStep === 'liveness_check')
                <div id="liveness-form">
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Liveness Verification</h2>
                    <p class="text-gray-600 mb-6">Complete a quick liveness check to verify you're a real person</p>

                    <div class="bg-amber-50 border border-amber-200 p-4 rounded-lg mb-6">
                        <h3 class="font-semibold text-amber-900 mb-2">Deposit Requirement</h3>
                        <p class="text-amber-800 mb-3">To unlock full transfer features (Level 3), you need to deposit ₦{{ number_format($depositRequirement['required']) }}</p>
                        <div class="bg-white rounded p-3">
                            <div class="flex justify-between mb-2">
                                <span class="text-sm text-gray-700">Deposited:</span>
                                <span class="font-bold text-gray-900">₦{{ number_format($depositRequirement['deposited']) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-700">Remaining:</span>
                                <span class="font-bold text-gray-900">₦{{ number_format($depositRequirement['remaining']) }}</span>
                            </div>
                        </div>
                    </div>

                    <button id="initiateLivenessBtn" class="w-full bg-indigo-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-indigo-700 transition mb-4">
                        Start Liveness Check
                    </button>

                    <div id="livenessStatus" style="display: none;" class="bg-blue-50 p-4 rounded-lg">
                        <p class="text-blue-900">Liveness check in progress... Please wait.</p>
                    </div>
                </div>

                <script>
                document.getElementById('initiateLivenessBtn').addEventListener('click', function() {
                    this.disabled = true;
                    this.textContent = 'Initiating...';

                    fetch("{{ route('user.onboarding.initiate-liveness') }}", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('[name="_token"]').value,
                            'Content-Type': 'application/json',
                        }
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            if (data.redirect_url) {
                                // Redirect to Kora's liveness check
                                window.location.href = data.redirect_url;
                            } else {
                                // Poll for completion
                                document.getElementById('initiateLivenessBtn').style.display = 'none';
                                document.getElementById('livenessStatus').style.display = 'block';
                                checkLivenessCompletion();
                            }
                        } else {
                            alert(data.message || 'Failed to initiate liveness check');
                            this.disabled = false;
                            this.textContent = 'Start Liveness Check';
                        }
                    })
                    .catch(e => {
                        alert('Error: ' + e.message);
                        this.disabled = false;
                        this.textContent = 'Start Liveness Check';
                    });
                });

                function checkLivenessCompletion() {
                    setTimeout(() => {
                        fetch("{{ route('user.onboarding.check-liveness-status') }}")
                            .then(r => r.json())
                            .then(data => {
                                if (data.success) {
                                    window.location.href = data.redirect_url;
                                } else {
                                    checkLivenessCompletion();
                                }
                            });
                    }, 3000);
                }
                </script>
            @endif
        </div>

        <!-- Support -->
        <div class="text-center mt-8">
            <p class="text-gray-600 text-sm">
                Need help? <a href="{{ route('support') }}" class="text-indigo-600 hover:underline">Contact support</a>
            </p>
        </div>
    </div>
</div>
@endsection
