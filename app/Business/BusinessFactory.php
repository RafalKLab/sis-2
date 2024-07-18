<?php

namespace App\Business;

use App\Business\ActivityLog\Manager\ActivityLogManager;
use App\Business\ActivityLog\Transfer\ActivityLogTransferObject;
use App\Business\ActivityLog\Writer\ActivityLogMysqlWriter;
use App\Business\Order\Manager\OrderManager;
use App\Business\Order\Reader\OrderReader;
use App\Business\Table\Config\TableConfig;
use App\Business\Table\Manager\TableManager;
use App\Business\Table\Reader\AdminTableReader;
use App\Business\Table\Reader\TableReaderInterface;
use App\Business\Table\Reader\UserTableReader;

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
        return new TableManager(
            $this->createTableConfig(),
            $this->createUserTableReader(),
        );
    }

    public function createTableManagerAdmin(): TableManager
    {
        return new TableManager(
            $this->createTableConfig(),
            $this->createAdminTableReader(),
        );
    }

    public function createOrderManager(): OrderManager
    {
        return new OrderManager();
    }

    private function createTableConfig(): TableConfig
    {
        return new TableConfig();
    }

    private function createAdminTableReader(): AdminTableReader
    {
        return new AdminTableReader();
    }

    private function createUserTableReader(): UserTableReader
    {
        return new UserTableReader();
    }
}
