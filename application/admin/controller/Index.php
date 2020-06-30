<?php
/*
 * @Created by: VSCode
 * @User: BigLop
 * @Date: 2020-06-12 16:29:56
 * @LastEditTime: 2020-06-27 14:32:31
 */

namespace app\admin\controller;

use app\common\model\Video;
use app\common\model\User;
use app\common\taglib\Url;

class Index extends Base
{
    public function index()
    {
        $title = '获客视频';

        $adminUser = $this->adminUser;

        $sidebar_nav = [
            [
                'type' => 'header',
                'name' => '基本',
            ],
            [
                'type' => 'a',
                'name' => '控制台',
                'icon' => 'la la-tachometer-alt',
                'url' => Url::tagTpurl(['url' => 'admin/index/dashboard'])
            ],
            [
                'type' => 'header',
                'name' => '应用'
            ],
            [
                'type' => 'nav',
                'name' => '系统',
                'icon' => 'la la-cog',
                'data' => [
                    [
                        'type' => 'a',
                        'name' => '小程序设置',
                        'url' => Url::tagTpurl(['url' => 'admin/system/xcx'])
                    ],
                ]
            ],
            [
                'type' => 'nav',
                'name' => '视频',
                'icon' => 'la la-video',
                'data' => [
                    [
                        'type' => 'a',
                        'name' => '视频数据',
                        'url' => Url::tagTpurl(['url' => 'admin/video/list'])
                    ],
                    [
                        'type' => 'a',
                        'name' => '添加视频',
                        'url' => Url::tagTpurl(['url' => 'admin/video/info'])
                    ]
                ]
            ],
            [
                'type' => 'nav',
                'name' => '用户',
                'icon' => 'la la-user',
                'data' => [
                    [
                        'type' => 'a',
                        'name' => '用户数据',
                        'url' => Url::tagTpurl(['url' => 'admin/user/list'])
                    ]
                ]
            ],
            [
                'type' => '工具',
                'name' => '应用'
            ],
            [
                'type' => 'nav',
                'name' => '数据库',
                'icon' => 'la la-database',
                'data' => [
                    [
                        'type' => 'a',
                        'name' => '数据库管理',
                        'url' => Url::tagTpurl(['url' => 'admin/database/export'])
                    ],
                    [
                        'type' => 'a',
                        'name' => '执行SQL语句',
                        'url' => Url::tagTpurl(['url' => 'admin/database/sql'])
                    ],
                    // [
                    //     'type' => 'a',
                    //     'name' => '数据批量替换',
                    //     'url' => Url::tagTpurl(['url' => 'admin/index/index'])
                    // ]
                ]
            ],
        ];
        $this->assign(compact('title', 'adminUser', 'sidebar_nav'));

        return $this->label_fetch();
    }

    public function dashboard()
    {
        global $_W;

        $title = '控制台';

        $video_num = Video::count();
        $user_num = User::count();

        $board = [
            '视频数量' => [
                'icon' => 'la la-file-video',
                'num' => $video_num,
                'bg' => 'indigo'
            ],
            '商品数量' => [
                'icon' => 'la la-shopping-bag',
                'num' => '0',
                'bg' => 'azura'
            ],
            '订单数' => [
                'icon' => 'la la-list',
                'num' => '0',
                'bg' => 'orange'
            ],
            '用户数' => [
                'icon' => 'la la-user',
                'num' => $user_num,
                'bg' => 'pink'
            ],
        ];

        $carousel = [
            [
                'title' => '运行环境',
                'text' => PHP_OS . " ({$_SERVER['SERVER_SOFTWARE']})"
            ],
            [
                'title' => '服务器IP/端口',
                'text' => $_SERVER['HTTP_HOST']
            ],
            [
                'title' => 'PHP版本',
                'text' => PHP_VERSION
            ],
            [
                'title' => 'ThinkPHP版本',
                'text' => app()->version()
            ],
            [
                'title' => '最大上传限制',
                'text' => get_cfg_var("file_uploads") ? get_cfg_var("upload_max_filesize") : "<strong class='text-danger'>×</strong>"
            ],
            [
                'title' => '服务器日期',
                'text' => date('Y-m-d')
            ]
        ];

        $version = $_W['current_module']['version'];

        $new_users = User::whereNotNull('nickname')->whereNotNull('headimg')->limit(7)->select();

        $this->assign(compact('title', 'board', 'carousel', 'version', 'new_users'));

        return $this->label_fetch();
    }
}
