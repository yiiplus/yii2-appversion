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
 * Version 版本模型
 *
 * @category  PHP
 * @package   Yii2
 * @author    陈思辰 <chensichen@mocaapp.cn>
 * @copyright 2019 重庆次元能力科技有限公司
 * @license   https://www.moego.com/licence.txt Licence
 * @link      http://www.moego.com
 *
 * @property int $id 主键id
 * @property int $app_id 应用关联id
 * @property int $code 版本号 格式 1001001001
 * @property int $min_code 版本号 格式 1001001001
 * @property string $name 版本号 格式 1.1.1
 * @property string $min_name 最小版本号 格式 1.1.1
 * @property int $type 更新类型 1 一般更新 2 强制更新 3 静默更新 4 可忽略更新 5 静默可忽略更新
 * @property int $platform 平台 0 iOS 1 安卓
 * @property int $scope 发布范围（1 全量、2 白名单、3 ip发布）
 * @property string $desc 版本描述 最长字符
 * @property string $comment 备注 最长字符
 * @property int $status 发布范围（1 全量、2 白名单、3 ip发布）
 * @property int $operated_id 用户id
 * @property int $is_del 状态；0正常；1主动删除；2后台删除
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 * @property int $deleted_at 删除时间
 */
class Version extends ActiveRecord
{
    /**
     * 更新类型
     */
    const UPDATE_TYPE = [
        1 => '一般更新',
        2 => '强制更新',
        3 => '静默更新',
        4 => '可忽略更新',
        5 => '静默可忽略更新',
    ];

    /**
     * 更新范围
     */
    const SCOPE_TYPE = [
        self::SCOPE_ALL => '全量更新',
        self::SCOPE_IP => 'IP白名单'
    ];

    /**
     * 更新范围 全量更新
     */
    const SCOPE_ALL = 1;

    /**
     * 更新范围 IP白名单
     */
    const SCOPE_IP = 2;

    /**
     * 上架状态
     */
    const STATUS_ON = 1;

    /**
     * 上架状态
     */
    const STATUS_OFF = 2;

    /**
     * 上架状态
     */
    const ALLOW_UPDATE = true;

    /**
     * 上下架状态
     */
    const STATUS_TYPE = [
        self::STATUS_ON => '上架',
        self::STATUS_OFF => '下架'
    ];

    /**
     * ip 白名单
     */
    const SCOPE_IP_SUFFIX = '_scope';

    /**
     * 表名
     *
     * @return string
     */
    public static function tableName()
    {
        return 'yp_appversion_version';
    }

    /**
     * 基本规则
     *
     * @return array
     */
    public function rules()
    {
        return [
            ['nameAttr', 'unique', 'targetAttribute' => ['app_id', 'platform', 'name', 'deleted_at'], 'message' => '该应用版本已经创建了！'],
            [['app_id', 'nameAttr', 'minNameAttr', 'type', 'scope', 'platform', 'desc'], 'required'],
            [['app_id', 'type', 'platform', 'scope', 'status', 'operated_id', 'is_del', 'created_at', 'updated_at', 'deleted_at'], 'integer'],
            [['desc', 'comment'], 'string'],
            [['nameAttr', 'minNameAttr'], 'match', 'pattern'=>'/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/', 'message'=>'格式形如为 999.999.999'],
            [['nameAttr', 'minNameAttr'], 'string', 'max' => 64],
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
            'app_id' => '所属应用',
            'name' => '版本名',
            'nameAttr' => '版本名',
            'min_name' => '最小版本名',
            'minNameAttr' => '最小版本名',
            'type' => '更新类型',
            'platform' => '平台',
            'scope' => '发布范围',
            'desc' => '版本描述',
            'comment' => '备注',
            'status' => '上架状态',
            'operated_id' => '操作人',
            'operator' => '操作人',
            'is_del' => 'Is Del',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
            'deleted_at' => 'Deleted At',
        ];
    }

    /**
     * 版本号修改器
     *
     * @return bool|float|int
     */
    public function getNameAttr()
    {
        return self::nameIntToStr($this->name);
    }

    /**
     * 版本号设置器
     *
     * @param $name
     */
    public function setNameAttr($name)
    {
        $this->name = self::nameStrToInt($name);
    }

    /**
     * 最小版本号修改器
     *
     * @return bool|float|int
     */
    public function getMinNameAttr()
    {
        return self::nameIntToStr($this->min_name);
    }

    /**
     * 最小版本号设置器
     *
     * @param $min_name
     */
    public function setMinNameAttr($min_name)
    {
        $this->min_name = self::nameStrToInt($min_name);
    }

    /**
     * 版本号转换
     *
     * @param string $name 版本号XXX.XXX.XXX
     *
     * @return bool|float|int
     */
    public static function nameStrToInt($name)
    {
        $ret = preg_match('/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/', $name);
        if ($ret) {
            list($major, $minor, $sub) = explode('.', $name);
            $versionCode = 1000000000 + $major * 1000000 + $minor * 1000 + $sub;
            return $versionCode;
        }
        return false;
    }

    /**
     * 版本号转换
     *
     * @param integer $name 1222333444
     *
     * @return bool|float|int
     */
    public static function nameIntToStr($name)
    {
        $ret = preg_match('/^[1][0-9]{9}$/', $name);
        if ($ret) {
            $sub = $name%1000;
            $minor = $name/1000%1000;
            $major = $name/1000000%1000;
            return $major . "." . $minor . "." . $sub;
        }
        return false;
    }

    /**
     * 应用关联
     */
    public function getApp()
    {
        return $this->hasOne(App::className(), ['id' => 'app_id']);
    }

    /**
     * 渠道关联
     *
     * @return ActiveQuery
     */
    public function getChannelVersions()
    {
        return $this->hasMany(ChannelVersion::className(), ['version_id' => 'id']);
    }

    /**
     * 渠道关联
     */
    public function getChannels()
    {
        return $this->hasMany(Channel::className(), ['id' => 'channel_id'])
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
     * 模型监控器
     *
     * @param bool $insert 插入与否
     *
     * @return bool
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                $this->status = self::STATUS_OFF;
            }
            $this->operated_id = Yii::$app->user->id;
            return true;
        } else {
            return false;
        }
    }

    /**
     * 保存以后更新缓存
     *
     * @param bool $insert 插入与否
     * @param array $changedAttributes 改变的参数
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        (new ChannelVersion())->flushCache($this->app_id);
    }

    /**
     * 删除操作
     */
    public function afterDelete()
    {
        parent::afterDelete();
        ChannelVersion::deleteAll(['version_id' => $this->id]);
        (new ChannelVersion())->flushCache($this->app_id);
    }
}
