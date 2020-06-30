<?php
/*
 * @Created by: VSCode
 * @User: BigLop
 * @Date: 2020-06-26 09:15:36
 * @LastEditTime: 2020-06-27 11:38:35
 * @FilePath: \dev\application\api\controller\Video.php
 */

namespace app\api\controller;

class Video extends Base
{
	/**
	 * 获取视频列表
	 */
	public function list()
	{
		$start = input('param.page', 0);
		$length = min(input('param.limit', 10), 50);
		$list = model('Video')->listData(['status' => 1], 'create_time desc', $start, $length, ['user' => ['where' => 'u.status=1']], 'v.id,uid,msg,src,cover,v.create_time,views,nickname,signature,headimg,certification,certification_type');
		if ($list['code'] > 1) {
			return json_return([], $list['code'], $list['msg']);
		}
		return json_return($list['list'], 1, '调用成功');
	}
}
