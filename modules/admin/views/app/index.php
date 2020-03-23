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

/* @var $this yii\web\View */
/* @var $searchModel yiiplus\appversion\modules\admin\models\AppSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '应用管理';
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
                        ['create'],
                        ['class' => 'btn btn-sm btn-success']
                    ) ?>
                </div>
            </div>
        </div>
    </div>

    <div class="box-body">
        <div id="example2_wrapper">
            <div class="row"><div class="col-sm-6"></div>
                <div class="col-sm-6"></div>
            </div>
            <div class="row">
                <div class="col-xs-12">
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'columns' => [
                            ['class' => 'yii\grid\SerialColumn'],
                            [
                                'attribute' => 'id',
                                'filter' => false, //不显示搜索框
                            ],
                            [
                                'attribute' => 'name',
                                'filter' => false, //不显示搜索框
                            ],
                            [
                                'attribute'=>'operated_id',
                                'value' => function ($model) {
                                    return $model->operator->username ?? null;
                                }
                            ],
                            [
                                'attribute'=>'created_at',
                                'filter' => false,
                                'value' => function ($model) {
                                    return date("Y-m-d H:i:s", $model->created_at);
                                }
                            ],
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'template' => '{flush-cache} {version-apple} {version-android} {update} {delete}',
                                'buttons' => [
                                    'flush-cache' => function ($url, $model, $key) {
                                        return Html::a('刷新缓存', $url, ['class' => 'btn btn-xs btn-info']);
                                    },
                                    'version-apple' => function ($url, $model, $key) {
                                        $url = "/appversion/version?VersionSearch%5Bapp_id%5D=$model->id&VersionSearch%5Bplatform%5D=" . App::IOS;
                                        return Html::a('<span class="fa fa-apple"></span> 苹果', $url, ['class' => 'btn btn-xs btn-success', 'title' => '苹果版本管理']);
                                    },
                                    'version-android' => function ($url, $model, $key) {
                                        $url = "/appversion/version?VersionSearch%5Bapp_id%5D=$model->id&VersionSearch%5Bplatform%5D=" . App::ANDROID;
                                        return Html::a('<span class="fa fa-android"></span> 安卓', $url, ['class' => 'btn btn-xs btn-success', 'title' => '安卓版本管理']);
                                    },
                                    'update' => function ($url, $model, $key) {
                                        return Html::a('应用设置', $url, ['class' => 'btn btn-xs btn-primary']);
                                    },
                                    'delete' => function ($url, $model, $key) {
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
