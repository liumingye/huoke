<?php
/*
 * @Created by: VSCode
 * @User: BigLop
 * @Date: 2020-06-23 15:00:07
 * @LastEditTime: 2020-06-24 17:14:18
 * @FilePath: \dev\application\admin\controller\User.php
 */

namespace app\admin\controller;

class User extends Base
{
    public function list()
    {
        $this->assign([
            'title' => '用户数据'
        ]);
        return $this->label_fetch();
    }

    public function info()
    {
        if ($this->request->isPost()) {
            $param = input('post.');
            $res = model('User')->saveData($param);
            if ($res['code'] > 1) {
                return json_return([], $res['code'], $res['msg']);
            }
            return json_return([], 1, '保存成功');
        }

        $id = input('id');

        $res = model('User')->infoData(['id' => $id]);
        $info = [
            'id' => '',
            'nickname' => '',
            'signature' => '',
            'mobile' => '',
            'wechat' => '',
            'headimg' => '',
            'money' => '',
            'certification' => '',
            'certification_type' => '',
            'status' => ''
        ];

        if (isset($res['info'])) {
            $info = array_merge($info, $res['info']);
        }

        $this->assign([
            'title' => '用户信息',
            'info' => $info
        ]);
        return $this->label_fetch();
    }

    public function data()
    {

        if (!$this->request->isPost()) {
            $this->error('请求错误');
        }
        // 表格参数
        $draw = input('param.draw'); // 计数器
        $columns = input('param.columns');
        $length = min(input('param.length', 10), 100);
        $start = input('param.start', 0);
        // 表格排序
        $param = input('param.order');
        $order = [];
        if (isset($param)) {
            foreach ($param as $val) {
                $order[$columns[$val['column']]['data']] = $val['dir'];
            }
        }
        // 表格搜索
        $where = [];
        $param = input('post.search');
        if (isset($param)) {
            $search = [];
            foreach ($param as $val) {
                if (isset($val['name']) && isset($val['value'])) {
                    if (array_key_exists($val['name'], $search)) {
                        $search[$val['name']] .=  ',' . $val['value'];
                    } else {
                        $search[$val['name']] = $val['value'];
                    }
                }
            }
            if (isset($search['nickname']) && $search['nickname']) {
                $search['nickname'] = htmlspecialchars(urldecode($search['nickname']));
                $where[] = ['nickname', 'like', '%' . $search['nickname'] . '%'];
            }
        }

        $res = model('User')->listData($where, $order, $start, $length);
        $total =  $res['total'];
        $data =  $res['list'];

        return json(['draw' => $draw, 'recordsTotal' => $total, 'recordsFiltered' => $total, 'data' => $data]);
    }
}
