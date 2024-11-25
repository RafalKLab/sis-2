<?php

namespace App\Console\Commands\Admin;

use App\Models\Table\TableField;
use Illuminate\Console\Command;
use shared\ConfigDefaultInterface;

class DeleteTableFields extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:delete-table-fields';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deletes selected table fields';

    public function handle()
    {
        $targetFields = [
            ConfigDefaultInterface::FIELD_ITEM_LOAD_DATE_FROM_WAREHOUSE,
            ConfigDefaultInterface::FIELD_ITEM_DELIVERY_DATE_TO_BUYER,
        ];

        try {

            TableField::whereIn('type', $targetFields)->delete();

            $this->info('Fields deleted');
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
        }
    }
}
