<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\VendorOrder; // your vendor order model
use Carbon\Carbon;

class MarkOrdersCompleted extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:mark-completed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Marks eligible vendor orders as completed based on return/exchange window';

    /**
     * Execute the console command.
     */
      public function handle()
    {
        $dryRun = $this->option('dry-run');
        $this->info("Starting vendor order completion check...");

        VendorOrder::where('is_completed', '!=', 'Yes')
            ->orderBy('id') // Ensures stable chunking
            ->chunk(500, function ($orders) use ($dryRun) {
                foreach ($orders as $order) {
                    if ($order->canBeCompleted()) {
                        if ($dryRun) {
                            $this->line("Would mark Order ID {$order->id} as completed.");
                        } else {
                            $order->update([
                                'is_completed' => 'Yes',
                                'completed_at' => now(),
                            ]);
                            $this->info("Order ID {$order->id} marked as completed.");
                        }
                    }
                }
            });

        $this->info($dryRun ? "Dry run finished!" : "Order completion process finished!");
    }
}
