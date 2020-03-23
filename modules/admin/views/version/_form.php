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
/* @var $model yiiplus\appversion\modules\admin\models\Version */
/* @var $form yii\widgets\ActiveForm */
/* @var $channelVersion yii\widgets\ActiveForm */
?>

<div class="version-form">

    <?php $form = ActiveForm::begin();?>

    <?php
        $apps = App::find()->select(['id', 'name'])->asArray()->all();
    ?>
    <?= $form->field($model, 'app_id')
        ->dropdownList(App::getAppOptions(), ['prompt'=>'选择应用', "disabled" => 'disabled']); ?>

    <?= $form->field($model, 'platform')->dropdownList(App::PLATFORM_OPTIONS, ['prompt'=>'选择平台', "disabled" => 'disabled']) ?>

    <?= $form->field($model, 'nameAttr')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'minNameAttr')->textInput(['maxlength' => true]) ?>

    <?php
    $html =  <<<EOF
<a role="button" data-toggle="collapse" href="#updateTypeHelp" aria-expanded="false" aria-controls="updateTypeHelp">
  <i class="fa fa-fw fa-question-circle"></i>
</a>
EOF;
    ?>
    <?= $form->field($model, 'type')->dropDownList(Version::UPDATE_TYPE, ['prompt'=>'选择更新类型'])->label('更新类型' . $html) ?>
    <div class="collapse" id="updateTypeHelp">
        <div class="well">
            <div class="alert bg-info" role="alert">对于 iOS AppStore 的更新来说：静默更新、可忽略更新、静默可忽略更新都只弹一次提示更新的对话框</div>
            <h4>1 一般更新</h4>
            <p>
                每次APP启动都会弹出更新提示，但是更新对话框可以点击关闭，然后用户可以继续使用。

                用户下次再次启动APP，更新对话框依然弹出来提示用户更新，用户依然可以关闭继续使用。
            </p>

            <h4>2 强制更新</h4>
            <p>
            顾名思义，弹出更新后就必须更新，否则无法进行任何操作，退出应用再进来依然是这样。
            </p>
            <h4>3 静默更新</h4>
            <p>
            APP检测到更新信息后，判断如果是WI-FI情况下，会在后台下载好Apk文件，下次用户再启动APP的时候会提示用户直接安装新版APP。

            用户可以关闭更新提示框继续使用，但是下次再打开依然会提示用户安装新版APP。
            </p>
            <h4>4 可忽略更新</h4>
            <p>
            顾名思义，用户点击忽略后，不在对该版本进行提示，直到下一次版本更新才会重新提示版本更新。

            <h4>5 静默可忽略更新</h4>
            <p>
            检测到新版本后先下载，下载完成之后弹更新对话框，随后逻辑同可忽略更新
            </p>
        </div>
    </div>

    <?php
    $html =  <<<EOF
<a role="button" data-toggle="collapse" href="#scopeTypelHelp" aria-expanded="false" aria-controls="scopeTypelHelp">
  <i class="fa fa-fw fa-question-circle"></i>
</a>
EOF;
    ?>
    <?= $form->field($model, 'scope')->dropdownList(Version::SCOPE_TYPE)->label("更新范围" . $html) ?>
    <div class="collapse" id="scopeTypelHelp">
        <div class="well">
            <h4>1 全量更新</h4>
            所有设备都在此次更新的范围内
            <h4>2 IP白名单</h4>
            根据 APP 管理里面设定的 IP 地址来进行更新，符合 IP白名单的，则会传递更新信息
        </div>
    </div>

    <?php
    if ($channelVersion) {
        echo $form->field($channelVersion, 'url')->textInput(['maxlength' => true]);
    }
    ?>

    <?php
    $html =  <<<EOF
<a type="button" data-container="body" data-toggle="popover" data-placement="right" data-content="用于向用户端展示的版本更新描述信息">
  <i class="fa fa-fw fa-question-circle"></i>
</a>
EOF;
    ?>
    <?= $form->field($model, 'desc')->textarea(['rows' => 6])->label('版本描述' . $html) ?>

    <?php
    $html =  <<<EOF
<a type="button" data-container="body" data-toggle="popover" data-placement="right" data-content="用于给自己查看的信息">
  <i class="fa fa-fw fa-question-circle"></i>
</a>
EOF;
    ?>
    <?= $form->field($model, 'comment')->label('备注信息' . $html) ?>

    <div class="form-group">
        <?= Html::submitButton('保存', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
$popoverRegister = <<<JS
    $(function () {
      $('[data-toggle="popover"]').popover()
    })
JS;
$this->registerJs($popoverRegister);
?>
