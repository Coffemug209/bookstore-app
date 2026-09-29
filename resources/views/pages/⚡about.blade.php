<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * Public About Us page for the BookStore application.
 */
new #[Layout('layouts.public')] #[Title('About Us - BookStore')] class extends Component {
    //
};
?>

<div>
    {{-- Header --}}
    <section class=" py-16 text-white text-center">
        <div class="mx-auto max-w-4xl px-4">
            <h1 class="text-4xl font-extrabold sm:text-5xl">About BookStore</h1>
            <p class="mt-4 text-lg text-blue-100 max-w-2xl mx-auto">
                Bridging readers and stories through an intuitive, accessible, and reliable digital book shopping platform.
            </p>
        </div>
    </section>

    {{-- Company Story & Vision --}}
    <section class="py-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-12 lg:grid-cols-2 lg:items-center">
                <div class="space-y-6">
                    <h2 class="text-3xl font-bold text-zinc-900 dark:text-white">Our Story & Mission</h2>
                    <p class="text-base text-zinc-600 dark:text-zinc-400 leading-relaxed">
                        BookStore was established as an innovative online book platform designed to empower passionate readers, students, educators, and professionals. We curate a comprehensive catalog that ranges from timeless fiction and non-fiction masterpieces to cutting-edge technology and business guides.
                    </p>
                    <p class="text-base text-zinc-600 dark:text-zinc-400 leading-relaxed">
                        With customer satisfaction at our core, we implement seamless ordering mechanisms with flexible <strong>Cash on Delivery (COD)</strong> payment methods, ensuring a safe, transparent, and hassle-free shopping experience for every customer across Indonesia.
                    </p>
                    <div class="pt-2">
                        <a href="{{ route('catalog') }}" wire:navigate class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-6 py-3 text-sm font-bold text-white shadow hover:bg-blue-700 transition">
                            Explore Catalog &rarr;
                        </a>
                    </div>
                </div>

                <div class="relative">
                    <div class="aspect-[4/3] overflow-hidden rounded-2xl shadow-xl bg-zinc-100 dark:bg-zinc-800">
                        <img src="https://unsplash.com/photos/hand-reaching-for-books-on-shelf-B30XL_m3fso"
                             alt="Library books and bookstore environment"
                             class="h-full w-full object-cover">
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Core Values --}}
    <section class="py-16 bg-zinc-50 dark:bg-zinc-800/40">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-zinc-900 dark:text-white">Our Core Values</h2>
                <p class="mt-2 text-zinc-500">Principles that guide our team and customer relationships</p>
            </div>

            <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
                <div class="rounded-2xl border border-zinc-200 bg-white p-8 shadow-sm dark:border-zinc-700 dark:bg-zinc-900 text-center">
                    <div class="mx-auto mb-5 flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">
                        <flux:icon.book-open class="size-7" />
                    </div>
                    <h3 class="text-xl font-bold text-zinc-900 dark:text-white">Authenticity & Quality</h3>
                    <p class="mt-3 text-sm text-zinc-500 leading-relaxed">
                        We are committed to delivering genuine, premium books with clear descriptions, synopsis, and accurate categorization.
                    </p>
                </div>

                <div class="rounded-2xl border border-zinc-200 bg-white p-8 shadow-sm dark:border-zinc-700 dark:bg-zinc-900 text-center">
                    <div class="mx-auto mb-5 flex h-14 w-14 items-center justify-center rounded-2xl bg-green-50 text-green-600 dark:bg-green-900/30 dark:text-green-400">
                        <flux:icon.truck class="size-7" />
                    </div>
                    <h3 class="text-xl font-bold text-zinc-900 dark:text-white">Reliable Delivery</h3>
                    <p class="mt-3 text-sm text-zinc-500 leading-relaxed">
                        Streamlined order fulfillment and safe shipment directly to your specified address with real-time status tracking.
                    </p>
                </div>

                <div class="rounded-2xl border border-zinc-200 bg-white p-8 shadow-sm dark:border-zinc-700 dark:bg-zinc-900 text-center">
                    <div class="mx-auto mb-5 flex h-14 w-14 items-center justify-center rounded-2xl bg-purple-50 text-purple-600 dark:bg-purple-900/30 dark:text-purple-400">
                        <flux:icon.chat-bubble-left-right class="size-7" />
                    </div>
                    <h3 class="text-xl font-bold text-zinc-900 dark:text-white">Customer-Centric</h3>
                    <p class="mt-3 text-sm text-zinc-500 leading-relaxed">
                        Our administrative support team is always ready to assist you with inquiries, recommendations, and order questions.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- Company Stats --}}
    <section class="py-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-2 gap-8 text-center md:grid-cols-4">
                    <div>
                        <div class="text-4xl font-extrabold sm:text-5xl">100%</div>
                        <div class="mt-2 text-sm text-blue-200 uppercase tracking-wide">Original Books</div>
                    </div>
                    <div>
                        <div class="text-4xl font-extrabold sm:text-5xl">5+</div>
                        <div class="mt-2 text-sm text-blue-200 uppercase tracking-wide">Book Categories</div>
                    </div>
                    <div>
                        <div class="text-4xl font-extrabold sm:text-5xl">COD</div>
                        <div class="mt-2 text-sm text-blue-200 uppercase tracking-wide">Payment Supported</div>
                    </div>
                    <div>
                        <div class="text-4xl font-extrabold sm:text-5xl">24/7</div>
                        <div class="mt-2 text-sm text-blue-200 uppercase tracking-wide">Online Ordering</div>
                    </div>
                </div>
        </div>
    </section>
</div>