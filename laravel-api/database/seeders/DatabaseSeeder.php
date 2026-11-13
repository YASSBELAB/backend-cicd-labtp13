<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Client;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::firstOrCreate(['name' => 'ADMIN']);
        $userRole = Role::firstOrCreate(['name' => 'USER']);

        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
            ]
        );
        $admin->roles()->syncWithoutDetaching([$adminRole->id]);

        $normalUser = User::firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Normal User',
                'password' => Hash::make('password'),
            ]
        );
        $normalUser->roles()->syncWithoutDetaching([$userRole->id]);

        Category::factory(5)->create();

        Product::factory(20)->create();

        Client::factory(10)->create();

        Order::factory(15)->create();

        OrderItem::factory(30)->create();
    }
}
