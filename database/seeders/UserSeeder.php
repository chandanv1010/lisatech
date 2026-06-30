<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $catalogue = DB::table('user_catalogues')->where('name', 'Administrator')->first();
        if (!$catalogue) {
            $catalogueId = DB::table('user_catalogues')->insertGetId([
                'name' => 'Administrator',
                'description' => 'Administrator account group',
                'publish' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $catalogueId = $catalogue->id;
        }

        $admin = User::firstOrNew(['email' => 'admin@gmail.com']);
        if (!$admin->exists) {
            $admin->fill([
                'name' => 'Administrator',
                'password' => Hash::make('admin@gmail.com'),
                'user_catalogue_id' => $catalogueId,
                'publish' => 1,
            ])->save();
        } else {
            $admin->forceFill([
                'name' => 'Administrator',
                'password' => Hash::make('admin@gmail.com'),
                'user_catalogue_id' => $admin->user_catalogue_id ?: $catalogueId,
                'publish' => 1,
            ])->save();
        }
    }
}
