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

use yii\web\UploadedFile;
use yiiplus\appversion\modules\admin\models\App;
use yiiplus\appversion\modules\admin\models\Version;
use Yii;
use yiiplus\appversion\modules\admin\models\ChannelVersion;
use yiiplus\appversion\modules\admin\models\ChannelVersionSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ChannelVersionController 渠道关联表
 *
 * @category  PHP
 * @package   Yii2
 * @author    陈思辰 <chensichen@mocaapp.cn>
 * @copyright 2019 重庆次元能力科技有限公司
 * @license   https://www.moego.com/licence.txt Licence
 * @link      http://www.moego.com
 */
class ChannelVersionController extends Controller
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
     * 首页
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ChannelVersionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 创建版本控制
     *
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $versionId = Yii::$app->request->get('version_id');
        $version = Version::findOne($versionId);
        if (!$version) {
            Yii::$app->getSession()->setFlash('error', '不存在的版本号');
            return $this->redirect(['/appversion/app']);
        }
        if ($version->status == Version::STATUS_ON) {
            Yii::$app->getSession()->setFlash('error', '不能修改已上架的渠道管理');
            return $this->redirect(['index',  'ChannelVersionSearch[version_id]' => $version->id]);
        }
        $model = new ChannelVersion();
        $model->version_id = $version->id;
        if ($model->load(Yii::$app->request->post(), null)) {
            if ($model->version->platform == App::ANDROID) {
                $file = UploadedFile::getInstances($model, 'url');

                if (empty($file)) {
                    Yii::$app->getSession()->setFlash('error', '没有渠道包，上传失败');
                    return $this->redirect(Yii::$app->request->referrer);
                }

                $cos_url = Yii::$app->params['yiiplus.appversion.configs']['cos_url'] ?? Yii::$app->cos->cos_url;
                $path = $cos_url . Yii::$app->storage->save($file[0], ChannelVersion::UPLOAD_APK_DIR);
                if ($path) {
                    $model->url = $path;
                    $model->size = $file[0]->size ?? 0;
                } else {
                    Yii::$app->getSession()->setFlash('error', '渠道包上传失败');
                    return $this->redirect(['index',  'ChannelVersionSearch[version_id]' => $version->id]);
                }
            }
            $model->save();
            Yii::$app->getSession()->setFlash('success', '保存成功！');
            return $this->redirect(['index',  'ChannelVersionSearch[version_id]' => $version->id]);
        }

        return $this->render('create', [
            'model' => $model,
            'version' => $version
        ]);
    }

    /**
     * 更新
     *
     * @param integer $id 渠道id
     *
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $version = Version::findOne(Yii::$app->request->get('version_id'));
        if (!$version) {
            Yii::$app->getSession()->setFlash('error', '不存在的版本号');
            return $this->redirect(['/appversion/app']);
        }
        if ($version->status == Version::STATUS_ON) {
            Yii::$app->getSession()->setFlash('error', '不能修改已上架的渠道管理');
            return $this->redirect(['index',  'ChannelVersionSearch[version_id]' => $version->id]);
        }

        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            // 清除空的 url 值
            unset($model->url);

            if ($model->version->platform == App::ANDROID && ($file = UploadedFile::getInstances($model, 'url'))) {
                $cos_url = Yii::$app->params['yiiplus.appversion.configs']['cos_url'] ?? Yii::$app->cos->cos_url;
                $path = $cos_url . Yii::$app->storage->save($file[0], ChannelVersion::UPLOAD_APK_DIR);
                $model->url = $path;
            }
            $model->save();
            Yii::$app->getSession()->setFlash('success', '保存成功！');
            return $this->redirect(['index',  'ChannelVersionSearch[version_id]' => $version->id]);
        }

        return $this->render('update', [
            'model' => $model,
            'version' => $version
        ]);
    }

    /**
     * 删除
     *
     * @param integer $id 渠道id
     *
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        if ($model = $this->findModel($id)) {
            if ($model->version->status == Version::STATUS_ON) {
                Yii::$app->getSession()->setFlash('error', '不能删除已上架的渠道管理');
                return $this->redirect(['index',  'ChannelVersionSearch[version_id]' => $model->version->id]);
            }
            $model->delete();
            Yii::$app->getSession()->setFlash('success', '删除成功！');
        }

        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * 模型查找
     *
     * @param integer $id 渠道id
     *
     * @return ChannelVersion
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = ChannelVersion::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
