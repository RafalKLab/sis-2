<?php

namespace shared;

interface ConfigDefaultInterface
{
    public const ROLE_ADMIN = 'admin';
    public const ROLE_USER = 'user';

    public const AVAILABLE_ROLES = [
        self::ROLE_USER,
        self::ROLE_ADMIN,
    ];
}
