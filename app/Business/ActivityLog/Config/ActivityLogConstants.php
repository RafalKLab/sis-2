<?php

namespace App\Business\ActivityLog\Config;

interface ActivityLogConstants
{
    public const INFO_LOG = 'Info';
    public const WARNING_LOG = 'Warning';
    public const DANGER_LOG = 'Danger';

    public const ACTION_ADD = 'Added';
    public const ACTION_UPDATE = 'updated to';
    public const ACTION_DELETE = 'Deleted';
}
