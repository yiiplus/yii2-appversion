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

namespace yiiplus\appversion\modules\admin\controllers;

use Yii;
use yii\web\Response;
use yiiplus\appversion\modules\admin\models\App;
use yiiplus\appversion\modules\admin\models\Channel;
use yiiplus\appversion\modules\admin\models\ChannelVersion;
use yiiplus\appversion\modules\admin\models\Version;
use yiiplus\appversion\modules\admin\models\VersionSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * VersionController 版本管理
 *
 * @category  PHP
 * @package   Yii2
 * @author    陈思辰 <chensichen@mocaapp.cn>
 * @copyright 2019 重庆次元能力科技有限公司
 * @license   https://www.moego.com/licence.txt Licence
 * @link      http://www.moego.com
 */
class VersionController extends Controller
{

    /**
     * 过滤器
     *
     * @return array
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * 版本管理首页
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new VersionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * 创建版本
     *
     * @param bool|integer $appId 应用id
     * @param bool|integer $platform 平台类型 0、1
     *
     * @return string|Response
     */
    public function actionCreate($platform = false)
    {
        $model = new Version();
        $channelVersion = ($platform == App::IOS) ? new ChannelVersion() : null;

        $appId = Yii::$app->request->get('app_id');
        if ($appId) {
            $model->app_id = $appId;
        }
        if (isset(App::PLATFORM_OPTIONS[$platform])) {
            $model->platform = $platform;
        }

        if ($model->load(Yii::$app->request->post(), null) && $model->save()) {
            if ($platform == App::IOS) {
                $channelVersion->load(Yii::$app->request->post(), null);
                $channelVersion->channel_id = Channel::IOS_OFFICIAL;
                $model->link('channelVersions', $channelVersion);
            }
            Yii::$app->getSession()->setFlash('success', '保存成功！');
            return $this->redirect(['index',  'VersionSearch[platform]' => $model->platform, 'VersionSearch[app_id]' => $model->app_id]);
        }

        return $this->render('create', [
            'model' => $model,
            'channelVersion' => $channelVersion
        ]);
    }

    /**
     * 版本更新
     *
     * @param integer $id 版本id
     *
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $channelVersion = null;
        if ($model && $model->platform == App::IOS) {
            $channelVersion = $model->getChannelVersions()->where(['channel_id' => Channel::IOS_OFFICIAL])->one();
        }

        if ($model->status == Version::STATUS_ON) {
            Yii::$app->getSession()->setFlash('error', '不能编辑已上架的版本');
            return $this->redirect(['index',  'VersionSearch[platform]' => $model->platform, 'VersionSearch[app_id]' => $model->app_id]);
        }

        if ($model->load(Yii::$app->request->post(), null) && $model->save()) {
            if ($model->platform == App::IOS) {
                $channelVersion->load(Yii::$app->request->post(), null);
                $channelVersion->save();
            }
            Yii::$app->getSession()->setFlash('success', '保存成功！');
            return $this->redirect(['index',  'VersionSearch[platform]' => $model->platform, 'VersionSearch[app_id]' => $model->app_id]);
        }

        return $this->render('update', [
            'model' => $model,
            'channelVersion' => $channelVersion
        ]);
    }

    /**
     * 废弃与启用
     *
     * @param integer $id 版本id
     *
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionStatusToggle($id)
    {
        $model = $this->findModel($id);
        $model->status = ($model->status != 1) ? 1 : 2;
        $model->save();

        Yii::$app->getSession()->setFlash('success', '操作成功');
        return $this->redirect(['index',  'VersionSearch[platform]' => $model->platform, 'VersionSearch[app_id]' => $model->app_id]);
    }


    /**
     * 删除
     *
     * @param integer $id 版本id
     *
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        if ($model = $this->findModel($id)) {
            if ($model->status == Version::STATUS_ON) {
                Yii::$app->getSession()->setFlash('error', '不能删除已上架的版本');
                return $this->redirect(['index',  'VersionSearch[platform]' => $model->platform, 'VersionSearch[app_id]' => $model->app_id]);
            }
            $model->delete();
            Yii::$app->getSession()->setFlash('success', '删除成功！');
        }

        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * 根据主键 id 查找模型，如果不存在则返回 404 错误
     *
     * @param $id
     *
     * @return Version|null
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = Version::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
