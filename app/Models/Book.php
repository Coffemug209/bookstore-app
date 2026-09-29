<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * Represents a book in the bookstore catalog.
 *
 * @property int $id
 * @property int|null $category_id
 * @property string $title
 * @property string $synopsis
 * @property string $author
 * @property float $price
 * @property string|null $cover_image
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Book extends Model
{
    protected $fillable = ['category_id', 'title', 'synopsis', 'author', 'price', 'cover_image'];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
        ];
    }

    /**
     * Get the category this book belongs to.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the cart items associated with this book.
     */
    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Get the order items associated with this book.
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the URL for the cover image or a placeholder if none exists.
     */
    public function coverImageUrl(): string
    {
        if ($this->cover_image) {
            return asset('storage/'.$this->cover_image);
        }

        return 'https://placehold.co/400x560/e2e8f0/64748b?text=No+Cover';
    }
}
