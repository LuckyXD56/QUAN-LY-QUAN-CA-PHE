<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\Table;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Admin User
        User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'), // password is 'password'
                'role' => 'admin',
            ]
        );

        // 2. Create Categories
        $catCoffee = Category::firstOrCreate(['name' => 'Cà phê'], ['description' => 'Các loại cà phê truyền thống và pha máy', 'sort_order' => 1]);
        $catTea = Category::firstOrCreate(['name' => 'Trà & Trà sữa'], ['description' => 'Trà trái cây, trà sữa các loại', 'sort_order' => 2]);
        $catFood = Category::firstOrCreate(['name' => 'Đồ ăn vặt'], ['description' => 'Bánh ngọt, đồ ăn nhẹ', 'sort_order' => 3]);

        // 3. Create Products
        Product::firstOrCreate(['name' => 'Cà phê sữa đá'], [
            'category_id' => $catCoffee->id,
            'price' => 25000,
            'description' => 'Cà phê pha phin truyền thống với sữa đặc',
            'is_available' => true
        ]);
        Product::firstOrCreate(['name' => 'Bạc xỉu'], [
            'category_id' => $catCoffee->id,
            'price' => 29000,
            'description' => 'Nhiều sữa, ít cà phê',
            'is_available' => true
        ]);
        Product::firstOrCreate(['name' => 'Trà đào cam sả'], [
            'category_id' => $catTea->id,
            'price' => 35000,
            'description' => 'Trà đào thanh mát với cam và sả',
            'is_available' => true
        ]);
        Product::firstOrCreate(['name' => 'Bánh sừng trâu'], [
            'category_id' => $catFood->id,
            'price' => 20000,
            'description' => 'Bánh croissant thơm bơ',
            'is_available' => true
        ]);

        // 4. Create Tables
        Table::firstOrCreate(['name' => 'Bàn 1'], ['qr_token' => Str::random(16), 'is_active' => true]);
        Table::firstOrCreate(['name' => 'Bàn 2'], ['qr_token' => Str::random(16), 'is_active' => true]);
        Table::firstOrCreate(['name' => 'Bàn VIP 1'], ['qr_token' => Str::random(16), 'is_active' => true]);
    }
}
