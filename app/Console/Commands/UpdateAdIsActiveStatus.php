<?php

namespace App\Console\Commands;

use App\Models\Ad;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateAdIsActiveStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-ad-is-active-status';

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
        $activeAds = Ad::where(['status' => 'duration', 'is_active' => 1])->where('end_date', '<=', now())->get();
        foreach ($activeAds as $ad) {
            $ad->update(['is_active' => 0]);
        }
        $this->info('Ad has been successfully stopped');
    }
}
