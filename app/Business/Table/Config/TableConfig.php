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
                'type' => 'string',
                'order' => 1,
            ],
            [
                'name' => 'Užsakymo data',
                'type' => 'date',
                'order' => 2,
            ],
            [
                'name' => 'Užsakymo būsena',
                'type' => 'string',
                'order' => 3,
            ],
            [
                'name' => 'Pardavėjas',
                'type' => 'string',
                'order' => 4,
            ],
            [
                'name' => 'Importuotojas',
                'type' => 'string',
                'order' => 5,
            ],
            [
                'name' => 'Pirkėjas',
                'type' => 'string',
                'order' => 6,
            ],
            [
                'name' => 'Gavėjas',
                'type' => 'string',
                'order' => 7,
            ],
            [
                'name' => 'Prekės',
                'type' => 'string',
                'order' => 8,
            ],
            [
                'name' => 'Išmatavimai',
                'type' => 'string',
                'order' => 9,
            ],
            [
                'name' => 'Kokybė',
                'type' => 'string',
                'order' => 10,
            ],
            [
                'name' => 'Pardavėjas',
                'type' => 'string',
                'order' => 11,
            ],
            [
                'name' => 'MR/WPB',
                'type' => 'string',
                'order' => 12,
            ],
            [
                'name' => 'Kiekis',
                'type' => 'number',
                'order' => 13,
            ],
            [
                'name' => 'Mat.vnt.',
                'type' => 'string',
                'order' => 14,
            ],
            [
                'name' => 'FSC/PEFC',
                'type' => 'string',
                'order' => 15,
            ],
            [
                'name' => 'Pask. Šalis',
                'type' => 'string',
                'order' => 16,
            ],
            [
                'name' => 'Išskr. Šalis',
                'type' => 'string',
                'order' => 17,
            ],
            [
                'name' => 'Vežėjas',
                'type' => 'string',
                'order' => 18,
            ],
            [
                'name' => 'Trans. nr.',
                'type' => 'string',
                'order' => 19,
            ],
            [
                'name' => 'Trans. tipas',
                'type' => 'string',
                'order' => 20,
            ],
            [
                'name' => 'Muitinė',
                'type' => 'string',
                'order' => 21,
            ],
            [
                'name' => 'Sandėlis',
                'type' => 'string',
                'order' => 22,
            ],
            [
                'name' => 'Pasikrovimo data',
                'type' => 'date',
                'order' => 23,
            ],
            [
                'name' => 'Pristatymo data',
                'type' => 'date',
                'order' => 24,
            ],
            [
                'name' => 'Pirk 1 m3/m2 kaina',
                'type' => 'number',
                'order' => 25,
            ],
            [
                'name' => 'Pirkimo suma',
                'type' => 'number',
                'order' => 26,
            ],
            [
                'name' => 'Trans. kaina 1',
                'type' => 'number',
                'order' => 27,
            ],
            [
                'name' => 'Trans. kaina 2',
                'type' => 'number',
                'order' => 28,
            ],
            [
                'name' => 'Muitas 7%',
                'type' => 'string',
                'order' => 29,
            ],
            [
                'name' => 'Antidem 15,8%',
                'type' => 'string',
                'order' => 30,
            ],
            [
                'name' => 'Brokeris',
                'type' => 'string',
                'order' => 31,
            ],
            [
                'name' => 'Sandėliai',
                'type' => 'string',
                'order' => 32,
            ],
            [
                'name' => 'Bank',
                'type' => 'string',
                'order' => 33,
            ],
            [
                'name' => 'Kitos išlaidos',
                'type' => 'string',
                'order' => 34,
            ],
            [
                'name' => 'Brokas',
                'type' => 'string',
                'order' => 35,
            ],
            [
                'name' => 'Agentas',
                'type' => 'string',
                'order' => 36,
            ],
            [
                'name' => 'Faktoringas',
                'type' => 'string',
                'order' => 37,
            ],
            [
                'name' => 'Savikaina',
                'type' => 'string',
                'order' => 38,
            ],
            [
                'name' => 'Pard - 1 m3 kaina',
                'type' => 'string',
                'order' => 39,
            ],
            [
                'name' => 'Pard. Suma',
                'type' => 'string',
                'order' => 40,
            ],
            [
                'name' => 'Pelnas',
                'type' => 'string',
                'order' => 41,
            ],
            [
                'name' => 'Data Pirk.',
                'type' => 'date',
                'order' => 42,
            ],
            [
                'name' => 'SF. Pirk.',
                'type' => 'string',
                'order' => 43,
            ],
            [
                'name' => 'Data BG',
                'type' => 'date',
                'order' => 44,
            ],
            [
                'name' => 'SF. BG',
                'type' => 'string',
                'order' => 45,
            ],
            [
                'name' => 'Data Trans',
                'type' => 'date',
                'order' => 46,
            ],
            [
                'name' => 'SF. Trans',
                'type' => 'string',
                'order' => 47,
            ],
            [
                'name' => 'Data EE',
                'type' => 'date',
                'order' => 48,
            ],
            [
                'name' => 'SF. EE',
                'type' => 'string',
                'order' => 49,
            ],
            [
                'name' => 'Data Brok',
                'type' => 'date',
                'order' => 50,
            ],
            [
                'name' => 'SF. Brok',
                'type' => 'string',
                'order' => 51,
            ],
            [
                'name' => 'Data Sand',
                'type' => 'date',
                'order' => 52,
            ],
            [
                'name' => 'SF. Sand',
                'type' => 'string',
                'order' => 53,
            ],
            [
                'name' => 'Data Agent',
                'type' => 'date',
                'order' => 54,
            ],
            [
                'name' => 'SF. Agent',
                'type' => 'string',
                'order' => 55,
            ],
            [
                'name' => 'Data Factor',
                'type' => 'date',
                'order' => 56,
            ],
            [
                'name' => 'SF. Factor',
                'type' => 'string',
                'order' => 57,
            ],
            [
                'name' => 'Data Pard.',
                'type' => 'date',
                'order' => 58,
            ],
            [
                'name' => 'SF. Pard.',
                'type' => 'string',
                'order' => 59,
            ],
        ];
    }

}
