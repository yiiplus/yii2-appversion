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
use yiiplus\appversion\modules\admin\models\Version;
use yiiplus\appversion\modules\admin\models\App;

/* @var $this yii\web\View */
/* @var $searchModel yiiplus\appversion\modules\admin\models\VersionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '版本管理';
$this->params['breadcrumbs'][] = ['label' => '应用管理', 'url' => ['app/index']];
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
        <h3 class="box-title">搜索</h3>
        <div class="margin-bottom"></div>
        <div class="row">
            <div class="col-xs-8">
                <?php  echo $this->render('_search', ['model' => $searchModel]); ?>
            </div>
            <div class="col-xs-4">
                <div class="btn-group pull-right grid-create-btn" style="margin-right: 10px">
                    <?= Html::a(
                        '<i class="fa fa-plus"></i><span class="hidden-xs">&nbsp;&nbsp;新增',
                        ['create', 'app_id' => $searchModel->app_id, 'platform' => $searchModel->platform],
                        ['class' => 'btn btn-sm btn-success']
                    )
                    ?>
                </div>
            </div>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-sm-12">
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'options' => [
                        'style'=>'white-space: pre-line;'
                    ],
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],
                        [
                            'attribute'=>'app_id',
                            'value' => function ($model) {
                                return $model->app->name ?? null;
                            }
                        ],
                        [
                            'attribute'=>'name',
                            'value' => function ($model) {
                                return $model->nameAttr ?? null;
                            }
                        ],
                        [
                            'attribute'=>'min_name',
                            'value' => function ($model) {
                                return $model->minNameAttr ?? null;
                            }
                        ],
                        [
                            'attribute'=>'type',
                            'value' => function ($model) {
                                return Version::instance()->getUpdateType($model->app_id)[$model->type] ?? null;
                            }
                        ],
                        [
                            'attribute'=>'platform',
                            'value' => function ($model) {
                                return App::PLATFORM_OPTIONS[$model->platform] ?? null;
                            }
                        ],
                        [
                            'attribute'=>'scope',
                            'value' => function ($model) {
                                return Version::SCOPE_TYPE[$model->scope] ?? null;
                            }
                        ],
                        'desc:text',
                        [
                            'attribute'=>'status',
                            'value' => function ($model) {
                                return Version::STATUS_TYPE[$model->status] . "中" ?? null;
                            }
                        ],
                        'comment:text',
                        [
                            'attribute'=>'operated_id',
                            'value' => function ($model) {
                                return $model->operator->username ?? null;
                            }
                        ],
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'template' => '{status-toggle} {channel/index} {update} {delete}',
                            'buttons' => [
                                'status-toggle' => function ($url, $model, $key) {
                                    if ($model->status == Version::STATUS_ON) {
                                        return Html::a('下架', $url, ['class' => 'btn btn-xs btn-success', 'data-pjax'=>"0", 'data-confirm'=>"您确定要下架吗？", 'data-method'=>"post"]);
                                    } else {
                                        return Html::a('上架', $url, ['class' => 'btn btn-xs btn-warning', 'data-pjax'=>"0", 'data-confirm'=>"您确定要上架吗？", 'data-method'=>"post"]);
                                    }
                                },
                                'channel/index' => function ($url, $model, $key) {
                                    if ($model->platform == App::ANDROID) {
                                        $url = "/appversion/channel-version?ChannelVersionSearch%5Bversion_id%5D=$model->id";
                                        return Html::a('渠道管理', $url, ['class' => 'btn btn-xs btn-success']);
                                    }
                                    return '';
                                },
                                'update' => function ($url, $model, $key) {
                                    if ($model->status == Version::STATUS_OFF) {
                                        return Html::a('编辑', $url, ['class' => 'btn btn-xs btn-primary']);
                                    }
                                    return '';
                                },
                                'delete' => function ($url, $model, $key) {
                                    if ($model->status == Version::STATUS_OFF) {
                                        return Html::a(
                                            '删除',
                                            $url,
                                            ['class' => 'btn btn-xs btn-danger', 'data-pjax' => "0", 'data-confirm' => "您确定要删除此项吗？", 'data-method' => "post"]
                                        );
                                    }
                                    return '';
                                },
                            ],
                            'header' => '操作',
                        ],
                    ],
                    'layout'=>"{items}<div class='col-sm-11'>{summary}<div class='pull-right'>{pager}</div></div>",
                    'tableOptions' => ['class' => 'table table-hover']
                ]); ?>
            </div>
        </div>
    </div>
</div>
