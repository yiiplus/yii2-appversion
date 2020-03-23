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
 * ChannelVersion 渠道包模型
 *
 * @category  PHP
 * @package   Yii2
 * @author    陈思辰 <chensichen@mocaapp.cn>
 * @copyright 2019 重庆次元能力科技有限公司
 * @license   https://www.moego.com/licence.txt Licence
 * @link      http://www.moego.com
 *
 * @property int $id 主键id
 * @property int $version_id 版本关联id
 * @property int $channel_id 渠道主键id
 * @property string $url 安卓对应该渠道的 APK 下载地址， iOS 为 appstore 地址
 * @property integer $size 安卓对应的 apk 大小
 * @property int $operated_id 用户id
 * @property int $is_del 状态；0正常；1主动删除；2后台删除
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 * @property int $deleted_at 删除时间
 */
class ChannelVersion extends ActiveRecord
{
    /**
     * 上传路径 上传路径
     */
    const UPLOAD_APK_DIR = 'version/apk';

    /**
     * redis 根据 app_id 和 channel_id 保存版本信息
     */
    const REDIS_APP_VERSION = 'app_%s_platform_%s_channel_%s';

    /**
     * 版本缓存过期时间
     */
    const REDIS_APP_CHANNEL_VERSIONS_EXPIRE = 12 * 60 * 60;

    /**
     * 表名
     *
     * @return string
     */
    public static function tableName()
    {
        return 'yp_appversion_channel_version';
    }

    /**
     * 基本规则
     *
     * @return array
     */
    public function rules()
    {
        return [
            [['version_id', 'channel_id'], 'unique', 'targetAttribute' => ['version_id', 'channel_id', 'deleted_at']],
            [['version_id', 'channel_id'], 'required'],
            [['version_id', 'size', 'channel_id', 'operated_id', 'is_del', 'created_at', 'updated_at', 'deleted_at'], 'integer'],
            [['url'], 'string', 'max' => 255],
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
            'id' => '主键',
            'app' => '应用',
            'version_id' => '版本',
            'channel_id' => '渠道',
            'url' => '链接地址',
            'operated_id' => '操作人',
            'is_del' => '删除',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
            'deleted_at' => '删除时间',
        ];
    }

    /**
     * 渠道关联
     *
     * @return ActiveQuery
     */
    public function getChannel()
    {
        return $this->hasOne(Channel::className(), ['id' => 'channel_id']);
    }

    /**
     * 版本关联
     *
     * @return ActiveQuery
     */
    public function getVersion()
    {
        return $this->hasOne(Version::className(), ['id' => 'version_id']);
    }

    /**
     * 根据版本获取最新的版本信息
     *
     * @param object $model 动态参数模型
     *
     * @return array
     */
    public function getLatest($model)
    {
        // 条件：app_id、platform、channel、name
        // 查询缓存
        $redisKey = sprintf(ChannelVersion::REDIS_APP_VERSION, $model->app_id, $model->platform, $model->channel);
        $name = Version::nameStrToInt($model->name);
        // 更新范围
        $scope = (new App)->scopeIpStatus($model->app_id);
        if (!$scope && yii::$app->redis->hexists($redisKey, $name . Version::SCOPE_IP_SUFFIX)) {
            $name .= Version::SCOPE_IP_SUFFIX;
        }

        $version = Yii::$app->redis->hget($redisKey, $name);
        // 缓存中输出结果
        if ($version) {
            return json_decode($version, true);
        } else {
            return $this->transformers();
        }
    }

    /**
     * 查询数据库
     *
     * @param object $model 动态参数模型
     * @param bool $scopeIp 是否白名单
     *
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getVersionInfo($model, $scopeIp = false)
    {
        // 查询数据库
        $query =  Version::find()
            ->joinWith(['channelVersions', 'channels'], false)
            ->select([
                Version::tableName() . '.*',
                ChannelVersion::tableName() . '.channel_id',
                ChannelVersion::tableName() . '.version_id',
                ChannelVersion::tableName() . '.url',
                ChannelVersion::tableName() . '.size',
                Channel::tableName() . '.status',
            ])
            ->where([
                Version::tableName() . ".app_id" => $model->app_id,
                Version::tableName() . ".platform" => $model->platform,
                Version::tableName() . '.status' => Version::STATUS_ON,
                Version::tableName() . ".is_del" => Version::NOT_DELETED,
                Channel::tableName() . ".status" => Channel::ACTIVE_STATUS,
                Channel::tableName() . ".is_del" => Channel::NOT_DELETED,
                ChannelVersion::tableName() . ".channel_id" => $model->channel,
                ChannelVersion::tableName() . ".is_del" => ChannelVersion::NOT_DELETED
            ]);
        if ($scopeIp) {
            $query->andWhere([Version::tableName() . '.scope' => Version::SCOPE_ALL]);
        }
        $query->andWhere(['<=', 'min_name', $model->name]);
        $query->orderBy(['name' => SORT_DESC]);
        return $query->asArray()->one();
    }

    /**
     * 查询数据库
     *
     * @param integer $appId 应用id
     * @param integer $platformId 平台id
     * @param integer $channelId 渠道id
     *
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getVersionArr($appId, $platformId, $channelId)
    {
        // 查询数据库
        $query = Version::find()
            ->select(['name'])
            ->where(["app_id" => $appId, "platform" => $platformId])
            ->orderBy(['name' => SORT_DESC])
            ->indexBy('name');

        $versionArr = [];
        foreach ($query->each() as $v) {
            $model = (Object)[];
            $model->app_id = $appId;
            $model->platform = $platformId;
            $model->channel = $channelId;
            $model->name = $v->name;
            $version = $this->getVersionInfo($model);

            // 安卓版本不存在调用官方渠道包
            if (!$version && $model->platform == App::ANDROID) {
                $model->channel = Channel::ANDROID_OFFICIAL;
                $version = $this->getVersionInfo($model);
            }

            if ($version) {
                $versionArr[$v->name] = json_encode($this->transformers($version));
                if ($version['scope'] == Version::SCOPE_IP) {
                    $scopeVersion = $this->getVersionInfo($model, true);
                    $versionArr[$v->name . Version::SCOPE_IP_SUFFIX] = json_encode($this->transformers($scopeVersion));
                }
            }
        }
        return $versionArr;
    }

    /**
     * 更新缓存
     *
     * @param int $appId 应用id
     * @param int $channelId 渠道id
     *
     * @return bool
     */
    public function flushCache($appId = 0, $channelId = 0)
    {
        if ($appId && !$channelId) {
            $channels = Channel::find()->select(['id', 'code'])->all();
            if (!$channels) {
                return false;
            }
            foreach ($channels as $channel) {
                $platformId = Channel::getPlatform($channel->id);
                $redisKey = sprintf(ChannelVersion::REDIS_APP_VERSION, $appId, $platformId, $channel->code);
                yii::$app->redis->del($redisKey);

                // 重建缓存
                $versions = $this->getVersionArr($appId, $platformId, $channel->id);

                // hash 缓存处理
                foreach ($versions as $field => $version) {
                    yii::$app->redis->hset($redisKey, $field, $version);
                }
            }
        }

        if ($appId && $channelId) {
            $channel = Channel::find()->select(['id', 'code'])->where(['id' => $channelId])->one();
            if (!$channel && !isset($channel->code)) {
                return false;
            }

            $platformId = Channel::getPlatform($channelId);
            $redisKey = sprintf(ChannelVersion::REDIS_APP_VERSION, $appId, $platformId, $channel->code);
            yii::$app->redis->del($redisKey);

            // 重建缓存
            $versions = $this->getVersionArr($appId, $platformId, $channelId);
            // hash 缓存处理
            foreach ($versions as $field => $version) {
                yii::$app->redis->hset($redisKey, $field, $version);
            }
        }

        if (!$appId && $channelId) {
            $channel = Channel::find()->select(['id', 'code'])->where(['id' => $channelId])->one();
            if (!$channel && !isset($channel->code)) {
                return false;
            }

            $apps = App::find()->select('id')->column();
            if (!$apps) {
                return false;
            }

            $platformId = Channel::getPlatform($channelId);
            foreach ($apps as $app) {
                $redisKey = sprintf(ChannelVersion::REDIS_APP_VERSION, $app, $platformId, $channel->code);
                yii::$app->redis->del($redisKey);

                // 重建缓存
                $versions = $this->getVersionArr($app, $platformId, $channelId);
                // hash 缓存处理
                foreach ($versions as $field => $version) {
                    yii::$app->redis->hset($redisKey, $field, $version);
                }
            }
        }
        return true;
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
     * 接口结果转换
     *
     * @param array $data 版本信息
     *
     * @return array
     */
    public function transformers($data = [])
    {
        // 将name 转换成整形和字符串三段式
        $code = $data['name'] ?? 1000000000;
        $name = Version::nameIntToStr($code);
        $version_info = [
            'name' => $name,
            'code' => (int)$code,
            'type' => $data['type'] ?? 1,
            'scope' => $data['scope'] ?? 1,
            'desc' => $data['desc'] ?? '',
            'url' => $data['url'] ?? '',
            'size' => bcdiv($data['size'] ?? 0, 1024 * 1024, 2),
        ];
        return $version_info;
    }

    /**
     * 模型监控器
     *
     * @param array $insert 输入字段
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
     * 保存以后更新缓存
     *
     * @param array $insert 输入字段
     * @param array $changedAttributes
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        (new ChannelVersion())->flushCache($this->version->app_id, $this->id);
    }

    /**
     * 删除操作
     */
    public function afterDelete()
    {
        parent::afterDelete();
        (new ChannelVersion())->flushCache($this->version->app_id, $this->id);
    }
}
