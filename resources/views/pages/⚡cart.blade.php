<?php

use App\Models\CartItem;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * Shopping Cart page component.
 * Allows authenticated users to view, update quantities, remove books, and navigate to checkout.
 */
new #[Layout('layouts.public')] #[Title('Shopping Cart - BookStore')] class extends Component {
    /**
     * Update quantity of a specific cart item.
     */
    public function updateQuantity(int $cartItemId, int $quantity): void
    {
        $item = CartItem::where('user_id', auth()->id())->findOrFail($cartItemId);

        if ($quantity < 1) {
            $item->delete();
            Flux::toast(variant: 'warning', text: __('Item removed from cart.'));
        } else {
            $item->update(['quantity' => $quantity]);
        }
    }

    /**
     * Remove an item from the cart.
     */
    public function removeItem(int $cartItemId): void
    {
        CartItem::where('user_id', auth()->id())->findOrFail($cartItemId)->delete();
        Flux::toast(variant: 'warning', text: __('Item removed from cart.'));
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
    <section class="bg-gradient-to-br from-blue-600 to-indigo-800 py-10 text-white">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-extrabold sm:text-4xl">Shopping Cart</h1>
            <p class="mt-1 text-blue-100 text-sm">Review your selected books before checkout</p>
        </div>
    </section>

    <section class="py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            @if($this->cartItems->isEmpty())
                <div class="rounded-2xl border border-zinc-200 bg-white p-16 text-center shadow-sm dark:border-zinc-700 dark:bg-zinc-800 max-w-xl mx-auto">
                    <div class="mx-auto mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">
                        <flux:icon.shopping-cart class="size-10" />
                    </div>
                    <h2 class="text-2xl font-bold text-zinc-900 dark:text-white">Your Cart is Empty</h2>
                    <p class="mt-2 text-sm text-zinc-500">Looks like you haven't added any books to your cart yet.</p>
                    <div class="mt-6">
                        <a href="{{ route('catalog') }}" wire:navigate
                           class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-6 py-3 font-bold text-white shadow hover:bg-blue-700 transition">
                            <flux:icon.book-open class="size-4" />
                            Browse Catalog
                        </a>
                    </div>
                </div>
            @else
                <div class="grid grid-cols-1 gap-8 lg:grid-cols-12">
                    {{-- Cart Items List --}}
                    <div class="lg:col-span-8 space-y-4">
                        @foreach($this->cartItems as $item)
                            <div wire:key="cart-item-{{ $item->id }}"
                                 class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-800">
                                <div class="flex items-center gap-4">
                                    <img src="{{ $item->book->coverImageUrl() }}" alt="{{ $item->book->title }}"
                                         class="h-24 w-16 shrink-0 rounded-lg object-cover shadow">
                                    <div>
                                        <h3 class="font-bold text-base text-zinc-900 dark:text-white">{{ $item->book->title }}</h3>
                                        <p class="text-xs text-zinc-500">{{ $item->book->author }}</p>
                                        @if($item->book->category)
                                            <span class="mt-1 inline-block rounded-md bg-blue-50 px-2 py-0.5 text-xs font-semibold text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                                                {{ $item->book->category->name }}
                                            </span>
                                        @endif
                                        <p class="mt-1 text-xs text-zinc-400">Unit Price: Rp {{ number_format($item->book->price, 0, ',', '.') }}</p>
                                    </div>
                                </div>

                                <div class="flex w-full sm:w-auto items-center justify-between sm:justify-end gap-6 pt-3 sm:pt-0 border-t sm:border-t-0 border-zinc-100 dark:border-zinc-700">
                                    {{-- Quantity buttons --}}
                                    <div class="flex items-center rounded-lg border border-zinc-200 dark:border-zinc-700">
                                        <button
                                            wire:click="updateQuantity({{ $item->id }}, {{ $item->quantity - 1 }})"
                                            class="flex h-9 w-9 items-center justify-center text-zinc-600 hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-zinc-700"
                                        >
                                            <flux:icon.minus class="size-3.5" />
                                        </button>
                                        <span class="w-10 text-center font-bold text-sm">{{ $item->quantity }}</span>
                                        <button
                                            wire:click="updateQuantity({{ $item->id }}, {{ $item->quantity + 1 }})"
                                            class="flex h-9 w-9 items-center justify-center text-zinc-600 hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-zinc-700"
                                        >
                                            <flux:icon.plus class="size-3.5" />
                                        </button>
                                    </div>

                                    <div class="text-right min-w-[100px]">
                                        <p class="text-base font-extrabold text-blue-600 dark:text-blue-400">
                                            Rp {{ number_format($item->book->price * $item->quantity, 0, ',', '.') }}
                                        </p>
                                    </div>

                                    <button
                                        wire:click="removeItem({{ $item->id }})"
                                        class="rounded-lg p-2 text-zinc-400 hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-900/30 transition"
                                        title="Remove item"
                                    >
                                        <flux:icon.trash class="size-5" />
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Order Summary --}}
                    <div class="lg:col-span-4">
                        <div class="sticky top-24 rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-800 space-y-5">
                            <h2 class="text-xl font-bold text-zinc-900 dark:text-white">Order Summary</h2>

                            <div class="space-y-3 text-sm">
                                <div class="flex justify-between text-zinc-600 dark:text-zinc-400">
                                    <span>Total Items</span>
                                    <span class="font-medium text-zinc-900 dark:text-white">{{ $this->cartItems->sum('quantity') }} items</span>
                                </div>
                                <div class="flex justify-between text-zinc-600 dark:text-zinc-400">
                                    <span>Estimated Shipping</span>
                                    <span class="font-semibold text-green-600">FREE</span>
                                </div>
                                <div class="border-t border-zinc-100 dark:border-zinc-700 pt-3 flex justify-between text-base font-extrabold text-zinc-900 dark:text-white">
                                    <span>Total Price</span>
                                    <span class="text-blue-600 dark:text-blue-400 text-lg">
                                        Rp {{ number_format($this->total, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>

                            <div class="rounded-xl bg-amber-50 p-4 dark:bg-amber-900/20 text-xs text-amber-800 dark:text-amber-300">
                                <p class="font-bold flex items-center gap-1.5">
                                    <flux:icon.banknotes class="size-4" />
                                    Cash on Delivery (COD)
                                </p>
                                <p class="mt-1">Pay when your books arrive at your address.</p>
                            </div>

                            <a href="{{ route('checkout') }}" wire:navigate
                               class="block w-full text-center rounded-xl bg-blue-600 py-3.5 font-bold text-white shadow-md hover:bg-blue-700 transition active:scale-95">
                                Proceed to Checkout
                            </a>

                            <div class="text-center">
                                <a href="{{ route('catalog') }}" wire:navigate class="text-xs font-semibold text-zinc-500 hover:text-blue-600">
                                    &larr; Continue Shopping
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>
</div>