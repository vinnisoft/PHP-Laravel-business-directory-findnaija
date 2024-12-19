<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        if (!User::where('email', 'admin.findnaija@yopmail.com')->exists()) {
            $admin = User::create([
                'first_name' => 'Admin',
                'last_name' => 'Findnaija',
                'email' => 'admin.findnaija@yopmail.com',
                'password' => Hash::make('12345678')
            ]);
            $admin->assignRole('admin');
            foreach (getAllRouteNames() as $route) {
                $admin->givePermissionTo($route);
            }
        }
        foreach (getAllRouteNames() as $route) {
            User::where('email', 'admin.findnaija@yopmail.com')->first()->givePermissionTo($route);
        }
    }
}
