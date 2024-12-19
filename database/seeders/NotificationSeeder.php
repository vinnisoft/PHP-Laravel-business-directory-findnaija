<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\AppNotification;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $notifications = [
            ['type' => 'mail', 'name' => 'Recieve emails from Findnaija'],
            ['type' => 'mail', 'name' => 'Direct Messages'],
            ['type' => 'mail', 'name' => 'Status'],
            ['type' => 'mail', 'name' => 'Businesses You Might Like'],
            ['type' => 'fcm', 'name' => 'Check Ins'],
            ['type' => 'fcm', 'name' => 'Community Chat'],
            ['type' => 'fcm', 'name' => 'Direct Messages'],
            ['type' => 'fcm', 'name' => 'Suggestions'],
            ['type' => 'fcm', 'name' => 'Reviews'],
        ];
        foreach ($notifications as $notification) {
            AppNotification::firstOrCreate($notification);
        }
    }
}
