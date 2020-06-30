<?php
/*
 * @Created by: VSCode
 * @User: BigLop
 * @Date: 2020-06-20 16:13:41
 * @LastEditTime: 2020-06-24 14:08:57
 */

namespace app\admin\controller;

class System extends Base
{
    /**
     * 基本设置
     */
    public function xcx()
    {
        if ($this->request->isPost()) {
            $param = input('post.');
            $data['xxcg'] = $param['xxcg'];
            $data['gssl'] = $param['gssl'];
            $data['hrbm'] = $param['hrbm'];
            $res = model('Config')->set('xcx', $data);
            if ($res) {
                return json_return([], 1, '保存成功');
            }
            return json_return([], 1001, '保存失败');
        }
        $xcx_config = model('Config')->get('xcx');
        $this->assign([
            'title' => '基本设置',
            'xcx_config' => $xcx_config
        ]);
        return $this->label_fetch();
    }
}
