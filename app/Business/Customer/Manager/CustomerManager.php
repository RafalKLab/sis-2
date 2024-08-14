<?php

namespace App\Business\Customer\Manager;

use App\Models\Note\Note;
use App\Models\Order\ItemBuyer;
use App\Models\Order\OrderItem;
use App\Models\Order\OrderItemData;
use App\Models\Table\TableField;
use Illuminate\Database\Eloquent\Collection;

class CustomerManager
{
    protected const FIELD_IDENTIFIER = 'customer';

    public function getCustomersData(): array
    {
        $customers = ItemBuyer::all();

        return $this->retrieveCustomersOrders($customers);
    }

    protected function retrieveCustomersOrders(Collection $customers): array
    {
        $customersOrders = [];
        foreach ($customers as $customer) {
            $order = $customer->item->order->getKeyField();
            $orderId = $customer->item->order->id;
            if (!array_key_exists($customer->name, $customersOrders)) {
                $customersOrders[$customer->name]['orders'] = [$order => $orderId];
                $customersOrders[$customer->name]['notes'] = $this->retrieveCustomerNotes($customer->name);
            } else {
                if (in_array($order, $customersOrders[$customer->name]['orders'])) {
                    continue;
                }

                $customersOrders[$customer->name]['orders'][$order] = $orderId;
            }
        }

        return $customersOrders;
    }

    private function retrieveCustomerNotes(string $customer): array
    {
        $result = [];
        $notes = Note::where('target', $customer)
            ->where('identifier', self::FIELD_IDENTIFIER)
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($notes as $note) {
            $result[] = [
                'note_id' => $note->id,
                'author' => $note->user->name,
                'message' => $note->message,
                'created_at' => $note->created_at->format('Y-m-d H:i'),
            ];
        }

        return $result;
    }
}
