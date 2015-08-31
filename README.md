# yii2-gii

```php
if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['modules']['gii'] = [
        'class'=>'yii\gii\Module',
        'generators' => [
            'model' => [
                'class' => 'yii\gii\generators\model\Generator',
                'templates' => ['With constants' => '@app/vendor/yiisoft/yii2-gii/generators/model/with_constants'],
            ]
        ],
        'allowedIPs'=>['127.0.0.1','192.168.1.*'],
    ];
}
```
