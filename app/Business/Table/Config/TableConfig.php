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
                'type' => 'text',
                'order' => 3,
            ],
            [
                'name' => 'Pardavėjas',
                'type' => 'text',
                'order' => 4,
            ],
            [
                'name' => 'Importuotojas',
                'type' => 'text',
                'order' => 5,
            ],
            [
                'name' => 'Pirkėjas',
                'type' => 'text',
                'order' => 6,
            ],
            [
                'name' => 'Gavėjas',
                'type' => 'text',
                'order' => 7,
            ],
            [
                'name' => 'Prekės',
                'type' => 'text',
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
                'name' => 'Pardavėjas',
                'type' => 'text',
                'order' => 11,
            ],
            [
                'name' => 'MR/WPB',
                'type' => 'text',
                'order' => 12,
            ],
            [
                'name' => 'Kiekis',
                'type' => 'number',
                'order' => 13,
            ],
            [
                'name' => 'Mat.vnt.',
                'type' => 'text',
                'order' => 14,
            ],
            [
                'name' => 'FSC/PEFC',
                'type' => 'text',
                'order' => 15,
            ],
            [
                'name' => 'Pask. Šalis',
                'type' => 'text',
                'order' => 16,
            ],
            [
                'name' => 'Išskr. Šalis',
                'type' => 'text',
                'order' => 17,
            ],
            [
                'name' => 'Vežėjas',
                'type' => 'text',
                'order' => 18,
            ],
            [
                'name' => 'Trans. nr.',
                'type' => 'text',
                'order' => 19,
            ],
            [
                'name' => 'Trans. tipas',
                'type' => 'text',
                'order' => 20,
            ],
            [
                'name' => 'Muitinė',
                'type' => 'text',
                'order' => 21,
            ],
            [
                'name' => 'Sandėlis',
                'type' => 'text',
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
                'type' => 'text',
                'order' => 29,
            ],
            [
                'name' => 'Antidem 15,8%',
                'type' => 'text',
                'order' => 30,
            ],
            [
                'name' => 'Brokeris',
                'type' => 'text',
                'order' => 31,
            ],
            [
                'name' => 'Sandėliai',
                'type' => 'text',
                'order' => 32,
            ],
            [
                'name' => 'Bank',
                'type' => 'text',
                'order' => 33,
            ],
            [
                'name' => 'Kitos išlaidos',
                'type' => 'text',
                'order' => 34,
            ],
            [
                'name' => 'Brokas',
                'type' => 'text',
                'order' => 35,
            ],
            [
                'name' => 'Agentas',
                'type' => 'text',
                'order' => 36,
            ],
            [
                'name' => 'Faktoringas',
                'type' => 'text',
                'order' => 37,
            ],
            [
                'name' => 'Savikaina',
                'type' => 'text',
                'order' => 38,
            ],
            [
                'name' => 'Pard - 1 m3 kaina',
                'type' => 'text',
                'order' => 39,
            ],
            [
                'name' => 'Pard. Suma',
                'type' => 'text',
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
