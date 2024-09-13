<?php

namespace shared;

interface ConfigWarehouseInterface
{
    public const ITEM_STOCK_TYPE_INCOMING = 'incoming';
    public const ITEM_STOCK_TYPE_OUTGOING = 'outgoing';

    public const ITEM_STOCK_TYPES = [
        self::ITEM_STOCK_TYPE_INCOMING,
        self::ITEM_STOCK_TYPE_OUTGOING,
    ];
}
