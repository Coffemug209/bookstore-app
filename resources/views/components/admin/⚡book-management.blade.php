<?php

use App\Models\Book;
use App\Models\Category;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

/**
 * Admin Book Management component.
 * Allows admin to create, update, and delete books including cover image uploads.
 */
new #[Title('Book Management')] class extends Component {
    use WithPagination, WithFileUploads;

    public string $search = '';

    public ?int $editingBookId = null;

    public string $title = '';
    public string $synopsis = '';
    public string $author = '';
    public string $price = '';
    public ?int $categoryId = null;
    public $coverImage = null;

    public bool $showModal = false;

    public ?int $deletingBookId = null;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $this->reset(['editingBookId', 'title', 'synopsis', 'author', 'price', 'categoryId', 'coverImage']);
        $this->showModal = true;
    }

    public function openEditModal(int $bookId): void
    {
        $book = Book::findOrFail($bookId);
        $this->editingBookId = $book->id;
        $this->title = $book->title;
        $this->synopsis = $book->synopsis;
        $this->author = $book->author;
        $this->price = (string) $book->price;
        $this->categoryId = $book->category_id;
        $this->coverImage = null;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate([
            'title'       => 'required|string|max:255',
            'synopsis'    => 'required|string',
            'author'      => 'required|string|max:255',
            'price'       => 'required|numeric|min:0',
            'categoryId'  => 'nullable|exists:categories,id',
            'coverImage'  => 'nullable|image|max:2048',
        ]);

        $imagePath = null;
        if ($this->coverImage) {
            $imagePath = $this->coverImage->store('books', 'public');
        }

        $data = [
            'title'       => $this->title,
            'synopsis'    => $this->synopsis,
            'author'      => $this->author,
            'price'       => $this->price,
            'category_id' => $this->categoryId,
        ];

        if ($imagePath) {
            $data['cover_image'] = $imagePath;
        }

        if ($this->editingBookId) {
            Book::findOrFail($this->editingBookId)->update($data);
            Flux::toast(variant: 'success', text: __('Book updated successfully!'));
        } else {
            Book::create($data);
            Flux::toast(variant: 'success', text: __('Book added successfully!'));
        }

        $this->showModal = false;
        $this->resetPage();
    }

    public function confirmDelete(int $bookId): void
    {
        $this->deletingBookId = $bookId;
    }

    public function deleteBook(): void
    {
        if ($this->deletingBookId) {
            Book::findOrFail($this->deletingBookId)->delete();
            $this->deletingBookId = null;
            Flux::toast(variant: 'success', text: __('Book deleted successfully!'));
        }
    }

    #[Computed]
    public function books()
    {
        return Book::with('category')
            ->when($this->search, fn ($q) => $q->where(function ($query) {
                $query->where('title', 'like', "%{$this->search}%")
                    ->orWhere('author', 'like', "%{$this->search}%");
            }))
            ->latest()
            ->paginate(10);
    }

    #[Computed]
    public function categories()
    {
        return Category::orderBy('name')->get();
    }
};
?>

<div class="p-4">
    {{-- Page Header --}}
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Book Management') }}</flux:heading>
        <flux:subheading size="lg" class="mb-4">{{ __('Add, edit, and remove books from the catalog') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    {{-- Toolbar --}}
    <div class="mb-4 flex items-center justify-between gap-4">
        <flux:input
            wire:model.live.debounce.300ms="search"
            placeholder="Search by title or author..."
            icon="magnifying-glass"
            class="max-w-sm"
        />
        <flux:button variant="primary" wire:click="openCreateModal" icon="plus">
            Add Book
        </flux:button>
    </div>

    {{-- Books Table --}}
    <div class="overflow-x-auto rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
        <table class="w-full text-left text-sm text-zinc-600 dark:text-zinc-300">
            <thead class="border-b border-zinc-200 bg-zinc-50 text-xs uppercase text-zinc-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-400">
                <tr>
                    <th class="px-4 py-3">Cover</th>
                    <th class="px-4 py-3">Title</th>
                    <th class="px-4 py-3">Author</th>
                    <th class="px-4 py-3">Category</th>
                    <th class="px-4 py-3">Price</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                @forelse($this->books as $book)
                    <tr wire:key="book-{{ $book->id }}" class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                        <td class="px-4 py-3">
                            <img src="{{ $book->coverImageUrl() }}" alt="{{ $book->title }}" class="h-14 w-10 rounded object-cover shadow">
                        </td>
                        <td class="px-4 py-3 font-semibold text-zinc-900 dark:text-white">{{ $book->title }}</td>
                        <td class="px-4 py-3">{{ $book->author }}</td>
                        <td class="px-4 py-3">
                            @if($book->category)
                                <span class="inline-flex rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-semibold text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                                    {{ $book->category->name }}
                                </span>
                            @else
                                <span class="text-zinc-400">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 font-bold text-zinc-900 dark:text-white">Rp {{ number_format($book->price, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <flux:button size="sm" wire:click="openEditModal({{ $book->id }})" icon="pencil">Edit</flux:button>
                                <flux:button size="sm" variant="danger" wire:click="confirmDelete({{ $book->id }})" icon="trash">Delete</flux:button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-zinc-500">
                            No books found. Add your first book!
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $this->books->links() }}
    </div>

    {{-- Create/Edit Modal --}}
    <flux:modal wire:model="showModal" class="md:w-2xl">
        <form wire:submit="save">
            <div class="space-y-5 p-1">
                <flux:heading size="lg">
                    {{ $editingBookId ? 'Edit Book' : 'Add New Book' }}
                </flux:heading>

                <flux:field>
                    <flux:label>Title</flux:label>
                    <flux:input wire:model="title" placeholder="Book title" />
                    <flux:error name="title" />
                </flux:field>

                <flux:field>
                    <flux:label>Author</flux:label>
                    <flux:input wire:model="author" placeholder="Author name" />
                    <flux:error name="author" />
                </flux:field>

                <flux:field>
                    <flux:label>Category</flux:label>
                    <flux:select wire:model="categoryId" placeholder="Select category...">
                        <flux:select.option :value="null">— No Category —</flux:select.option>
                        @foreach($this->categories as $category)
                            <flux:select.option :value="$category->id">{{ $category->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="categoryId" />
                </flux:field>

                <flux:field>
                    <flux:label>Price (Rp)</flux:label>
                    <flux:input wire:model="price" type="number" min="0" placeholder="0" />
                    <flux:error name="price" />
                </flux:field>

                <flux:field>
                    <flux:label>Synopsis</flux:label>
                    <flux:textarea wire:model="synopsis" rows="4" placeholder="Book synopsis..." />
                    <flux:error name="synopsis" />
                </flux:field>

                <flux:field>
                    <flux:label>Cover Image</flux:label>
                    <input type="file" wire:model="coverImage" accept="image/*" class="block w-full text-sm text-zinc-500 file:mr-4 file:rounded file:border-0 file:bg-blue-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-blue-700 hover:file:bg-blue-100">
                    @if($coverImage)
                        <img src="{{ $coverImage->temporaryUrl() }}" class="mt-2 h-24 rounded object-cover">
                    @endif
                    <flux:error name="coverImage" />
                </flux:field>

                <div class="flex justify-end gap-2">
                    <flux:button wire:click="$set('showModal', false)" variant="ghost">Cancel</flux:button>
                    <flux:button type="submit" variant="primary">
                        {{ $editingBookId ? 'Update Book' : 'Save Book' }}
                    </flux:button>
                </div>
            </div>
        </form>
    </flux:modal>

    {{-- Delete Confirmation Modal --}}
    <flux:modal wire:model="deletingBookId" class="max-w-sm">
        <div class="space-y-4 p-1">
            <flux:heading size="lg">Delete Book?</flux:heading>
            <flux:text>This action cannot be undone. The book will be permanently removed.</flux:text>
            <div class="flex justify-end gap-2">
                <flux:button wire:click="$set('deletingBookId', null)" variant="ghost">Cancel</flux:button>
                <flux:button wire:click="deleteBook" variant="danger">Delete</flux:button>
            </div>
        </div>
    </flux:modal>
</div>