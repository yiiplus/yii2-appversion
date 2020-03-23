yiiplus/yii2-appversion
=======================
易加脚手架里面的 app版本控制

Feature
------------
* 支持创建多个应用的版本控制
* 支持创建渠道
* 支持ip白名单更新
* 支持按渠道获得最近更新
* 支持搜索某渠道不存的时候，返回官方渠道包在渠道包（仅限安卓）
* 支持最小版本更新，让版本高低不同获得不同的最近更新信息

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist yiiplus/yii2-appversion "*"
```

or add

```
"yiiplus/yii2-appversion": "*"
```

to the require section of your `composer.json` file.


Usage
-----
### 配置
定义后台添加 .admin/config/main.php 的中添加 modules
```$xslt
'appversion' => [
    'class' => 'yiiplus\appversion\modules\admin\Module',
],
````
定义 Api 接口 .app/config/main.php 添加 modules
```$xslt
'appversion' => [
    'class' => 'yiiplus\appversion\modules\appversion\Module',
],
````
并且需要在 urlManager 数组中添加
```$xslt
[
    'class' => 'yii\rest\UrlRule',
    'controller' => [
        'version',
    ],
    'pluralize' => false,
],
```

### 执行数据迁移
首先需要确保你的数据库中没有以下数据表
* yp_appversion_version
* yp_appversion_channel
* yp_appversion_channel_version
* yp_appversion_app

然后执行迁移文件命令
```bash
./yii migrate --migrationPath=@yiiplus/appversion/migrations
```

### 输出
* 后台 `xxx.com/appversion/app/index`
* app版本控制信息 `xxx.com/version`

下一章讲解接口使用

接口说明
-----

**请求URL：**
- `/appversion`

**请求方式：**
- GET

**参数：**

| 参数名       | 必选 | 类型   | 说明      |
| :----------- | :--- | :----- | --------- |
| app_id    | 是   | int | 应用id |
| platform | 是   | string | ios & Android |
| code | 是 | int | 版本code eq: 2158 |
| name | 是 | string | 版本号  eq: 1.0.0  1.10.15  1.1.156 |
| timestamp | 是 | string | 时间 |
| channel | 是 | string | 渠道码 |

```$xslt
{
    "success": true,
    "data":{
        "code":7182,
        "min_code":5000,
        "name":"1.0.0",
        "min_name":"0.0.5",
        "type":2, // 更新类型 1 一般更新 2 强制更新 3 静默更新 （详见后文）
        "scope": 1, // 发布范围 1 全量发布 详见后文）
        "desc":"1.更新了XXXX\n 2.解决了XXX\n 3.增加了xx功能，使用一名程序员祭天\n"
        "url":"http://moego.net/moego_official.apk" // iOS 为 App Store 地址 安卓为对应渠道包
    },
    "time_point": 1573553053
}
```
### 参数 code、min_code、name、min_name
作用：客户端根据版本号来决定更新，后台内部根据 code 值判断版本大小，客户端可自定code OR name 来判断版本
#### code、name 当前该设备版本能支持的最新版本，比对当前版本是否要更新。
#### min_code、min_name 当前该设备最低版本对应的更新

### 参数 type
作用：客户端根据 type 类型来决定采用何种更新
```
对于 iOS AppStore 的更新来说：静默更新、可忽略更新、静默可忽略更新都只弹一次提示更新的对话框
```

具体有如下几种：
#### 1 一般更新
每次APP启动都会弹出更新提示，但是更新对话框可以点击关闭，然后用户可以继续使用。

用户下次再次启动APP，更新对话框依然弹出来提示用户更新，用户依然可以关闭继续使用。
#### 2 强制更新
顾名思义，弹出更新后就必须更新，否则无法进行任何操作，退出应用再进来依然是这样。

#### 3 静默更新
APP检测到更新信息后，判断如果是WI-FI情况下，会在后台下载好Apk文件，下次用户再启动APP的时候会提示用户直接安装新版APP。

用户可以关闭更新提示框继续使用，但是下次再打开依然会提示用户安装新版APP。
#### 4 可忽略更新
顾名思义，用户点击忽略后，不在对该版本进行提示，直到下一次版本更新才会重新提示版本更新。

#### 5 静默可忽略更新
检测到新版本后先下载，下载完成之后弹更新对话框，随后逻辑同可忽略更新


### 参数 scope 
作用：客户端根据 scope 返回的值去判断该设备是否在更新的范围内
#### 1 全量更新
所有设备都在此次更新的范围内

#### 2 IP白名单
根据 APP 管理里面设定的 IP 地址来进行更新，符合 IP白名单的，则会传递更新信息
