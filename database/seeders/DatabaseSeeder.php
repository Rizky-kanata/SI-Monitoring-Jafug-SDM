<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::query()->updateOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Administrator',
                'email' => 'admin@example.com',
                'password' => 'admin',
            ]
        );

        if (! $admin->remember_token) {
            $admin->forceFill([
                'remember_token' => Str::random(60),
            ])->save();
        }

        $this->call([
            ProfilSeeder::class,
            KepangkatanSeeder::class,
        ]);
    }
}
