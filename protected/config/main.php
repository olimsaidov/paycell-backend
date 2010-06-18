<?php

return array(
    'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
    'name' => 'PayCell',
    'language' => 'ru',
    'preload' => array('log'),

    'import' => array(
        'application.models.*',
        'application.components.*',
    ),

    'components' => array(
        'currencyFormatter' => array(
            'class' => 'CurrencyFormatter',
        ),
        'authManager' => array(
            'class' => 'PhpAuthManager',
            'defaultRoles' => array('guest'),
        ),
        'user' => array(
            'class' => 'WebUser',
            'allowAutoLogin' => true
        ),
        'db' => array(
            'connectionString' => 'mysql:host=localhost;dbname=paycell',
            'emulatePrepare' => true,
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8',
            'enableParamLogging' => true,
        ),
        'errorHandler' => array(
            'errorAction' => 'site/error'
        ),
        'log' => array(
            'class' => 'CLogRouter',
            'routes' => array(
                array(
                    'class' => 'CFileLogRoute',
                    'levels' => 'error, warning, info'
                ),
            ),
        ),
        'urlManager' => array(
            'urlFormat' => 'path',
            'showScriptName' => false
        )
    ),
    'params' => array(
        'adminEmail' => 'webmaster@example.com',
        'transferPercent' => 0.2,
        'koshelok' => 6,
        'dollarCourse' => '1589.38',
    )
);
