<?php

namespace App\Business\Table\Config;

class TableConfig implements TableConfigInterface
{
    public const MAIN_TABLE_NAME = 'Orders';

    public function getTableName(): string
    {
        return self::MAIN_TABLE_NAME;
    }

    public function getTableFields(): array
    {
        return [
            [
                'name' => 'Užsakymo nr.',
                'type' => 'id',
                'order' => 1,
            ],
            [
                'name' => 'Užsakymo data',
                'type' => 'date',
                'order' => 2,
            ],
            [
                'name' => 'Užsakymo būsena',
                'type' => 'select status',
                'order' => 3,
            ],
            [
                'name' => 'Pardavėjas',
                'type' => 'dynamic select',
                'order' => 4,
            ],
            [
                'name' => 'Pirkėjas 1',
                'type' => 'dynamic select',
                'order' => 5,
            ],
            [
                'name' => 'Pirkėjas 2',
                'type' => 'dynamic select',
                'order' => 5,
            ],
            [
                'name' => 'Pirkėjas 3',
                'type' => 'dynamic select',
                'order' => 5,
            ],
            [
                'name' => 'Prekės',
                'type' => 'dynamic select',
                'order' => 8,
            ],
            [
                'name' => 'Išmatavimai',
                'type' => 'text',
                'order' => 9,
            ],
            [
                'name' => 'Kokybė',
                'type' => 'text',
                'order' => 10,
            ],
            [
                'name' => 'Klijai',
                'type' => 'select glue',
                'order' => 12,
            ],
            [
                'name' => 'Kiekis',
                'type' => 'amount',
                'order' => 13,
            ],
            [
                'name' => 'Mat.vnt.',
                'type' => 'select measurement',
                'order' => 14,
            ],
            [
                'name' => 'FSC/PEFC',
                'type' => 'select certification',
                'order' => 15,
            ],
            [
                'name' => 'Pask. Šalis',
                'type' => 'select country',
                'order' => 16,
            ],
            [
                'name' => 'Išskr. Šalis',
                'type' => 'text',
                'order' => 17,
            ],
            [
                'name' => 'Trans. tipas',
                'type' => 'select transport',
                'order' => 20,
            ],
            [
                'name' => 'Vežėjas',
                'type' => 'dynamic select',
                'order' => 18,
            ],
            [
                'name' => 'Trans. nr.',
                'type' => 'text',
                'order' => 19,
            ],
            [
                'name' => 'Muitinė',
                'type' => 'dynamic select',
                'order' => 21,
            ],
            [
                'name' => 'Sandėlis',
                'type' => 'dynamic select',
                'order' => 22,
            ],
            [
                'name' => 'Pasikrovimo data',
                'type' => 'load date',
                'order' => 23,
            ],
            [
                'name' => 'Pristatymo data',
                'type' => 'delivery date',
                'order' => 24,
            ],
            [
                'name' => 'Pirk 1 m3/m2 kaina',
                'type' => 'purchase number',
                'order' => 25,
            ],
            [
                'name' => 'Pirkimo suma',
                'type' => 'purchase sum',
                'order' => 26,
            ],
            [
                'name' => 'Trans. kaina 1',
                'type' => 'transport price 1',
                'order' => 27,
            ],
            [
                'name' => 'Trans. kaina 2',
                'type' => 'transport price 2',
                'order' => 28,
            ],
            [
                'name' => 'Muitas 7%',
                'type' => 'duty 7',
                'order' => 29,
            ],
            [
                'name' => 'Antidem 15,8%',
                'type' => 'duty 15',
                'order' => 30,
            ],
            [
                'name' => 'Brokeris',
                'type' => 'broker',
                'order' => 31,
            ],
            [
                'name' => 'Sandėliai',
                'type' => 'warehouses',
                'order' => 32,
            ],
            [
                'name' => 'Bank',
                'type' => 'bank',
                'order' => 33,
            ],
            [
                'name' => 'Kitos išlaidos',
                'type' => 'other costs',
                'order' => 34,
            ],
            [
                'name' => 'Brokas',
                'type' => 'flaw',
                'order' => 35,
            ],
            [
                'name' => 'Agentas',
                'type' => 'agent',
                'order' => 36,
            ],
            [
                'name' => 'Faktoringas',
                'type' => 'factoring',
                'order' => 37,
            ],
            [
                'name' => 'Savikaina',
                'type' => 'prime cost',
                'order' => 38,
            ],
            [
                'name' => 'Pardavimo kaina m3/m2',
                'type' => 'sales number',
                'order' => 39,
            ],
            [
                'name' => 'Pardavimo suma',
                'type' => 'sales sum',
                'order' => 40,
            ],
            [
                'name' => 'Pelnas',
                'type' => 'text',
                'order' => 41,
            ],
            [
                'name' => 'Data Pirk.',
                'type' => 'date',
                'order' => 42,
            ],
            [
                'name' => 'SF. Pirk.',
                'type' => 'text',
                'order' => 43,
            ],
            [
                'name' => 'Data BG',
                'type' => 'date',
                'order' => 44,
            ],
            [
                'name' => 'SF. BG',
                'type' => 'text',
                'order' => 45,
            ],
            [
                'name' => 'Data Trans',
                'type' => 'date',
                'order' => 46,
            ],
            [
                'name' => 'SF. Trans',
                'type' => 'text',
                'order' => 47,
            ],
            [
                'name' => 'Data EE',
                'type' => 'date',
                'order' => 48,
            ],
            [
                'name' => 'SF. EE',
                'type' => 'text',
                'order' => 49,
            ],
            [
                'name' => 'Data Brok',
                'type' => 'date',
                'order' => 50,
            ],
            [
                'name' => 'SF. Brok',
                'type' => 'text',
                'order' => 51,
            ],
            [
                'name' => 'Data Sand',
                'type' => 'date',
                'order' => 52,
            ],
            [
                'name' => 'SF. Sand',
                'type' => 'text',
                'order' => 53,
            ],
            [
                'name' => 'Data Agent',
                'type' => 'date',
                'order' => 54,
            ],
            [
                'name' => 'SF. Agent',
                'type' => 'text',
                'order' => 55,
            ],
            [
                'name' => 'Data Factor',
                'type' => 'date',
                'order' => 56,
            ],
            [
                'name' => 'SF. Factor',
                'type' => 'text',
                'order' => 57,
            ],
            [
                'name' => 'Data Pard.',
                'type' => 'date',
                'order' => 58,
            ],
            [
                'name' => 'SF. Pard.',
                'type' => 'text',
                'order' => 59,
            ],
            [
                'name' => 'Dokumentai',
                'type' => 'file',
                'order' => 60,
            ],
        ];
    }

}
