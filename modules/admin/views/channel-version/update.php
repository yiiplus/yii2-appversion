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
/* @var $model yiiplus\appversion\modules\admin\models\ChannelVersion */

$this->title = '渠道关联:' . $model->id;
$this->params['breadcrumbs'][] = ['label' => '应用管理', 'url' => ['app/index']];
$this->params['breadcrumbs'][] = ['label' => '版本管理', 'url' => ['version/index', "VersionSearch[app_id]" => $version->app_id, "VersionSearch[platform]" => $version->platform]];
$this->params['breadcrumbs'][] = ['label' => '渠道关联管理', 'url' => ['index', 'ChannelVersionSearch[version_id]' => $model->version_id]];
$this->params['breadcrumbs'][] = '渠道关联编辑';
?>
<div class="channel-version-update">
    <div class="box">
        <div class="box-body">
            <div class="row">
                <div class="col-sm-6 col-sm-offset-3">
                    <?= $this->render('_form', [
                        'model' => $model,
                        'version' => $version
                    ]) ?>
                </div>
            </div>
        </div>
    </div>
</div>
