<?php

use App\Models\Category;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Admin Category Management component.
 * Allows admin to create, edit, and delete book categories.
 */
new #[Title('Category Management')] class extends Component {
    use WithPagination;

    public string $search = '';

    public ?int $editingCategoryId = null;

    public string $name = '';

    public string $description = '';

    public bool $showModal = false;

    public ?int $deletingCategoryId = null;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $this->reset(['editingCategoryId', 'name', 'description']);
        $this->showModal = true;
    }

    public function openEditModal(int $categoryId): void
    {
        $category = Category::findOrFail($categoryId);
        $this->editingCategoryId = $category->id;
        $this->name = $category->name;
        $this->description = $category->description ?? '';
        $this->showModal = true;
    }


    public function save(): void
    {
        $this->validate([
            'name'        => 'required|string|max:255|unique:categories,name'.($this->editingCategoryId ? ','.$this->editingCategoryId : ''),
            'description' => 'nullable|string|max:500',
        ]);

        if ($this->editingCategoryId) {
            Category::findOrFail($this->editingCategoryId)->update([
                'name'        => $this->name,
                'description' => $this->description,
            ]);
            Flux::toast(variant: 'success', text: __('Category updated successfully!'));
        } else {
            Category::create([
                'name'        => $this->name,
                'description' => $this->description,
            ]);
            Flux::toast(variant: 'success', text: __('Category added successfully!'));
        }

        $this->showModal = false;
        $this->resetPage();
    }

 
    public function confirmDelete(int $categoryId): void
    {
        $this->deletingCategoryId = $categoryId;
    }

    public function deleteCategory(): void
    {
        if ($this->deletingCategoryId) {
            Category::findOrFail($this->deletingCategoryId)->delete();
            $this->deletingCategoryId = null;
            Flux::toast(variant: 'success', text: __('Category deleted successfully!'));
        }
    }

    #[Computed]
    public function categories()
    {
        return Category::withCount('books')
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->latest()
            ->paginate(10);
    }
};
?>

<div class="p-4">
    {{-- Page Header --}}
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Category Management') }}</flux:heading>
        <flux:subheading size="lg" class="mb-4">{{ __('Manage book categories for the catalog') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    {{-- Toolbar --}}
    <div class="mb-4 flex items-center justify-between gap-4">
        <flux:input
            wire:model.live.debounce.300ms="search"
            placeholder="Search categories..."
            icon="magnifying-glass"
            class="max-w-sm"
        />
        <flux:button variant="primary" wire:click="openCreateModal" icon="plus">
            Add Category
        </flux:button>
    </div>

    {{-- Categories Table --}}
    <div class="overflow-x-auto rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
        <table class="w-full text-left text-sm text-zinc-600 dark:text-zinc-300">
            <thead class="border-b border-zinc-200 bg-zinc-50 text-xs uppercase text-zinc-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-400">
                <tr>
                    <th class="px-4 py-3">Name</th>
                    <th class="px-4 py-3">Description</th>
                    <th class="px-4 py-3">Books Count</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                @forelse($this->categories as $category)
                    <tr wire:key="cat-{{ $category->id }}" class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                        <td class="px-4 py-3 font-semibold text-zinc-900 dark:text-white">{{ $category->name }}</td>
                        <td class="px-4 py-3 text-zinc-500">{{ $category->description ?: '—' }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-semibold text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                                {{ $category->books_count }} books
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <flux:button size="sm" wire:click="openEditModal({{ $category->id }})" icon="pencil">Edit</flux:button>
                                <flux:button size="sm" variant="danger" wire:click="confirmDelete({{ $category->id }})" icon="trash">Delete</flux:button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-zinc-500">
                            No categories yet. Add your first category!
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $this->categories->links() }}
    </div>

    {{-- Create/Edit Modal --}}
    <flux:modal wire:model="showModal" class="max-w-md">
        <form wire:submit="save">
            <div class="space-y-5 p-1">
                <flux:heading size="lg">
                    {{ $editingCategoryId ? 'Edit Category' : 'Add New Category' }}
                </flux:heading>

                <flux:field>
                    <flux:label>Category Name</flux:label>
                    <flux:input wire:model="name" placeholder="e.g. Science Fiction" />
                    <flux:error name="name" />
                </flux:field>

                <flux:field>
                    <flux:label>Description <span class="text-zinc-400">(optional)</span></flux:label>
                    <flux:textarea wire:model="description" rows="3" placeholder="Brief description of this category..." />
                    <flux:error name="description" />
                </flux:field>

                <div class="flex justify-end gap-2">
                    <flux:button wire:click="$set('showModal', false)" variant="ghost">Cancel</flux:button>
                    <flux:button type="submit" variant="primary">
                        {{ $editingCategoryId ? 'Update' : 'Save' }}
                    </flux:button>
                </div>
            </div>
        </form>
    </flux:modal>

    {{-- Delete Confirmation Modal --}}
    <flux:modal wire:model="deletingCategoryId" class="max-w-sm">
        <div class="space-y-4 p-1">
            <flux:heading size="lg">Delete Category?</flux:heading>
            <flux:text>Books in this category will become uncategorized. This action cannot be undone.</flux:text>
            <div class="flex justify-end gap-2">
                <flux:button wire:click="$set('deletingCategoryId', null)" variant="ghost">Cancel</flux:button>
                <flux:button wire:click="deleteCategory" variant="danger">Delete</flux:button>
            </div>
        </div>
    </flux:modal>
</div>