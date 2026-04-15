<!-- Payment Authentication Modal -->
<div id="paymentAuthModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-slate-200 dark:border-white/10 max-w-md w-full p-6">
        <h3 class="text-lg font-semibold text-slate-950 dark:text-white mb-4">
            Verify Your Identity
        </h3>

        <form id="paymentAuthForm" method="post" class="space-y-4">
            @csrf

            <!-- Authentication Method Tabs -->
            <div class="flex gap-2 p-1 bg-slate-100 dark:bg-slate-800 rounded-lg mb-4">
                @if($user->isPinEnabled() || !$user->isTwoFactorEnabled())
                    <button type="button" class="auth-method-btn flex-1 py-2 px-3 rounded-md text-sm font-semibold transition" data-method="pin" {{  $user->isPinEnabled() || !$user->isTwoFactorEnabled() ? 'active' : '' }}>
                        PIN
                    </button>
                @endif
                
                @if($user->isTwoFactorEnabled())
                    <button type="button" class="auth-method-btn flex-1 py-2 px-3 rounded-md text-sm font-semibold transition" data-method="2fa" {{ !$user->isPinEnabled() ? 'active' : '' }}>
                        2FA
                    </button>
                @endif
                
                @if($user->isPasskeyEnabled())
                    <button type="button" class="auth-method-btn flex-1 py-2 px-3 rounded-md text-sm font-semibold transition" data-method="passkey">
                        Passkey
                    </button>
                @endif
            </div>

            <input type="hidden" name="auth_method" id="authMethod" value="pin">

            <!-- PIN verification -->
            <div id="pinMethod" class="space-y-3">
                <div>
                    <label for="paymentPin" class="block text-sm font-semibold text-slate-700 dark:text-zinc-300 mb-2">
                        Enter your 4-digit PIN
                    </label>
                    <input 
                        type="password"
                        id="paymentPin"
                        name="pin"
                        class="w-full rounded-lg border border-slate-300 dark:border-white/20 px-3 py-2 text-center text-lg tracking-widest focus:outline-none focus:ring-2 focus:ring-sky-500 dark:bg-zinc-800 dark:text-white"
                        placeholder="••••"
                        maxlength="4"
                        inputmode="numeric"
                        pattern="[0-9]{4}"
                    />
                </div>
            </div>

            <!-- 2FA verification -->
            <div id="2faMethod" class="space-y-3 hidden">
                <div>
                    <label for="twoFACode" class="block text-sm font-semibold text-slate-700 dark:text-zinc-300 mb-2">
                        Enter 6-digit code from Google Authenticator
                    </label>
                    <input 
                        type="text"
                        id="twoFACode"
                        name="two_fa_code"
                        class="w-full rounded-lg border border-slate-300 dark:border-white/20 px-3 py-2 text-center text-lg tracking-widest focus:outline-none focus:ring-2 focus:ring-sky-500 dark:bg-zinc-800 dark:text-white"
                        placeholder="000000"
                        maxlength="6"
                        inputmode="numeric"
                        pattern="[0-9]{6}"
                    />
                </div>
            </div>

            <!-- Passkey verification -->
            <div id="passkeyMethod" class="space-y-3 hidden">
                <p class="text-sm text-slate-600 dark:text-zinc-400">
                    Biometric authentication will be performed on your device
                </p>
                <input type="hidden" name="passkey_id" id="passkeyId">
            </div>

            <div class="flex gap-3">
                <button type="submit" class="flex-1 h-10 rounded-lg bg-sky-600 text-white font-semibold hover:bg-sky-700 transition dark:bg-sky-500 dark:hover:bg-sky-600">
                    Verify
                </button>
                <button type="button" id="closeAuthModal" class="flex-1 h-10 rounded-lg border border-slate-300 text-slate-700 font-semibold hover:bg-slate-50 transition dark:border-white/20 dark:text-zinc-300 dark:hover:bg-white/5">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.querySelector('#paymentAuthForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const method = document.getElementById('authMethod').value;
    const formData = new FormData(this);
    
    try {
        const response = await fetch('{{ route("payment.verify-auth") }}', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                method: method,
                pin: document.getElementById('paymentPin')?.value,
                two_fa_code: document.getElementById('twoFACode')?.value,
                passkey_id: document.getElementById('passkeyId')?.value,
                '_token': document.querySelector('[name="_token"]').value
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            document.getElementById('paymentAuthModal').classList.add('hidden');
            // Trigger payment submission
            document.dispatchEvent(new CustomEvent('authVerified', { detail: result }));
        } else {
            alert(result.message || 'Verification failed');
        }
    } catch (error) {
        console.error('Verification error:', error);
        alert('An error occurred during verification');
    }
});

// Authentication method switching
document.querySelectorAll('.auth-method-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const method = this.dataset.method;
        document.getElementById('authMethod').value = method;
        
        // Hide all methods
        document.getElementById('pinMethod').classList.add('hidden');
        document.getElementById('2faMethod').classList.add('hidden');
        document.getElementById('passkeyMethod').classList.add('hidden');
        
        // Show selected method
        switch(method) {
            case 'pin':
                document.getElementById('pinMethod').classList.remove('hidden');
                document.getElementById('paymentPin').focus();
                break;
            case '2fa':
                document.getElementById('2faMethod').classList.remove('hidden');
                document.getElementById('twoFACode').focus();
                break;
            case 'passkey':
                document.getElementById('passkeyMethod').classList.remove('hidden');
                break;
        }
        
        // Update button styles
        document.querySelectorAll('.auth-method-btn').forEach(b => {
            b.classList.remove('bg-sky-600', 'text-white', 'dark:bg-sky-500');
            b.classList.add('text-slate-700', 'dark:text-zinc-300');
        });
        this.classList.remove('text-slate-700', 'dark:text-zinc-300');
        this.classList.add('bg-sky-600', 'text-white', 'dark:bg-sky-500');
    });
});

// Close modal
document.getElementById('closeAuthModal').addEventListener('click', function() {
    document.getElementById('paymentAuthModal').classList.add('hidden');
});

// Prevent modal close on Escape by default - let it be controlled by JS
document.getElementById('paymentAuthModal').addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        e.preventDefault();
    }
});
</script>
