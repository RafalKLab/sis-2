<?php

namespace shared;

interface ConfigDefaultInterface
{
    /* Roles */
    public const ROLE_ADMIN = 'admin';
    public const ROLE_USER = 'user';

    public const FILE_SYSTEM_PRIVATE = 'private';

    public const PERMISSION_REGISTER_ORDER = 'Register order';

    public const AVAILABLE_ROLES = [
        self::ROLE_USER,
        self::ROLE_ADMIN,
    ];

    public const AVAILABLE_PERMISSIONS = [
        self::PERMISSION_REGISTER_ORDER
    ];

    /* Flash messages */
    public const FLASH_SUCCESS = 'success';
    public const FLASH_ERROR = 'error';
}
