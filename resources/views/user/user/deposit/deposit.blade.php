@extends($activeTemplate.'layouts.dashboard')

@section('content')

<section class="space-y-6">
    <div class="hero-surface p-6 sm:p-8">
        <div>
            <div class="flex flex-wrap gap-2">
                <span class="section-kicker">Deposit</span>
                <span class="section-kicker">Funding</span>
            </div>

            <div class="space-y-4">
                <h2 class="max-w-2xl text-3xl font-semibold tracking-tight text-slate-950 dark:text-white sm:text-4xl">
                    Top up your wallet
                </h2>
                <p class="max-w-2xl section-copy">
                    Add funds to your account instantly. Choose a payment method, enter the amount, and complete the transaction securely.
                </p>
            </div>
        </div>
    </div>

    <section class="panel-card p-6">
        <div class="mb-6">
            <p class="section-kicker">Direct Checkout</p>
            <h3 class="mt-3 section-title">Quick Deposit</h3>
            <p class="mt-2 text-sm text-slate-500 dark:text-zinc-400">
                These options create direct hosted checkout sessions for wallet funding. Your automatic top-up bank accounts stay separate from these instant card or bank transfer checkout flows.
            </p>
        </div>

        <div class="grid gap-6 xl:grid-cols-2">
            <div class="rounded-3xl border border-slate-200 bg-slate-50/70 p-5 dark:border-white/10 dark:bg-white/5">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-semibold text-slate-950 dark:text-white">Kora Pay</p>
                        <p class="mt-2 text-sm text-slate-500 dark:text-zinc-400">Redirect users to Kora hosted checkout and confirm payment when they return.</p>
                    </div>
                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ !empty($depositSettings['kora_enabled']) ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300' : 'bg-slate-200 text-slate-700 dark:bg-white/10 dark:text-zinc-300' }}">
                        {{ !empty($depositSettings['kora_enabled']) ? 'Enabled' : 'Disabled' }}
                    </span>
                </div>

                <form action="{{ route('user.deposit.kora.start') }}" method="POST" class="mt-5 space-y-4">
                    @csrf
                    <div class="space-y-2">
                        <label for="kora_amount" class="block text-sm font-semibold text-slate-700 dark:text-zinc-300">Amount</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-lg font-semibold text-slate-500 dark:text-zinc-400">{{ $general->cur_sym }}</span>
                            <input type="number" min="100" step="0.01" name="amount" id="kora_amount" class="w-full rounded-2xl border border-slate-200 bg-white pl-8 pr-4 py-3 text-sm text-slate-700 transition focus:border-sky-500 focus:outline-none dark:border-white/10 dark:bg-zinc-900 dark:text-zinc-200" placeholder="500.00" @disabled(empty($depositSettings['kora_enabled'])) required>
                        </div>
                    </div>

                    <button type="submit" class="inline-flex h-11 items-center rounded-full bg-sky-600 px-6 text-sm font-semibold text-white transition hover:bg-sky-700 disabled:cursor-not-allowed disabled:opacity-60 dark:bg-sky-500 dark:hover:bg-sky-600" @disabled(empty($depositSettings['kora_enabled']))>
                        Pay with Kora
                    </button>
                </form>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-slate-50/70 p-5 dark:border-white/10 dark:bg-white/5">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-semibold text-slate-950 dark:text-white">Quickteller Pay</p>
                        <p class="mt-2 text-sm text-slate-500 dark:text-zinc-400">Open the Quickteller inline widget on this page and verify the payment after checkout completes.</p>
                    </div>
                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ !empty($depositSettings['quickteller_enabled']) ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300' : 'bg-slate-200 text-slate-700 dark:bg-white/10 dark:text-zinc-300' }}">
                        {{ !empty($depositSettings['quickteller_enabled']) ? ($depositSettings['quickteller_mode'] ?? 'TEST') : 'Disabled' }}
                    </span>
                </div>

                <div class="mt-5 space-y-4">
                    <div class="space-y-2">
                        <label for="quickteller_amount" class="block text-sm font-semibold text-slate-700 dark:text-zinc-300">Amount</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-lg font-semibold text-slate-500 dark:text-zinc-400">{{ $general->cur_sym }}</span>
                            <input type="number" min="100" step="0.01" id="quickteller_amount" class="w-full rounded-2xl border border-slate-200 bg-white pl-8 pr-4 py-3 text-sm text-slate-700 transition focus:border-sky-500 focus:outline-none dark:border-white/10 dark:bg-zinc-900 dark:text-zinc-200" placeholder="500.00" @disabled(empty($depositSettings['quickteller_enabled']))>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        <button type="button" id="quicktellerButton" class="inline-flex h-11 items-center rounded-full bg-slate-950 px-6 text-sm font-semibold text-white transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-60 dark:bg-white dark:text-slate-950 dark:hover:bg-slate-200" @disabled(empty($depositSettings['quickteller_enabled']))>
                            Pay with Quickteller
                        </button>
                        <span id="quicktellerStatus" class="text-sm text-slate-500 dark:text-zinc-400"></span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!--section class="panel-card p-6">
        <div class="mb-6">
            <p class="section-kicker">Payment Method</p>
            <h3 class="mt-3 section-title">Select your preferred gateway</h3>
        </div>

        <form action="{{route('user.deposit.insert')}}" method="post" class="space-y-6">
            @csrf
            
            <div class="grid gap-6 sm:grid-cols-2">
                <div class="space-y-2">
                    <label for="gateway" class="block text-sm font-semibold text-slate-700 dark:text-zinc-300">Payment Gateway</label>
                    <select id="gateway" name="gateway_id" onchange="myFunction()" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 transition focus:border-sky-500 focus:outline-none dark:border-white/10 dark:bg-zinc-900 dark:text-zinc-200 dark:focus:border-sky-500">
                        <option selected disabled>Select A Payment Method</option>
                        @foreach($gatewayCurrency as $data)
                        <option data-id="{{$data->id}}" data-name="{{$data->name}}"
                            data-currency="{{$data->currency}}"
                            data-method_code="{{$data->method_code}}"
                            data-min_amount="{{showAmount($data->min_amount)}}"
                            data-max_amount="{{showAmount($data->max_amount)}}"
                            data-base_symbol="{{$data->baseSymbol()}}"
                            data-fix_charge="{{showAmount($data->fixed_charge)}}"
                            data-percent_charge="{{showAmount($data->percent_charge)}}">
                            {{__($data->name)}}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-2">
                    <label for="currency" class="block text-sm font-semibold text-slate-700 dark:text-zinc-300">Currency</label>
                    <input type="text" name="currency" id="currency" readonly
                        class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 dark:border-white/10 dark:bg-white/5 dark:text-zinc-300" />
                </div>
                <input type="hidden" id="method_code" name="method_code">
                <input type="hidden" id="name" name="name">
            </div>

            <div class="rounded-2xl border border-sky-200 bg-sky-50/50 p-4 dark:border-sky-500/20 dark:bg-sky-500/10">
                <p class="text-sm text-sky-950 dark:text-sky-50">
                    <span class="font-semibold">Amount range:</span>
                    <span id="min" class="text-sky-700 dark:text-sky-300"></span>
                    <span id="max" class="text-sky-700 dark:text-sky-300"></span>
                </p>
            </div>

            <div class="space-y-2">
                <label for="amount" class="block text-sm font-semibold text-slate-700 dark:text-zinc-300">Deposit Amount</label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-lg font-semibold text-slate-500 dark:text-zinc-400">{{$general->cur_sym}}</span>
                    <input type="number" name="amount" id="amount" step="0.01" class="w-full rounded-2xl border border-slate-200 bg-white pl-8 pr-4 py-3 text-sm text-slate-700 transition focus:border-sky-500 focus:outline-none dark:border-white/10 dark:bg-zinc-900 dark:text-zinc-200 dark:focus:border-sky-500"
                        placeholder="0.00" required />
                </div>
            </div>

            <div class="flex gap-3 pt-4">
                <button type="submit" class="inline-flex h-11 items-center rounded-full bg-sky-600 px-6 text-sm font-semibold text-white transition hover:bg-sky-700 dark:bg-sky-500 dark:hover:bg-sky-600">
                    Proceed to Payment
                </button>
                <a href="{{ route('user.home') }}" class="inline-flex h-11 items-center rounded-full border border-slate-200 bg-white px-6 text-sm font-semibold text-slate-700 transition hover:border-sky-200 hover:text-sky-700 dark:border-white/10 dark:bg-zinc-900 dark:text-zinc-200 dark:hover:border-sky-500/30 dark:hover:text-sky-300">
                    Cancel
                </a>
            </div>
        </form>
    </section-->

    <section class="panel-card p-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="section-kicker">Transaction History</p>
                <h3 class="mt-3 section-title">Your recent deposit transactions</h3>
            </div>
        </div>

        <div class="mt-6 space-y-3">
            @forelse($logs as $log)
                <article class="rounded-2xl border border-slate-200/80 bg-slate-50 p-4 dark:border-white/10 dark:bg-white/5">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div>
                            <p class="text-sm font-semibold text-slate-950 dark:text-white">{{$general->cur_sym}}{{showAmount($log->amount)}}</p>
                            <p class="mt-1 text-xs text-slate-500 dark:text-zinc-400">{{date('M d, Y H:i A', strtotime($log->created_at))}}</p>
                        </div>
                        <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $log->status == 1 ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300' }}">
                            {{ $log->status == 1 ? 'Completed' : 'Pending' }}
                        </span>
                    </div>
                </article>
            @empty
                <div class="rounded-2xl border border-dashed border-slate-200 p-6 text-center text-sm text-slate-500 dark:border-white/10 dark:text-zinc-400">
                    No deposit history yet. Your transactions will appear here.
                </div>
            @endforelse
        </div>

        @if($logs->hasPages())
            <div class="mt-6 flex items-center justify-center">
                {{$logs->links()}}
            </div>
        @endif
    </section>
</section>

@endsection

@push('script')
@if (!empty($depositSettings['quickteller_enabled']))
<script src="{{ strtoupper((string) ($depositSettings['quickteller_mode'] ?? 'TEST')) === 'LIVE' ? 'https://newwebpay.interswitchng.com/inline-checkout.js' : 'https://newwebpay.qa.interswitchng.com/inline-checkout.js' }}"></script>
@endif
<script>
    (function () {
        "use strict";
        function myFunction() {
            var name = document.getElementById("gateway").options[document.getElementById("gateway").selectedIndex].getAttribute('data-name');
            var currency = document.getElementById("gateway").options[document.getElementById("gateway").selectedIndex].getAttribute('data-currency');
            var method_code = document.getElementById("gateway").options[document.getElementById("gateway").selectedIndex].getAttribute('data-method_code');
            var min_amount = document.getElementById("gateway").options[document.getElementById("gateway").selectedIndex].getAttribute('data-min_amount');
            var max_amount = document.getElementById("gateway").options[document.getElementById("gateway").selectedIndex].getAttribute('data-max_amount');
            document.getElementById("currency").value = currency;
            document.getElementById("min").innerHTML = "{{$general->cur_sym}}" + min_amount + " to ";
            document.getElementById("max").innerHTML = "{{$general->cur_sym}}" + max_amount;
            document.getElementById("method_code").value = method_code;
            document.getElementById("name").value = name;
        }
        
        document.getElementById("gateway").addEventListener("change", myFunction);
    })();
</script>
<script>
    (function () {
        "use strict";

        const button = document.getElementById('quicktellerButton');
        if (!button) {
            return;
        }

        const amountInput = document.getElementById('quickteller_amount');
        const statusNode = document.getElementById('quicktellerStatus');

        button.addEventListener('click', async function () {
            const amount = Number(amountInput.value || 0);

            if (!amount || amount < 100) {
                statusNode.textContent = 'Enter at least {{ $general->cur_sym }}100.00 to continue.';
                return;
            }

            statusNode.textContent = 'Preparing Quickteller checkout...';
            button.disabled = true;

            try {
                const response = await fetch('{{ route('user.deposit.quickteller.initialize') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ amount }),
                });

                const payload = await response.json();
                if (!response.ok || !payload.success) {
                    throw new Error(payload.message || 'Unable to initialize Quickteller checkout.');
                }

                const data = payload.data;
                statusNode.textContent = 'Opening Quickteller...';

                window.webpayCheckout({
                    ...data,
                    onComplete: function (response) {
                        const ref = response.txnref || response.txn_ref || data.txn_ref;
                        window.location.href = '{{ route('user.deposit.quickteller.callback') }}' + '?txn_ref=' + encodeURIComponent(ref);
                    }
                });
            } catch (error) {
                statusNode.textContent = error.message;
            } finally {
                button.disabled = false;
            }
        });
    })();
</script>
@endpush
@push('script')
<script>
            modal.find('.withdraw-charge').text($(this).data('charge'));
            modal.find('.withdraw-after_charge').text($(this).data('after_charge'));
            modal.find('.withdraw-rate').text($(this).data('rate'));
            modal.find('.withdraw-payable').text($(this).data('payable'));
            var list = [];
            var details = Object.entries($(this).data('info'));

            var ImgPath = "{{asset(imagePath()['verify']['deposit']['path'])}}/";
            var singleInfo = '';
            for (var i = 0; i < details.length; i++) {
                if (details[i][1].type == 'file') {
                    singleInfo += `<li class="list-group-item">
                                        <span class="font-weight-bold "> ${details[i][0].replaceAll('_', " ")} </span> : <img src="${ImgPath}/${details[i][1].field_name}" alt="@lang('Image')" class="w-100">
                                    </li>`;
                } else {
                    singleInfo += `<li class="list-group-item">
                                        <span class="font-weight-bold "> ${details[i][0].replaceAll('_', " ")} </span> : <span class="font-weight-bold ml-3">${details[i][1].field_name}</span>
                                    </li>`;
                }
            }

            if (singleInfo) {
                modal.find('.withdraw-detail').html(`<br><strong class="my-3">@lang('Payment Information')</strong>  ${singleInfo}`);
            } else {
                modal.find('.withdraw-detail').html(`${singleInfo}`);
            }
            modal.modal('show');
        });

        $('.detailBtn').on('click', function() {
            var modal = $('#detailModal');
            var feedback = $(this).data('admin_feedback');
            modal.find('.withdraw-detail').html(`<p> ${feedback} </p>`);
            modal.modal('show');
        });
    })(jQuery);
</script>
@endpush
