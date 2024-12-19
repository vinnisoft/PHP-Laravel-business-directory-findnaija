<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Story;

class UpdateStoryStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:story-status';

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
        $storiesToUpdate = Story::where('status', '1')
            ->whereDate('created_at', '<=', now()
            ->subHours(24))
            ->get();
        foreach ($storiesToUpdate as $story) {
            $story->update(['status' => '0']);
        }
        $this->info('Story statuses updated successfully.');
    }
}
