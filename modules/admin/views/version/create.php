<?php
/**
 * 萌股 - 二次元潮流聚集地
 *
 * PHP version 7
 *
 * @category  PHP
 * @package   Yii2
 * @author    陈思辰 <chensichen@mocaapp.cn>
 * @copyright 2019 重庆次元能力科技有限公司
 * @license   https://www.moego.com/licence.txt Licence
 * @link      http://www.moego.com
 */

/* @var $this yii\web\View */
/* @var $model yiiplus\appversion\modules\admin\models\Version */
/* @var $channelVersion yiiplus\appversion\modules\admin\models\ChannelVersion */

$this->title = '版本编辑';
$this->params['breadcrumbs'][] = ['label' => '应用列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->app->name . '版本列表', 'url' => ['index', "VersionSearch[app_id]" => $model->app_id, "VersionSearch[platform]" => $model->platform]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="version-create">
    <div class="box">
        <div class="box-body">
            <div class="row">
                <div class="col-sm-6 col-sm-offset-3">
                    <?= $this->render('_form', [
                        'model' => $model,
                        'channelVersion' => $channelVersion
                    ]) ?>
                </div>
            </div>
        </div>
    </div>
</div>
