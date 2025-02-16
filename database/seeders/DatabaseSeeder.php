<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\PointTransaction;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Create 5 Users
        User::factory(4)->create()->each(function ($user, $index) {
            if ($index === 0) {
                // Ensure the first user has a fixed email and role
                $user->update([
                    'email' => 'admin@admin.com',
                    'password' => Hash::make('password'), // Set a default password
                ]);
            }
        
            // Create 3 Customers for each User
            Customer::factory(3)->sequence(
                fn ($seq) => [
                    'business_id' => $user->id,
                    'email' => fake()->unique()->safeEmail(),
                    'phone' => fake()->phoneNumber(),
                    'total_points' => 0,
                    'name' => fake()->name(),
                ]
            )->create()->each(function ($customer) {
                // Create 5 Point Transactions for each Customer
                PointTransaction::factory(5)->create([
                    'customer_id' => $customer->id,
                    'type' => fake()->randomElement(['add', 'deduct']),
                    'points' => fake()->numberBetween(10, 500),
                ]);
            });
        });
        
    }
}
