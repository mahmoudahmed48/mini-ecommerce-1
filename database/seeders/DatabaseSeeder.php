<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Electronics', 'slug' => 'electronics'],
            ['name' => 'Clothing', 'slug' => 'clothing'],
            ['name' => 'Phones', 'slug' => 'phones'],
            ['name' => 'Books', 'slug' => 'books'],
            ['name' => 'Home', 'slug' => 'home'],
        ];

        foreach($categories as $category)
        {
            Category::create($category);
        }

        $products = [
            [
                'name' => 'Iphone 14',
                'slug' => 'iphone14',
                'description' => 'description for iphone 14',
                'price' => '180',
                'category_id' => '3',
                'stock' => '2500',
                'image' => 'products/iphone14.jpg'
            ],
            [
                'name' => 'Black T-Shirt',
                'slug' => 'black-t-shirt',
                'description' => 'description for black t-shirt',
                'price' => '25',
                'category_id' => '2',
                'stock' => '150',
                'image' => 'products/Btshirt.jpg'
            ],
        ];

        foreach ($products as $product) 
        {
            Product::create($product);
        }

        $this->command->info('Created Successfully ✅');


    }
}
