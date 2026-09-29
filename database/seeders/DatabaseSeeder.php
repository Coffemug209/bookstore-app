<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Seed the application database with initial data including admin user,
 * sample categories, and sample books.
 */
class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@bookstore.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Create regular user
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'user@bookstore.com',
            'role' => 'user',
        ]);

        // Create sample categories
        $categories = [
            ['name' => 'Fiction',     'description' => 'Novels, short stories, and other fictional works'],
            ['name' => 'Non-Fiction', 'description' => 'Factual books including biographies, history, and science'],
            ['name' => 'Technology',  'description' => 'Programming, IT, and technology books'],
            ['name' => 'Business',    'description' => 'Business, entrepreneurship, and finance books'],
            ['name' => 'Self-Help',   'description' => 'Personal development and motivational books'],
        ];

        foreach ($categories as $categoryData) {
            Category::create($categoryData);
        }

        // Create sample books
        $books = [
            [
                'category_id' => 1,
                'title' => 'The Great Gatsby',
                'author' => 'F. Scott Fitzgerald',
                'synopsis' => 'A story of the fabulously wealthy Jay Gatsby and his love for the beautiful Daisy Buchanan.',
                'price' => 95000,
                'cover_image' => null,
            ],
            [
                'category_id' => 3,
                'title' => 'Clean Code',
                'author' => 'Robert C. Martin',
                'synopsis' => 'A handbook of agile software craftsmanship for writing clean, maintainable code.',
                'price' => 225000,
                'cover_image' => null,
            ],
            [
                'category_id' => 4,
                'title' => 'Zero to One',
                'author' => 'Peter Thiel',
                'synopsis' => 'Notes on startups, or how to build the future by PayPal co-founder Peter Thiel.',
                'price' => 145000,
                'cover_image' => null,
            ],
            [
                'category_id' => 5,
                'title' => 'Atomic Habits',
                'author' => 'James Clear',
                'synopsis' => 'An easy and proven way to build good habits and break bad ones.',
                'price' => 165000,
                'cover_image' => null,
            ],
            [
                'category_id' => 2,
                'title' => 'Sapiens',
                'author' => 'Yuval Noah Harari',
                'synopsis' => 'A brief history of humankind from the Stone Age to the 21st century.',
                'price' => 185000,
                'cover_image' => null,
            ],
            [
                'category_id' => 1,
                'title' => 'To Kill a Mockingbird',
                'author' => 'Harper Lee',
                'synopsis' => 'The story of young Scout Finch and her father Atticus, a lawyer who defends a Black man accused of rape.',
                'price' => 110000,
                'cover_image' => null,
            ],
        ];

        foreach ($books as $bookData) {
            Book::create($bookData);
        }
    }
}
