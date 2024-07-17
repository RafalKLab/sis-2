<?php

namespace App\Console\Commands;

use App\Models\MainTable;
use App\Models\Order\Order;
use App\Models\Orders;
use App\Models\Table\Table;
use Illuminate\Console\Command;

class testorders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:testorders';

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
        $mainTable = Table::where('name', 'Orders')->first();

//        $oder = Order::find(31);
        $oder = Order::create();

        $oder->children()->create();

//        for ($i = 0; $i < 5; $i++) {
//            $oder = Order::create();
//            foreach ($mainTable->fields as $field) {
//                $data = [
//                    'value' => $this->getRandomWord(),
//                    'field_id' => $field->id,
//                ];
//                $oder->data()->create($data);
//            }
//        }
    }

    private function getRandomWord() {
        // Array of predefined words
        $words = [
            'diamond', 'plastic', 'silicon', 'ceramic', 'granite', 'textile', 'leather',
            'acetate', 'alloyed', 'arsenic', 'asphalt', 'bismuth', 'boronik', 'calcium',
            'cementy', 'chromic', 'clayish', 'cobalty', 'coppery', 'crystal'
        ];

        // Pick a random word from the array
        $randomIndex = array_rand($words);
        return $words[$randomIndex];
    }
}
