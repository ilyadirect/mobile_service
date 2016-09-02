<?php

$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'Mobile-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'modules' => [
        'settings' => [
            'class' => backend\modules\settings\Settings::className(),
        ],
        'content' => [
            'class' => backend\modules\content\Content::className(),
        ],

        'treemanager' =>  [
            'class' => '\kartik\tree\Module',
            'treeStructure' => [
                'depthAttribute' => 'depth',
            ],
            'dataStructure' => [
                'icon' => null,
                'icon_type' => 1,
            ]
            // other module settings, refer detailed documentation
        ]
    ],
    'components' => [
        'user' => [
            'identityClass' => common\models\ar\User::className(),
            'enableAutoLogin' => true,
        ],

        'errorHandler' => [
            'errorAction' => 'site/error',
        ],

        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
    ],

    'as beforeRequest' => [
        'class' => yii\filters\AccessControl::className(),
        'rules' => [
            [
                'allow' => true,
                'controllers' => ['site'],
                'actions' => ['login', 'error', 'remember-password', 'reset-password'],
            ],
            [
                'allow' => false,
                'roles' => ['@'],
                'controllers' => ['site'],
                'actions' => ['reset-password'],
            ],
            [
                'allow' => true,
                'roles' => ['@'],
            ],
        ],
        'denyCallback' => function () {
            return Yii::$app->response->redirect(['site/login']);
        },
    ],

    'params' => $params,
];
