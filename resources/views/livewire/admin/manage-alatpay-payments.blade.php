<div>
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-neutral-900 dark:text-white">ALATPay Payments</h2>
            <p class="text-sm text-neutral-500 mt-1">
                Payment initiations from web booking and API, plus webhook/callback status updates.
            </p>
        </div>
    </div>

    <div class="mb-4 grid gap-3 rounded-lg border border-neutral-200 bg-neutral-50 p-4 text-sm dark:border-neutral-700 dark:bg-neutral-800 md:grid-cols-2">
        <div>
            <p class="font-medium text-neutral-900 dark:text-white">Webhook URL (set on ALATPay dashboard)</p>
            <code class="break-all text-xs text-neutral-700 dark:text-neutral-200">{{ $webhookUrl }}</code>
        </div>
        <div>
            <p class="font-medium text-neutral-900 dark:text-white">Callback URL</p>
            <code class="break-all text-xs text-neutral-700 dark:text-neutral-200">{{ $callbackUrl }}</code>
        </div>
    </div>

    <div class="mb-4 grid gap-3 md:grid-cols-3">
        <input type="search" wire:model.live.debounce.300ms="search" placeholder="Search reference, email, PNR..."
            class="rounded-lg border border-neutral-300 bg-white px-3 py-2 text-sm dark:border-neutral-600 dark:bg-neutral-800 dark:text-white" />
        <select wire:model.live="status"
            class="rounded-lg border border-neutral-300 bg-white px-3 py-2 text-sm dark:border-neutral-600 dark:bg-neutral-800 dark:text-white">
            <option value="">All statuses</option>
            <option value="PENDING">PENDING</option>
            <option value="SUCCESS">SUCCESS</option>
            <option value="FAILED">FAILED</option>
            <option value="CANCELED">CANCELED</option>
        </select>
        <select wire:model.live="source"
            class="rounded-lg border border-neutral-300 bg-white px-3 py-2 text-sm dark:border-neutral-600 dark:bg-neutral-800 dark:text-white">
            <option value="">All sources</option>
            <option value="web">Web</option>
            <option value="api">API</option>
        </select>
    </div>

    <div class="overflow-x-auto rounded-lg border border-neutral-200 dark:border-neutral-700">
        <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-700 text-sm">
            <thead class="bg-neutral-50 dark:bg-neutral-800">
                <tr>
                    <th class="px-4 py-3 text-left font-medium text-neutral-600 dark:text-neutral-300">Reference</th>
                    <th class="px-4 py-3 text-left font-medium text-neutral-600 dark:text-neutral-300">Source</th>
                    <th class="px-4 py-3 text-left font-medium text-neutral-600 dark:text-neutral-300">Email</th>
                    <th class="px-4 py-3 text-left font-medium text-neutral-600 dark:text-neutral-300">Amount</th>
                    <th class="px-4 py-3 text-left font-medium text-neutral-600 dark:text-neutral-300">PNR</th>
                    <th class="px-4 py-3 text-left font-medium text-neutral-600 dark:text-neutral-300">Status</th>
                    <th class="px-4 py-3 text-left font-medium text-neutral-600 dark:text-neutral-300">Initiated</th>
                    <th class="px-4 py-3 text-right font-medium text-neutral-600 dark:text-neutral-300">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700 bg-white dark:bg-neutral-900">
                @forelse ($payments as $payment)
                    <tr>
                        <td class="px-4 py-3 font-mono text-xs text-neutral-900 dark:text-white">{{ $payment->reference }}</td>
                        <td class="px-4 py-3 uppercase text-neutral-600 dark:text-neutral-300">{{ $payment->source ?: '—' }}</td>
                        <td class="px-4 py-3 text-neutral-700 dark:text-neutral-200">{{ $payment->email }}</td>
                        <td class="px-4 py-3 text-neutral-700 dark:text-neutral-200">{{ number_format((float) $payment->amount, 2) }}</td>
                        <td class="px-4 py-3 text-neutral-700 dark:text-neutral-200">{{ $payment->flightOrder->pnr ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <span class="rounded-full px-2 py-1 text-xs
                                {{ strtoupper((string) $payment->payment_status) === 'SUCCESS' ? 'bg-green-100 text-green-700' : (strtoupper((string) $payment->payment_status) === 'PENDING' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                {{ $payment->payment_status }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-neutral-500">{{ optional($payment->created_at)->format('Y-m-d H:i') }}</td>
                        <td class="px-4 py-3 text-right">
                            <button type="button" wire:click="view({{ $payment->id }})" class="text-primary hover:underline">Details</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-neutral-500">No ALATPay payment initiations yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $payments->links() }}</div>

    @if ($selectedPayment)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4" wire:click="closeDetail">
            <div class="max-h-[90vh] w-full max-w-3xl overflow-y-auto rounded-lg bg-white p-6 shadow-xl dark:bg-neutral-900" wire:click.stop>
                <div class="mb-4 flex items-start justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-semibold text-neutral-900 dark:text-white">Payment {{ $selectedPayment->reference }}</h3>
                        <p class="text-sm text-neutral-500">Gateway status: {{ $selectedPayment->gateway_status ?: '—' }}</p>
                    </div>
                    <button type="button" wire:click="closeDetail" class="text-sm text-neutral-500 hover:text-neutral-800">Close</button>
                </div>

                <div class="grid gap-3 text-sm md:grid-cols-2">
                    <p><strong>Status:</strong> {{ $selectedPayment->payment_status }}</p>
                    <p><strong>Source:</strong> {{ $selectedPayment->source ?: '—' }}</p>
                    <p><strong>Email:</strong> {{ $selectedPayment->email }}</p>
                    <p><strong>Amount:</strong> {{ number_format((float) $selectedPayment->amount, 2) }}</p>
                    <p><strong>Channel:</strong> {{ $selectedPayment->channel ?: '—' }}</p>
                    <p><strong>Transaction ID:</strong> {{ $selectedPayment->transaction_id ?: '—' }}</p>
                    <p><strong>ALATPay Order ID:</strong> {{ $selectedPayment->alatpay_order_id ?: '—' }}</p>
                    <p><strong>PNR:</strong> {{ $selectedPayment->flightOrder->pnr ?? '—' }}</p>
                </div>

                <h4 class="mt-6 mb-2 font-semibold text-neutral-900 dark:text-white">Events (initiation / callback / webhook)</h4>
                <div class="space-y-3">
                    @forelse ($events as $event)
                        <div class="rounded border border-neutral-200 p-3 text-xs dark:border-neutral-700">
                            <div class="mb-1 flex flex-wrap gap-2 text-neutral-700 dark:text-neutral-200">
                                <span class="font-semibold uppercase">{{ $event->event_type }}</span>
                                <span>{{ $event->status ?: '—' }}</span>
                                <span>{{ optional($event->created_at)->format('Y-m-d H:i:s') }}</span>
                                @if (!is_null($event->signature_valid))
                                    <span>signature: {{ $event->signature_valid ? 'valid' : 'invalid' }}</span>
                                @endif
                            </div>
                            <pre class="max-h-40 overflow-auto whitespace-pre-wrap break-all text-[11px] text-neutral-600 dark:text-neutral-300">{{ json_encode($event->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                        </div>
                    @empty
                        <p class="text-sm text-neutral-500">No events recorded yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    @endif
</div>
