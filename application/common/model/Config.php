<?php
/*
 * @Created by: VSCode
 * @User: BigLop
 * @Date: 2020-06-22 09:14:50
 * @LastEditTime: 2020-06-24 14:05:30
 * @FilePath: \dev\application\common\model\Config.php
 */ 

namespace app\common\model;

class Config extends Base
{
    protected $name = 'config';

    protected function convertData($configItem)
    {
        if (!empty($configItem)) {
            switch ($configItem['ctype']) {
                case 1:
                    $configItem['data'] = intval($configItem['data']);
                    break;
                case 2:
                    $configItem['data'] = unserialize($configItem['data']);
                    break;
            }
        }
        return $configItem;
    }
    /**
     * 获取
     * @param string $cname 名称
     * @param string $key 数据键名
     * @return mixed
     */
    public function get($cname, $key = 'data')
    {
        $item = $this->where(['cname' =>  $this->uniacid . "_" . $cname])->find();
        if (!empty($item)) {
            $item = $item->toArray();
            $item = $this->convertData($item);
        }
        return $key ? $item[$key] : $item;
    }
    /**
     * 设置
     * @param string $cname 名称
     * @param string $value 数据
     */
    public function set($cname, $value)
    {
        $data = array('cname' => $this->uniacid . "_" . $cname, 'ctype' => 0);
        if (is_array($value)) {
            $data['ctype'] = 2;
            $data['data'] = serialize($value);
        } elseif (is_integer($value)) {
            $data['ctype'] = 1;
            $data['data'] = intval($value);
        } else {
            $data['data'] = $value;
        }
        $data['dateline'] = time();
        return $this->insert($data, true);
    }
}
