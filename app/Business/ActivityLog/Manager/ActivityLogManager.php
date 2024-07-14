<?php

namespace App\Business\ActivityLog\Manager;

use App\Business\ActivityLog\Transfer\ActivityLogTransferObject;
use App\Business\ActivityLog\Writer\ActivityLogWriterInterface;

class ActivityLogManager
{
    /**
     * @var ActivityLogWriterInterface
     */
    protected ActivityLogWriterInterface $writer;

    /**
     * @param ActivityLogWriterInterface $writer
     */
    public function __construct(ActivityLogWriterInterface $writer)
    {
        $this->writer = $writer;
    }

    public function log(ActivityLogTransferObject $transferObject): void
    {
        $this->writer->write($transferObject);
    }
}
