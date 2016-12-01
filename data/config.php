<?php
return [
    'cacheTimestamp' => 1479810735,
    'database' => [
        'driver' => 'pdo_mysql',
        'dbname' => 'real_crm',
        'user' => 'crmuser_new',
        'password' => 'Reb@123',
        'port' => '',
        'host' => 'localhost'
    ],
    'useCache' => false,
    'recordsPerPage' => 20,
    'recordsPerPageSmall' => 5,
    'applicationName' => 'RebeluteCRM',
    'version' => '4.2.7',
    'timeZone' => 'UTC',
    'dateFormat' => 'MM/DD/YYYY',
    'timeFormat' => 'HH:mm',
    'weekStart' => 0,
    'thousandSeparator' => ',',
    'decimalMark' => '.',
    'exportDelimiter' => ';',
    'currencyList' => [
        0 => 'USD'
    ],
    'defaultCurrency' => 'USD',
    'baseCurrency' => 'USD',
    'currencyRates' => [
        
    ],
    'outboundEmailIsShared' => false,
    'outboundEmailFromName' => '',
    'outboundEmailFromAddress' => '',
    'smtpServer' => '',
    'smtpPort' => '25',
    'smtpAuth' => false,
    'smtpSecurity' => '',
    'smtpUsername' => '',
    'smtpPassword' => '',
    'languageList' => [
        0 => 'en_US',
        1 => 'cs_CZ',
        2 => 'de_DE',
        3 => 'es_ES',
        4 => 'fr_FR',
        5 => 'id_ID',
        6 => 'it_IT',
        7 => 'nl_NL',
        8 => 'tr_TR',
        9 => 'ro_RO',
        10 => 'ru_RU',
        11 => 'pl_PL',
        12 => 'pt_BR',
        13 => 'uk_UA',
        14 => 'vi_VN'
    ],
    'language' => 'en_US',
    'logger' => [
        'path' => 'data/logs/espo.log',
        'level' => 'WARNING',
        'rotation' => true,
        'maxFileNumber' => 30
    ],
    'authenticationMethod' => 'Espo',
    'globalSearchEntityList' => [
        0 => 'Account',
        1 => 'Contact',
        2 => 'Lead',
        3 => 'Opportunity'
    ],
    'tabList' => [
        0 => 'Account',
        1 => 'Contact',
        2 => 'Lead',
        3 => 'Opportunity',
        4 => 'Calendar',
        5 => 'Meeting',
        6 => 'Call',
        7 => 'Task',
        8 => 'Case',
        9 => 'Email',
        10 => 'Document',
        11 => 'Campaign',
        12 => 'KnowledgeBaseArticle',
        13 => 'Webhook',
        14 => 'Parameter'
    ],
    'quickCreateList' => [
        0 => 'Account',
        1 => 'Contact',
        2 => 'Lead',
        3 => 'Opportunity',
        4 => 'Meeting',
        5 => 'Call',
        6 => 'Task',
        7 => 'Case',
        8 => 'Email'
    ],
    'exportDisabled' => false,
    'assignmentEmailNotifications' => false,
    'assignmentEmailNotificationsEntityList' => [
        0 => 'Lead',
        1 => 'Opportunity',
        2 => 'Task',
        3 => 'Case'
    ],
    'assignmentNotificationsEntityList' => [
        0 => 'Meeting',
        1 => 'Call',
        2 => 'Task',
        3 => 'Email'
    ],
    'portalStreamEmailNotifications' => true,
    'streamEmailNotificationsEntityList' => [
        0 => 'Case'
    ],
    'emailMessageMaxSize' => 10,
    'notificationsCheckInterval' => 10,
    'disabledCountQueryEntityList' => [
        0 => 'Email'
    ],
    'maxEmailAccountCount' => 2,
    'followCreatedEntities' => false,
    'b2cMode' => false,
    'restrictedMode' => false,
    'theme' => 'Espo',
    'massEmailMaxPerHourCount' => 100,
    'personalEmailMaxPortionSize' => 10,
    'inboundEmailMaxPortionSize' => 20,
    'authTokenLifetime' => 0,
    'authTokenMaxIdleTime' => 120,
    'userNameRegularExpression' => '[^a-z0-9\\-@_\\.\\s]',
    'addressFormat' => 1,
    'displayListViewRecordCount' => true,
    'dashboardLayout' => [
        0 => (object) [
            'name' => 'My Espo',
            'layout' => [
                0 => (object) [
                    'id' => 'default-activities',
                    'name' => 'Activities',
                    'x' => 2,
                    'y' => 2,
                    'width' => 2,
                    'height' => 2
                ],
                1 => (object) [
                    'id' => 'default-stream',
                    'name' => 'Stream',
                    'x' => 0,
                    'y' => 0,
                    'width' => 2,
                    'height' => 4
                ],
                2 => (object) [
                    'id' => 'default-tasks',
                    'name' => 'Tasks',
                    'x' => 2,
                    'y' => 0,
                    'width' => 2,
                    'height' => 2
                ]
            ]
        ]
    ],
    'calendarEntityList' => [
        0 => 'Meeting',
        1 => 'Call',
        2 => 'Task'
    ],
    'isInstalled' => true,
    'siteUrl' => 'http://realcrm.rebelute.in',
    'passwordSalt' => 'afe50dbbaab9b61a',
    'defaultPermissions' => [
        'user' => 2865556,
        'group' => 2865556
    ],
    'userThemesDisabled' => false,
    'avatarsDisabled' => false,
    'dashletsOptions' => (object) [
        
    ]
];
?>