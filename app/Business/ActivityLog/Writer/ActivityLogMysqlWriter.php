<?php

namespace App\Business\ActivityLog\Writer;

use App\Business\ActivityLog\Transfer\ActivityLogTransferObject;
use App\Models\Logs\ActivityLog;

class ActivityLogMysqlWriter implements ActivityLogWriterInterface
{

    /**
     * @param ActivityLogTransferObject $transferObject
     *
     * @return void
     */
    public function write(ActivityLogTransferObject $transferObject): void
    {
        ActivityLog::create([
            'user' => $transferObject->getUser(),
            'title' => $transferObject->getTitle(),
            'description' => $transferObject->getFormattedDescription()
        ]);
    }
}
