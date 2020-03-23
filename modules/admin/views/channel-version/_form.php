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
use kartik\file\FileInput;
use yiiplus\appversion\modules\admin\models\App;
use yiiplus\appversion\modules\admin\models\Channel;

/* @var $this yii\web\View */
/* @var $model yiiplus\appversion\modules\admin\models\ChannelVersion */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="channel-version-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($version->app, 'name')->textInput(["disabled" => 'disabled']) ?>

    <?= $form->field($model->version, 'name')->textInput(["disabled" => 'disabled']) ?>

    <?= $form->field($model->version, 'platform')->dropDownList(App::PLATFORM_OPTIONS, ["disabled" => 'disabled']) ?>

    <?php
    $html =  <<<EOF
<a role="button" data-toggle="collapse" href="#channelHelp" aria-expanded="false" aria-controls="channelHelp">
  <i class="fa fa-fw fa-question-circle"></i>
</a>
EOF;
    ?>
    <?= $form->field($model, 'channel_id')->dropdownList(Channel::getChannelOptions($version, $model->channel_id), $model->channel_id ? ["disabled" => 'disabled'] : [])->label("渠道选择" . $html); ?>
    <div class="collapse" id="channelHelp">
        <div class="well">
            <h4>1 IOS</h4>
            IOS 仅有 official 官方渠道，渠道地址填 App Store 地址
            <h4>2 安卓</h4>
            安卓 除了有 official 官方渠道，还有其他应用市场渠道，添加地方为渠道管理，链接地址需要上传渠道包生成
        </div>
    </div>

    <?php
    if ($version->platform == App::IOS) {
        echo $form->field($model, 'url')->textInput(['maxlength' => true]);
    } else {
        echo $form->field($model, 'url')
            ->widget(
                FileInput::classname(),
                [
                    'options' => ['multiple' => false],
                    'pluginOptions' =>
                        [
                            'previewFileType' => 'image',
                            'initialPreviewAsData' => true,
                            'dropZoneTitle' => '上传apk包',
                            'maxFileCount' => 1,
                            'showUpload' => false,
                            'fileActionSettings' =>
                                ['showRemove' => false]
                        ],
                ]
            );
    }
    ?>


    <div class="form-group">
        <?= Html::submitButton('保存', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
