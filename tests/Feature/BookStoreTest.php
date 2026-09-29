<?php

use App\Models\Book;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Contact;
use App\Models\Order;
use App\Models\User;

test('public pages are accessible to guests', function () {
    $this->get(route('home'))->assertSuccessful();
    $this->get(route('about'))->assertSuccessful();
    $this->get(route('contact'))->assertSuccessful();
    $this->get(route('catalog'))->assertSuccessful();
});

test('users can search books by title or author in catalog', function () {
    $category = Category::create(['name' => 'Tech Test', 'description' => 'Tech']);
    $book = Book::create([
        'title' => 'Mastering Laravel Testing',
        'author' => 'Taylor Otwell',
        'synopsis' => 'Great testing guide',
        'price' => 150000,
        'category_id' => $category->id,
    ]);

    $this->get(route('catalog', ['q' => 'Mastering']))
        ->assertSuccessful()
        ->assertSee('Mastering Laravel Testing');
});

test('guests can submit contact inquiry to admin', function () {
    Livewire::test('pages::contact')
        ->set('name', 'Budi Santoso')
        ->set('email', 'budi@example.com')
        ->set('subject', 'Book Availability')
        ->set('message', 'Do you have books on system architecture in stock?')
        ->call('submit')
        ->assertHasNoErrors()
        ->assertSet('sent', true);

    expect(Contact::where('email', 'budi@example.com')->exists())->toBeTrue();
});

test('authenticated users can add books to cart and checkout via COD', function () {
    $user = User::factory()->create(['role' => 'user']);
    $category = Category::create(['name' => 'Fiction', 'description' => 'Fiction']);
    $book = Book::create([
        'title' => 'Adventure Novel',
        'author' => 'Jane Doe',
        'synopsis' => 'An exciting journey.',
        'price' => 75000,
        'category_id' => $category->id,
    ]);

    // Add to cart
    Livewire::actingAs($user)
        ->test('pages::catalog')
        ->call('addToCart', $book->id);

    expect(CartItem::where('user_id', $user->id)->where('book_id', $book->id)->value('quantity'))->toBe(1);

    // Checkout via COD
    Livewire::actingAs($user)
        ->test('pages::checkout')
        ->set('shippingAddress', 'Jl. Merdeka No. 45, Jakarta')
        ->set('phone', '08123456789')
        ->set('notes', 'Please deliver in afternoon')
        ->call('placeOrder')
        ->assertHasNoErrors()
        ->assertSet('orderPlaced', true);

    // Verify order created and cart cleared
    $order = Order::where('user_id', $user->id)->first();
    expect($order)->not->toBeNull()
        ->and($order->payment_method)->toBe('cod')
        ->and((float) $order->total_amount)->toBe(75000.0)
        ->and($order->items()->count())->toBe(1)
        ->and(CartItem::where('user_id', $user->id)->count())->toBe(0);
});

test('regular users cannot access admin management routes', function () {
    $user = User::factory()->create(['role' => 'user']);

    $this->actingAs($user)
        ->get(route('admin.books'))
        ->assertForbidden();

    $this->actingAs($user)
        ->get(route('admin.categories'))
        ->assertForbidden();

    $this->actingAs($user)
        ->get(route('admin.users'))
        ->assertForbidden();

    $this->actingAs($user)
        ->get(route('admin.orders'))
        ->assertForbidden();
});

test('admin can access management routes and perform CRUD', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)
        ->get(route('admin.books'))
        ->assertSuccessful();

    // Category CRUD
    Livewire::actingAs($admin)
        ->test('admin.category-management')
        ->set('name', 'Science Fiction')
        ->set('description', 'Sci-fi novels')
        ->call('save')
        ->assertHasNoErrors();

    $category = Category::where('name', 'Science Fiction')->first();
    expect($category)->not->toBeNull();

    // Book CRUD
    Livewire::actingAs($admin)
        ->test('admin.book-management')
        ->set('title', 'Dune Chronicles')
        ->set('author', 'Frank Herbert')
        ->set('synopsis', 'Epic sci-fi series set on Arrakis.')
        ->set('price', 199000)
        ->set('categoryId', $category->id)
        ->call('save')
        ->assertHasNoErrors();

    expect(Book::where('title', 'Dune Chronicles')->exists())->toBeTrue();
});
