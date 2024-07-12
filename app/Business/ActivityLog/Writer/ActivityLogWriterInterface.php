<?php

namespace App\Business\ActivityLog\Writer;

use App\Business\ActivityLog\Transfer\ActivityLogTransferObject;

interface ActivityLogWriterInterface
{
    /**
     * @param ActivityLogTransferObject $transferObject
     *
     * @return void
     */
    public function write(ActivityLogTransferObject $transferObject): void;
}
