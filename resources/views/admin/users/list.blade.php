@extends('admin.layouts.app')
@section('panel')
    <section class="space-y-6">
        <!-- Filters -->
        <div class="panel-card p-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex-1">
                    <h3 class="font-semibold text-slate-950 dark:text-white">User Accounts</h3>
                    <p class="mt-1 text-sm text-slate-600 dark:text-zinc-400">Manage platform users and their accounts</p>
                </div>
                <a href="{{ route('admin.users.create') }}" class="inline-flex h-10 items-center rounded-full bg-sky-600 px-5 text-sm font-semibold text-white transition hover:bg-sky-700 dark:bg-sky-500 dark:hover:bg-sky-600">
                    Create User
                </a>
            </div>

            <!-- Quick Filter Tabs -->
            <div class="mt-6 flex flex-wrap gap-2 border-b border-slate-200 dark:border-white/10">
                <a href="{{ route('admin.users.all') }}" class="{{ request()->routeIs('admin.users.all') ? 'border-b-2 border-sky-600 text-sky-600 dark:border-sky-400 dark:text-sky-400' : 'text-slate-600 dark:text-zinc-400' }} px-4 py-3 text-sm font-medium transition">
                    All Users
                </a>
                <a href="{{ route('admin.users.active') }}" class="{{ request()->routeIs('admin.users.active') ? 'border-b-2 border-sky-600 text-sky-600 dark:border-sky-400 dark:text-sky-400' : 'text-slate-600 dark:text-zinc-400' }} px-4 py-3 text-sm font-medium transition">
                    Active
                </a>
                <a href="{{ request()->route('admin.users.banned') ? route('admin.users.banned') : '#' }}" class="{{ request()->routeIs('admin.users.banned') ? 'border-b-2 border-sky-600 text-sky-600 dark:border-sky-400 dark:text-sky-400' : 'text-slate-600 dark:text-zinc-400' }} px-4 py-3 text-sm font-medium transition">
                    Banned
                </a>
                <a href="{{ route('admin.users.email.verified') }}" class="{{ request()->routeIs('admin.users.email.verified') ? 'border-b-2 border-sky-600 text-sky-600 dark:border-sky-400 dark:text-sky-400' : 'text-slate-600 dark:text-zinc-400' }} px-4 py-3 text-sm font-medium transition">
                    Email Verified
                </a>
                <a href="{{ route('admin.users.sms.verified') }}" class="{{ request()->routeIs('admin.users.sms.verified') ? 'border-b-2 border-sky-600 text-sky-600 dark:border-sky-400 dark:text-sky-400' : 'text-slate-600 dark:text-zinc-400' }} px-4 py-3 text-sm font-medium transition">
                    SMS Verified
                </a>
            </div>
        </div>

        <!-- Users Table -->
        <div class="overflow-hidden rounded-2xl border border-slate-200 dark:border-white/10">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50 dark:border-white/10 dark:bg-white/5">
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 dark:text-zinc-400">@lang('User')</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 dark:text-zinc-400">@lang('Email-Phone')</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 dark:text-zinc-400">@lang('Status')</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 dark:text-zinc-400">@lang('Joined At')</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 dark:text-zinc-400">@lang('Balance')</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-slate-600 dark:text-zinc-400">@lang('Action')</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-white/10">
                        @forelse($users as $user)
                            <tr class="hover:bg-slate-50 dark:hover:bg-white/5 transition">
                                <td class="px-6 py-4">
                                    <div class="space-y-1">
                                        <p class="font-semibold text-slate-950 dark:text-white">{{ $user->fullname }}</p>
                                        <p class="text-xs text-slate-500 dark:text-zinc-400">
                                            <a href="{{ route('admin.users.detail', $user->id) }}" class="hover:text-sky-600 dark:hover:text-sky-400 transition">@{{ $user->username }}</a>
                                        </p>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="space-y-1 text-sm">
                                        <p class="text-slate-950 dark:text-white">{{ $user->email }}</p>
                                        <p class="text-slate-600 dark:text-zinc-400">{{ $user->mobile }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-2">
                                        @if($user->status == 1)
                                            <span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300">Active</span>
                                        @else
                                            <span class="inline-flex items-center rounded-full bg-rose-100 px-2.5 py-1 text-xs font-semibold text-rose-700 dark:bg-rose-900/30 dark:text-rose-300">Banned</span>
                                        @endif
                                        
                                        @if($user->ev == 1)
                                            <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-1 text-xs font-semibold text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">Email ✓</span>
                                        @endif
                                        
                                        @if($user->sv == 1)
                                            <span class="inline-flex items-center rounded-full bg-purple-100 px-2.5 py-1 text-xs font-semibold text-purple-700 dark:bg-purple-900/30 dark:text-purple-300">SMS ✓</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="space-y-1 text-sm">
                                        <p class="font-medium text-slate-950 dark:text-white">{{ showDateTime($user->created_at) }}</p>
                                        <p class="text-slate-600 dark:text-zinc-400">{{ diffForHumans($user->created_at) }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="font-semibold text-slate-950 dark:text-white">{{ $general->cur_sym }}{{ showAmount($user->balance) }}</p>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.users.detail', $user->id) }}" class="inline-flex h-9 items-center rounded-full border border-slate-200 bg-white px-4 text-xs font-semibold text-slate-700 transition hover:border-sky-200 hover:text-sky-700 dark:border-white/10 dark:bg-zinc-900 dark:text-zinc-200 dark:hover:border-sky-500/30 dark:hover:text-sky-300">
                                            View
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <p class="text-slate-600 dark:text-zinc-400">{{ $emptyMessage }}</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        @if($users->hasPages())
            <div class="flex items-center justify-between px-6 py-4">
                <div class="text-sm text-slate-600 dark:text-zinc-400">
                    Showing <span class="font-semibold text-slate-950 dark:text-white">{{ $users->firstItem() }}</span> to <span class="font-semibold text-slate-950 dark:text-white">{{ $users->lastItem() }}</span> of <span class="font-semibold text-slate-950 dark:text-white">{{ $users->total() }}</span>
                </div>
                <div class="flex gap-2">
                    {{ $users->links() }}
                </div>
            </div>
        @endif
    </section>
@endsection



                        






@push('breadcrumb-plugins')
    <form action="{{ route('admin.users.search', $scope ?? str_replace('admin.users.', '', request()->route()->getName())) }}" method="GET" class="form-inline float-sm-right bg--white">
        <div class="input-group has_append">
            <input type="text" name="search" class="form-control" placeholder="@lang('Username or email')" value="{{ $search ?? '' }}">
            <div class="input-group-append">
                <button class="btn btn--primary" type="submit"><i class="fa fa-search"></i></button>
            </div>
        </div>
    </form>
@endpush
