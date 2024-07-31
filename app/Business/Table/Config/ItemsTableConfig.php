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
                'name' => 'Pardavėjas',
                'type' => 'dynamic select',
                'group' => 'PREKĖS IR LOGISTIKA',
            ],
            [
                'name' => 'Pirkėjas 1',
                'type' => 'dynamic select',
                'group' => 'PREKĖS IR LOGISTIKA',
            ],
            [
                'name' => 'Pirkėjas 2',
                'type' => 'dynamic select',
                'group' => 'PREKĖS IR LOGISTIKA',
            ],
            [
                'name' => 'Pirkėjas 3',
                'type' => 'dynamic select',
                'group' => 'PREKĖS IR LOGISTIKA',
            ],
            [
                'name' => 'Prekės pavadinimas',
                'type' => 'dynamic select',
                'group' => 'PREKĖS IR LOGISTIKA',
            ],
            [
                'name' => 'Išmatavimai',
                'type' => 'dynamic select',
                'group' => 'PREKĖS IR LOGISTIKA',
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
                'name' => 'Kiekis',
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
                'type' => 'text',
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
                'type' => 'dynamic select',
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
        ];
    }

}
