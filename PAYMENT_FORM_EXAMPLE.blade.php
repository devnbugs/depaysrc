<!-- 
    EXAMPLE: Payment Form with PIN Confirmation Modal
    
    This example shows how to integrate the PaymentConfirmationModal Livewire component
    into a payment form (e.g., Data Purchase, Airtime, etc.)
    
    Key Changes:
    1. Remove inline form submission (action="" removed)
    2. Add onclick handler to submit button
    3. Include PaymentConfirmationModal component
    4. Include payment-helper.js
    5. Add event listeners for payment confirmation
-->

@extends($activeTemplate.'layouts.dashboard')

@section('content')
<section class="space-y-6">
    <div class="hero-surface p-6 sm:p-8">
        <h1 class="text-3xl font-semibold text-slate-950 dark:text-white">Buy Data</h1>
    </div>

    <div class="grid gap-6 lg:grid-cols-[1.2fr_0.8fr]">
        <div class="panel-card p-6">
            <!-- Payment Form (No action, no POST submit) -->
            <form id="data-purchase-form" class="space-y-6" novalidate>
                @csrf

                <!-- Wallet Balance -->
                <div class="rounded-3xl border border-emerald-200 bg-emerald-50/90 p-4">
                    <p class="text-xs font-semibold text-emerald-700">Wallet Balance</p>
                    <p class="mt-2 text-3xl font-semibold">{{ $general->cur_sym }}{{ showAmount($user->balance) }}</p>
                </div>

                <!-- Hidden Fields for Payment Type -->
                <input type="hidden" name="type" value="2"> <!-- 2 = Data -->
                <input type="hidden" name="bundle_name" id="bundle-name-input" value="">

                <!-- Network Selection -->
                <div class="space-y-2">
                    <label for="network" class="block text-sm font-medium text-slate-700">
                        Network <span class="text-red-500">*</span>
                    </label>
                    <select name="network" id="network" required class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                        <option value="">Choose a network</option>
                        @foreach($dataCatalog as $item)
                            <option value="{{ $item['network_code'] }}">{{ $item['network'] }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Plan Selection -->
                <div class="space-y-2">
                    <label for="plan" class="block text-sm font-medium text-slate-700">
                        Plan <span class="text-red-500">*</span>
                    </label>
                    <select name="plan" id="plan" required class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                        <option value="">Select a plan</option>
                    </select>
                </div>

                <!-- Phone Number -->
                <div class="space-y-2">
                    <label for="phone" class="block text-sm font-medium text-slate-700">
                        Phone Number <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="tel" 
                        name="phone" 
                        id="phone" 
                        placeholder="08012345678" 
                        maxlength="11" 
                        required
                        pattern="[0-9]{10,11}"
                        class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm"
                    >
                </div>

                <!-- Hidden Amount (populated from selected plan) -->
                <input type="hidden" name="amount" id="amount-input" value="">

                <!-- Submit Button (Triggers Modal, Not Form Submission) -->
                <button 
                    type="button"
                    id="submit-btn"
                    class="app-submit-button h-12 rounded-full bg-slate-950 text-white font-semibold hover:bg-slate-800 w-full"
                    onclick="handleDataPurchaseSubmit()"
                >
                    Complete Purchase
                </button>
            </form>
        </div>

        <!-- Summary Panel -->
        <div class="space-y-4">
            <div class="panel-card p-6">
                <p class="section-kicker">Plan Summary</p>
                <div id="plan-summary" class="mt-4 space-y-2 text-sm">
                    <p class="text-slate-500">Select a plan to see details</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===== PAYMENT CONFIRMATION MODAL ===== -->
@livewire('payment-confirmation-modal')

@push('script')
<script src="{{ asset('js/payment-helper.js') }}"></script>

<script>
    // Data catalog from view
    const dataCatalog = @json($dataCatalog);
    const generalSettings = @json($general);

    // Form elements
    const form = document.getElementById('data-purchase-form');
    const networkSelect = document.getElementById('network');
    const planSelect = document.getElementById('plan');
    const phoneInput = document.getElementById('phone');
    const amountInput = document.getElementById('amount-input');
    const bundleNameInput = document.getElementById('bundle-name-input');
    const planSummary = document.getElementById('plan-summary');

    // Update plans when network changes
    networkSelect.addEventListener('change', () => {
        planSelect.innerHTML = '<option value="">Select a plan</option>';
        planSummary.innerHTML = '<p class="text-slate-500">Select a plan to see details</p>';
        
        const network = dataCatalog.find(n => n.network_code === networkSelect.value);
        if (!network) return;

        network.validities.forEach(validity => {
            const optgroup = document.createElement('optgroup');
            optgroup.label = validity.label;
            
            validity.plans.forEach(plan => {
                const option = document.createElement('option');
                option.value = plan.id;
                option.dataset.name = plan.name;
                option.dataset.amount = plan.amount;
                option.textContent = `${plan.name} - ${generalSettings.cur_sym}${parseFloat(plan.amount).toFixed(2)}`;
                optgroup.appendChild(option);
            });
            
            planSelect.appendChild(optgroup);
        });
    });

    // Update summary when plan changes
    planSelect.addEventListener('change', () => {
        const selected = planSelect.options[planSelect.selectedIndex];
        const planName = selected.dataset.name || '';
        const amount = parseFloat(selected.dataset.amount || 0);

        amountInput.value = amount;
        bundleNameInput.value = planName;

        if (planName && amount > 0) {
            planSummary.innerHTML = `
                <div class="space-y-2">
                    <p><strong>Plan:</strong> ${planName}</p>
                    <p><strong>Cost:</strong> ${generalSettings.cur_sym}${amount.toFixed(2)}</p>
                    <p class="text-xs text-slate-500 pt-2">This amount will be deducted from your wallet upon confirmation.</p>
                </div>
            `;
        }
    });

    /**
     * Handle form submission - Show payment confirmation modal
     */
    function handleDataPurchaseSubmit() {
        // Validate form
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        // Get form data
        const formData = PaymentHelper.getFormData(form);

        // Validate phone
        if (!PaymentHelper.validatePhone(formData.phone)) {
            showAlert('error', 'Please enter a valid phone number.');
            phoneInput.focus();
            return;
        }

        // Show confirmation modal
        PaymentHelper.showConfirmation(formData);
    }

    /**
     * Handle payment confirmed - Redirect to bills page
     */
    PaymentHelper.onPaymentConfirmed(({ success, redirect }) => {
        if (success) {
            showAlert('success', 'Payment processing started. Redirecting...');
            setTimeout(() => {
                window.location.href = redirect || '/user/bills';
            }, 1500);
        }
    });

    /**
     * Handle payment failed
     */
    PaymentHelper.onPaymentFailed(({ error }) => {
        showAlert('error', error || 'Payment failed. Please try again.');
    });

    /**
     * Helper: Show alert
     */
    function showAlert(type, message) {
        // Using your existing alert system
        const alertClass = type === 'error' ? 'text-red-600' : 'text-green-600';
        const alertBox = document.createElement('div');
        alertBox.className = `fixed top-4 right-4 p-4 rounded-lg ${alertClass} bg-opacity-20 z-50`;
        alertBox.textContent = message;
        document.body.appendChild(alertBox);
        
        setTimeout(() => {
            alertBox.remove();
        }, 3000);
    }

    // Allow Enter key to submit
    form.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            handleDataPurchaseSubmit();
        }
    });
</script>

@endpush
@endsection

<!--

    INTEGRATION CHECKLIST:

    ✅ 1. Remove form action="" and method="POST"
    ✅ 2. Change submit button type="button" with onclick handler
    ✅ 3. Add hidden input for payment type (type="2")
    ✅ 4. Store selected amount and bundle name in hidden inputs
    ✅ 5. Include @livewire('payment-confirmation-modal')
    ✅ 6. Include payment-helper.js script
    ✅ 7. Add event listeners for payment confirmation
    ✅ 8. Implement form validation
    ✅ 9. Handle success/failure responses

    TODO FOR OTHER BILL TYPES:
    
    For Airtime:
    - Change type="1"
    - Show network selection
    - Show amount input
    
    For Cable TV:
    - Change type="3"
    - Add smartcard input
    - Show bundle selection
    
    For Utility:
    - Change type="4"
    - Add meter/account input
    - Show service selection

-->
