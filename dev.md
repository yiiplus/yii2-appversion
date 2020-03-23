```$xslt
测试目录
--migrationPath=@admin/runtime/tmp-extensions/yii2-appversion/migrations

生成迁移
./yii migrate/create create_app --migrationPath=@vendor/yiiplus/yii2-app-version/migrations 

迁移生成
./yii migrate --migrationPath=@vendor/yiiplus/yii2-app-version/migrations 
```

```$xslt
后台添加
admin/config/main.php

        'appversion' => [
            'class' => 'yiiplus\appversion\modules\admin\Module',
        ],

app/config/main.php

        'appversion' => [
            'class' => 'yiiplus\appversion\modules\appversion\Module',
        ],


 在urlManager 数组中添加
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => [
                        'version',
                    ],
                    'pluralize' => false,
                ],

```

````$xslt
开发别名添加
common/config/main.php
在 aliases

'yiiplus/appversion' => '@admin/runtime/tmp-extensions/yii2-appversion',
````

````$xslt

 将 build: ./manifests/dockerfiles/cgi

  修改为  image: leslack0819/lsc_cgi_test01
````

```$xslt
模板生成路径
../runtime/tmp-extensions/yii2-appversion/modules/admin/views
```