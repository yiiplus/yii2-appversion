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

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yiiplus\appversion\modules\admin\models\App;
use yiiplus\appversion\modules\admin\models\Version;

/* @var $this yii\web\View */
/* @var $model yiiplus\appversion\modules\admin\models\VersionSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="version-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="row">
        <div class="col-sm-3">
            <?= $form->field($model, 'name') ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'min_name') ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'app_id')->dropDownList(App::getAppOptions()) ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'type')->dropDownList(Version::UPDATE_TYPE, ['prompt'=>'选择更新类型']) ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'platform')->dropDownList(App::PLATFORM_OPTIONS, ['prompt'=>'选择平台']) ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'status')->dropDownList(Version::STATUS_TYPE, ['prompt'=>'选择上架状态']) ?>
        </div>

    </div>

    <div class="form-group">
        <?= Html::submitButton('搜索', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('重置', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
