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

namespace yiiplus\appversion\modules\admin\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * AppSearch 应用搜索模型
 *
 * @category  PHP
 * @package   Yii2
 * @author    陈思辰 <chensichen@mocaapp.cn>
 * @copyright 2019 重庆次元能力科技有限公司
 * @license   https://www.moego.com/licence.txt Licence
 * @link      http://www.moego.com
 * @package yiiplus\appversion\modules\admin\models
 */
class AppSearch extends App
{
    /**
     * 规则
     *
     * @return array
     */
    public function rules()
    {
        return [
            [['id', 'operated_id', 'is_del', 'created_at', 'updated_at', 'deleted_at'], 'integer'],
            [['name', 'application_id'], 'safe'],
        ];
    }


    /**
     * 场景配置
     *
     * @return array
     */
    public function scenarios()
    {
        return Model::scenarios();
    }

    /**
     * 搜索
     *
     * @param array $params 搜索参数
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = App::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        $this->load($params, null);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'operated_id' => $this->operated_id,
            'is_del' => $this->is_del,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'application_id', $this->application_id]);

        $query->andWhere(['is_del' => self::NOT_DELETED]);

        return $dataProvider;
    }
}
