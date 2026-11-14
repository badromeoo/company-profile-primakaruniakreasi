<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'email_verified_at' => now(),
                'password' => 'password',
            ],
        );

        $admin = User::query()->firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrator',
                'email_verified_at' => now(),
                'password' => 'password',
            ],
        );

        $admin->forceFill(['is_admin' => true])->save();

        $this->call([
            HomeContentSeeder::class,
            CategorySeeder::class,
            PostSeeder::class,
        ]);
    }
}
