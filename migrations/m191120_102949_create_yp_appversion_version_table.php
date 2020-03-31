<?php

use yii\db\Migration;

/**
 * 添加版本表
 */
class m191120_102949_create_yp_appversion_version_table extends Migration
{
    /**
     * 执行迁移
     */
    public function safeUp()
    {
        $this->createTable('{{%yp_appversion_version}}', [
            'id' => "int(11) NOT NULL AUTO_INCREMENT COMMENT '主键id'",
            'app_id' => "int(11) COMMENT '应用关联id'",
            'name' => "int(11) COMMENT '版本号 存储格式1999888777 展现形式999.888.777'",
            'min_name' => "int(11) COMMENT '最小版本号 存储格式1999888777 展现形式999.888.777'",
            'type' => "tinyint(1) NOT NULL DEFAULT '1' COMMENT '更新类型 1 一般更新 2 强制更新 3 静默更新 4 可忽略更新 5 静默可忽略更新'",
            'platform' => "tinyint(1) NOT NULL DEFAULT '0' COMMENT '平台 1 iOS 2 安卓 '",
            'scope' => "tinyint(1) NOT NULL DEFAULT '0' COMMENT '发布范围（1 全量、2 白名单、3 ip发布）'",
            'desc' => "text COMMENT '版本描述 最长字符'",
            'comment' => "text COMMENT '备注'",
            'status' => "tinyint(1) NOT NULL DEFAULT '0' COMMENT '上架状态 1 上架 2 下架'",
            'operated_id' => "int(11) COMMENT '用户id'",
            'is_del' => "tinyint(1) DEFAULT '0' COMMENT '状态；0正常；1主动删除；2后台删除'",
            'created_at' => "int(11) DEFAULT NULL COMMENT '创建时间'",
            'updated_at' => "int(11) DEFAULT NULL COMMENT '更新时间'",
            "deleted_at" => "int(11) DEFAULT NULL COMMENT '删除时间'",
            "PRIMARY KEY(`id`)"
        ], "ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COMMENT='版本表'");

        $this->createIndex('unique_index_app_id_name_platform', '{{%yp_appversion_version}}', ['app_id', 'platform', 'name'], true);
    }

    /**
     * 回滚迁移
     */
    public function safeDown()
    {
        $this->dropTable('{{%yp_appversion_version}}');
    }
}
