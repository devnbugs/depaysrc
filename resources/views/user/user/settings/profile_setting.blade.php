@extends($activeTemplate.'layouts.dashboard')
@section('content')

<section class="space-y-6">
    <div class="hero-surface p-6 sm:p-8">
        <div>
            <div class="flex flex-wrap gap-2">
                <span class="section-kicker">Settings</span>
                <span class="section-kicker">Profile</span>
            </div>

            <div class="space-y-4">
                <h2 class="max-w-2xl text-3xl font-semibold tracking-tight text-slate-950 dark:text-white sm:text-4xl">
                    Edit your profile
                </h2>
                <p class="max-w-2xl section-copy">
                    Keep your personal information up to date and secure. Update your profile details, address, and contact information.
                </p>
            </div>
        </div>
    </div>

    <section class="panel-card p-6">
        @if($user->hasLockedIdentity())
            <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-900 dark:border-emerald-800/40 dark:bg-emerald-900/20 dark:text-emerald-100">
                Your name and address are now locked to verified {{ strtoupper($user->identity_source ?: 'identity') }} data. Only external phone verification remains available here.
            </div>
        @endif

        <form action="{{ route('user.profile.setting') }}" method="post" enctype="multipart/form-data" class="space-y-6">
            @csrf
            
            <!-- Profile Picture Section -->
            <div class="rounded-3xl border border-slate-200 bg-slate-50/50 p-6 dark:border-white/10 dark:bg-white/5">
                <p class="text-sm font-semibold text-slate-700 dark:text-zinc-300 mb-4">Profile Photo</p>
                
                <div class="flex items-start gap-6">
                    <div class="relative shrink-0">
                        <img src="{{ getImage(imagePath()['profile']['user']['path'].'/'. $user->image, imagePath()['profile']['user']['size']) }}" 
                            id="account-upload-img"
                            alt="Profile photo"
                            class="h-24 w-24 rounded-2xl border-2 border-slate-200 object-cover dark:border-white/10" />
                        <label for="account-upload" class="absolute bottom-0 right-0 inline-flex h-8 w-8 items-center justify-center rounded-full bg-sky-600 text-white cursor-pointer hover:bg-sky-700 transition">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 5v14M5 12h14"></path>
                            </svg>
                        </label>
                    </div>
                    
                    <div class="space-y-2">
                        <p class="text-sm text-slate-600 dark:text-zinc-400">
                            Click the + button to upload a new photo. Allowed: JPG, GIF, PNG. Max size: 800 kB
                        </p>
                        <input type="file" id="account-upload" hidden name="image" accept="image/*" />
                    </div>
                </div>
            </div>

            <!-- Name Section -->
            <div class="grid gap-6 sm:grid-cols-2">
                <div class="space-y-2">
                    <label for="firstname" class="block text-sm font-semibold text-slate-700 dark:text-zinc-300">First Name</label>
                    <input type="text" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 transition focus:border-sky-500 focus:outline-none dark:border-white/10 dark:bg-zinc-900 dark:text-zinc-200 dark:focus:border-sky-500" 
                        id="firstname" name="firstname" placeholder="First Name" value="{{$user->firstname}}" minlength="3" @disabled($user->hasLockedIdentity()) required />
                </div>

                <div class="space-y-2">
                    <label for="lastname" class="block text-sm font-semibold text-slate-700 dark:text-zinc-300">Last Name</label>
                    <input type="text" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 transition focus:border-sky-500 focus:outline-none dark:border-white/10 dark:bg-zinc-900 dark:text-zinc-200 dark:focus:border-sky-500" 
                        id="lastname" name="lastname" placeholder="Last Name" value="{{$user->lastname}}" @disabled($user->hasLockedIdentity()) required />
                </div>
            </div>

            <!-- Contact Information -->
            <div class="space-y-4">
                <p class="text-sm font-semibold text-slate-700 dark:text-zinc-300">Contact Information</p>
                
                <div class="grid gap-6 sm:grid-cols-2">
                    <div class="space-y-2">
                        <label for="email" class="block text-sm font-semibold text-slate-700 dark:text-zinc-300">Email Address</label>
                        <input type="email" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-500 dark:border-white/10 dark:bg-white/5 dark:text-zinc-400" 
                            id="email" placeholder="Email Address" value="{{$user->email}}" disabled />
                        <p class="text-xs text-slate-500 dark:text-zinc-400">Contact support to change email</p>
                    </div>

                    <div class="space-y-2">
                        <label for="phone" class="block text-sm font-semibold text-slate-700 dark:text-zinc-300">Mobile Number</label>
                        <input type="tel" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-500 dark:border-white/10 dark:bg-white/5 dark:text-zinc-400" 
                            id="phone" value="{{$user->mobile}}" disabled />
                        <p class="text-xs text-slate-500 dark:text-zinc-400">
                            @if($user->phone_verified_externally_at)
                                Verified externally on {{ $user->phone_verified_externally_at->format('d M Y H:i') }}.
                            @elseif(!empty($identitySettings['phone_verification_enabled']))
                                Use WhatsApp OTP below to verify this number externally.
                            @else
                                Contact support to change phone.
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            @if(!empty($identitySettings['phone_verification_enabled']))
                <div class="rounded-3xl border border-slate-200 bg-slate-50/50 p-6 dark:border-white/10 dark:bg-white/5">
                    <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                        <div>
                            <p class="text-sm font-semibold text-slate-700 dark:text-zinc-300">WhatsApp OTP Verification</p>
                            <p class="mt-2 text-sm text-slate-500 dark:text-zinc-400">Request an external OTP for {{ $user->mobile }} and confirm it here to mark the phone number as externally verified.</p>
                        </div>

                        <form action="{{ route('user.profile.phone.request-otp') }}" method="POST">
                            @csrf
                            <button type="submit" class="inline-flex h-11 items-center rounded-full border border-slate-200 bg-white px-6 text-sm font-semibold text-slate-700 transition hover:border-sky-200 hover:text-sky-700 dark:border-white/10 dark:bg-zinc-900 dark:text-zinc-200 dark:hover:border-sky-500/30 dark:hover:text-sky-300">
                                Request WhatsApp OTP
                            </button>
                        </form>
                    </div>

                    <form action="{{ route('user.profile.phone.verify-otp') }}" method="POST" class="mt-5 app-action-row">
                        @csrf
                        <input type="text" name="otp" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 transition focus:border-sky-500 focus:outline-none dark:border-white/10 dark:bg-zinc-900 dark:text-zinc-200" placeholder="Enter OTP from WhatsApp">
                        <button type="submit" class="app-action-fit app-submit-button inline-flex h-11 rounded-full bg-sky-600 px-6 text-sm font-semibold text-white transition hover:bg-sky-700 dark:bg-sky-500 dark:hover:bg-sky-600">
                            Verify OTP
                        </button>
                    </form>
                </div>
            @endif

            <!-- Personal & KYC Information -->
            <div class="space-y-4">
                <p class="text-sm font-semibold text-slate-700 dark:text-zinc-300">Personal & KYC Information</p>
                
                <div class="grid gap-6 sm:grid-cols-2">
                    <div class="space-y-2">
                        <label for="whatsapp_phone" class="block text-sm font-semibold text-slate-700 dark:text-zinc-300">WhatsApp Phone Number</label>
                        <input type="tel" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 transition focus:border-sky-500 focus:outline-none dark:border-white/10 dark:bg-zinc-900 dark:text-zinc-200 dark:focus:border-sky-500" 
                            id="whatsapp_phone" name="whatsapp_phone" placeholder="+234..." value="{{$user->whatsapp_phone}}" />
                        <p class="text-xs text-slate-500 dark:text-zinc-400">Optional - Your WhatsApp contact number for verification</p>
                    </div>

                    <div class="space-y-2">
                        <label for="mother_maiden_name" class="block text-sm font-semibold text-slate-700 dark:text-zinc-300">Mother's Maiden Name</label>
                        <input type="text" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 transition focus:border-sky-500 focus:outline-none dark:border-white/10 dark:bg-zinc-900 dark:text-zinc-200 dark:focus:border-sky-500" 
                            id="mother_maiden_name" name="mother_maiden_name" placeholder="Mother's Maiden Name" value="{{$user->mother_maiden_name}}" />
                        <p class="text-xs text-slate-500 dark:text-zinc-400">Optional - Used for KYC verification</p>
                    </div>
                </div>

                <div class="space-y-2">
                    <label for="kyc_additional_data" class="block text-sm font-semibold text-slate-700 dark:text-zinc-300">Additional KYC Information</label>
                    <textarea class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 transition focus:border-sky-500 focus:outline-none dark:border-white/10 dark:bg-zinc-900 dark:text-zinc-200 dark:focus:border-sky-500 resize-none" 
                        id="kyc_additional_data" name="kyc_additional_data" placeholder="Any additional KYC information..." rows="3">{{ json_encode($user->kyc_additional_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</textarea>
                    <p class="text-xs text-slate-500 dark:text-zinc-400">Optional - Additional information for identity verification</p>
                </div>
            </div>

            <!-- Address Information -->
            <div class="space-y-4">
                <p class="text-sm font-semibold text-slate-700 dark:text-zinc-300">Address</p>
                
                <div class="grid gap-6 sm:grid-cols-2">
                    <div class="space-y-2">
                        <label for="address" class="block text-sm font-semibold text-slate-700 dark:text-zinc-300">Street Address</label>
                        <input type="text" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 transition focus:border-sky-500 focus:outline-none dark:border-white/10 dark:bg-zinc-900 dark:text-zinc-200 dark:focus:border-sky-500" 
                            id="address" name="address" placeholder="Street Address" value="{{@$user->address->address}}" @disabled($user->hasLockedIdentity()) required />
                    </div>

                    <div class="space-y-2">
                        <label for="city" class="block text-sm font-semibold text-slate-700 dark:text-zinc-300">City</label>
                        <input type="text" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 transition focus:border-sky-500 focus:outline-none dark:border-white/10 dark:bg-zinc-900 dark:text-zinc-200 dark:focus:border-sky-500" 
                            id="city" name="city" placeholder="City" value="{{@$user->address->city}}" @disabled($user->hasLockedIdentity()) required />
                    </div>
                </div>

                <div class="grid gap-6 sm:grid-cols-3">
                    <div class="space-y-2">
                        <label for="state" class="block text-sm font-semibold text-slate-700 dark:text-zinc-300">State</label>
                        <input type="text" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 transition focus:border-sky-500 focus:outline-none dark:border-white/10 dark:bg-zinc-900 dark:text-zinc-200 dark:focus:border-sky-500" 
                            id="state" name="state" placeholder="State" value="{{@$user->address->state}}" @disabled($user->hasLockedIdentity()) required />
                    </div>

                    <div class="space-y-2">
                        <label for="zip" class="block text-sm font-semibold text-slate-700 dark:text-zinc-300">Zip/Postal Code</label>
                        <input type="text" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 transition focus:border-sky-500 focus:outline-none dark:border-white/10 dark:bg-zinc-900 dark:text-zinc-200 dark:focus:border-sky-500" 
                            id="zip" name="zip" placeholder="Zip Code" value="{{@$user->address->zip}}" @disabled($user->hasLockedIdentity()) required />
                    </div>

                    <div class="space-y-2">
                        <label for="country" class="block text-sm font-semibold text-slate-700 dark:text-zinc-300">Country</label>
                        <input type="text" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-500 dark:border-white/10 dark:bg-white/5 dark:text-zinc-400" 
                            id="country" value="{{@$user->address->country}}" disabled />
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="app-action-row border-t border-slate-200 pt-6 dark:border-white/10">
                <button type="submit" class="app-submit-button inline-flex h-11 rounded-full bg-sky-600 px-6 text-sm font-semibold text-white transition hover:bg-sky-700 dark:bg-sky-500 dark:hover:bg-sky-600">
                    Save Changes
                </button>
                <a href="{{ route('user.home') }}" class="inline-flex h-11 items-center justify-center rounded-full border border-slate-200 bg-white px-6 text-sm font-semibold text-slate-700 transition hover:border-sky-200 hover:text-sky-700 dark:border-white/10 dark:bg-zinc-900 dark:text-zinc-200 dark:hover:border-sky-500/30 dark:hover:text-sky-300">
                    Cancel
                </a>
            </div>
        </form>
    </section>
</section>

@endsection

@push('script')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const input = document.getElementById('account-upload');
        const preview = document.getElementById('account-upload-img');

        if (!input || !preview) {
            return;
        }

        input.addEventListener('change', (event) => {
            const file = event.target.files && event.target.files[0];

            if (!file) {
                return;
            }

            const nextUrl = URL.createObjectURL(file);
            preview.src = nextUrl;
            preview.onload = () => URL.revokeObjectURL(nextUrl);
        });
    });
</script>
@endpush
