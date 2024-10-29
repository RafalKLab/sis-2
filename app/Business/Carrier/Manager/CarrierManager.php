<?php

namespace App\Business\Carrier\Manager;

use App\Models\Note\Note;
use App\Models\Order\ItemBuyer;
use App\Models\Order\OrderItemData;
use App\Service\TableService;
use shared\ConfigDefaultInterface;

class CarrierManager
{
    public function getCarriersData(): array
    {
        $carriers = $this->getCarriersOrders();
        $carriers = $this->populateWithNotes($carriers);

        return $carriers;
    }

    private function getCarriersOrders(): array
    {
        $data = [];
        $carrierFieldId = TableService::getFieldByIdentifier(ConfigDefaultInterface::FIELD_IDENTIFIER_CARRIER)->id;
        $carrierData = OrderItemData::where('field_id', $carrierFieldId)->where('value', '!=', '-')->get();

        foreach ($carrierData as $carrier) {
            $order = $carrier->orderItem->order;
            $data[$carrier->value]['orders'][$order->getKeyField()] = $order->id;
        }

        $data = $this->collectBuyerCarriers($data);

        return $data;
    }

    private function populateWithNotes(array $carriers): array
    {
        foreach ($carriers as $carrierName => &$carrierData) {
            $notes =  $carrierData['notes'] = Note::where('target', $carrierName)
                ->where('identifier', ConfigDefaultInterface::FIELD_IDENTIFIER_CARRIER)
                ->orderBy('created_at', 'desc')
                ->get();

            $notesDetails = [];
            foreach ($notes as $note) {
                $notesDetails[] = [
                    'note_id' => $note->id,
                    'author' => $note->user->name,
                    'message' => $note->message,
                    'created_at' => $note->created_at->format('Y-m-d H:i'),
                ];
            }

            $carrierData['notes'] = $notesDetails;
        }
        unset($carrierData);

        return $carriers;
    }

    private function collectBuyerCarriers(array $data): array
    {
        $buyerCarriers = ItemBuyer::whereNotNull('carrier')->pluck('carrier')->unique()->toArray();
        $buyerCarriersWithOrders = [];
        foreach ($buyerCarriers as $carrier) {
            $orders = [];
            $buyers = ItemBuyer::where('carrier', $carrier)->get();
            foreach ($buyers as $buyer) {
                $orders[$buyer->item->order->getKeyField()] = $buyer->item->order->id;
            }

            $buyerCarriersWithOrders[$carrier]['orders'] = $orders;
        }

        $mergedArray = array_merge_recursive($buyerCarriersWithOrders, $data);

        return $mergedArray;
    }
}
