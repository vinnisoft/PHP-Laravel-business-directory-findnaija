<?php

namespace Database\Seeders;

use App\Models\AdGoal;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\AppNotification;

class AdGoalSeeder extends Seeder
{
    public function run(): void
    {
        $goals = [
            ['title' => 'More Exposure', 'description' => 'Invite more people to know you.'],
            ['title' => 'More Sales', 'description' => 'Get people to patronize your business more often.'],
            ['title' => 'More Website Visits', 'description' => 'Get people to visit your website and learn more'],
            ['title' => 'More Messages', 'description' => 'Chat with people interested in your business.'],
        ];
        foreach ($goals as $goal) {
            AdGoal::firstOrCreate($goal);
        }
    }
}
