<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            ['name' => 'Basic', 'price' => 0, 'saving' => 0, 'type' => 'basic', 'description' => 'Basic'],
            ['name' => 'Oga Plan', 'price' => 20, 'saving' => 0, 'type' => 'monthly', 'description' => 'Oga Plan'],
            ['name' => 'Odogwu Plan', 'price' => 200, 'saving' => 40, 'type' => 'yearly', 'description' => 'Odogwu Plan'],
        ];
        foreach ($plans as $plan) {
            if (!Plan::where('type', $plan['type'])->exists()) {
                Plan::create($plan);
            }
        }
    }
}
