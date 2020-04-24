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
use yiiplus\appversion\modules\admin\models\Channel;
use yiiplus\appversion\modules\admin\models\Version;

/* @var $this yii\web\View */
/* @var $searchModel yiiplus\appversion\modules\admin\models\ChannelVersionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '渠道关联管理';
$this->params['breadcrumbs'][] = ['label' => '应用管理', 'url' => ['app/index']];
$this->params['breadcrumbs'][] = ['label' => '版本管理', 'url' => ['version/index', "VersionSearch[app_id]" => $searchModel->version->app_id, "VersionSearch[platform]" => $searchModel->version->platform]];
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

        <?php if ($searchModel->version->status == Version::STATUS_OFF) { ?>
        <div class="btn-group pull-right grid-create-btn" style="margin-right: 10px">
            <?= Html::a(
                '<i class="fa fa-plus"></i><span class="hidden-xs">&nbsp;&nbsp;新增',
                ['create', 'version_id' => $searchModel->version_id],
                ['class' => 'btn btn-sm btn-success']
            ) ?>
        </div>
        <?php } ?>
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
                        'columns' => [
                            ['class' => 'yii\grid\SerialColumn'],
                            'id',
                            [
                                'attribute'=>'app',
                                'value' => function ($model) {
                                    return $model->version->app->name ?? null;
                                }
                            ],
                            [
                                'attribute'=>'version_id',
                                'value' => function ($model) {
                                    return Version::nameIntToStr($model->version->name ?? null);
                                }
                            ],
                            [
                                'attribute'=>'channel_id',
                                'value' => function ($model) {
                                    return Channel::findOne($model->channel_id)->name ?? null;
                                }
                            ],
                            'url:url',
                            [
                                'attribute'=>'operated_id',
                                'value' => function ($model) {
                                    return $model->operator->username ?? null;
                                }
                            ],
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'template' => '{update} {delete}',
                                'buttons' => [
                                    'update' => function ($url, $model, $key) use ($searchModel) {
                                        if ($searchModel->version->status == Version::STATUS_OFF) {
                                            $url .= "&version_id=" . $searchModel->version_id;
                                            return Html::a('编辑', $url, ['class' => 'btn btn-xs btn-primary']);
                                        }
                                        return '上架状态中，不能修改';
                                    },
                                    'delete' => function ($url, $model, $key) use ($searchModel) {
                                        if ($searchModel->version->status == Version::STATUS_OFF) {
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
                        'showFooter' => false,
                        'layout'=>"{items}<div class='col-sm-11'>{summary}<div class='pull-right'>{pager}</div></div>",
                        'tableOptions' => ['class' => 'table table-hover']
                    ]); ?>
                </div>
            </div>
        </div>
    </div>
</div>
