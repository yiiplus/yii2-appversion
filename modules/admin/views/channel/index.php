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
use yii\grid\GridView;
use yii\bootstrap\Alert;
use yiiplus\appversion\modules\admin\models\App;
use yiiplus\appversion\modules\admin\models\Channel;

/* @var $this yii\web\View */
/* @var $searchModel yiiplus\appversion\modules\admin\models\ChannelSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '渠道管理';
$this->params['breadcrumbs'][] = $this->title;
?>

<?php
if (!empty(Yii::$app->session->getFlash('success'))) {
    echo Alert::widget([
        'options' => ['class' => 'alert-info'],
        'body' => Yii::$app->session->getFlash('success'),
    ]);
} elseif (!empty(Yii::$app->session->getFlash('error'))) {
    echo Alert::widget([
        'options' => ['class' => 'alert-error'],
        'body' => Yii::$app->session->getFlash('error'),
    ]);
}
?>

<div class="box">
    <div class="box-header">
        <h3 class="box-title"><?= $this->title ?></h3>

        <div class="btn-group pull-right grid-create-btn" style="margin-right: 10px">
            <?= Html::a(
                '<i class="fa fa-plus"></i><span class="hidden-xs">&nbsp;&nbsp;新增',
                ['create'],
                ['class' => 'btn btn-sm btn-success']
            ) ?>
        </div>
    </div>

    <div class="box-body">
        <div id="example2_wrapper">
            <div class="row"><div class="col-sm-6"></div>
                <div class="col-sm-6"></div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'columns' => [
                            ['class' => 'yii\grid\SerialColumn'],
                            'id',
                            'name',
                            [
                                'attribute'=>'platform',
                                'filter' => Html::activeDropDownList(
                                    $searchModel,
                                    'platform',
                                    App::PLATFORM_OPTIONS,
                                    ['prompt' =>['text'=>'全部', 'options'=>[]], 'class'=> 'form-control']
                                ),
                                'value' => function ($model) {
                                    return App::PLATFORM_OPTIONS[$model->platform];
                                }
                            ],
                            'code',
                            [
                                'attribute'=>'status',
                                'filter' => Html::activeDropDownList(
                                    $searchModel,
                                    'status',
                                    Channel::STATUS_OPTIONS,
                                    ['prompt' =>['text'=>'全部', 'options'=>[]], 'class'=> 'form-control']
                                ),
                                'value' => function ($model) {
                                    return Channel::STATUS_OPTIONS[$model->status];
                                }
                            ],
                            [
                                'attribute'=>'operated_id',
                                'value' => function ($model) {
                                    return $model->operator->username ?? null;
                                }
                            ],
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'template' => '{update} {status-toggle} {delete}',
                                'buttons' => [
                                    'update' => function ($url, $model, $key) {
                                        if (($model->id == Channel::IOS_OFFICIAL) || ($model->id == Channel::ANDROID_OFFICIAL)) {
                                            return '';
                                        }
                                        return Html::a('编辑', $url, ['class' => 'btn btn-xs btn-primary']);
                                    },
                                    'status-toggle' => function ($url, $model, $key) {
                                        if ($model->status == 1) {
                                            return Html::a('废弃', $url, ['class' => 'btn btn-xs btn-warning']);
                                        } else {
                                            return Html::a('启用', $url, ['class' => 'btn btn-xs btn-success']);
                                        }
                                    },
                                    'delete' => function ($url, $model, $key) {
                                        if (($model->id == Channel::IOS_OFFICIAL) || ($model->id == Channel::ANDROID_OFFICIAL)) {
                                            return '';
                                        }
                                        return Html::a(
                                            '删除',
                                            $url,
                                            ['class' => 'btn btn-xs btn-danger', 'data-pjax'=>"0", 'data-confirm'=>"您确定要删除此项吗？", 'data-method'=>"post"]
                                        );
                                    },
                                ],
                                'header' => '操作',
                            ],
                        ],
                        'showFooter' => false,
                        'layout'=>"{items}<div class='col-sm-11'>{summary}<div class='pull-right'>{pager}</div></div>",
                        'tableOptions' => ['class' => 'table table-hover']
                    ]); ?>
                </div>
            </div>
        </div>
    </div>
</div>