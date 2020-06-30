<?php
/*
 * @Created by: VSCode
 * @User: BigLop
 * @Date: 2020-06-09 14:44:51
 * @LastEditTime: 2020-06-24 12:58:34
 */

namespace app\common\model;

class User extends Base
{
    // 设置数据表（不含前缀）
    protected $name = 'user';

    // 自动写入时间戳
    protected $autoWriteTimestamp = 'datetime';

    /**
     * 列出数据
     */
    public function listData($where, $order, $start = 0, $length = 10, $with = [], $field = '*')
    {
        $total = $this->where($where)->count();
        $list = $this->field($field)->where($where)->order($order)->limit($start, $length)->select();
        return ['code' => 1, 'msg' => '数据列表', 'total' => $total, 'list' => $list];
    }

    /**
     * 读取数据
     */
    public function infoData($where, $field = '*')
    {
        if (empty($where) || !is_array($where)) {
            return ['code' => 1001, 'msg' => '参数错误'];
        }
        $info = $this->field($field)->where($where)->find();
        if (empty($info)) {
            return ['code' => 1002, 'msg' => '获取数据失败'];
        }
        $info = $info->toArray();

        unset($info['session_key']);
        unset($info['session3r']);

        return ['code' => 1, 'msg' => '获取成功', 'info' => $info];
    }

    /**
     * 保存数据
     */
    public function saveData($data)
    {
        $validate = validate('User');
        if (!$validate->check($data)) {
            return ['code' => 1001, 'msg' => '参数错误：' . $validate->getError()];
        }
        $data['update_time'] = date("Y-m-d H:i:s", time());
        // 自动判断 插入 或 更新
        if (!empty($data['id'])) {
            $res = $this->allowField(true)->where(['id' => $data['id']])->update($data);
        } else {
            $data['create_time'] = date("Y-m-d H:i:s", time());
            $data['uniacid'] = $this->uniacid;
            $res = $this->allowField(true)->insert($data);
        }
        if (false === $res) {
            return ['code' => 1002, 'msg' => '保存失败：' . $this->getError()];
        }
        return ['code' => 1, 'msg' => '保存成功'];
    }

    /**
     * 增加用户
     */
    public function addUser($data)
    {
        return $this->allowField(true)->save($data);
    }

    /**
     * 更新用户信息
     */
    public function updateUser($data)
    {
        return $this->allowField(true)->isUpdate(true, [
            'id' => $data['id'],
            'session3r' => $data['session3r']
        ])->save($data);
    }

    /**
     * 更新用户 Session3r
     * $data ["openid"]
     */
    public function updateSession3r($data)
    {
        return $this->allowField('session3r')->isUpdate(true, [
            'openid' => $data['openid']
        ])->save($data);
    }
}
