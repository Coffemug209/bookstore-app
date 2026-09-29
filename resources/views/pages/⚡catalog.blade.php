<?php

use App\Models\Book;
use App\Models\CartItem;
use App\Models\Category;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Public Book Catalog page with search across title, author, and category filtering.
 * Includes Add to Cart functionality with persistent user cart.
 */
new #[Layout('layouts.public')] #[Title('Book Catalog - BookStore')] class extends Component {
    use WithPagination;

    /** @var string Search query matching title or author */
    #[Url(as: 'q')]
    public string $search = '';

    /** @var int|null Category filter */
    #[Url(as: 'category')]
    public ?int $categoryId = null;

    /**
     * Reset pagination when searching.
     */
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Reset pagination when changing category.
     */
    public function updatedCategoryId(): void
    {
        $this->resetPage();
    }

    /**
     * Add the specified book to the user's shopping cart.
     */
    public function addToCart(int $bookId): void
    {
        if (! auth()->check()) {
            $this->redirectRoute('login', navigate: true);

            return;
        }

        $cartItem = CartItem::firstOrCreate(
            ['user_id' => auth()->id(), 'book_id' => $bookId],
            ['quantity' => 0]
        );

        $cartItem->increment('quantity');

        Flux::toast(variant: 'success', text: __('Book added to your shopping cart!'));
    }

    #[Computed]
    public function books()
    {
        return Book::with('category')
            ->when($this->search, fn ($q) => $q->where(function ($query) {
                $query->where('title', 'like', "%{$this->search}%")
                    ->orWhere('author', 'like', "%{$this->search}%")
                    ->orWhere('synopsis', 'like', "%{$this->search}%");
            }))
            ->when($this->categoryId, fn ($q) => $q->where('category_id', $this->categoryId))
            ->latest()
            ->paginate(12);
    }

    #[Computed]
    public function categories()
    {
        return Category::withCount('books')->orderBy('name')->get();
    }
};
?>

<div>
    {{-- Header --}}
    <section class="py-12 text-white">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-extrabold sm:text-4xl">Book Catalog</h1>
            <p class="mt-2 text-blue-100 text-sm sm:text-base">
                Discover our extensive collection of books. Filter by category or search by title and author.
            </p>
        </div>
    </section>

    <section class="py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            {{-- Search & Filter Controls --}}
            <div class="mb-8 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div class="w-full md:max-w-md">
                    <flux:input
                        wire:model.live.debounce.300ms="search"
                        placeholder="Search by title, author, or synopsis..."
                        icon="magnifying-glass"
                        clearable
                    />
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <button
                        wire:click="$set('categoryId', null)"
                        class="rounded-xl px-4 py-2 text-xs font-bold transition {{ is_null($categoryId) ? 'bg-blue-600 text-white shadow' : 'bg-zinc-100 text-zinc-700 hover:bg-zinc-200 dark:bg-zinc-800 dark:text-zinc-300' }}"
                    >
                        All Categories
                    </button>
                    @foreach($this->categories as $cat)
                        <button
                            wire:click="$set('categoryId', {{ $cat->id }})"
                            class="rounded-xl px-4 py-2 text-xs font-bold transition {{ $categoryId === $cat->id ? 'bg-blue-600 text-white shadow' : 'bg-zinc-100 text-zinc-700 hover:bg-zinc-200 dark:bg-zinc-800 dark:text-zinc-300' }}"
                        >
                            {{ $cat->name }} ({{ $cat->books_count }})
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Results count & Active filters info --}}
            <div class="mb-6 flex items-center justify-between text-xs text-zinc-500">
                <div>
                    Showing <strong>{{ $this->books->firstItem() ?? 0 }}</strong> to <strong>{{ $this->books->lastItem() ?? 0 }}</strong> of <strong>{{ $this->books->total() }}</strong> books
                    @if($search)
                        &bull; Matching keyword: <span class="font-semibold text-zinc-800 dark:text-zinc-200">"{{ $search }}"</span>
                    @endif
                </div>
            </div>

            {{-- Books Grid --}}
            @if($this->books->isEmpty())
                <div class="rounded-2xl border border-dashed border-zinc-300 p-12 text-center dark:border-zinc-700">
                    <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-zinc-100 dark:bg-zinc-800 text-zinc-400">
                        <flux:icon.book-open class="size-8" />
                    </div>
                    <h3 class="text-lg font-bold text-zinc-900 dark:text-white">No Books Found</h3>
                    <p class="mt-1 text-sm text-zinc-500">Try adjusting your search terms or clearing the category filter.</p>
                    <div class="mt-4">
                        <flux:button wire:click="$set('search', ''); $set('categoryId', null);" size="sm">
                            Clear Filters
                        </flux:button>
                    </div>
                </div>
            @else
                <div class="grid grid-cols-2 gap-5 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6">
                    @foreach($this->books as $book)
                        <div wire:key="catalog-book-{{ $book->id }}" class="group flex flex-col justify-between rounded-xl border border-zinc-200 bg-white p-3.5 shadow-sm transition hover:shadow-md dark:border-zinc-700 dark:bg-zinc-800">
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
                                <h3 class="line-clamp-2 text-sm font-bold text-zinc-900 dark:text-white" title="{{ $book->title }}">{{ $book->title }}</h3>
                                <p class="mt-1 text-xs text-zinc-500 line-clamp-1">{{ $book->author }}</p>
                                <p class="mt-2 line-clamp-2 text-[11px] text-zinc-400">{{ $book->synopsis }}</p>
                            </div>
                            <div class="mt-4 pt-3 border-t border-zinc-100 dark:border-zinc-700">
                                <div class="mb-2">
                                    <span class="text-sm font-extrabold text-blue-600 dark:text-blue-400">
                                        Rp {{ number_format($book->price, 0, ',', '.') }}
                                    </span>
                                </div>
                                <button
                                    wire:click="addToCart({{ $book->id }})"
                                    class="w-full flex items-center justify-center gap-1.5 rounded-lg bg-blue-600 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-blue-700 active:scale-95"
                                >
                                    <flux:icon.shopping-cart class="size-3.5" />
                                    <span>Add to Cart</span>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-10">
                    {{ $this->books->links() }}
                </div>
            @endif
        </div>
    </section>
</div>