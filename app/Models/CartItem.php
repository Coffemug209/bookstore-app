<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Represents an item in a user's shopping cart (persistent).
 *
 * @property int $id
 * @property int $user_id
 * @property int $book_id
 * @property int $quantity
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class CartItem extends Model
{
    protected $fillable = ['user_id', 'book_id', 'quantity'];

    /**
     * Get the user who owns this cart item.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the book associated with this cart item.
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }
}
