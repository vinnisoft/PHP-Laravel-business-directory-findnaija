<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Business;
use App\Notifications\BusinessRegistrationNotification;

class ExpiredBusinessRegistrationNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:expired-business-notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $exporedBusiness = Business::where('registration_expire_date', '!=', NULL)->whereDate('registration_expire_date', '<=', today())->select('id', 'name', 'user_id')->get();
        foreach ($exporedBusiness as $business) {
            $user = User::findOrFail($business->user_id);
            Business::where('id', $business->id)->update(['status' => '3']);
            if (isset($user->fcm_token)) {
                $user->notify(new BusinessRegistrationNotification($user->first_name, $business->name));
            }
        }
        $this->info('Notification had been sent!');
    }
}
