<?php

namespace App\Business;

use App\Business\ActivityLog\Manager\ActivityLogManager;
use App\Business\ActivityLog\Transfer\ActivityLogTransferObject;
use App\Business\ActivityLog\Writer\ActivityLogMysqlWriter;
use App\Business\Customer\Manager\CustomerManager;
use App\Business\Order\Calculator\OrderDataCalculator;
use App\Business\Order\Manager\OrderManager;
use App\Business\Statistics\Manager\StatisticsManager;
use App\Business\Table\Config\ItemsTableConfig;
use App\Business\Table\Config\TableConfig;
use App\Business\Table\Manager\TableManager;
use App\Business\Table\Reader\AdminTableReader;
use App\Business\Table\Reader\UserTableReader;
use App\Business\Warehouse\Manager\WarehouseManager;

class BusinessFactory
{
    public function createOrderDataCalculator(): OrderDataCalculator
    {
        return new OrderDataCalculator();
    }

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

    public function createItemsTableManagerAdmin(): TableManager
    {
        return new TableManager(
            $this->createItemsTableConfig(),
            $this->createAdminTableReader(),
        );
    }

    public function createOrderManager(): OrderManager
    {
        return new OrderManager();
    }

    public function createCustomerManager(): CustomerManager
    {
        return new CustomerManager();
    }

    public function createStatisticsManager(): StatisticsManager
    {
        return new StatisticsManager();
    }

    public function createWarehouseManager(): WarehouseManager
    {
        return new WarehouseManager();
    }

    private function createTableConfig(): TableConfig
    {
        return new TableConfig();
    }

    private function createItemsTableConfig(): ItemsTableConfig
    {
        return new ItemsTableConfig();
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
