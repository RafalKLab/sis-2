<?php

namespace App\Console\Commands\Admin;

use App\Business\BusinessFactory;
use App\Business\Table\Config\TableConfig;
use Illuminate\Console\Command;

class CreateInitTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-init-table';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create init orders table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        (new BusinessFactory())->createTableManager()->createInitTable();

        $this->info(sprintf('Table %s with fields created', TableConfig::MAIN_TABLE_NAME));
    }
}
