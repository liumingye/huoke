<?php
/*
 * @Created by: VSCode
 * @User: BigLop
 * @Date: 2020-06-26 09:15:36
 * @LastEditTime: 2020-06-26 14:14:26
 * @FilePath: \dev\application\api\controller\User.php
 */

namespace app\api\controller;

use app\common\model\Video;
use app\common\model\Follow;
// use think\Validate;

class User extends Base
{
    private $openid = '';
    private $session_key = '';
    private $logintag = '';
    private $sessionid = '';
    private $uniacid = '';

    public function initialize()
    {
        global $_W;
        $this->uniacid = $_W["uniacid"];
        if (isset($_GET["logintag"]) && isset($_GET["state"])) {
            /*$state = input('param.state');
            $logintag = input('param.logintag');
            $user = User::where(["id" => $logintag, "session3r" => $state])->find();
            if(!$user){
                die();
            }*/
            $this->logintag = input('param.logintag');
            $this->sessionid = input('param.state');
        }
    }

    /**
     * 用户登录接口
     */
    public function login()
    {
        global $_W;
        if (!isset($_GET["code"]) || trim($_GET["code"]) == '') {
            return json_return([], 1001, '非法操作');
        }
        // code -> openid
        $code = input("get.code");
        $url = "https://api.weixin.qq.com/sns/jscode2session?appid=" . trim($_W['account']->account['key']) . "&secret=" . trim($_W['account']->account['secret']) . "&js_code=" . $code . "&grant_type=authorization_code";
        $json = file_get_contents($url);
        $arr = json_decode($json, true);
        if (!isset($arr["session_key"]) || !isset($arr["openid"])) {
            return json_return([], 1002, '获取异常');
        }
        $this->openid = $arr["openid"];
        $this->session_key = $arr["session_key"];
        $arr["expires_in"] = time() + 7200;
        // 生成 sessionid 验证登录
        $this->sessionid = md5(time() . $this->openid);
        // 检查 openid 是否存在，返回 openid 对应的用户
        $res = model('User')->where(["openid" => $this->openid, "uniacid" => $this->uniacid])->find();
        if (!$res) {
            $res = model('User')->addUser(["openid" => $this->openid, "session_key" => $this->session_key, "expires_in" => $arr["expires_in"], "session3r" => $this->sessionid, "uniacid" => $this->uniacid]);
            if ($res) {
                $res = model('User')->where(["openid" => $this->openid])->find();
                $data = [
                    "logintag" => $res['id'],
                    "sessionid" => $this->sessionid,
                    "uniacid" => $this->uniacid
                ];
                return json_return($data, 1, '登录成功');
            } else {
                return json_return([], 1003, '用户注册失败');
            }
        } else {
            // 更新 sessionid
            model('User')->updateSession3r([
                'openid' => $this->openid,
                'session3r' => $this->sessionid
            ]);
            // 返回用户 id 和 sessionid
            $res = model('User')->where(["openid" => $this->openid])->find();
            $data = [
                "logintag" => $res['id'],
                "sessionid" => $this->sessionid,
                "uniacid" => $this->uniacid
            ];
            return json_return($data, 1, '登录成功');
        }
        return json_return([], 1004, '无此用户');
    }

    /**
     * 更新用户微信信息
     */
    public function updateWxInfo()
    {
        if (!$this->sessionid || !$this->logintag) {
            return json_return([], 1001, '用户未登录');
        }

        $nickname = input('param.nickname');
        $headimg = input('param.headimg');

        if (parse_url($headimg)["host"] != "wx.qlogo.cn") {
            return json_return([], 1002, '头像链接错误');
        }

        $res = model('User')->updateUser([
            'id' => $this->logintag,
            'session3r' => $this->sessionid,
            'nickname' =>  $nickname,
            'headimg' => $headimg
        ]);

        if ($res) {
            return json_return([], 1, '更新成功');
        }

        return json_return([], 1003, '更新失败');
    }

    /**
     * 根据ID 获取 用户信息 和 用户视频
     */
    public function getUserInfo()
    {
        $limit = input('param.limit', 10);
        $page = input('param.page', 0);
        $limit = min($limit, 50);
        $id = input('param.id');

        // 用户信息
        $user = User::where(['id' => $id])
            ->alias('u')
            ->field('u.nickname,u.headimg')
            ->find();

        // 关注数 粉丝数
        $follow = Follow::where(['fans' => $id])->count();
        $fans = Follow::where(['uid' => $id])->count();
        $user['follow'] = $follow;
        $user['fans'] = $fans;

        // 用户视频
        $video = Video::alias('c')
            ->where(['uid' => $id])
            ->limit($limit)
            ->page($page)
            ->field('c.*')
            ->select();

        if ($user) {
            $data = [
                'video' => $video,
                'user' => $user
            ];
            return json_return($data, 1, '调用成功');
        } else {
            return json_return([], 1001, '无此用户');
        }
    }

    /**
     * 根据ID 获取 关注列表
     */
    public function followers()
    {
        $limit = input('param.limit', 10);
        $page = input('param.page', 0);
        $limit = min($limit, 50);
        $id = input('param.id');

        $follow = Follow::alias('f')
            ->where(['fans' => $id])
            ->join('users u', 'u.id = f.uid')
            ->limit($limit)
            ->page($page)
            ->select();
        return json_return($follow, 1, '调用成功');
    }

    /**
     * 根据ID 获取 粉丝列表
     */
    public function following()
    {
        $limit = input('param.limit', 10);
        $page = input('param.page', 0);
        $limit = min($limit, 50);
        $id = input('param.id');

        $fans = Follow::alias('f')
            ->where(['uid' => $id])
            ->join('users u', 'u.id = f.fans')
            ->limit($limit)
            ->page($page)
            ->select();
        return json_return($fans, 1, '调用成功');
    }


    /**
     * 关注 & 取消关注
     */
    public function follow()
    {
        if (!$this->sessionid || !$this->logintag) {
            return json_return([], 1001, '用户未登录');
        }
        $user = User::where(["id" => $this->logintag, "session3r" => $this->state])->find();
        $userid = $user['id'];
        $fansid = input('param.id');
        if (request()->method() == "DELETE") {
            $res = Follow::where([
                'id' => $userid,
                'fans' => $fansid
            ])->delete();
            if ($res) {
                return json_return([], 1, '取消关注成功');
            } else {
                return json_return([], 1002, '取消关注失败');
            }
        } else {
            $follow = Follow::where(['id' => $userid, 'fans' => $fansid])->find();
            // 没有关注
            if (!$follow) {
                $res = Follow::insert([
                    'id' => $userid,
                    'fans' => $fansid
                ]);
                if ($res) {
                    return json_return([], 1, '关注成功');
                }
            }
            // 关注了
            return json_return([], 1003, '关注失败');
        }
    }
}
