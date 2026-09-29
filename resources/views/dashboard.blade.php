<x-layouts::app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-4">
        {{-- Header / Welcome --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <flux:heading size="xl" level="1">Welcome, {{ auth()->user()->name }}!</flux:heading>
                <flux:subheading size="lg">
                    @if(auth()->user()->isAdmin())
                        Admin Control Panel — Monitor operational status, orders, books, and users.
                    @else
                        Welcome back to BookStore. Discover new books and track your orders.
                    @endif
                </flux:subheading>
            </div>
            <div>
                <a href="{{ route('catalog') }}" wire:navigate class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700">
                    <flux:icon.book-open class="size-4" />
                    Browse Catalog
                </a>
            </div>
        </div>

        <flux:separator variant="subtle" />

        @if(auth()->user()->isAdmin())
            @php
                $booksCount = \App\Models\Book::count();
                $categoriesCount = \App\Models\Category::count();
                $usersCount = \App\Models\User::count();
                $ordersCount = \App\Models\Order::count();
                $totalRevenue = \App\Models\Order::where('status', '!=', 'cancelled')->sum('total_amount');
                $recentOrders = \App\Models\Order::with(['user', 'items.book'])->latest()->take(5)->get();
            @endphp

            {{-- Stat Cards Grid --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Total Books</p>
                            <p class="mt-1 text-2xl font-bold text-zinc-900 dark:text-white">{{ $booksCount }}</p>
                        </div>
                        <div class="rounded-lg bg-blue-50 p-3 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">
                            <flux:icon.book-open class="size-6" />
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('admin.books') }}" wire:navigate class="text-xs font-semibold text-blue-600 hover:underline dark:text-blue-400">Manage Books &rarr;</a>
                    </div>
                </div>

                <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Categories</p>
                            <p class="mt-1 text-2xl font-bold text-zinc-900 dark:text-white">{{ $categoriesCount }}</p>
                        </div>
                        <div class="rounded-lg bg-purple-50 p-3 text-purple-600 dark:bg-purple-900/30 dark:text-purple-400">
                            <flux:icon.folder class="size-6" />
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('admin.categories') }}" wire:navigate class="text-xs font-semibold text-purple-600 hover:underline dark:text-purple-400">Manage Categories &rarr;</a>
                    </div>
                </div>

                <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Total Users</p>
                            <p class="mt-1 text-2xl font-bold text-zinc-900 dark:text-white">{{ $usersCount }}</p>
                        </div>
                        <div class="rounded-lg bg-green-50 p-3 text-green-600 dark:bg-green-900/30 dark:text-green-400">
                            <flux:icon.users class="size-6" />
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('admin.users') }}" wire:navigate class="text-xs font-semibold text-green-600 hover:underline dark:text-green-400">View Users &rarr;</a>
                    </div>
                </div>

                <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Total Orders</p>
                            <p class="mt-1 text-2xl font-bold text-zinc-900 dark:text-white">{{ $ordersCount }}</p>
                        </div>
                        <div class="rounded-lg bg-amber-50 p-3 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400">
                            <flux:icon.shopping-bag class="size-6" />
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('admin.orders') }}" wire:navigate class="text-xs font-semibold text-amber-600 hover:underline dark:text-amber-400">Manage Orders &rarr;</a>
                    </div>
                </div>
            </div>

            {{-- Recent Orders Section --}}
            <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                <div class="mb-4 flex items-center justify-between">
                    <flux:heading size="lg">Recent Orders</flux:heading>
                    <a href="{{ route('admin.orders') }}" wire:navigate class="text-sm font-medium text-blue-600 hover:underline dark:text-blue-400">View all orders &rarr;</a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-zinc-600 dark:text-zinc-300">
                        <thead class="border-b border-zinc-200 bg-zinc-50 text-xs uppercase text-zinc-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-400">
                            <tr>
                                <th class="px-4 py-3">Order ID</th>
                                <th class="px-4 py-3">Customer</th>
                                <th class="px-4 py-3">Items</th>
                                <th class="px-4 py-3">Total</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                            @forelse($recentOrders as $order)
                                <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                                    <td class="px-4 py-3 font-mono">#{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</td>
                                    <td class="px-4 py-3 font-medium text-zinc-900 dark:text-white">{{ $order->user->name }}</td>
                                    <td class="px-4 py-3">{{ $order->items->count() }} item(s)</td>
                                    <td class="px-4 py-3 font-semibold text-zinc-900 dark:text-white">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
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
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $statusColors[$order->status] ?? 'bg-zinc-100 text-zinc-800' }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-zinc-400 text-xs">{{ $order->created_at->format('d M Y, H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-6 text-center text-zinc-500">No orders placed yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            @php
                $userOrders = auth()->user()->orders()->with('items.book')->latest()->take(5)->get();
                $cartCount = auth()->user()->cartItems()->sum('quantity');
            @endphp

            <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900 md:col-span-1">
                    <flux:heading size="lg">Quick Actions</flux:heading>
                    <div class="mt-4 space-y-3">
                        <a href="{{ route('catalog') }}" wire:navigate class="flex items-center justify-between rounded-lg border border-zinc-200 p-3 hover:bg-zinc-50 dark:border-zinc-700 dark:hover:bg-zinc-800">
                            <div class="flex items-center gap-3">
                                <flux:icon.book-open class="size-5 text-blue-600" />
                                <span class="font-medium text-sm">Browse Catalog</span>
                            </div>
                            <flux:icon.chevron-right class="size-4 text-zinc-400" />
                        </a>
                        <a href="{{ route('cart') }}" wire:navigate class="flex items-center justify-between rounded-lg border border-zinc-200 p-3 hover:bg-zinc-50 dark:border-zinc-700 dark:hover:bg-zinc-800">
                            <div class="flex items-center gap-3">
                                <flux:icon.shopping-cart class="size-5 text-green-600" />
                                <span class="font-medium text-sm">My Shopping Cart</span>
                            </div>
                            <span class="rounded-full bg-green-100 px-2 py-0.5 text-xs font-bold text-green-800 dark:bg-green-900/30 dark:text-green-400">{{ $cartCount }}</span>
                        </a>
                        <a href="{{ route('contact') }}" wire:navigate class="flex items-center justify-between rounded-lg border border-zinc-200 p-3 hover:bg-zinc-50 dark:border-zinc-700 dark:hover:bg-zinc-800">
                            <div class="flex items-center gap-3">
                                <flux:icon.chat-bubble-left-right class="size-5 text-purple-600" />
                                <span class="font-medium text-sm">Contact Support</span>
                            </div>
                            <flux:icon.chevron-right class="size-4 text-zinc-400" />
                        </a>
                    </div>
                </div>

                <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900 md:col-span-2">
                    <flux:heading size="lg">My Recent Orders</flux:heading>
                    <div class="mt-4">
                        @if($userOrders->isEmpty())
                            <div class="py-8 text-center text-zinc-500">
                                <flux:icon.shopping-bag class="mx-auto mb-2 size-8 opacity-40" />
                                <p>You haven't placed any orders yet.</p>
                                <a href="{{ route('catalog') }}" wire:navigate class="mt-2 inline-block text-sm font-semibold text-blue-600 hover:underline">Start shopping &rarr;</a>
                            </div>
                        @else
                            <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                                @foreach($userOrders as $order)
                                    <div class="flex items-center justify-between py-3">
                                        <div>
                                            <p class="font-mono font-medium text-sm text-zinc-900 dark:text-white">#{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</p>
                                            <p class="text-xs text-zinc-400">{{ $order->items->count() }} item(s) &bull; {{ $order->created_at->format('d M Y') }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-bold text-sm text-zinc-900 dark:text-white">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
                                            <span class="inline-flex rounded-full bg-zinc-100 px-2 py-0.5 text-xs font-medium text-zinc-800 dark:bg-zinc-800 dark:text-zinc-300">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-layouts::app>
