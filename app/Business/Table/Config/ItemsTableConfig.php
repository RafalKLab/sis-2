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
                'identifier' => 'item_quality',
            ],
            [
                'name' => 'Pakuočių skaičius',
                'type' => 'item pallets',
                'group' => 'PREKĖS IR LOGISTIKA',
                'identifier' => 'item pallets',
            ],
            [
                'name' => 'Klijai',
                'type' => 'select glue',
                'group' => 'PREKĖS IR LOGISTIKA',
                'identifier' => 'item_glue',
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
                'identifier' => 'load_country',
            ],
            [
                'name' => 'Išskr. Šalis',
                'type' => 'select country',
                'group' => 'PREKĖS IR LOGISTIKA',
                'identifier' => 'dep_country',
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
                'identifier' => 'carrier',
            ],
            [
                'name' => 'Trans. nr.',
                'type' => 'text',
                'group' => 'PREKĖS IR LOGISTIKA',
                'identifier' => 'trans_number',
            ],
            [
                'name' => 'Muitinė',
                'type' => 'dynamic select',
                'group' => 'PREKĖS IR LOGISTIKA',
                'identifier' => 'customs_name',
            ],
            [
                'name' => 'Sandėlis',
                'type' => 'select warehouse',
                'group' => 'PREKĖS IR LOGISTIKA',
            ],
            [
                'name' => 'Kiekis paimtas iš sandėlio',
                'type' => 'amount from warehouse',
                'group' => 'APSKAITA',
                'identifier' => 'amount from warehouse',
            ],
            [
                'name' => 'Nepanaudotas kiekis',
                'type' => 'available amount from warehouse',
                'group' => 'PREKĖS IR LOGISTIKA',
                'identifier' => 'available amount from warehouse'
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
                'name' => 'Pristatymo data į sandelį',
                'type' => 'delivery date',
                'group' => 'PREKĖS IR LOGISTIKA',
            ],
//            [
//                'name' => 'Pasikrovimo data iš sandelio',
//                'type' => 'load date from warehouse',
//                'group' => 'PREKĖS IR LOGISTIKA',
//            ],
//            [
//                'name' => 'Pristatymo data klientui',
//                'type' => 'delivery date to buyer',
//                'group' => 'PREKĖS IR LOGISTIKA',
//            ],
            [
                'name' => 'Vnt. pirkimo kaina',
                'type' => 'purchase number',
                'group' => 'APSKAITA',
            ],
            [
                'name' => 'Vnt. savikaina',
                'type' => 'item prime cost',
                'group' => 'APSKAITA',
                'identifier' => 'item prime cost',
            ],
            [
                'name' => 'Pirkimo suma',
                'type' => 'purchase sum',
                'group' => 'APSKAITA',
            ],
            [
                'name' => 'Vnt. pardavimo kaina',
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
