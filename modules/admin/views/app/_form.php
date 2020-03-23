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

/* @var $this yii\web\View */
/* @var $model yiiplus\appversion\modules\admin\models\App */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="app-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true])?>

    <?php
    $html =  <<<EOF
<a type="button" data-container="body" data-toggle="popover" data-placement="right" data-content="应用标识码，不能重复">
  <i class="fa fa-fw fa-question-circle"></i>
</a>
EOF;
    ?>
    <?= $form->field($model, 'application_id')->textInput(['maxlength' => true])->label('应用Key' . $html) ?>

    <?php
    $html =  <<<EOF
<a role="button" data-toggle="collapse" href="#scopeIps" aria-expanded="false" aria-controls="scopeIps">
  <i class="fa fa-fw fa-question-circle"></i>
</a>
EOF;
    ?>
    <?= $form->field($model, 'scope_ips')->textarea(['rows' => 6, "placeholder" => "例：192.168.1.1, 192.168.*.2, 5-20.168.9.6"])->label('IP 白名单' . $html)?>
    <div class="collapse" id="scopeIps">
        <div class="well">
            <p>
               当添加的版本的更新范围选择了IP白名单的时候生效
            </p>
            <p>
                IP 以逗号间隔，支持中文逗号，支持逗号加换行间隔。如有重复 IP 则会去重存储。
            </p>
            <p>
                IP 通配符支持的格式有 *和指定区段两种：
            </p>
            <p>
                * 号代表任意符，形如 192.0.*.* 代表 192.0 开头的所有 IP，使用场景为 IP内网全部，或者根据城市IP段来进行限定通过白名单
            </p>
            <p>
                IP区段形如5-20，比 * 号更精确，形如 20-220.10.10.1  其中 20-220 代表了这个位置 20-220 的 IP 都会通过白名单，如果区段为 240-15 这种代表了240-255与0-15两种区间。使用场景同通配符，适合更加精确控制IP。
            </p>
            <p>
                当前IP: <?php echo Yii::$app->request->getUserIP() ?>
            </p>
        </div>
    </div>
    
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