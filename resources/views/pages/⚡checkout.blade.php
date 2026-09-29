<?php

use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * Checkout page component.
 * Allows users to review order summary, enter shipping address and contact number,
 * and simulate order placement with Payment at Delivery / Cash on Delivery (COD).
 */
new #[Layout('layouts.public')] #[Title('Checkout - BookStore')] class extends Component {
    /** @var string Shipping recipient address */
    public string $shippingAddress = '';

    /** @var string Contact phone number */
    public string $phone = '';

    /** @var string Optional order notes */
    public string $notes = '';

    /** @var bool Flag indicating successful order placement */
    public bool $orderPlaced = false;

    /** @var int|null Placed order ID */
    public ?int $orderId = null;

    /**
     * Place the order with Cash on Delivery (COD) payment method.
     */
    public function placeOrder(): void
    {
        $this->validate([
            'shippingAddress' => 'required|string|min:8|max:500',
            'phone'           => 'required|string|max:20',
            'notes'           => 'nullable|string|max:500',
        ]);

        $cartItems = CartItem::with('book')
            ->where('user_id', auth()->id())
            ->get();

        if ($cartItems->isEmpty()) {
            Flux::toast(variant: 'danger', text: __('Your cart is empty.'));

            return;
        }

        $total = (float) $cartItems->sum(fn ($item) => $item->book->price * $item->quantity);

        // 1. Create the Order
        $order = Order::create([
            'user_id'          => auth()->id(),
            'total_amount'     => $total,
            'status'           => 'pending',
            'payment_method'   => 'cod',
            'shipping_address' => $this->shippingAddress,
            'phone'            => $this->phone,
            'notes'            => $this->notes,
        ]);

        // 2. Create Order Items
        foreach ($cartItems as $item) {
            OrderItem::create([
                'order_id'   => $order->id,
                'book_id'    => $item->book_id,
                'quantity'   => $item->quantity,
                'unit_price' => $item->book->price,
                'subtotal'   => $item->book->price * $item->quantity,
            ]);
        }

        // 3. Clear user's cart
        CartItem::where('user_id', auth()->id())->delete();

        $this->orderId = $order->id;
        $this->orderPlaced = true;

        Flux::toast(variant: 'success', text: __('Your order has been placed successfully!'));
    }

    #[Computed]
    public function cartItems()
    {
        return CartItem::with('book.category')
            ->where('user_id', auth()->id())
            ->get();
    }

    #[Computed]
    public function total(): float
    {
        return (float) $this->cartItems->sum(fn ($item) => $item->book->price * $item->quantity);
    }
};
?>

<div>
    {{-- Header --}}
    <section class="py-10 text-white">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-extrabold sm:text-4xl">Checkout & Payment</h1>
            <p class="mt-1 text-blue-100 text-sm">Cash on Delivery (Payment at Delivery) Confirmation</p>
        </div>
    </section>

    <section class="py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            @if($orderPlaced)
                {{-- Order Success State --}}
                <div class="rounded-3xl border border-zinc-200 bg-white p-10 sm:p-16 text-center shadow-lg dark:border-zinc-700 dark:bg-zinc-800 max-w-2xl mx-auto">
                    <div class="mx-auto mb-6 flex h-24 w-24 items-center justify-center rounded-full bg-green-100 text-green-600 dark:bg-green-900/30 dark:text-green-400">
                        <flux:icon.check-circle class="size-14" />
                    </div>

                    <span class="inline-block rounded-full bg-green-100 px-3.5 py-1 text-xs font-bold text-green-800 dark:bg-green-900/40 dark:text-green-300 mb-3">
                        Order Received
                    </span>
                    <h2 class="text-3xl font-extrabold text-zinc-900 dark:text-white">Thank You for Your Order!</h2>
                    <p class="mt-2 text-zinc-600 dark:text-zinc-400">
                        Your order <span class="font-mono font-bold text-zinc-900 dark:text-white">#{{ str_pad($orderId, 5, '0', STR_PAD_LEFT) }}</span> has been registered into our system.
                    </p>

                    <div class="my-8 rounded-2xl bg-amber-50 p-6 text-left dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800/40 space-y-3">
                        <div class="flex items-center gap-2 text-amber-900 dark:text-amber-200 font-bold">
                            <flux:icon.banknotes class="size-5" />
                            <span>Payment Method: Cash on Delivery (COD)</span>
                        </div>
                        <p class="text-xs text-amber-800 dark:text-amber-300 leading-relaxed">
                            Please prepare the exact cash amount when our courier delivers the package to your address. Our team will verify and dispatch your books shortly.
                        </p>
                    </div>

                    <div class="flex flex-wrap justify-center gap-4">
                        <a href="{{ route('dashboard') }}" wire:navigate
                           class="rounded-xl border border-zinc-300 px-6 py-3 font-semibold text-zinc-700 hover:bg-zinc-50 dark:border-zinc-600 dark:text-zinc-200 dark:hover:bg-zinc-750 transition">
                            View Dashboard
                        </a>
                        <a href="{{ route('catalog') }}" wire:navigate
                           class="rounded-xl bg-blue-600 px-6 py-3 font-bold text-white shadow hover:bg-blue-700 transition">
                            Continue Shopping
                        </a>
                    </div>
                </div>
            @elseif($this->cartItems->isEmpty())
                <div class="rounded-2xl border border-zinc-200 bg-white p-12 text-center shadow-sm dark:border-zinc-700 dark:bg-zinc-800 max-w-lg mx-auto">
                    <p class="text-zinc-600 dark:text-zinc-400">Your cart is empty. Add items before checking out.</p>
                    <a href="{{ route('catalog') }}" wire:navigate class="mt-4 inline-block font-bold text-blue-600 hover:underline">
                        &larr; Browse Catalog
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 gap-8 lg:grid-cols-12">
                    {{-- Shipping Form --}}
                    <div class="lg:col-span-7">
                        <div class="rounded-2xl border border-zinc-200 bg-white p-8 shadow-sm dark:border-zinc-700 dark:bg-zinc-800">
                            <h2 class="text-xl font-bold text-zinc-900 dark:text-white mb-6">Delivery Details</h2>

                            <form wire:submit="placeOrder" class="space-y-6">
                                <flux:field>
                                    <flux:label>Shipping Address</flux:label>
                                    <flux:textarea
                                        wire:model="shippingAddress"
                                        rows="3"
                                        placeholder="Full delivery address (Street, building/house number, RT/RW, District, City, Postal Code)..."
                                        required
                                    />
                                    <flux:error name="shippingAddress" />
                                </flux:field>

                                <flux:field>
                                    <flux:label>Phone / WhatsApp Number</flux:label>
                                    <flux:input
                                        wire:model="phone"
                                        type="tel"
                                        placeholder="+62 812 3456 7890"
                                        required
                                    />
                                    <flux:error name="phone" />
                                </flux:field>

                                <flux:field>
                                    <flux:label>Order Notes <span class="text-zinc-400 text-xs">(optional)</span></flux:label>
                                    <flux:textarea
                                        wire:model="notes"
                                        rows="2"
                                        placeholder="Additional delivery instructions (e.g. Leave with security, call before arrival)..."
                                    />
                                    <flux:error name="notes" />
                                </flux:field>

                                {{-- COD Payment Simulation Block --}}
                                <div class="rounded-xl border border-amber-200 bg-amber-50 p-5 dark:border-amber-900 dark:bg-amber-900/20">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-amber-500 text-white">
                                            <flux:icon.banknotes class="size-6" />
                                        </div>
                                        <div>
                                            <h3 class="font-bold text-amber-900 dark:text-amber-200 text-sm">Payment at Delivery (COD)</h3>
                                            <p class="text-xs text-amber-700 dark:text-amber-300">
                                                Pay comfortably in cash upon package arrival.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <flux:button type="submit" variant="primary" class="w-full">
                                    <span wire:loading.remove wire:target="placeOrder">
                                        Confirm & Place Order (COD)
                                    </span>
                                    <span wire:loading wire:target="placeOrder">
                                        Processing Order...
                                    </span>
                                </flux:button>
                            </form>
                        </div>
                    </div>

                    {{-- Order Review Sidebar --}}
                    <div class="lg:col-span-5">
                        <div class="sticky top-24 rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-800 space-y-4">
                            <h2 class="text-lg font-bold text-zinc-900 dark:text-white">Order Summary ({{ $this->cartItems->sum('quantity') }} items)</h2>

                            <div class="divide-y divide-zinc-100 dark:divide-zinc-700 max-h-80 overflow-y-auto pr-1">
                                @foreach($this->cartItems as $item)
                                    <div class="flex items-center gap-3 py-3" wire:key="checkout-item-{{ $item->id }}">
                                        <img src="{{ $item->book->coverImageUrl() }}" alt="{{ $item->book->title }}"
                                             class="h-16 w-12 shrink-0 rounded object-cover shadow">
                                        <div class="flex-1 min-w-0">
                                            <p class="font-bold text-xs text-zinc-900 dark:text-white truncate">{{ $item->book->title }}</p>
                                            <p class="text-[11px] text-zinc-500">{{ $item->book->author }}</p>
                                            <p class="text-xs font-semibold text-zinc-600 dark:text-zinc-400 mt-0.5">
                                                {{ $item->quantity }} &times; Rp {{ number_format($item->book->price, 0, ',', '.') }}
                                            </p>
                                        </div>
                                        <div class="text-right shrink-0">
                                            <p class="text-xs font-extrabold text-blue-600 dark:text-blue-400">
                                                Rp {{ number_format($item->book->price * $item->quantity, 0, ',', '.') }}
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="border-t border-zinc-100 dark:border-zinc-700 pt-4 space-y-2 text-sm">
                                <div class="flex justify-between text-zinc-500">
                                    <span>Subtotal</span>
                                    <span class="font-medium text-zinc-800 dark:text-white">Rp {{ number_format($this->total, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between text-zinc-500">
                                    <span>Shipping Cost</span>
                                    <span class="font-semibold text-green-600">FREE</span>
                                </div>
                                <div class="border-t border-zinc-200 dark:border-zinc-700 pt-2 flex justify-between text-base font-extrabold text-zinc-900 dark:text-white">
                                    <span>Total Payment</span>
                                    <span class="text-blue-600 dark:text-blue-400 text-lg">
                                        Rp {{ number_format($this->total, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>
</div>