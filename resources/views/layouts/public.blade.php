<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-900">
        {{-- Navigation Bar --}}
        <nav x-data="{ open: false }" class="sticky top-0 z-50 border-b border-zinc-200 bg-white/90 backdrop-blur-md dark:border-zinc-700 dark:bg-zinc-900/90">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 items-center justify-between">
                    {{-- Logo --}}
                    <a href="{{ route('home') }}" wire:navigate class="flex items-center gap-2 text-xl font-bold text-blue-600">
                        <flux:icon.book-open class="size-7" />
                        <span>{{ config('app.name', 'BookStore') }}</span>
                    </a>

                    {{-- Desktop Nav Links --}}
                    <div class="hidden items-center gap-6 md:flex">
                        <a href="{{ route('home') }}" wire:navigate class="text-sm font-medium transition hover:text-blue-600 {{ request()->routeIs('home') ? 'text-blue-600 font-bold' : 'text-zinc-600 dark:text-zinc-300' }}">Home</a>
                        <a href="{{ route('catalog') }}" wire:navigate class="text-sm font-medium transition hover:text-blue-600 {{ request()->routeIs('catalog') ? 'text-blue-600 font-bold' : 'text-zinc-600 dark:text-zinc-300' }}">Catalog</a>
                        <a href="{{ route('about') }}" wire:navigate class="text-sm font-medium transition hover:text-blue-600 {{ request()->routeIs('about') ? 'text-blue-600 font-bold' : 'text-zinc-600 dark:text-zinc-300' }}">About Us</a>
                        <a href="{{ route('contact') }}" wire:navigate class="text-sm font-medium transition hover:text-blue-600 {{ request()->routeIs('contact') ? 'text-blue-600 font-bold' : 'text-zinc-600 dark:text-zinc-300' }}">Contact</a>
                    </div>

                    {{-- Auth / Cart --}}
                    <div class="flex items-center gap-3">
                        @auth
                            <a href="{{ route('cart') }}" wire:navigate class="relative flex items-center gap-1.5 rounded-lg border border-zinc-200 px-3 py-1.5 text-sm font-medium text-zinc-700 hover:bg-zinc-50 dark:border-zinc-700 dark:text-zinc-200 dark:hover:bg-zinc-800">
                                <flux:icon.shopping-cart class="size-4 text-blue-600" />
                                <span class="hidden sm:inline">Cart</span>
                                @php $cartCount = auth()->user()->cartItems()->sum('quantity'); @endphp
                                @if($cartCount > 0)
                                    <span class="flex h-5 min-w-5 items-center justify-center rounded-full bg-blue-600 px-1 text-[11px] font-bold text-white">{{ $cartCount }}</span>
                                @endif
                            </a>
                            <a href="{{ route('dashboard') }}" wire:navigate class="text-sm font-medium text-zinc-600 hover:text-blue-600 dark:text-zinc-300">
                                Dashboard
                            </a>
                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="rounded-lg px-2.5 py-1.5 text-xs font-semibold text-zinc-500 hover:bg-zinc-100 hover:text-red-600 dark:text-zinc-400 dark:hover:bg-zinc-800">
                                    Log out
                                </button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" wire:navigate class="text-sm font-semibold text-zinc-600 hover:text-blue-600 dark:text-zinc-300">Log in</a>
                            <a href="{{ route('register') }}" wire:navigate class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-bold text-white shadow-sm hover:bg-blue-700 transition">Register</a>
                        @endauth

                        {{-- Mobile menu button --}}
                        <button @click="open = !open" class="md:hidden rounded-lg p-2 text-zinc-600 hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-zinc-800">
                            <flux:icon.bars-3 class="size-6" />
                        </button>
                    </div>
                </div>

                {{-- Mobile Nav Drawer --}}
                <div x-show="open" x-cloak class="md:hidden border-t border-zinc-200 py-3 space-y-2 dark:border-zinc-700">
                    <a href="{{ route('home') }}" wire:navigate class="block px-3 py-2 rounded-lg text-base font-medium text-zinc-700 hover:bg-zinc-50 dark:text-zinc-200 dark:hover:bg-zinc-800">Home</a>
                    <a href="{{ route('catalog') }}" wire:navigate class="block px-3 py-2 rounded-lg text-base font-medium text-zinc-700 hover:bg-zinc-50 dark:text-zinc-200 dark:hover:bg-zinc-800">Catalog</a>
                    <a href="{{ route('about') }}" wire:navigate class="block px-3 py-2 rounded-lg text-base font-medium text-zinc-700 hover:bg-zinc-50 dark:text-zinc-200 dark:hover:bg-zinc-800">About Us</a>
                    <a href="{{ route('contact') }}" wire:navigate class="block px-3 py-2 rounded-lg text-base font-medium text-zinc-700 hover:bg-zinc-50 dark:text-zinc-200 dark:hover:bg-zinc-800">Contact Support</a>
                </div>
            </div>
        </nav>

        {{-- Page Content --}}
        <main>
            {{ $slot }}
        </main>

        {{-- Footer --}}
        <footer class="mt-16 border-t border-zinc-200 bg-zinc-50 py-8 dark:border-zinc-700 dark:bg-zinc-900">
            <div class="mx-auto max-w-7xl px-4 text-center text-sm text-zinc-500">
                <p>&copy; {{ date('Y') }} {{ config('app.name', 'BookStore') }}. All rights reserved.</p>
            </div>
        </footer>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
