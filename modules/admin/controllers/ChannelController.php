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
use yiiplus\appversion\modules\admin\models\Channel;
use yiiplus\appversion\modules\admin\models\ChannelSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yiiplus\appversion\modules\admin\models\Version;

/**
 * ChannelController 渠道管理
 *
 * @category  PHP
 * @package   Yii2
 * @author    陈思辰 <chensichen@mocaapp.cn>
 * @copyright 2019 重庆次元能力科技有限公司
 * @license   https://www.moego.com/licence.txt Licence
 * @link      http://www.moego.com
 */
class ChannelController extends Controller
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
     * 渠道首页
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new ChannelSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 渠道创建
     *
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new Channel();

        if ($model->load(Yii::$app->request->post(), null) && $model->save()) {
            Yii::$app->getSession()->setFlash('success', '渠道创建成功');
            return $this->redirect('index');
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * 渠道添加
     *
     * @param integer $id 渠道id
     *
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        // 不允许修改官方渠道
        if ($id == Channel::IOS_OFFICIAL || $id == Channel::ANDROID_OFFICIAL) {
            Yii::$app->getSession()->setFlash('error', '不能修改官方渠道' . $id);
            return $this->redirect('index');
        }

        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post(), null) && $model->save()) {
            Yii::$app->getSession()->setFlash('success', '更新成功');
            return $this->redirect('index');
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * 废弃与启用 status 1正常 2废弃
     *
     * @param integer $id 渠道id
     *
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionStatusToggle($id)
    {
        $model = $this->findModel($id);
        $model->status = ($model->status != Version::STATUS_ON) ? Version::STATUS_ON : Version::STATUS_OFF;
        $model->save();

        Yii::$app->getSession()->setFlash('success', '操作成功');
        return $this->redirect('index');
    }

    /**
     * 删除
     *
     * @param integer $id 渠道id
     *
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        if ($model = $this->findModel($id)) {
            $model->delete();
            Yii::$app->getSession()->setFlash('success', '删除成功！');
        }
        return $this->redirect(['index']);
    }

    /**
     * 根据主键 id 查找模型，如果不存在则返回 404 错误
     *
     * @param integer $id 渠道id
     *
     * @return Channel|null
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = Channel::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
