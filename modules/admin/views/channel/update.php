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
/* @var $model yiiplus\appversion\modules\admin\models\Channel */

$this->title = '安卓渠道创更新: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => '渠道管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = '编辑';
?>
<div class="channel-update">
    <div class="box">
        <div class="box-body">
            <div class="row">
                <div class="col-sm-6 col-sm-offset-3">
                    <?= $this->render('_form', [
                        'model' => $model,
                    ]) ?>
                </div>
            </div>
        </div>
    </div>
</div>