<?php

namespace App\Service;

use App\Business\Table\Config\ItemsTableConfig;
use App\Models\Table\Table;
use App\Models\Table\TableField;
use shared\ConfigDefaultInterface;

class TableService
{
    public static function getFieldByType(string $type): TableField
    {
       $field = TableField::where('type', $type)->first();

       if (!$field) {
           throw new \Exception(sprintf('Missing field with type: %s', $type));
       }

       return $field;
    }

    public static function getFieldByIdentifier(string $identifier): TableField
    {
        $field = TableField::where('identifier', $identifier)->first();

        if (!$field) {
            throw new \Exception(sprintf('Missing field with type: %s', $identifier));
        }

        return $field;
    }

    public static function getItemsTable(): Table
    {
        return Table::where('name', ItemsTableConfig::TABLE_NAME)->first();
    }

    public static function getFieldById(int | string $id): TableField
    {
        return TableField::find($id);
    }

    public static function getSumRepresentationForInvoiceField(TableField $field, int $orderId): array
    {
        $invoiceSumData = [
            'represented_field' => '',
            'amount' => '',
            'exists' => false,
        ];

        $identifier = $field->identifier;
        if (!$identifier) {
            return $invoiceSumData;
        }

        if (!array_key_exists($identifier, ConfigDefaultInterface::INVOICE_AND_REPRESENTED_SUM_MAP)) {
            return $invoiceSumData;
        }

        $representationFieldType = ConfigDefaultInterface::INVOICE_AND_REPRESENTED_SUM_MAP[$identifier];
        $representationField = self::getFieldByType($representationFieldType);
        $amount = OrderService::getOrderDataFor($representationField->id, $orderId)?->value;
        $formattedAmount = number_format($amount, 2, '.', '');

        return [
            'exists' => true,
            'represented_field' => $representationField->name,
            'amount' => $formattedAmount
        ];
    }
}
