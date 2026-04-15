@extends('admin.layouts.app')

@section('panel')
<div class="space-y-6">
    <!-- Header -->
    <div>
        <p class="section-kicker">@lang('Payment Gateways')</p>
        <h2 class="mt-2 section-title">@lang('Manage Payment Methods')</h2>
        <p class="mt-2 section-copy max-w-2xl">@lang('Enable, disable, and configure payment gateways for your platform.')</p>
    </div>

    <!-- Gateway Grid -->
    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
        @forelse($gateways->sortBy('alias') as $gateway)
            <article class="panel-card rounded-2xl border border-slate-200 dark:border-white/10 overflow-hidden flex flex-col">
                <!-- Image Container -->
                <div class="h-32 overflow-hidden bg-slate-100 dark:bg-white/5">
                    <img src="{{ getImage(imagePath()['gateway']['path'].'/'. $gateway->image,imagePath()['gateway']['size'])}}" alt="{{ __($gateway->name) }}" class="h-full w-full object-cover" />
                </div>

                <!-- Content -->
                <div class="flex-grow p-5">
                    <h3 class="text-base font-semibold text-slate-950 dark:text-white">{{__($gateway->name)}}</h3>
                    <p class="mt-2 text-xs uppercase tracking-[0.18em] text-slate-500 dark:text-zinc-400">{{ $gateway->alias }}</p>

                    <!-- Status Badge -->
                    <div class="mt-4">
                        @if($gateway->status == 1)
                            <span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">@lang('Active')</span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700 dark:bg-slate-900/30 dark:text-slate-400">@lang('Disabled')</span>
                        @endif
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex flex-col gap-2 border-t border-slate-200 dark:border-white/10 p-4">
                    @if($gateway->status == 0)
                        <form action="{{ route('admin.gateway.automatic.activate')}}" method="POST" class="contents">
                            @csrf
                            <input value="{{$gateway->code}}" type="hidden" name="code">
                            <button type="submit" class="inline-flex items-center justify-center rounded-full bg-emerald-600 px-3 py-2 text-sm font-semibold text-white transition hover:bg-emerald-700">
                                <svg class="h-4 w-4 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                @lang('Activate')
                            </button>
                        </form>
                    @else
                        <form action="{{route('admin.gateway.automatic.deactivate')}}" method="POST" class="contents">
                            @csrf
                            <input value="{{$gateway->code}}" type="hidden" name="code">
                            <button type="submit" class="inline-flex items-center justify-center rounded-full bg-red-600 px-3 py-2 text-sm font-semibold text-white transition hover:bg-red-700">
                                <svg class="h-4 w-4 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                @lang('Deactivate')
                            </button>
                        </form>
                    @endif

                    <a href="{{ route('admin.gateway.automatic.edit', $gateway->alias) }}" class="inline-flex items-center justify-center rounded-full bg-sky-600 px-3 py-2 text-sm font-semibold text-white transition hover:bg-sky-700">
                        <svg class="h-4 w-4 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="11 4 14.82 5.18 16.82 3.18 17.82 9 23 10 18 14.18 20.18 16.82 14.82 17.82 13 23 9 18 3 20 5.18 14.18 4 11 9 9 11 4"></polygon></svg>
                        @lang('Edit')
                    </a>
                </div>
            </article>
        @empty
            <div class="col-span-full rounded-2xl border border-dashed border-slate-200 bg-slate-50 p-8 text-center dark:border-white/10 dark:bg-white/5">
                <svg class="mx-auto h-12 w-12 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a4 4 0 0 1 4-4h2a4 4 0 0 1 4 4v4"></path></svg>
                <p class="mt-4 text-slate-600 dark:text-zinc-400">{{ __($emptyMessage) }}</p>
            </div>
        @endforelse
    </div>
</div>

@endsection


