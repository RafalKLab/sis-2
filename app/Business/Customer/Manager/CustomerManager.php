<?php

namespace App\Business\Customer\Manager;

use App\Models\Order\OrderItem;
use App\Models\Order\OrderItemData;
use App\Models\Table\TableField;

class CustomerManager
{
    protected const FIELD_IDENTIFIER = 'customer';

    public function getCustomersData(): array
    {
        $customerIdentifierFields = $this->getCustomerIdentifierFields();
        $customers = $this->getCustomers($customerIdentifierFields);

        return $this->retrieveCustomersOrders($customers);
    }

    private function getCustomerIdentifierFields(): array
    {
        return TableField::where('identifier', self::FIELD_IDENTIFIER)->pluck('id')->toArray();
    }

    private function getCustomers(array $customerIdentifierFields): array
    {
        return OrderItemData::whereIn('field_id', $customerIdentifierFields)
            ->where('value', '!=', '-')
            ->distinct()
            ->pluck('value')->toArray();
    }

    protected function retrieveCustomersOrders(array $customers): array
    {
        $customersOrders = [];
        foreach ($customers as $customer) {
            $orderItems = [];
            $orderItemData = OrderItemData::where('value', $customer)->get();


            foreach ($orderItemData as $itemDatum) {
                $orderItems[$itemDatum->order_item_id] = $itemDatum->order_item_id;
            }

            $orders = [];
            foreach ($orderItems as $orderItem) {
                $orderEntity = OrderItem::find($orderItem)->order;
                $orders[$orderEntity->getKeyField()] = $orderEntity->id;
            }

            $customersOrders[$customer]['orders'] = $orders;
        }

        return $customersOrders;
    }
}
