<?php

namespace App\Business\ActivityLog\Config;

interface ActivityLogConstants
{
    public const INFO_LOG = 'Info';
    public const WARNING_LOG = 'Warning';
    public const DANGER_LOG = 'Danger';
    public const ACTION_ADD = 'Added';
    public const ACTION_UPLOAD = 'Uploaded';
    public const ACTION_UPDATE = 'updated to';
    public const ACTION_BLOCKED = 'Blocked';
    public const ACTION_UNBLOCKED = 'Unblocked';
    public const ACTION_DELETE = 'Deleted';
    public const ACTION_GIVE_PERMISSION = 'Gave permission';
    public const ACTION_REMOVE_PERMISSION = 'Removed permission';
}
