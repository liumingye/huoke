<?php
/*
 * @Created by: VSCode
 * @User: BigLop
 * @Date: 2020-06-23 09:35:28
 * @LastEditTime: 2020-06-27 11:16:14
 * @FilePath: \dev\application\admin\controller\Video.php
 */

namespace app\admin\controller;

class Video extends Base
{
    /**
     * 视频数据
     */
    public function list()
    {
        $this->assign([
            'title' => '视频数据'
        ]);
        return $this->label_fetch();
    }
    /**
     * 视频编辑 & 视频添加
     */
    public function info()
    {
        if ($this->request->isPost()) {
            $param = input('post.');
            $res = model('Video')->saveData($param);
            if ($res['code'] > 1) {
                return json_return([], $res['code'], $res['msg']);
            }
            return json_return([], 1, '保存成功');
        }

        $id = input('id');

        $res = model('Video')->infoData(['id' => $id]);
        $info = [
            'id' => '',
            'msg' => '',
            'status' => '',
            'duration' => '',
            'views' => '',
            'src' => '',
            'cover' => '',
            'create_time' => '',
            'user' => [
                'id' => '',
                'nickname' => ''
            ],
        ];
        if (isset($res['info'])) {
            $info = array_merge($info, $res['info']);
            $res = model('User')->infoData(['id' => $info['uid']]);
            if (isset($res['info'])) {
                $info['user'] = array_merge($info['user'], $res['info']);
            }
        }

        $this->assign([
            'title' => '视频信息',
            'info' => $info
        ]);
        return $this->label_fetch();
    }
    /**
     * 视频删除
     */
    public function del()
    {
        $ids = input("post.ids");
        if (!empty($ids)) {
            $where = [];
            $where[] = ['id', 'in', $ids];
            $res = model('Video')->delData($where);
            if ($res['code'] > 1) {
                return $this->error($res['msg']);
            }
            return $this->success($res['msg']);
        }
        return $this->error('参数错误');
    }
    /**
     * 视频接口
     */
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
            if (isset($search['status']) && $search['status'] != "") {
                $where[] = ['status', 'in', $search['status']];
            }
            if (isset($search['create_time']) && $search['create_time'] != "") {
                list($start_date, $end_date) = explode(' - ', str_replace('/', '-', $search['create_time']));
                $where[] = ['create_time', 'between time', [$start_date . " 00:00:00", $end_date . " 23:59:59"]];
            }
            if (isset($search['msg']) && $search['msg'] != "") {
                $search['msg'] = htmlspecialchars(urldecode($search['msg']));
                $where[] = ['msg', 'like', '%' . $search['msg'] . '%'];
            }
        }
        // 调用模型
        $res = model('Video')->listData($where, $order, $start, $length, ['user']);
        $recordsTotal = $recordsFiltered =  $res['total'];
        $data =  $res['list'];
        return json(compact('draw', 'recordsTotal', 'recordsFiltered', 'data'));
    }
}
