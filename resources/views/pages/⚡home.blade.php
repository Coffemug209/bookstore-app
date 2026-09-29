<?php

use App\Models\Book;
use App\Models\Category;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * Public home/landing page for the BookStore.
 * Displays hero section, featured books, categories, and promotional info.
 */
new #[Layout('layouts.public')] #[Title('Welcome to BookStore')] class extends Component {
    #[Computed]
    public function featuredBooks()
    {
        return Book::with('category')->latest()->take(6)->get();
    }

    #[Computed]
    public function categories()
    {
        return Category::withCount('books')->get();
    }
};
?>

<div>
    {{-- Hero Section --}}
    <section class="bg-gradient-to-br py-20 text-white">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <span class="inline-block rounded-full px-4 py-1 text-sm font-semibold uppercase tracking-wider text-blue-100 backdrop-blur-sm mb-4">
                    Online Bookstore & Library
                </span>
                <h1 class="mb-6 text-4xl font-extrabold sm:text-5xl lg:text-6xl tracking-tight">
                    Discover Your Next Favorite Book
                </h1>
                <p class="mx-auto mb-8 max-w-2xl text-lg text-blue-100 sm:text-xl">
                    Explore curated books across multiple genres. Order with easy and simple Cash on Delivery (COD) system.
                </p>
                <div class="flex flex-wrap justify-center gap-4">
                    <a href="{{ route('catalog') }}" wire:navigate
                       class="inline-flex items-center gap-2 rounded-xl bg-white px-7 py-3.5 text-base font-bold text-blue-600 shadow-lg transition hover:bg-blue-50 active:scale-95">
                        <flux:icon.book-open class="size-5" />
                        Explore Catalog
                    </a>
                    <a href="{{ route('about') }}" wire:navigate
                       class="inline-flex items-center gap-2 rounded-xl border border-white/30 bg-white/10 px-7 py-3.5 text-base font-semibold text-white backdrop-blur-sm transition hover:bg-white/20">
                        About Us
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- Featured Books --}}
    <section class="py-14 bg-zinc-50 dark:bg-zinc-800/30">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-8 flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-zinc-900 dark:text-white">Featured Books</h2>
                    <p class="text-sm text-zinc-500">Hand-picked selections from our diverse collection</p>
                </div>
                <a href="{{ route('catalog') }}" wire:navigate class="text-sm font-semibold text-blue-600 hover:text-blue-700">
                    View Full Catalog &rarr;
                </a>
            </div>

            <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6">
                @foreach($this->featuredBooks as $book)
                    <div class="group flex flex-col justify-between rounded-xl bg-white p-3.5 shadow-sm transition hover:shadow-md dark:bg-zinc-800 border border-zinc-100 dark:border-zinc-700">
                        <div>
                            <div class="relative mb-3 aspect-[2/3] overflow-hidden rounded-lg bg-zinc-100 dark:bg-zinc-700">
                                <img src="{{ $book->coverImageUrl() }}" alt="{{ $book->title }}"
                                     class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
                                @if($book->category)
                                    <span class="absolute left-2 top-2 rounded-md bg-blue-600/90 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wider text-white shadow backdrop-blur-sm">
                                        {{ $book->category->name }}
                                    </span>
                                @endif
                            </div>
                            <h3 class="line-clamp-2 text-sm font-bold text-zinc-900 dark:text-white">{{ $book->title }}</h3>
                            <p class="mt-1 text-xs text-zinc-500 line-clamp-1">{{ $book->author }}</p>
                        </div>
                        <div class="mt-3 pt-2 border-t border-zinc-100 dark:border-zinc-700 flex items-center justify-between">
                            <span class="text-sm font-extrabold text-blue-600 dark:text-blue-400">
                                Rp {{ number_format($book->price, 0, ',', '.') }}
                            </span>
                            <a href="{{ route('catalog') }}" wire:navigate class="rounded-md bg-blue-50 p-1.5 text-blue-600 hover:bg-blue-600 hover:text-white transition dark:bg-blue-900/30 dark:text-blue-400">
                                <flux:icon.arrow-right class="size-3.5" />
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Why Choose Us / Features --}}
    <section class="py-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-zinc-900 dark:text-white">Why Shop With BookStore?</h2>
                <p class="mt-2 text-zinc-500">Your seamless and convenient online bookstore experience</p>
            </div>

            <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-2xl border border-zinc-100 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-800 text-center">
                    <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">
                        <flux:icon.book-open class="size-7" />
                    </div>
                    <h3 class="text-lg font-bold text-zinc-900 dark:text-white">Extensive Collection</h3>
                    <p class="mt-2 text-sm text-zinc-500">From academic to bestsellers, find books across multiple genres and topics.</p>
                </div>

                <div class="rounded-2xl border border-zinc-100 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-800 text-center">
                    <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-amber-50 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400">
                        <flux:icon.banknotes class="size-7" />
                    </div>
                    <h3 class="text-lg font-bold text-zinc-900 dark:text-white">Cash on Delivery (COD)</h3>
                    <p class="mt-2 text-sm text-zinc-500">Pay safely when the books are delivered to your doorstep. No upfront card required.</p>
                </div>

                <div class="rounded-2xl border border-zinc-100 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-800 text-center">
                    <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-green-50 text-green-600 dark:bg-green-900/30 dark:text-green-400">
                        <flux:icon.truck class="size-7" />
                    </div>
                    <h3 class="text-lg font-bold text-zinc-900 dark:text-white">Fast Delivery</h3>
                    <p class="mt-2 text-sm text-zinc-500">Quick processing and reliable shipping right to your registered address.</p>
                </div>

                <div class="rounded-2xl border border-zinc-100 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-800 text-center">
                    <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-purple-50 text-purple-600 dark:bg-purple-900/30 dark:text-purple-400">
                        <flux:icon.chat-bubble-left-right class="size-7" />
                    </div>
                    <h3 class="text-lg font-bold text-zinc-900 dark:text-white">Dedicated Support</h3>
                    <p class="mt-2 text-sm text-zinc-500">Contact our administrator anytime with inquiries, feedback, or book requests.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA Section --}}
    <section class="bg-blue-600 py-16 text-white text-center">
        <div class="mx-auto max-w-4xl px-4">
            <h2 class="text-3xl font-extrabold sm:text-4xl">Ready to Start Reading?</h2>
            <p class="mt-4 text-lg text-blue-100">Join our community of readers. Create your account today or browse our rich catalog.</p>
            <div class="mt-8 flex justify-center gap-4">
                @guest
                    <a href="{{ route('register') }}" wire:navigate class="rounded-xl bg-white px-7 py-3.5 font-bold text-blue-600 shadow-md hover:bg-blue-50 transition">
                        Register Free
                    </a>
                    <a href="{{ route('login') }}" wire:navigate class="rounded-xl border border-white/40 bg-white/10 px-7 py-3.5 font-semibold text-white hover:bg-white/20 transition">
                        Log In
                    </a>
                @else
                    <a href="{{ route('catalog') }}" wire:navigate class="rounded-xl bg-white px-7 py-3.5 font-bold text-blue-600 shadow-md hover:bg-blue-50 transition">
                        Browse Catalog
                    </a>
                @endguest
            </div>
        </div>
    </section>
</div>