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

use Yii;
use common\models\system\AdminUser;
use yii\db\ActiveQuery;

/**
 * Channel 模型基类
 *
 * @category  PHP
 * @package   Yii2
 * @author    陈思辰 <chensichen@mocaapp.cn>
 * @copyright 2019 重庆次元能力科技有限公司
 * @license   https://www.moego.com/licence.txt Licence
 * @link      http://www.moego.com
 *
 * @property int $id 主键id
 * @property string $name 渠道名称
 * @property int $platform 平台 0 iOS 1 安卓
 * @property string $code 渠道码 安卓官方包渠道码为official，其他渠道则另加，iOS仅有一个渠道为 official
 * @property int $status 状态 1 正常 2 废弃
 * @property int $operated_id 用户id
 * @property int $is_del 状态；0正常；1主动删除；2后台删除
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 * @property int $deleted_at 删除时间
 */
class Channel extends ActiveRecord
{
    /**
     * 启用状态
     */
    const STATUS_OPTIONS = [
        self::ACTIVE_STATUS => '正常',
        2 => '废弃'
    ];

    /**
     * 启用
     */
    const ACTIVE_STATUS = 1;

    /**
     * iOS 官方渠道
     */
    const IOS_OFFICIAL = 1;

    /**
     * Android 官方渠道
     */
    const ANDROID_OFFICIAL = 2;

    /**
     * 表名
     *
     * @return string
     */
    public static function tableName()
    {
        return 'yp_appversion_channel';
    }

    /**
     * 基本规则
     *
     * @return array
     */
    public function rules()
    {
        return [
            [['name', 'code'],'required'],
            ['platform', 'default', 'value' => App::ANDROID],
            ['status', 'default', 'value' => self::ACTIVE_STATUS],
            [['platform', 'status', 'operated_id', 'is_del', 'created_at', 'updated_at', 'deleted_at'], 'integer'],
            [['name', 'code'], 'string', 'max' => 64],
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
            'name' => '渠道名',
            'platform' => '平台',
            'code' => '渠道码',
            'status' => '启用状态',
            'operated_id' => '操作人',
            'is_del' => '删除',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
            'deleted_at' => '删除时间',
        ];
    }

    /**
     * 渠道包关联
     *
     * @return ActiveQuery
     */
    public function getChannelVersions()
    {
        return $this->hasMany(ChannelVersion::className(), ['channel_id' => 'id']);
    }

    /**
     * 版本关联
     *
     * @return ActiveQuery
     */
    public function getVersions()
    {
        return $this->hasMany(Version::className(), ['id' => 'version_id'])
            ->via('channelVersions');
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
     * 前端下拉选项获取
     *
     * @param bool|object $version 版本兑现
     * @param integer $channelId 渠道id
     *
     * @return array|false
     */
    public static function getChannelOptions($version, $channelId = 0)
    {
        $existChannels = [];
        if ($version) {
            // 已经存在的渠道
            $existChannels = $version->getChannelVersions()
                ->select('channel_id')
                ->where([
                    'is_del' => self::NOT_DELETED,
                    'version_id' => $version->id,
                ])
                ->asArray()
                ->column();
        }
        if ($channelId) {
            $existChannels = array_diff($existChannels, [$channelId]);
        }
        $channels = self::find()->select(['id', 'name'])
            ->where(['platform' => $version->platform])
            ->andWhere(['status' => Channel::ACTIVE_STATUS])
            ->andWhere(['is_del' => self::NOT_DELETED])
            ->asArray()->all();

        $channels = array_combine(array_column($channels, 'id'), array_column($channels, 'name'));

        $existChannels = array_flip($existChannels);

        $options = array_diff_key($channels, $existChannels);

        return $options;
    }

    /**
     * 获取平台
     *
     * @param integer $channelId 渠道id
     *
     * @return int
     */
    public static function getPlatform($channelId)
    {
        if ($channelId == Channel::IOS_OFFICIAL) {
            return App::IOS;
        } else {
            return App::ANDROID;
        }
    }

    /**
     * 保存前处理
     *
     * @param bool $insert 保存与否
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
     * 保存后处理
     *
     * @param bool $insert 插入与否
     * @param array $changedAttributes 改变的参数
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        (new ChannelVersion())->flushCache(0, $this->id);
    }

    /**
     * 删除操作
     */
    public function afterDelete()
    {
        parent::afterDelete();
        ChannelVersion::deleteAll(['channel_id' => $this->id]);
        (new ChannelVersion())->flushCache(0, $this->id);
    }
}
