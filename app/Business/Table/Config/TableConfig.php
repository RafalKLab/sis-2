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
                'type' => 'Text',
                'order' => 3,
            ],
            [
                'name' => 'Pardavėjas',
                'type' => 'Text',
                'order' => 4,
            ],
            [
                'name' => 'Importuotojas',
                'type' => 'Text',
                'order' => 5,
            ],
            [
                'name' => 'Pirkėjas',
                'type' => 'Text',
                'order' => 6,
            ],
            [
                'name' => 'Gavėjas',
                'type' => 'Text',
                'order' => 7,
            ],
            [
                'name' => 'Prekės',
                'type' => 'Text',
                'order' => 8,
            ],
            [
                'name' => 'Išmatavimai',
                'type' => 'Text',
                'order' => 9,
            ],
            [
                'name' => 'Kokybė',
                'type' => 'Text',
                'order' => 10,
            ],
            [
                'name' => 'Pardavėjas',
                'type' => 'Text',
                'order' => 11,
            ],
            [
                'name' => 'MR/WPB',
                'type' => 'Text',
                'order' => 12,
            ],
            [
                'name' => 'Kiekis',
                'type' => 'number',
                'order' => 13,
            ],
            [
                'name' => 'Mat.vnt.',
                'type' => 'Text',
                'order' => 14,
            ],
            [
                'name' => 'FSC/PEFC',
                'type' => 'Text',
                'order' => 15,
            ],
            [
                'name' => 'Pask. Šalis',
                'type' => 'Text',
                'order' => 16,
            ],
            [
                'name' => 'Išskr. Šalis',
                'type' => 'Text',
                'order' => 17,
            ],
            [
                'name' => 'Vežėjas',
                'type' => 'Text',
                'order' => 18,
            ],
            [
                'name' => 'Trans. nr.',
                'type' => 'Text',
                'order' => 19,
            ],
            [
                'name' => 'Trans. tipas',
                'type' => 'Text',
                'order' => 20,
            ],
            [
                'name' => 'Muitinė',
                'type' => 'Text',
                'order' => 21,
            ],
            [
                'name' => 'Sandėlis',
                'type' => 'Text',
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
                'type' => 'Text',
                'order' => 29,
            ],
            [
                'name' => 'Antidem 15,8%',
                'type' => 'Text',
                'order' => 30,
            ],
            [
                'name' => 'Brokeris',
                'type' => 'Text',
                'order' => 31,
            ],
            [
                'name' => 'Sandėliai',
                'type' => 'Text',
                'order' => 32,
            ],
            [
                'name' => 'Bank',
                'type' => 'Text',
                'order' => 33,
            ],
            [
                'name' => 'Kitos išlaidos',
                'type' => 'Text',
                'order' => 34,
            ],
            [
                'name' => 'Brokas',
                'type' => 'Text',
                'order' => 35,
            ],
            [
                'name' => 'Agentas',
                'type' => 'Text',
                'order' => 36,
            ],
            [
                'name' => 'Faktoringas',
                'type' => 'Text',
                'order' => 37,
            ],
            [
                'name' => 'Savikaina',
                'type' => 'Text',
                'order' => 38,
            ],
            [
                'name' => 'Pard - 1 m3 kaina',
                'type' => 'Text',
                'order' => 39,
            ],
            [
                'name' => 'Pard. Suma',
                'type' => 'Text',
                'order' => 40,
            ],
            [
                'name' => 'Pelnas',
                'type' => 'Text',
                'order' => 41,
            ],
            [
                'name' => 'Data Pirk.',
                'type' => 'date',
                'order' => 42,
            ],
            [
                'name' => 'SF. Pirk.',
                'type' => 'Text',
                'order' => 43,
            ],
            [
                'name' => 'Data BG',
                'type' => 'date',
                'order' => 44,
            ],
            [
                'name' => 'SF. BG',
                'type' => 'Text',
                'order' => 45,
            ],
            [
                'name' => 'Data Trans',
                'type' => 'date',
                'order' => 46,
            ],
            [
                'name' => 'SF. Trans',
                'type' => 'Text',
                'order' => 47,
            ],
            [
                'name' => 'Data EE',
                'type' => 'date',
                'order' => 48,
            ],
            [
                'name' => 'SF. EE',
                'type' => 'Text',
                'order' => 49,
            ],
            [
                'name' => 'Data Brok',
                'type' => 'date',
                'order' => 50,
            ],
            [
                'name' => 'SF. Brok',
                'type' => 'Text',
                'order' => 51,
            ],
            [
                'name' => 'Data Sand',
                'type' => 'date',
                'order' => 52,
            ],
            [
                'name' => 'SF. Sand',
                'type' => 'Text',
                'order' => 53,
            ],
            [
                'name' => 'Data Agent',
                'type' => 'date',
                'order' => 54,
            ],
            [
                'name' => 'SF. Agent',
                'type' => 'Text',
                'order' => 55,
            ],
            [
                'name' => 'Data Factor',
                'type' => 'date',
                'order' => 56,
            ],
            [
                'name' => 'SF. Factor',
                'type' => 'Text',
                'order' => 57,
            ],
            [
                'name' => 'Data Pard.',
                'type' => 'date',
                'order' => 58,
            ],
            [
                'name' => 'SF. Pard.',
                'type' => 'Text',
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
