<?php

namespace shared;

interface ConfigDefaultInterface
{
    /* Roles */
    public const ROLE_ADMIN = 'admin';
    public const ROLE_USER = 'user';

    public const PERMISSION_REGISTER_ORDER = 'Register order';
    public const PERMISSION_UPLOAD_FILE = 'Upload file';
    public const PERMISSION_SEE_UPLOADED_FILES = 'See uploaded files';
    public const PERMISSION_DELETE_UPLOADED_FILES = 'Delete uploaded files';
    public const PERMISSION_SEE_ALL_ORDERS = 'See all orders';

    public const AVAILABLE_ROLES = [
        self::ROLE_USER,
        self::ROLE_ADMIN,
    ];

    public const AVAILABLE_PERMISSIONS = [
        self::PERMISSION_REGISTER_ORDER,
        self::PERMISSION_UPLOAD_FILE,
        self::PERMISSION_SEE_UPLOADED_FILES,
        self::PERMISSION_DELETE_UPLOADED_FILES,
        self::PERMISSION_SEE_ALL_ORDERS
    ];

    /* Filesystem */
    public const FILE_SYSTEM_PRIVATE = 'private';

    /* Flash messages */
    public const FLASH_SUCCESS = 'success';
    public const FLASH_ERROR = 'error';

    /* Field settings */
    public const FIELD_TYPE_TEXT = 'text';
    public const FIELD_TYPE_DATE = 'date';
    public const FIELD_TYPE_SELECT_STATUS = 'select status';
    public const FIELD_TYPE_SELECT_GLUE = 'select glue';
    public const FIELD_TYPE_SELECT_MEASUREMENT = 'select measurement';

    public const AVAILABLE_FIELD_TYPES = [
        self::FIELD_TYPE_TEXT,
        self::FIELD_TYPE_DATE,
        self::FIELD_TYPE_SELECT_STATUS
    ];

    /* Order status select color map */
    public const ORDER_STATUS_MAP = [
        'Užsakyta' => '',
        'Apmokėta' => 'pink',
        'Paruošta' => 'yellow',
        'Užsk. Trans' => 'blue',
        'Važiuoja' => 'green',
        'Muitinėje' => 'gray',
        'Išmuitinta' => 'brown',
        'Sandėlyje' => 'orange',
        'Pristatyta' => 'purple',
        'Atšaukta' => 'red'
    ];

    public const ORDER_GLUE_MAP = [
        'MR',
        'WPB',
    ];

    public const ORDER_MEASUREMENT_MAP = [
        'm3',
        'm2',
        'm',
        'kg',
    ];
}
