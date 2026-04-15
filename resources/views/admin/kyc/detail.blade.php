@extends('admin.layouts.app')

@section('panel')
<div class="space-y-6">
    @php $user = App\Models\User::whereId($kyc->user_id)->first(); @endphp
    
    <!-- Header -->
    <div>
        <p class="section-kicker">@lang('KYC Management')</p>
        <h2 class="mt-2 section-title">@lang('KYC Submission Details')</h2>
    </div>

    <!-- Main Content Grid -->
    <div class="grid gap-8 lg:grid-cols-[1fr_1.2fr]">
        <!-- Document Image -->
        <div class="panel-card rounded-2xl border border-slate-200 dark:border-white/10 overflow-hidden p-6 flex items-center justify-center bg-slate-50 dark:bg-white/5">
            <img
                src="{{ @getImage(imagePath()['profile']['user']['path'].'/'. $kyc->front,imagePath()['profile']['user']['size']) }}"
                class="max-w-full max-h-96 object-cover rounded-lg"
                alt="KYC Document"
            />
        </div>

        <!-- Details Card -->
        <div class="panel-card rounded-2xl border border-slate-200 dark:border-white/10 overflow-hidden">
            <div class="border-b border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 px-6 py-4">
                <h3 class="text-lg font-semibold text-slate-950 dark:text-white">@lang('Submission Information')</h3>
            </div>

            <div class="space-y-6 px-6 py-5">
                <!-- Customer Info -->
                <div>
                    <h4 class="text-sm font-semibold uppercase tracking-wider text-slate-600 dark:text-zinc-400 mb-3">@lang('Customer Details')</h4>
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-600 dark:text-zinc-400">@lang('Name'):</span>
                            <span class="font-semibold text-slate-950 dark:text-white">{{ @$user->firstname }} {{ @$user->lastname }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-600 dark:text-zinc-400">@lang('Username'):</span>
                            <a href="{{ route('admin.users.detail', $kyc->user_id) }}" class="font-semibold text-sky-600 dark:text-sky-400 hover:text-sky-700 dark:hover:text-sky-300">@{{ @$user->username }}</a>
                        </div>
                    </div>
                </div>

                <div class="border-t border-slate-200 dark:border-white/10 pt-6"></div>

                <!-- Address Info -->
                <div>
                    <h4 class="text-sm font-semibold uppercase tracking-wider text-slate-600 dark:text-zinc-400 mb-3">@lang('KYC Address')</h4>
                    <p class="text-slate-950 dark:text-white leading-relaxed">
                        {{ $kyc->address }}, {{ $kyc->city }}, {{ $kyc->state }}, {{ $kyc->country }}. ({{ $kyc->zip }})
                    </p>
                </div>

                <div class="border-t border-slate-200 dark:border-white/10 pt-6"></div>

                <!-- ID Information -->
                <div class="space-y-3">
                    <h4 class="text-sm font-semibold uppercase tracking-wider text-slate-600 dark:text-zinc-400">@lang('ID Information')</h4>
                    <div class="space-y-2">
                        <div class="flex items-center justify-between p-3 rounded-lg bg-slate-50 dark:bg-white/5">
                            <span class="text-slate-600 dark:text-zinc-400">@lang('ID Type')</span>
                            <span class="font-semibold text-slate-950 dark:text-white">{{ $kyc->type }}</span>
                        </div>
                        <div class="flex items-center justify-between p-3 rounded-lg bg-slate-50 dark:bg-white/5">
                            <span class="text-slate-600 dark:text-zinc-400">@lang('ID Number')</span>
                            <span class="font-semibold text-slate-950 dark:text-white">{{ $kyc->number ?? 'N/A' }}</span>
                        </div>
                        <div class="flex items-center justify-between p-3 rounded-lg bg-slate-50 dark:bg-white/5">
                            <span class="text-slate-600 dark:text-zinc-400">@lang('Expiry Date')</span>
                            <span class="font-semibold text-slate-950 dark:text-white">{{ $kyc->expiry ?? 'N/A' }}</span>
                        </div>
                        <div class="flex items-center justify-between p-3 rounded-lg bg-slate-50 dark:bg-white/5">
                            <span class="text-slate-600 dark:text-zinc-400">@lang('Submitted')</span>
                            <span class="font-semibold text-slate-950 dark:text-white">{{ date('d M, Y h:i A', strtotime($kyc->created_at)) }}</span>
                        </div>
                    </div>
                </div>

                <div class="border-t border-slate-200 dark:border-white/10 pt-6"></div>

                <!-- Status -->
                <div>
                    <h4 class="text-sm font-semibold uppercase tracking-wider text-slate-600 dark:text-zinc-400 mb-3">@lang('KYC Status')</h4>
                    @if($kyc->status == 1)
                        <span class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1.5 text-sm font-semibold text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">
                            <svg class="mr-2 h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            @lang('Verified')
                        </span>
                    @elseif($kyc->status == 2)
                        <span class="inline-flex items-center rounded-full bg-red-100 px-3 py-1.5 text-sm font-semibold text-red-700 dark:bg-red-900/30 dark:text-red-400">
                            <svg class="mr-2 h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                            @lang('Declined')
                        </span>
                    @else
                        <span class="inline-flex items-center rounded-full bg-amber-100 px-3 py-1.5 text-sm font-semibold text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">
                            <svg class="mr-2 h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                            @lang('Pending Review')
                        </span>
                    @endif
                </div>

                <div class="border-t border-slate-200 dark:border-white/10 pt-6"></div>

                <!-- Action Buttons -->
                @if($kyc->status == 0)
                    <div class="flex flex-col gap-3 sm:flex-row">
                        <a href="{{route('admin.users.verifykyc',$kyc->id)}}" class="flex-1 inline-flex items-center justify-center rounded-full bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-700 dark:bg-emerald-700 dark:hover:bg-emerald-800">
                            <svg class="mr-2 h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            @lang('Approve')
                        </a>
                        <a href="{{route('admin.users.declinekyc',$kyc->id)}}" class="flex-1 inline-flex items-center justify-center rounded-full bg-red-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-red-700 dark:bg-red-700 dark:hover:bg-red-800">
                            <svg class="mr-2 h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                            @lang('Decline')
                        </a>
                    </div>
                @elseif($kyc->status == 2)
                    <a href="{{route('admin.users.verifykyc',$kyc->id)}}" class="inline-flex items-center justify-center rounded-full bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-700 dark:bg-emerald-700 dark:hover:bg-emerald-800 w-full">
                        <svg class="mr-2 h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        @lang('Approve')
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
