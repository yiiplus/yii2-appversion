<?php

use yii\db\Migration;

/**
 * 添加渠道表
 */
class m191120_103045_create_yp_appversion_channel_table extends Migration
{
    /**
     * 执行迁移
     */
    public function safeUp()
    {
        $this->createTable('{{%yp_appversion_channel}}', [
            'id' => "int(11) NOT NULL AUTO_INCREMENT COMMENT '主键id'",
            'name' => "varchar(64) COMMENT '渠道名称'",
            'platform' => "tinyint(1) COMMENT '平台 1 iOS 2 安卓'",
            'code' => "varchar(64) COMMENT '渠道码 安卓官方包渠道码为official，其他渠道则另加，iOS仅有一个渠道为 official'",
            'status' => "tinyint(1) COMMENT '状态 1 正常 2 废弃'",
            'operated_id' => "int(11) COMMENT '用户id'",
            'is_del' => "tinyint(1) DEFAULT '0' COMMENT '状态；0正常；1主动删除；2后台删除'",
            'created_at' => "int(11) DEFAULT NULL COMMENT '创建时间'",
            'updated_at' => "int(11) DEFAULT NULL COMMENT '更新时间'",
            "deleted_at" => "int(11) DEFAULT NULL COMMENT '删除时间'",
            "PRIMARY KEY(`id`)"
        ], "ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COMMENT='渠道表'");

        $this->batchInsert(
            '{{%yp_appversion_channel}}',
            ['id', 'name', 'platform', 'code', 'status'],
            [
                [1, '苹果商店', 1, 'official', 1],
                [2, '安卓官方包', 2, 'official', 1],
            ]
        );
    }

    /**
     * 回滚迁移
     */
    public function safeDown()
    {
        $this->dropTable('{{%yp_appversion_channel}}');
    }
}
