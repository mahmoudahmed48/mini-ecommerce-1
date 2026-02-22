<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
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
            [
                'name' => 'Laptop 14',
                'slug' => 'laptop',
                'description' => 'description for laptop 14',
                'price' => '180',
                'category_id' => '3',
                'stock' => '2500',
                'image' => 'products/laptop14.jpg'
            ],
            [
                'name' => 'White T-Shirt',
                'slug' => 'white-t-shirt',
                'description' => 'description for white t-shirt',
                'price' => '25',
                'category_id' => '2',
                'stock' => '150',
                'image' => 'products/Wtshirt.jpg'
            ],
        ];

        foreach ($products as $product) 
        {
            Product::create($product);
        }

        User::create([
            'name' => 'Admin Top',
            'email' => 'admin@store.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'phone' => '01210646533',
            'address' => 'Egypt, Alexandria'
        ]);

        User::create([
            'name' => 'Nehal Muhammed',
            'email' => 'nehal@mail.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
            'phone' => '01144756948',
            'address' => 'Egypt, Alexandria'
        ]);


        $user = User::where('email', 'nehal@mail.com')->first();

        if ($user)
        {
            $cart = $user->getCart();

            $products = Product::limit(2)->get();

            foreach($products as $product)
            {
                $cart->addProduct($product, 2);
            }
        }

        $order = Order::create([
            'order_number' => 'ORD-2024-0001',
            'user_id' => $user->id,
            'status'  => 'pending',
            'total' => 615.95,
            'shipping_address' => 'Cairo Qism Alameeria',
            'phone' => '01250021051',
            'notes' => 'Please Call Before Come',
            'payment_method' => 'cash_on_delivery',
            'payment_status' => 'pending'
        ]);

        $order->items()->create([
            'product_id' => 1,
            'product_name' => 'Iphone 14',
            'product_price' => 299,
            'quantity' => 1,
            'total' => 299
        ]);

        $order->items()->create([
            'product_id' => 3,
            'product_name' => 'Laptop 14',
            'product_price' => 180,
            'quantity' => 1,
            'total' => 180
        ]);






        $this->command->info('Created Successfully ✅');


    }
}
