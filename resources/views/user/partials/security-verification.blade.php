{{-- Security Verification Field (PIN or 2FA) --}}
@if ($user->pin_state == 1)
    {{-- PIN is enabled --}}
    <div class="space-y-2">
        <label for="pin_code" class="block text-sm font-medium text-slate-700 dark:text-zinc-200">
            Authorization PIN <span class="text-red-500">*</span>
        </label>
        <input type="password" inputmode="numeric" maxlength="4" id="pin_code" name="pin_code" placeholder="Enter your 4-digit PIN" value="{{ old('pin_code') }}" required class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-sky-400 focus:ring-4 focus:ring-sky-100 dark:border-white/10 dark:bg-zinc-950 dark:text-white dark:placeholder:text-zinc-500 dark:focus:border-sky-500 dark:focus:ring-sky-500/10">
        @error('pin_code')
            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
        @enderror
    </div>
@elseif ($user->two_factor_enabled)
    {{-- Use 2FA when PIN is disabled but 2FA is enabled --}}
    <div class="space-y-2">
        <label for="authenticator_code" class="block text-sm font-medium text-slate-700 dark:text-zinc-200">
            2FA Authenticator Code <span class="text-red-500">*</span>
        </label>
        <input type="text" inputmode="numeric" maxlength="6" id="authenticator_code" name="authenticator_code" placeholder="Enter 6-digit code from your authenticator app" value="{{ old('authenticator_code') }}" required class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-sky-400 focus:ring-4 focus:ring-sky-100 dark:border-white/10 dark:bg-zinc-950 dark:text-white dark:placeholder:text-zinc-500 dark:focus:border-sky-500 dark:focus:ring-sky-500/10">
        <p class="text-xs text-slate-500 dark:text-zinc-400">Check your authenticator app (Google Authenticator, Authy, etc.)</p>
        @error('authenticator_code')
            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
        @enderror
    </div>
@endif
