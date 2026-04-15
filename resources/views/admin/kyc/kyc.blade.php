@extends('admin.layouts.app')

@section('panel')
<div class="space-y-6">
    <!-- Header with Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="panel-card p-6 rounded-2xl border border-slate-200 dark:border-white/10">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-600 dark:text-zinc-400">@lang('Total Submissions')</p>
                    <p class="mt-2 text-2xl font-semibold text-slate-950 dark:text-white">{{ count($kyc) }}</p>
                </div>
                <div class="inline-flex h-12 w-12 items-center justify-center rounded-xl bg-sky-100 text-sky-600 dark:bg-sky-900/30 dark:text-sky-400">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                </div>
            </div>
        </div>
        <div class="panel-card p-6 rounded-2xl border border-slate-200 dark:border-white/10">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-600 dark:text-zinc-400">@lang('Approved')</p>
                    <p class="mt-2 text-2xl font-semibold text-slate-950 dark:text-white">{{ count(array_filter($kyc, fn($item) => $item->status == 1)) }}</p>
                </div>
                <div class="inline-flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                </div>
            </div>
        </div>
        <div class="panel-card p-6 rounded-2xl border border-slate-200 dark:border-white/10">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-600 dark:text-zinc-400">@lang('Pending Review')</p>
                    <p class="mt-2 text-2xl font-semibold text-slate-950 dark:text-white">{{ count(array_filter($kyc, fn($item) => $item->status == 0)) }}</p>
                </div>
                <div class="inline-flex h-12 w-12 items-center justify-center rounded-xl bg-amber-100 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                </div>
            </div>
        </div>
    </div>

    <!-- KYC Table -->
    <div class="panel-card rounded-2xl border border-slate-200 dark:border-white/10 overflow-hidden">
        <div class="border-b border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 px-6 py-4">
            <h2 class="text-lg font-semibold text-slate-950 dark:text-white">@lang('KYC Submissions')</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5">
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-zinc-400">@lang('Document Type')</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-zinc-400">@lang('User')</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-zinc-400">@lang('Contact')</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-zinc-400">@lang('Submitted')</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-zinc-400">@lang('Status')</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-zinc-400">@lang('Action')</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-white/10">
                    @forelse($kyc as $data)
                        @php $user = App\Models\User::whereId($data->user_id)->first(); @endphp
                        <tr class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @if($data->front)
                                        <img src="{{ @getImage(imagePath()['profile']['user']['path'].'/'. $data->front,imagePath()['profile']['user']['size']) }}" alt="img" class="h-10 w-10 rounded-lg object-cover">
                                    @else
                                        <div class="h-10 w-10 rounded-lg bg-slate-200 dark:bg-white/10"></div>
                                    @endif
                                    <div>
                                        <p class="font-semibold text-slate-950 dark:text-white">{{ $data->type ?? 'N/A' }}</p>
                                        <p class="text-xs text-slate-500 dark:text-zinc-400">{{ date('d M, Y h:i A', strtotime($data->created_at)) }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div>
                                    <p class="font-semibold text-slate-950 dark:text-white">{{ @$user?->firstname ?? 'N/A' }} {{ @$user?->lastname ?? '' }}</p>
                                    <p class="text-sm text-slate-500 dark:text-zinc-400">@{{ @$user?->username ?? 'N/A' }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm">
                                    <p class="font-medium text-slate-950 dark:text-white">{{ @$user?->mobile ?? 'N/A' }}</p>
                                    <p class="text-slate-500 dark:text-zinc-400">{{ @$user?->email ?? 'N/A' }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600 dark:text-zinc-400">
                                {{ date('d M, Y', strtotime($data->created_at)) }}
                            </td>
                            <td class="px-6 py-4">
                                @if($data->status == 0)
                                    <span class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">@lang('Pending')</span>
                                @elseif($data->status == 1)
                                    <span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">@lang('Verified')</span>
                                @elseif($data->status == 2)
                                    <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-700 dark:bg-red-900/30 dark:text-red-400">@lang('Declined')</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.users.viewkyc',$data->id) }}" class="inline-flex items-center gap-2 rounded-full bg-sky-100 px-3 py-1.5 text-xs font-semibold text-sky-700 transition hover:bg-sky-200 dark:bg-sky-900/30 dark:text-sky-400 dark:hover:bg-sky-900/50">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                    @lang('View')
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="px-6 py-8 text-center text-slate-500 dark:text-zinc-400" colspan="6">{{ $empty_message ?? 'No KYC submissions found' }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
