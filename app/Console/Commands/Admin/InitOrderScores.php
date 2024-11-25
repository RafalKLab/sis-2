<?php

namespace App\Console\Commands\Admin;

use App\Business\Order\Score\OrderScoreCalculator;
use App\Models\Order\Order;
use Illuminate\Console\Command;

class InitOrderScores extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:init-order-scores';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sets scores for all orders';

    public function handle()
    {
        (new OrderScoreCalculator())->calculateInitScores();

        $this->info('Order scores were updated!');
    }
}
