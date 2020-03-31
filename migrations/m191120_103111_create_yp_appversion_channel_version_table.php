<?php

use yii\db\Migration;

/**
 * 添加版本渠道中间关联表
 */
class m191120_103111_create_yp_appversion_channel_version_table extends Migration
{
    /**
     * 执行迁移
     */
    public function safeUp()
    {
        $this->createTable('{{%yp_appversion_channel_version}}', [
            'id' => "int(11) NOT NULL AUTO_INCREMENT COMMENT '主键id'",
            'version_id' => "int(11) COMMENT '版本关联id'",
            'channel_id' => "int(11) COMMENT '渠道主键id'",
            'url' => "varchar(255) COMMENT '安卓对应该渠道的 APK 下载地址， iOS 为 appstore 地址'",
            'size' => "int(11) DEFAULT '0' COMMENT 'apk 大小'",
            'operated_id' => "int(11) COMMENT '用户id'",
            'is_del' => "tinyint(1) DEFAULT '0' COMMENT '状态；0正常；1主动删除；2后台删除'",
            'created_at' => "int(11) DEFAULT NULL COMMENT '创建时间'",
            'updated_at' => "int(11) DEFAULT NULL COMMENT '更新时间'",
            "deleted_at" => "int(11) DEFAULT NULL COMMENT '删除时间'",
            "PRIMARY KEY(`id`)"
        ], "ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COMMENT='版本渠道关联信息表'");

        $this->createIndex('index_version_channel_id', '{{%yp_appversion_channel_version}}', ['version_id', 'channel_id'], false);
    }

    /**
     * 回滚迁移
     */
    public function safeDown()
    {
        $this->dropTable('{{%yp_appversion_channel_version}}');
    }
}
