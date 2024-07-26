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

    public const AVAILABLE_ROLES = [
        self::ROLE_USER,
        self::ROLE_ADMIN,
    ];

    public const AVAILABLE_PERMISSIONS = [
        self::PERMISSION_REGISTER_ORDER,
        self::PERMISSION_UPLOAD_FILE,
        self::PERMISSION_SEE_UPLOADED_FILES,
        self::PERMISSION_DELETE_UPLOADED_FILES,
        self::PERMISSION_SEE_ALL_ORDERS
    ];

    /* Filesystem */
    public const FILE_SYSTEM_PRIVATE = 'private';

    /* Flash messages */
    public const FLASH_SUCCESS = 'success';
    public const FLASH_ERROR = 'error';

    /* Field settings */
    public const FIELD_TYPE_TEXT = 'text';
    public const FIELD_TYPE_DATE = 'date';
    public const FIELD_TYPE_SELECT_STATUS = 'select status';
    public const FIELD_TYPE_SELECT_GLUE = 'select glue';
    public const FIELD_TYPE_SELECT_MEASUREMENT = 'select measurement';
    public const FIELD_TYPE_SELECT_CERTIFICATION = 'select certification';
    public const FIELD_TYPE_SELECT_COUNTRY = 'select country';
    public const FIELD_TYPE_SELECT_TRANSPORT = 'select transport';

    public const FIELD_TYPE_PURCHASE_SUM = 'purchase sum';
    public const FIELD_TYPE_AMOUNT = 'amount';
    public const FIELD_TYPE_PURCHASE_NUMBER = 'purchase number';

    public const AVAILABLE_FIELD_TYPES = [
        self::FIELD_TYPE_TEXT,
        self::FIELD_TYPE_DATE,
        self::FIELD_TYPE_SELECT_STATUS
    ];

    /* Order status select color map */
    public const ORDER_STATUS_MAP = [
        'Užsakyta' => '',
        'Apmokėta' => 'pink',
        'Paruošta' => 'yellow',
        'Užsk. Trans' => 'blue',
        'Važiuoja' => 'green',
        'Muitinėje' => 'gray',
        'Išmuitinta' => 'brown',
        'Sandėlyje' => 'orange',
        'Pristatyta' => 'purple',
        'Atšaukta' => 'red'
    ];

    public const ORDER_GLUE_MAP = [
        'MR',
        'WPB',
    ];

    public const ORDER_MEASUREMENT_MAP = [
        'm3',
        'm2',
        'm',
        'kg',
    ];

    public const ORDER_CERTIFICATION_MAP = [
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

