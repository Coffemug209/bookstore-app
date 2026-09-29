<?php

use App\Models\Order;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Admin Order List component.
 * Shows all orders across all users with status management.
 */
new #[Title('Order Management')] class extends Component {
    use WithPagination;

    public string $statusFilter = '';

    public string $search = '';

    public array $statuses = [
        'pending'   => 'Pending',
        'confirmed' => 'Confirmed',
        'shipped'   => 'Shipped',
        'delivered' => 'Delivered',
        'cancelled' => 'Cancelled',
    ];

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updateStatus(int $orderId, string $status): void
    {
        Order::findOrFail($orderId)->update(['status' => $status]);
        Flux::toast(variant: 'success', text: __('Order status updated!'));
    }

    #[Computed]
    public function orders()
    {
        return Order::with(['user', 'items.book'])
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->when($this->search, fn ($q) => $q->where(function ($query) {
                $query->whereHas('user', function ($uQuery) {
                    $uQuery->where('name', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%");
                })->orWhere('id', 'like', "%{$this->search}%");
            }))
            ->latest()
            ->paginate(15);
    }
};
?>

<div class="p-4">
    {{-- Page Header --}}
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Order Management') }}</flux:heading>
        <flux:subheading size="lg" class="mb-4">{{ __('View and manage all customer orders') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    {{-- Filters --}}
    <div class="mb-4 flex flex-col gap-4 sm:flex-row sm:items-center">
        <flux:input
            wire:model.live.debounce.300ms="search"
            placeholder="Search by customer or order ID..."
            icon="magnifying-glass"
            class="max-w-xs"
        />
        <flux:select wire:model.live="statusFilter" placeholder="All statuses" class="max-w-xs">
            <flux:select.option value="">All Statuses</flux:select.option>
            @foreach($statuses as $value => $label)
                <flux:select.option :value="$value">{{ $label }}</flux:select.option>
            @endforeach
        </flux:select>
    </div>

    {{-- Orders Table --}}
    <div class="overflow-x-auto rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
        <table class="w-full text-left text-sm text-zinc-600 dark:text-zinc-300">
            <thead class="border-b border-zinc-200 bg-zinc-50 text-xs uppercase text-zinc-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-400">
                <tr>
                    <th class="px-4 py-3">Order ID</th>
                    <th class="px-4 py-3">Customer</th>
                    <th class="px-4 py-3">Items</th>
                    <th class="px-4 py-3">Total Amount</th>
                    <th class="px-4 py-3">Payment</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Order Date</th>
                    <th class="px-4 py-3 text-right">Update Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                @forelse($this->orders as $order)
                    <tr wire:key="order-{{ $order->id }}" class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                        <td class="px-4 py-3 font-mono font-bold text-zinc-900 dark:text-white">#{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</td>
                        <td class="px-4 py-3">
                            <div class="font-semibold text-zinc-900 dark:text-white">{{ $order->user->name }}</div>
                            <div class="text-xs text-zinc-400">{{ $order->user->email }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="font-medium">{{ $order->items->count() }}</span> book(s)
                            <div class="text-xs text-zinc-400">
                                @foreach($order->items->take(2) as $item)
                                    <div>{{ $item->book->title ?? 'Book' }} &times; {{ $item->quantity }}</div>
                                @endforeach
                                @if($order->items->count() > 2)
                                    <div>+{{ $order->items->count() - 2 }} more...</div>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3 font-bold text-zinc-900 dark:text-white">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex rounded-full bg-amber-50 px-2.5 py-0.5 text-xs font-bold text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">
                                COD
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            @php
                                $statusColors = [
                                    'pending'   => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400',
                                    'confirmed' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
                                    'shipped'   => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400',
                                    'delivered' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
                                    'cancelled' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
                                ];
                            @endphp
                            <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-bold {{ $statusColors[$order->status] ?? 'bg-zinc-100 text-zinc-800' }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-xs text-zinc-400">{{ $order->created_at->format('d M Y, H:i') }}</td>
                        <td class="px-4 py-3 text-right">
                            <select
                                wire:change="updateStatus({{ $order->id }}, $event.target.value)"
                                class="rounded-lg border border-zinc-300 bg-white px-2.5 py-1.5 text-xs font-medium text-zinc-700 shadow-sm dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200"
                            >
                                @foreach($statuses as $value => $label)
                                    <option value="{{ $value }}" {{ $order->status === $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-zinc-500">
                            No orders found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $this->orders->links() }}
    </div>
</div>