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
use yii\db\StaleObjectException;
use yii\web\Response;
use yiiplus\appversion\modules\admin\models\App;
use yiiplus\appversion\modules\admin\models\AppSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yiiplus\appversion\modules\admin\models\ChannelVersion;

/**
 * AppController 应用管理
 *
 * @category  PHP
 * @package   Yii2
 * @author    陈思辰 <chensichen@mocaapp.cn>
 * @copyright 2019 重庆次元能力科技有限公司
 * @license   https://www.moego.com/licence.txt Licence
 * @link      http://www.moego.com
 */
class AppController extends Controller
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
     * app 管理首页
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new AppSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * app 创建
     *
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new App();
        if ($model->load(Yii::$app->request->post(), null) && $model->save()) {
            Yii::$app->getSession()->setFlash('success', '保存成功！');
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * app 应用修改
     *
     * @param integer $id 应用id
     *
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post(), null) && $model->save()) {
            Yii::$app->getSession()->setFlash('success', '保存成功！');
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * 缓存重建
     *
     * @param integer $id 应用id
     *
     * @return Response
     */
    public function actionFlushCache($id)
    {
        (new ChannelVersion())->flushCache($id);
        Yii::$app->getSession()->setFlash('success', '刷新成功！');
        return $this->redirect(['index']);
    }

    /**
     * 应用删除
     *
     * @param integer $id 应用id
     *
     * @return Response
     * @throws NotFoundHttpException
     * @throws StaleObjectException
     * @throws \Throwable
     */
    public function actionDelete($id)
    {
        if ($model = $this->findModel($id)) {
            $model->delete();
            Yii::$app->getSession()->setFlash('success', '删除成功！');
        }

        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * 根据主键 id 查找模型，如果不存在则返回 404 错误
     *
     * @param integer $id 应用id
     *
     * @return App|null
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = App::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
