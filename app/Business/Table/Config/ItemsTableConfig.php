<?php

namespace App\Business\Table\Config;

class ItemsTableConfig implements TableConfigInterface
{
    public const TABLE_NAME = 'Items';

    public function getTableName(): string
    {
        return self::TABLE_NAME;
    }

    public function getTableFields(): array
    {
        return [
            [
                'name' => 'Bendras pardavimo kiekis',
                'type' => 'item sells amount',
                'group' => 'PREKĖS IR LOGISTIKA',
            ],
            [
                'name' => 'Pardavėjas',
                'type' => 'dynamic select',
                'group' => 'PREKĖS IR LOGISTIKA',
                'identifier' => 'item_seller',
            ],
            [
                'name' => 'Prekės pavadinimas',
                'type' => 'dynamic select',
                'group' => 'PREKĖS IR LOGISTIKA',
                'identifier' => 'item_name',
            ],
            [
                'name' => 'Išmatavimai',
                'type' => 'dynamic select',
                'group' => 'PREKĖS IR LOGISTIKA',
                'identifier' => 'item_measurements',
            ],
            [
                'name' => 'Kokybė',
                'type' => 'text',
                'group' => 'PREKĖS IR LOGISTIKA',
            ],
            [
                'name' => 'Klijai',
                'type' => 'select glue',
                'group' => 'PREKĖS IR LOGISTIKA',
            ],
            [
                'name' => 'Bendras pirkimo kiekis',
                'type' => 'amount',
                'group' => 'PREKĖS IR LOGISTIKA',
            ],
            [
                'name' => 'Mat.vnt.',
                'type' => 'select measurement',
                'group' => 'PREKĖS IR LOGISTIKA',
            ],
            [
                'name' => 'FSC/PEFC',
                'type' => 'select certification',
                'group' => 'PREKĖS IR LOGISTIKA',
            ],
            [
                'name' => 'Pask. Šalis',
                'type' => 'select country',
                'group' => 'PREKĖS IR LOGISTIKA',
            ],
            [
                'name' => 'Išskr. Šalis',
                'type' => 'select country',
                'group' => 'PREKĖS IR LOGISTIKA',
            ],
            [
                'name' => 'Trans. tipas',
                'type' => 'select transport',
                'group' => 'PREKĖS IR LOGISTIKA',
            ],
            [
                'name' => 'Vežėjas',
                'type' => 'dynamic select',
                'group' => 'PREKĖS IR LOGISTIKA',
            ],
            [
                'name' => 'Trans. nr.',
                'type' => 'text',
                'group' => 'PREKĖS IR LOGISTIKA',
            ],
            [
                'name' => 'Muitinė',
                'type' => 'dynamic select',
                'group' => 'PREKĖS IR LOGISTIKA',
            ],
            [
                'name' => 'Sandėlis',
                'type' => 'select warehouse',
                'group' => 'PREKĖS IR LOGISTIKA',
            ],
            [
                'name' => 'Kiekis keliaujantis į sandėlį',
                'type' => 'amount to warehouse',
                'group' => 'PREKĖS IR LOGISTIKA',
            ],
            [
                'name' => 'Pasikrovimo data',
                'type' => 'load date',
                'group' => 'PREKĖS IR LOGISTIKA',
            ],
            [
                'name' => 'Pristatymo data',
                'type' => 'delivery date',
                'group' => 'PREKĖS IR LOGISTIKA',
            ],
            [
                'name' => 'Pirk 1 m3/m2 kaina',
                'type' => 'purchase number',
                'group' => 'APSKAITA',
            ],
            [
                'name' => 'Pirkimo suma',
                'type' => 'purchase sum',
                'group' => 'APSKAITA',
            ],
            [
                'name' => 'Pardavimo kaina m3/m2',
                'type' => 'sales number',
                'group' => 'APSKAITA',
            ],
            [
                'name' => 'Pardavimo suma',
                'type' => 'sales sum',
                'group' => 'APSKAITA',
            ],
            [
                'name' => 'Kitos išlaidos',
                'type' => 'item other costs',
                'group' => 'APSKAITA',
            ],
        ];
    }

}
