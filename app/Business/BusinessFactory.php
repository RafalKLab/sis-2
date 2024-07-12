<?php

namespace App\Business;

use App\Business\ActivityLog\Manager\ActivityLogManager;
use App\Business\ActivityLog\Transfer\ActivityLogTransferObject;
use App\Business\ActivityLog\Writer\ActivityLogMysqlWriter;

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
}
