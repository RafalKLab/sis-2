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
                'group' => 'PREKĖS IR LOGISTIKA',
            ],
            [
                'name' => 'Užsakymo data',
                'type' => 'date',
                'group' => 'PREKĖS IR LOGISTIKA',
                'identifier' => 'order_date',
            ],
            [
                'name' => 'Užsakymo būsena',
                'type' => 'select status',
                'group' => 'PREKĖS IR LOGISTIKA',
            ],
//            [
//                'name' => 'Pardavėjas',
//                'type' => 'dynamic select',
//                'group' => 'PREKĖS IR LOGISTIKA',
//            ],
//            [
//                'name' => 'Pirkėjas 1',
//                'type' => 'dynamic select',
//                'group' => 'PREKĖS IR LOGISTIKA',
//            ],
//            [
//                'name' => 'Pirkėjas 2',
//                'type' => 'dynamic select',
//                'group' => 'PREKĖS IR LOGISTIKA',
//            ],
//            [
//                'name' => 'Pirkėjas 3',
//                'type' => 'dynamic select',
//                'group' => 'PREKĖS IR LOGISTIKA',
//            ],
//            [
//                'name' => 'Prekės',
//                'type' => 'dynamic select',
//                'group' => 'PREKĖS IR LOGISTIKA',
//            ],
//            [
//                'name' => 'Išmatavimai',
//                'type' => 'dynamic select',
//                'group' => 'PREKĖS IR LOGISTIKA',
//            ],
//            [
//                'name' => 'Kokybė',
//                'type' => 'text',
//                'group' => 'PREKĖS IR LOGISTIKA',
//            ],
//            [
//                'name' => 'Klijai',
//                'type' => 'select glue',
//                'group' => 'PREKĖS IR LOGISTIKA',
//            ],
//            [
//                'name' => 'Kiekis',
//                'type' => 'amount',
//                'group' => 'PREKĖS IR LOGISTIKA',
//            ],
//            [
//                'name' => 'Mat.vnt.',
//                'type' => 'select measurement',
//                'group' => 'PREKĖS IR LOGISTIKA',
//            ],
//            [
//                'name' => 'FSC/PEFC',
//                'type' => 'select certification',
//                'group' => 'PREKĖS IR LOGISTIKA',
//            ],
//            [
//                'name' => 'Pask. Šalis',
//                'type' => 'select country',
//                'group' => 'PREKĖS IR LOGISTIKA',
//            ],
//            [
//                'name' => 'Išskr. Šalis',
//                'type' => 'text',
//                'group' => 'PREKĖS IR LOGISTIKA',
//            ],
//            [
//                'name' => 'Trans. tipas',
//                'type' => 'select transport',
//                'group' => 'PREKĖS IR LOGISTIKA',
//            ],
//            [
//                'name' => 'Vežėjas',
//                'type' => 'dynamic select',
//                'group' => 'PREKĖS IR LOGISTIKA',
//            ],
//            [
//                'name' => 'Trans. nr.',
//                'type' => 'text',
//                'group' => 'PREKĖS IR LOGISTIKA',
//            ],
//            [
//                'name' => 'Muitinė',
//                'type' => 'dynamic select',
//                'group' => 'PREKĖS IR LOGISTIKA',
//            ],
//            [
//                'name' => 'Sandėlis',
//                'type' => 'dynamic select',
//                'group' => 'PREKĖS IR LOGISTIKA',
//            ],
//            [
//                'name' => 'Pasikrovimo data',
//                'type' => 'load date',
//                'group' => 'PREKĖS IR LOGISTIKA',
//            ],
//            [
//                'name' => 'Pristatymo data',
//                'type' => 'delivery date',
//                'group' => 'PREKĖS IR LOGISTIKA',
//            ],
//            [
//                'name' => 'Pirk 1 m3/m2 kaina',
//                'type' => 'purchase number',
//                'group' => 'APSKAITA',
//            ],
//            [
//                'name' => 'Pirkimo suma',
//                'type' => 'purchase sum',
//                'group' => 'APSKAITA',
//            ],
            [
                'name' => 'Bendra pirkimo suma',
                'type' => 'total purchase sum',
                'group' => 'APSKAITA',
            ],
            [
                'name' => 'Trans. kaina 1',
                'type' => 'transport price 1',
                'group' => 'APSKAITA',
            ],
            [
                'name' => 'Trans. kaina 2',
                'type' => 'transport price 2',
                'group' => 'APSKAITA',
            ],
            [
                'name' => 'Muitas 7%',
                'type' => 'duty 7',
                'group' => 'APSKAITA',
            ],
            [
                'name' => 'Antidem 15,8%',
                'type' => 'duty 15',
                'group' => 'APSKAITA',
            ],
            [
                'name' => 'Brokeris',
                'type' => 'broker',
                'group' => 'APSKAITA',
            ],
            [
                'name' => 'Sandėliai',
                'type' => 'warehouses',
                'group' => 'APSKAITA',
            ],
            [
                'name' => 'Bank',
                'type' => 'bank',
                'group' => 'APSKAITA',
            ],
            [
                'name' => 'Bendros kitos išlaidos',
                'type' => 'other costs',
                'group' => 'APSKAITA',
            ],
            [
                'name' => 'Brokas',
                'type' => 'flaw',
                'group' => 'APSKAITA',
            ],
            [
                'name' => 'Agentas',
                'type' => 'agent',
                'group' => 'APSKAITA',
            ],
            [
                'name' => 'Faktoringas',
                'type' => 'factoring',
                'group' => 'APSKAITA',
            ],
            [
                'name' => 'Savikaina',
                'type' => 'prime cost',
                'group' => 'APSKAITA',
            ],
//            [
//                'name' => 'Pardavimo kaina m3/m2',
//                'type' => 'sales number',
//                'group' => 'APSKAITA',
//            ],
            [
                'name' => 'Bendra pardavimo suma',
                'type' => 'total sales sum',
                'group' => 'APSKAITA',
            ],
            [
                'name' => 'Pelnas',
                'type' => 'profit',
                'group' => 'APSKAITA',
            ],
            [
                'name' => 'P-SF. Pard.',
                'type' => 'invoice',
                'group' => 'SĄSKAITOS FAKTŪROS',
            ],
            [
                'name' => 'SF. Pard.',
                'type' => 'invoice',
                'group' => 'SĄSKAITOS FAKTŪROS',
                'identifier' => 'sales_invoice',
            ],
            [
                'name' => 'P-SF. Pirk.',
                'type' => 'invoice',
                'group' => 'SĄSKAITOS FAKTŪROS',
            ],
            [
                'name' => 'SF. Trans 1',
                'type' => 'invoice',
                'group' => 'SĄSKAITOS FAKTŪROS',
                'identifier' => 'invoice_trans_1',
            ],
            [
                'name' => 'SF. Trans 2',
                'type' => 'invoice',
                'group' => 'SĄSKAITOS FAKTŪROS',
                'identifier' => 'invoice_trans_2',
            ],
            [
                'name' => 'SF. Kitos išlaidos',
                'type' => 'invoice',
                'group' => 'SĄSKAITOS FAKTŪROS',
                'identifier' => 'invoice_other_costs',
            ],
            [
                'name' => 'SF. Brokas',
                'type' => 'invoice',
                'group' => 'SĄSKAITOS FAKTŪROS',
                'identifier' => 'invoice_defect',
            ],
            [
                'name' => 'SF. Muitinė',
                'type' => 'invoice',
                'group' => 'SĄSKAITOS FAKTŪROS',
                'identifier' => 'invoice_customs',
            ],
            [
                'name' => 'SF. Sand.',
                'type' => 'invoice',
                'group' => 'SĄSKAITOS FAKTŪROS',
                'identifier' => 'invoice_warehouse',
            ],
            [
                'name' => 'SF. Agent',
                'type' => 'invoice',
                'group' => 'SĄSKAITOS FAKTŪROS',
                'identifier' => 'invoice_agent',
            ],
            [
                'name' => 'SF. Factor',
                'type' => 'invoice',
                'group' => 'SĄSKAITOS FAKTŪROS',
                'identifier' => 'invoice_factoring',
            ],
            [
                'name' => 'Dokumentai',
                'type' => 'file',
                'group' => 'SĄSKAITOS FAKTŪROS',
            ],
        ];
    }

}
