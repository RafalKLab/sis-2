<?php

namespace App\Business;

use App\Business\ActivityLog\Manager\ActivityLogManager;
use App\Business\ActivityLog\Transfer\ActivityLogTransferObject;
use App\Business\ActivityLog\Writer\ActivityLogMysqlWriter;
use App\Business\Table\Config\TableConfig;
use App\Business\Table\Manager\TableManager;
use App\Business\Table\Reader\AdminTableReader;
use App\Business\Table\Reader\TableReaderInterface;

class BusinessFactory
{
    public function createActivityLogManager(): ActivityLogManager
    {
        return new ActivityLogManager(
            $this->createActivityLogMysqlWriter()
        );
    }

    public function createActivityLogMysqlWriter(): ActivityLogMysqlWriter
    {
        return new ActivityLogMysqlWriter();
    }

    public function getActivityLogTransferObject(): ActivityLogTransferObject
    {
        return new ActivityLogTransferObject();
    }

    public function createTableManager(): TableManager
    {
        //TODO: Implement for user with different fields
//        return new TableManager(
//            $this->createTableConfig()
//        );
    }

    public function createTableManagerAdmin()
    {
        return new TableManager(
            $this->createTableConfig(),
            $this->createAdminTableReader(),
        );
    }

    private function createTableConfig(): TableConfig
    {
        return new TableConfig();
    }

    private function createAdminTableReader(): TableReaderInterface
    {
        return new AdminTableReader();
    }
}
