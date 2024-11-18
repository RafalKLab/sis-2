<?php

namespace App\Console\Commands\Admin;

use App\Models\Order\OrderData;
use App\Service\TableService;
use Illuminate\Console\Command;
use shared\ConfigDefaultInterface;

class CleanInvalidOrderData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clean-invalid-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create invalid order data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $invalidTerms = [
            'Važiuoja',
        ];

        $invalidTarget = [
            ConfigDefaultInterface::FIELD_TYPE_SELECT_STATUS,
        ];

        $response = '';

        foreach ($invalidTarget as $targetField) {
            $targetFieldId = TableService::getFieldByType($targetField)->id;

            $invalidEntries = OrderData::where('field_id', $targetFieldId)->whereIn('value', $invalidTerms)->get();

            foreach ($invalidEntries as $entry) {
                $entry->value = '';
                $entry->save();

                $response .= sprintf('%s, ', $entry->order->getKeyField());
            }
        }

        $this->info(sprintf('Invalid values have been reset: %s', $response));
    }
}
