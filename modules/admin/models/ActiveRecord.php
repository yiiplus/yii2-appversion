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

use yii\behaviors\TimestampBehavior;

/**
 * ActiveRecord 模型基类
 *
 * @category  PHP
 * @package   Yii2
 * @author    陈思辰 <chensichen@mocaapp.cn>
 * @copyright 2019 重庆次元能力科技有限公司
 * @license   https://www.moego.com/licence.txt Licence
 * @link      http://www.moego.com
 */
class ActiveRecord extends \yii\db\ActiveRecord
{
    /**
     * NOT_DELETED 是删除状态，它表示这条数据未被删除
     */
    const NOT_DELETED = 0;

    /**
     * ACTIVE_DELETE 是删除状态，它表示这条数据被主动删除
     */
    const ACTIVE_DELETE = 1;

    /**
     * BACKGROUND_DELETE 是删除状态，它表示这台数据被后台删除
     */
    const BACKGROUND_DELETE = 2;

    /**
     * FONT_END_PAGESIZE 是前端常用每页数目
     */
    const FONT_END_PAGESIZE = 21;

    /**
     * PAGENUM 是页数
     */
    const PAGE_NUM = 0;

    /**
     * APP IOS 版本
     */
    const MOEGO_IOS_VERSION = 26;

    /**
     * APP 安卓版本
     */
    const MOEGO_ANDROID_VERSION = 21;

    /**
     * 自动时间戳
     *
     * @return array
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }
}
