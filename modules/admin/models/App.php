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

use common\models\system\AdminUser;
use Yii;
use yii\db\ActiveQuery;

/**
 * App 模型基类
 *
 * @category  PHP
 * @package   Yii2
 * @author    陈思辰 <chensichen@mocaapp.cn>
 * @copyright 2019 重庆次元能力科技有限公司
 * @license   https://www.moego.com/licence.txt Licence
 * @link      http://www.moego.com
 *
 * @property int $id 主键id
 * @property string $name 应用名称
 * @property string $application_id 应用名称
 * @property string $scope_ips app ip白名单
 * @property int $operated_id 用户id
 * @property int $is_del 状态；0正常；1主动删除；2后台删除
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 * @property int $deleted_at 删除时间
 */
class App extends ActiveRecord
{
    /**
     * PLATFORM_OPTIONS APP的类型
     */
    const PLATFORM_OPTIONS = [
        self::ANDROID => 'Android',
        self::IOS => 'iOS'
    ];

    /**
     * ANDROID 安卓
     */
    const ANDROID = 1;

    /**
     * IOS ios
     */
    const IOS = 0;

    /**
     * redis 根据 app_id 和 channel_id 保存版本信息
     */
    const REDIS_APP_SCOPE_IPS = 'app_%s_scope_ips';

    /**
     * 表名
     *
     * @return string
     */
    public static function tableName()
    {
        return 'yp_appversion_app';
    }

    /**
     * 基本规则
     *
     * @return array
     */
    public function rules()
    {
        return [
            ['application_id', 'unique'],
            [['name', 'application_id'], 'required'],
            [['is_del', 'created_at', 'updated_at', 'deleted_at', 'operated_id'], 'integer'],
            [['name'], 'string', 'max' => 64],
            [['application_id'], 'string', 'max' => 255],
            ['application_id', 'match', 'pattern'=>'/^[a-zA-Z][a-zA-Z0-9_.]{4,29}$/', 'message'=>'5-30位字母、数字或“_”“.”, 字母开头'],
            ['scope_ips', 'filter', 'filter' => function ($value) {
                // 兼容中英文逗号处理, 筛选合适的ip
                $arr = [];
                $arr_tmp = explode(',', $value);
                foreach ($arr_tmp as $item) {
                    $arr_tmp1 = explode('，', $item);
                    foreach ($arr_tmp1 as $value) {
                        $ip = trim($value);
                        // 正则表达式，每个ip段检测0~255、*、区间段0~255-0~255
                        if (preg_match('/^((2(5[0-5]|[0-4]\d))|[0-1]?\d{1,2}|\*)(-((2(5[0-5]|[0-4]\d))|[0-1]?\d{1,2}|\*))?((\.((2(5[0-5]|[0-4]\d))|[0-1]?\d{1,2}|\*))(-((2(5[0-5]|[0-4]\d))|[0-1]?\d{1,2}|\*))?){3}$/', $ip)) {
                            $arr[] = $ip;
                        }
                    }
                }
                return implode(",\n", array_unique($arr));
            }],
        ];
    }

    /**
     * 字段中文名
     *
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '应用名称',
            'application_id' => '应用KEY',
            'scope_ips' => 'IP 白名单',
            'operated_id' => '操作人',
            'operator' => '操作人',
            'is_del' => '删除',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
            'deleted_at' => '删除时间',
        ];
    }

    /**
     * 下拉框获取 APP 选项
     *
     * @return array|false
     */
    public static function getAppOptions()
    {
        $channels = self::find()->select(['id', 'name'])->where(['is_del' => self::NOT_DELETED])->asArray()->all();
        return array_combine(array_column($channels, 'id'), array_column($channels, 'name'));
    }

    /**
     * 版本关联
     *
     * @return ActiveQuery
     */
    public function getVersions()
    {
        return $this->hasMany(Version::className(), ['app_id' => 'id']);
    }

    /**
     * 管理员关联
     *
     * @return ActiveQuery
     */
    public function getOperator()
    {
        return $this->hasOne(AdminUser::className(), ['id' => 'operated_id']);
    }

    /**
     * 获取 APP 的 ip 白名单设置
     *
     * @param integer $appId 应用id
     *
     * @return bool
     */
    public function scopeIpStatus($appId)
    {
        $redisKey = sprintf(App::REDIS_APP_SCOPE_IPS, $appId);
        $result = yii::$app->redis->get($redisKey);

        if ($result) {
            $ips = json_decode($result, true);
        } else {
            $app = App::findOne($appId);
            if ($app->scope_ips ?? false) {
                $ips = explode(",\n", $app->scope_ips);
                yii::$app->redis->set($redisKey, json_encode($ips));
            } else {
                $ips = [];
                yii::$app->redis->set($redisKey, json_encode([]));
            }
        }

        if (!empty($ips)) {
            $userIp = Yii::$app->request->getUserIP();
            // 循环判断用户 ip 是否在 rule 内，原理为将规则替换到用户ip里面后再进行比较
            foreach ($ips as $ip) {
                $userIpArr = explode('.', $userIp);
                $ipArr = explode('.', $ip);

                // 判断规则是否有 * ，有的话，替换到用户 ip 里面
                if (!empty($flAnyArr = preg_grep('/\*/', $ipArr))) {
                    $userIpArr = $flAnyArr + $userIpArr;
                    ksort($userIpArr);
                }

                // 判断规则是否有 XX-XX，有的话替换到用户 ip 里面
                if (!empty($flScopeArr = preg_grep('/((2(5[0-5]|[0-4]\d))|[0-1]?\d{1,2}|\*)(-((2(5[0-5]|[0-4]\d))|[0-1]?\d{1,2}|\*))/', $ipArr))) {
                    // 计算ip范围值
                    foreach ($flScopeArr as $key => $flScope) {
                        $flScope = explode('-', $flScope);
                        if ($flScope[0] <= $flScope[1]) {
                            // 如：100-200、 100-100 小于或相等的情况
                            if ($flScope[0] <= $userIpArr[$key] && $userIpArr[$key] <= $flScope[1]) {
                                $userIpArr[$key] = $flScopeArr[$key];
                            }
                        } else {
                            //如：155-100  取值范围 155 - 255   0-100
                            if (($flScope[0] <= $userIpArr[$key] && $userIpArr[$key] < 256) || (0 <= $userIpArr[$key] && $userIpArr[$key] <= $flScope[1])) {
                                $userIpArr[$key] = $flScopeArr[$key];
                            }
                        }
                    }
                }
                // 比较替换后用户 ip 是否和规则相等，得出结果
                if ($ipArr == $userIpArr) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * 删除ip范围缓存
     *
     * @param integer $appId 应用id
     *
     * @return bool
     */
    public function delRedisAppScopeIps($appId)
    {
        $redisKey = sprintf(App::REDIS_APP_SCOPE_IPS, $appId);
        yii::$app->redis->del($redisKey);
        return true;
    }

    /**
     * 模型监控器
     *
     * @param bool $insert 调用与否
     *
     * @return bool
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $this->operated_id = Yii::$app->user->id;
            return true;
        } else {
            return false;
        }
    }

    /**
     * 保存后操作
     *
     * @param bool $insert 调用与否
     * @param array $changedAttributes 改变的参数
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        $this->delRedisAppScopeIps($this->id);
    }

    /**
     * 删除操作
     */
    public function afterDelete()
    {
        parent::afterDelete();
        $version_ids = $this->getVersions()->select(['id'])->column();
        ChannelVersion::deleteAll(['in', 'version_id', $version_ids]);
        Version::deleteAll(['app_id' => $this->id]);
        (new ChannelVersion())->flushCache($this->id);
    }
}
