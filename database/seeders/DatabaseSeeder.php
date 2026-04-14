<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::query()->updateOrCreate(
            ['username' => 'mochamad_nizar_palefi_maady'],
            [
                'name' => 'Mochamad Nizar Palevi Ma\'ady',
                'email' => 'akbar.saputro1301@gmail.com',
                'password' => Hash::make('mochamad_nizar_palefi_maady'),
            ]
        );

        if (! $admin->remember_token) {
            $admin->forceFill([
                'remember_token' => Str::random(60),
            ])->save();
        }

        // Data dosen dan kepangkatan versi SDM diisi lewat input manual atau import Excel.
    }
}
