<?php

namespace shared;

interface ConfigDefaultInterface
{
    /* Roles */
    public const ROLE_ADMIN = 'admin';
    public const ROLE_USER = 'user';

    public const PERMISSION_REGISTER_ORDER = 'Register order';
    public const PERMISSION_UPLOAD_FILE = 'Upload file';
    public const PERMISSION_SEE_UPLOADED_FILES = 'See uploaded files';
    public const PERMISSION_DELETE_UPLOADED_FILES = 'Delete uploaded files';
    public const PERMISSION_SEE_ALL_ORDERS = 'See all orders';
    public const PERMISSION_SEE_ORDER_PRODUCTS = 'See order products';
    public const PERMISSION_ADD_ORDER_PRODUCTS = 'Add order products';
    public const PERMISSION_EDIT_ORDER_PRODUCTS = 'Edit order products';
    public const PERMISSION_REMOVE_ORDER_PRODUCTS = 'Remove order products';
    public const PERMISSION_REMOVE_ITEM_BUYER = 'Remove item buyer';
    public const PERMISSION_ACCESS_CUSTOMER_TABLE = 'Access customer table';
    public const PERMISSION_DELETE_CUSTOMER_NOTES = 'Delete customer notes';
    public const PERMISSION_MANAGE_INVOICE_TABLE = 'Manage invoice table';
    public const PERMISSION_MANAGE_WAREHOUSES = 'Manage warehouses';

    public const PERMISSION_ACCESS_CARRIER_TABLE = 'Access carrier table';
    public const PERMISSION_DELETE_CARRIER_NOTES = 'Delete carrier notes';

    public const AVAILABLE_ROLES = [
        self::ROLE_USER,
        self::ROLE_ADMIN,
    ];

    public const AVAILABLE_PERMISSIONS = [
        self::PERMISSION_REGISTER_ORDER,
        self::PERMISSION_SEE_ALL_ORDERS,
        self::PERMISSION_UPLOAD_FILE,
        self::PERMISSION_SEE_UPLOADED_FILES,
        self::PERMISSION_DELETE_UPLOADED_FILES,
        self::PERMISSION_SEE_ORDER_PRODUCTS,
        self::PERMISSION_ADD_ORDER_PRODUCTS,
        self::PERMISSION_EDIT_ORDER_PRODUCTS,
        self::PERMISSION_REMOVE_ORDER_PRODUCTS,
        self::PERMISSION_REMOVE_ITEM_BUYER,
        self::PERMISSION_ACCESS_CUSTOMER_TABLE,
        self::PERMISSION_DELETE_CUSTOMER_NOTES,
        self::PERMISSION_MANAGE_INVOICE_TABLE,
        self::PERMISSION_MANAGE_WAREHOUSES,
        self::PERMISSION_ACCESS_CARRIER_TABLE,
        self::PERMISSION_DELETE_CARRIER_NOTES,
    ];

    public const PERMISSION_GROUPS = [
        'Order related permissions' => [
            self::PERMISSION_REGISTER_ORDER,
            self::PERMISSION_SEE_ALL_ORDERS,
        ],
        'Order product related permissions' => [
            self::PERMISSION_SEE_ORDER_PRODUCTS,
            self::PERMISSION_ADD_ORDER_PRODUCTS,
            self::PERMISSION_EDIT_ORDER_PRODUCTS,
            self::PERMISSION_REMOVE_ORDER_PRODUCTS,
            self::PERMISSION_REMOVE_ITEM_BUYER,
        ],
        'Document related permissions' => [
            self::PERMISSION_UPLOAD_FILE,
            self::PERMISSION_SEE_UPLOADED_FILES,
            self::PERMISSION_DELETE_UPLOADED_FILES,
        ],
        'Customer table related permissions' => [
            self::PERMISSION_ACCESS_CUSTOMER_TABLE,
            self::PERMISSION_DELETE_CUSTOMER_NOTES,
        ],
        'Invoice table related permissions' => [
            self::PERMISSION_MANAGE_INVOICE_TABLE,
        ],
        'Warehouse table related permissions' => [
            self::PERMISSION_MANAGE_WAREHOUSES,
        ],
        'Carrier table related permissions' => [
            self::PERMISSION_ACCESS_CARRIER_TABLE,
            self::PERMISSION_DELETE_CARRIER_NOTES,
        ],
    ];

    /* Filesystem */
    public const FILE_SYSTEM_PRIVATE = 'private';

    /* Flash messages */
    public const FLASH_SUCCESS = 'success';
    public const FLASH_ERROR = 'error';

    /* Error messages */
    public const ERROR_MISSING_PERMISSION = 'User does not have permission for this action';

    /* Field settings */
    public const AUTO_CALCULATION_SETTING = 'disabled-auto-calculation';
    public const FIELD_TYPE_TEXT = 'text';
    public const FIELD_TYPE_FILE = 'file';
    public const FIELD_TYPE_DATE = 'date';
    public const FIELD_TYPE_SELECT_STATUS = 'select status';
    public const FIELD_TYPE_SELECT_GLUE = 'select glue';
    public const FIELD_TYPE_SELECT_MEASUREMENT = 'select measurement';
    public const FIELD_TYPE_SELECT_CERTIFICATION = 'select certification';
    public const FIELD_TYPE_SELECT_COUNTRY = 'select country';
    public const FIELD_TYPE_SELECT_TRANSPORT = 'select transport';
    public const FIELD_TYPE_DYNAMIC_SELECT = 'dynamic select';
    public const FIELD_TYPE_PURCHASE_SUM = 'purchase sum';
    public const FIELD_TYPE_TOTAL_PURCHASE_SUM = 'total purchase sum';
    public const FIELD_TYPE_SALES_SUM = 'sales sum';
    public const FIELD_TYPE_TOTAL_SALES_SUM = 'total sales sum';
    public const FIELD_TYPE_AMOUNT = 'amount';
    public const FIELD_TYPE_TOTAL_SALES_AMOUNT = 'item sells amount';
    public const FIELD_TYPE_PURCHASE_NUMBER = 'purchase number';
    public const FIELD_TYPE_SALES_NUMBER = 'sales number';
    public const FIELD_TYPE_DUTY_7 = 'duty 7';
    public const FIELD_TYPE_DUTY_15 = 'duty 15';
    public const FIELD_TYPE_TRANSPORT_PRICE_1 = 'transport price 1';
    public const FIELD_TYPE_TRANSPORT_PRICE_2 = 'transport price 2';
    public const FIELD_TYPE_PRIME_COST = 'prime cost';
    public const FIELD_TYPE_BROKER = 'broker';
    public const FIELD_TYPE_WAREHOUSES = 'warehouses';
    public const FIELD_TYPE_SELECT_WAREHOUSE = 'select warehouse';
    public const FIELD_TYPE_AMOUNT_TO_WAREHOUSE = 'amount to warehouse';
    public const FIELD_TYPE_BANK = 'bank';
    public const FIELD_TYPE_OTHER_COSTS = 'other costs';
    public const FIELD_TYPE_ITEM_OTHER_COSTS = 'item other costs';
    public const FIELD_TYPE_FLAW = 'flaw';
    public const FIELD_TYPE_AGENT = 'agent';
    public const FIELD_TYPE_FACTORING = 'factoring';
    public const FIELD_TYPE_PROFIT = 'profit';
    public const FIELD_TYPE_INVOICE = 'invoice';
    public const FIELD_IDENTIFIER_ITEM_NAME = 'item_name';
    public const FIELD_IDENTIFIER_CARRIER = 'carrier';
    public const FIELD_IDENTIFIER_ITEM_SELLER = 'item_seller';
    public const FIELD_IDENTIFIER_ITEM_MEASUREMENTS = 'item_measurements';
    public const FIELD_IDENTIFIER_ITEM_QUALITY = 'item_quality';
    public const FIELD_IDENTIFIER_SALES_INVOICE = 'sales_invoice';
    public const FIELD_IDENTIFIER_ORDER_DATE = 'order_date';
    public const FIELD_IDENTIFIER_INVOICE_TRANSPORT_1 = 'invoice_trans_1';
    public const FIELD_IDENTIFIER_INVOICE_TRANSPORT_2 = 'invoice_trans_2';
    public const FIELD_IDENTIFIER_INVOICE_OTHER_COSTS = 'invoice_other_costs';
    public const FIELD_IDENTIFIER_INVOICE_DEFECT = 'invoice_defect';
    public const FIELD_IDENTIFIER_INVOICE_CUSTOMS = 'invoice_customs';
    public const FIELD_IDENTIFIER_INVOICE_WAREHOUSE = 'invoice_warehouse';
    public const FIELD_IDENTIFIER_INVOICE_AGENT = 'invoice_agent';
    public const FIELD_IDENTIFIER_INVOICE_FACTORING = 'invoice_factoring';
    public const FIELD_IDENTIFIER_INVOICE_PURCHASE = 'invoice_purchase';

    public const INVOICE_AND_REPRESENTED_SUM_MAP = [
        self::FIELD_IDENTIFIER_SALES_INVOICE => self::FIELD_TYPE_TOTAL_SALES_SUM,
        self::FIELD_IDENTIFIER_INVOICE_TRANSPORT_1 => self::FIELD_TYPE_TRANSPORT_PRICE_1,
        self::FIELD_IDENTIFIER_INVOICE_TRANSPORT_2 => self::FIELD_TYPE_TRANSPORT_PRICE_2,
        self::FIELD_IDENTIFIER_INVOICE_OTHER_COSTS => self::FIELD_TYPE_OTHER_COSTS,
        self::FIELD_IDENTIFIER_INVOICE_DEFECT => self::FIELD_TYPE_FLAW,
        self::FIELD_IDENTIFIER_INVOICE_CUSTOMS => [self::FIELD_TYPE_DUTY_7, self::FIELD_TYPE_DUTY_15],
        self::FIELD_IDENTIFIER_INVOICE_WAREHOUSE => self::FIELD_TYPE_WAREHOUSES,
        self::FIELD_IDENTIFIER_INVOICE_AGENT => self::FIELD_TYPE_AGENT,
        self::FIELD_IDENTIFIER_INVOICE_FACTORING => self::FIELD_TYPE_FACTORING,
        self::FIELD_IDENTIFIER_INVOICE_PURCHASE => self::FIELD_TYPE_TOTAL_PURCHASE_SUM,

    ];

    public const EXPENSE_INVOICES = [
        self::FIELD_IDENTIFIER_INVOICE_FACTORING,
        self::FIELD_IDENTIFIER_INVOICE_TRANSPORT_1,
        self::FIELD_IDENTIFIER_INVOICE_TRANSPORT_2,
        self::FIELD_IDENTIFIER_INVOICE_OTHER_COSTS,
        self::FIELD_IDENTIFIER_INVOICE_DEFECT,
        self::FIELD_IDENTIFIER_INVOICE_CUSTOMS,
        self::FIELD_IDENTIFIER_INVOICE_AGENT,
        self::FIELD_IDENTIFIER_INVOICE_WAREHOUSE,
        self::FIELD_IDENTIFIER_INVOICE_PURCHASE
    ];

    public const INVOICE_STATUS_AWAITING = 'awaiting_payment';
    public const INVOICE_STATUS_PAID = 'paid';
    public const AVAILABLE_INVOICE_STATUS_SELECT = [
        self::INVOICE_STATUS_AWAITING => 'Laukia apmokėjimo',
        self::INVOICE_STATUS_PAID => 'Apmokėta'
    ];

    public const AVAILABLE_FIELD_TYPES = [
        self::FIELD_TYPE_TEXT,
        self::FIELD_TYPE_DATE,
        self::FIELD_TYPE_DYNAMIC_SELECT
    ];

    public const AVAILABLE_FIELD_GROUPS = [
        'PREKĖS IR LOGISTIKA',
        'APSKAITA',
        'SĄSKAITOS FAKTŪROS',
    ];

    /* Order status select color map */
    public const ORDER_STATUS_ORDERED = 'Užsakyta';
    public const ORDER_STATUS_PAID = 'Apmokėta';
    public const ORDER_STATUS_READY = 'Paruošta';
    public const ORDER_STATUS_TRANSPORT_ORDERED = 'Užsk. Trans';
    public const ORDER_STATUS_COMING = 'Važiuoja';
    public const ORDER_STATUS_IN_CUSTOMS = 'Muitinėje';
    public const ORDER_STATUS_CUSTOMIZED = 'Išmuitinta';
    public const ORDER_STATUS_IN_WAREHOUSE = 'Sandėlyje';
    public const ORDER_STATUS_DELIVERED = 'Pristatyta';
    public const ORDER_STATUS_CANCELED = 'Atšaukta';

    public const ORDER_STATUS_MAP = [
        self::ORDER_STATUS_ORDERED => '',
        self::ORDER_STATUS_PAID => 'pink',
        self::ORDER_STATUS_READY => 'yellow',
        self::ORDER_STATUS_TRANSPORT_ORDERED => 'blue',
        self::ORDER_STATUS_COMING => 'green',
        self::ORDER_STATUS_IN_CUSTOMS => 'gray',
        self::ORDER_STATUS_CUSTOMIZED => 'brown',
        self::ORDER_STATUS_IN_WAREHOUSE => 'orange',
        self::ORDER_STATUS_DELIVERED => 'purple',
        self::ORDER_STATUS_CANCELED => 'red'
    ];

    public const ORDER_GLUE_MAP = [
        'MR',
        'WPB',
    ];

    public const ORDER_MEASUREMENT_MAP = [
        'm³',
        'm²',
        'm',
        'kg',
    ];

    public const ORDER_CERTIFICATION_MAP = [
        'nėra',
        'FSC',
        'PEFC',
    ];

    public const ORDER_COUNTRY_MAP = [
        "Afghanistan",
        "Albania",
        "Algeria",
        "Andorra",
        "Angola",
        "Antigua and Barbuda",
        "Argentina",
        "Armenia",
        "Australia",
        "Austria",
        "Azerbaijan",
        "Bahamas",
        "Bahrain",
        "Bangladesh",
        "Barbados",
        "Belarus",
        "Belgium",
        "Belize",
        "Benin",
        "Bhutan",
        "Bolivia",
        "Bosnia and Herzegovina",
        "Botswana",
        "Brazil",
        "Brunei",
        "Bulgaria",
        "Burkina Faso",
        "Burundi",
        "Cabo Verde",
        "Cambodia",
        "Cameroon",
        "Canada",
        "Central African Republic",
        "Chad",
        "Chile",
        "China",
        "Colombia",
        "Comoros",
        "Congo, Democratic Republic of the",
        "Congo, Republic of the",
        "Costa Rica",
        "Cote d'Ivoire",
        "Croatia",
        "Cuba",
        "Cyprus",
        "Czechia",
        "Denmark",
        "Djibouti",
        "Dominica",
        "Dominican Republic",
        "Ecuador",
        "Egypt",
        "El Salvador",
        "Equatorial Guinea",
        "Eritrea",
        "Estonia",
        "Eswatini",
        "Ethiopia",
        "Fiji",
        "Finland",
        "France",
        "Gabon",
        "Gambia",
        "Georgia",
        "Germany",
        "Ghana",
        "Greece",
        "Grenada",
        "Guatemala",
        "Guinea",
        "Guinea-Bissau",
        "Guyana",
        "Haiti",
        "Honduras",
        "Hungary",
        "Iceland",
        "India",
        "Indonesia",
        "Iran",
        "Iraq",
        "Ireland",
        "Israel",
        "Italy",
        "Jamaica",
        "Japan",
        "Jordan",
        "Kazakhstan",
        "Kenya",
        "Kiribati",
        "Korea, North",
        "Korea, South",
        "Kuwait",
        "Kyrgyzstan",
        "Laos",
        "Latvia",
        "Lebanon",
        "Lesotho",
        "Liberia",
        "Libya",
        "Liechtenstein",
        "Lithuania",
        "Luxembourg",
        "Madagascar",
        "Malawi",
        "Malaysia",
        "Maldives",
        "Mali",
        "Malta",
        "Marshall Islands",
        "Mauritania",
        "Mauritius",
        "Mexico",
        "Micronesia",
        "Moldova",
        "Monaco",
        "Mongolia",
        "Montenegro",
        "Morocco",
        "Mozambique",
        "Myanmar",
        "Namibia",
        "Nauru",
        "Nepal",
        "Netherlands",
        "New Zealand",
        "Nicaragua",
        "Niger",
        "Nigeria",
        "North Macedonia",
        "Norway",
        "Oman",
        "Pakistan",
        "Palau",
        "Panama",
        "Papua New Guinea",
        "Paraguay",
        "Peru",
        "Philippines",
        "Poland",
        "Portugal",
        "Qatar",
        "Romania",
        "Russia",
        "Rwanda",
        "Saint Kitts and Nevis",
        "Saint Lucia",
        "Saint Vincent and the Grenadines",
        "Samoa",
        "San Marino",
        "Sao Tome and Principe",
        "Saudi Arabia",
        "Senegal",
        "Serbia",
        "Seychelles",
        "Sierra Leone",
        "Singapore",
        "Slovakia",
        "Slovenia",
        "Solomon Islands",
        "Somalia",
        "South Africa",
        "South Sudan",
        "Spain",
        "Sri Lanka",
        "Sudan",
        "Suriname",
        "Sweden",
        "Switzerland",
        "Syria",
        "Taiwan",
        "Tajikistan",
        "Tanzania",
        "Thailand",
        "Timor-Leste",
        "Togo",
        "Tonga",
        "Trinidad and Tobago",
        "Tunisia",
        "Turkey",
        "Turkmenistan",
        "Tuvalu",
        "Uganda",
        "Ukraine",
        "United Arab Emirates",
        "United Kingdom",
        "United States",
        "Uruguay",
        "Uzbekistan",
        "Vanuatu",
        "Vatican City",
        "Venezuela",
        "Vietnam",
        "Yemen",
        "Zambia",
        "Zimbabwe",
    ];

    public const ORDER_TRANSPORT_MAP = [
        'Road',
        'Sea',
        'Rail',
    ];
}

