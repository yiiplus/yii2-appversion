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
 * VersionSearch 版本搜索
 *
 * @category  PHP
 * @package   Yii2
 * @author    陈思辰 <chensichen@mocaapp.cn>
 * @copyright 2019 重庆次元能力科技有限公司
 * @license   https://www.moego.com/licence.txt Licence
 * @link      http://www.moego.com
 */
class VersionSearch extends Version
{
    /**
     * 规则
     *
     * @return array
     */
    public function rules()
    {
        return [
            [['id', 'app_id', 'type', 'platform', 'scope', 'status', 'operated_id', 'is_del', 'created_at', 'updated_at', 'deleted_at'], 'integer'],
            [['name', 'min_name', 'desc'], 'safe'],
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
        $query = Version::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params, null);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'app_id' => $this->app_id,
            'type' => $this->type,
            'platform' => $this->platform,
            'scope' => $this->scope,
            'status' => $this->status,
            'operated_id' => $this->operated_id,
            'is_del' => $this->is_del,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ]);

        $query->andFilterWhere(['like', 'name', Version::nameStrToInt($this->name)])
            ->andFilterWhere(['like', 'min_name', Version::nameStrToInt($this->min_name)])
            ->andFilterWhere(['like', 'desc', $this->desc]);

        $query->andWhere(['is_del' => self::NOT_DELETED]);

        $query->orderBy(['name' => SORT_DESC]);

        return $dataProvider;
    }
}
