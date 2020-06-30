<?php
/*
 * @Created by: VSCode
 * @User: BigLop
 * @Date: 2020-06-26 10:05:16
 * @LastEditTime: 2020-06-26 11:54:05
 * @FilePath: \dev\config\huoke.php
 */

return [
    'db' => [
        // 数据库备份配置
        'backup_path' => '../addons/' . MODULE_NAME . '/application/data/backup/database/',
        'part_size' => 20971520,
        'compress' => 1,
        'compress_level' => 4,
    ]
];
