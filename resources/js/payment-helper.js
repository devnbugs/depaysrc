/**
 * Payment Confirmation Modal Helper
 * 
 * Usage:
 * PaymentHelper.showConfirmation({
 *     type: 1,
 *     phone: '08012345678',
 *     amount: 1000,
 *     bundleName: 'MTN Airtime',
 *     reference: 'TRX123456',
 *     paymentData: { network: 'mtn' }
 * });
 */

window.PaymentHelper = {
    /**
     * Show payment confirmation modal
     */
    showConfirmation(options) {
        const {
            type = 1,
            phone = '',
            amount = 0,
            bundleName = '',
            reference = '',
            paymentData = {},
        } = options;

        if (!phone || !amount || amount <= 0) {
            console.error('Invalid payment options', options);
            showAlert('error', 'Invalid payment details. Please check your form.');
            return false;
        }

        // Generate reference if not provided
        const finalReference = reference || this.generateReference();

        // Dispatch Livewire event to show modal
        if (typeof Livewire !== 'undefined') {
            // Clear previous modal state
            document.querySelectorAll('[wire\\:model]').forEach(el => {
                if (el.closest('[wire\\:id*="payment-confirmation"]')) {
                    // Reset modal state
                }
            });

            // Show modal by dispatching event
            document.dispatchEvent(new CustomEvent('show-payment-confirmation', {
                detail: {
                    type,
                    phone,
                    amount,
                    bundleName,
                    reference: finalReference,
                    paymentData,
                }
            }));

            return true;
        } else {
            console.error('Livewire not loaded');
            return false;
        }
    },

    /**
     * Generate unique reference number
     */
    generateReference() {
        const timestamp = Date.now();
        const random = Math.random().toString(36).substr(2, 9).toUpperCase();
        return `TRX${timestamp}${random}`;
    },

    /**
     * Validate payment form
     */
    validateForm(form) {
        if (!form || !(form instanceof HTMLFormElement)) {
            console.error('Invalid form element');
            return false;
        }

        if (!form.checkValidity()) {
            form.reportValidity();
            return false;
        }

        return true;
    },

    /**
     * Extract form data
     */
    getFormData(form) {
        const formData = new FormData(form);
        const data = Object.fromEntries(formData);

        return {
            type: parseInt(data.type || 1),
            phone: data.phone || '',
            amount: parseFloat(data.amount || 0),
            bundleName: data.bundle_name || '',
            reference: data.reference || this.generateReference(),
            paymentData: {
                network: data.network,
                plan: data.plan,
                smartcard: data.smartcard,
                meter: data.meter,
                disco: data.disco,
                ...Object.fromEntries(
                    Array.from(formData.entries())
                        .filter(([key]) => key.startsWith('payment_'))
                        .map(([key, val]) => [key.replace('payment_', ''), val])
                )
            }
        };
    },

    /**
     * Handle form submission with confirmation
     */
    handleFormSubmit(form, options = {}) {
        return (e) => {
            e.preventDefault();

            if (!this.validateForm(form)) {
                return false;
            }

            const formData = this.getFormData(form);
            const mergedOptions = { ...formData, ...options };

            // Store form reference for later submission
            window.__paymentForm = form;
            window.__paymentOptions = mergedOptions;

            // Show confirmation modal
            return this.showConfirmation(mergedOptions);
        };
    },

    /**
     * Handle payment confirmed event
     */
    onPaymentConfirmed(callback) {
        document.addEventListener('payment-confirmed', (e) => {
            const { success, reference, redirect } = e.detail || {};
            if (callback && typeof callback === 'function') {
                callback({ success, reference, redirect });
            }
        });
    },

    /**
     * Handle payment failed event
     */
    onPaymentFailed(callback) {
        document.addEventListener('payment-failed', (e) => {
            const { error, reference } = e.detail || {};
            if (callback && typeof callback === 'function') {
                callback({ error, reference });
            }
        });
    },

    /**
     * Helper: Format currency
     */
    formatCurrency(amount, symbol = '₦') {
        return `${symbol}${parseFloat(amount).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',')}`;
    },

    /**
     * Helper: Validate phone number
     */
    validatePhone(phone) {
        const phoneRegex = /^[0-9]{10,11}$/;
        return phoneRegex.test(phone.replace(/\D/g, ''));
    },

    /**
     * Helper: Get payment type label
     */
    getTypeLabel(type) {
        return {
            1: 'Airtime Purchase',
            2: 'Data Purchase',
            3: 'Cable TV Payment',
            4: 'Utility Payment',
        }[parseInt(type)] || 'Payment';
    },
};

/**
 * Example: Basic form integration
 * 
 * <form id="payment-form" onsubmit="PaymentHelper.handleFormSubmit(this)">
 *     <input type="hidden" name="type" value="1">
 *     <input type="text" name="phone" required>
 *     <input type="number" name="amount" required>
 *     <input type="text" name="network" required>
 *     <button type="submit">Complete Payment</button>
 * </form>
 * 
 * <script>
 *     PaymentHelper.onPaymentConfirmed(({ success, redirect }) => {
 *         if (success) {
 *             window.location.href = redirect || '/user/bills';
 *         }
 *     });
 * </script>
 */
