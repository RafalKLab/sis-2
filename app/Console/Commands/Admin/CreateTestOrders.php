<?php

namespace App\Console\Commands\Admin;

use App\Models\Order\Order;
use Illuminate\Console\Command;

class CreateTestOrders extends Command
{
    protected const AMOUNT = 250;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-orders';

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
        $this->createTestOrders();
        $this->info('good');
    }

    private function createTestOrders()
    {
        for ($i=0; $i<self::AMOUNT; $i++) {
            Order::create();
        }
    }
}
