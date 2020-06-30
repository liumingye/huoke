<?php
/*
 * @Created by: VSCode
 * @User: BigLop
 * @Date: 2020-06-26 09:15:36
 * @LastEditTime: 2020-06-26 13:28:04
 * @FilePath: \dev\application\api\controller\Shop.php
 */ 

namespace app\api\controller;

use app\common\model\Product;
use app\common\model\Category;

class Shop extends Base
{

	// 获取指定分类的所有子分类ID号
	private function getAllChildcateIds($categoryID)
	{
		//初始化ID数组
		$array[] = $categoryID;
		do {
			$ids = '';
			$where['parentid'] = array('in', $categoryID);
			$cate = Category::where($where)->select();
			foreach ($cate as $v) {
				$array[] = $v['id'];
				$ids .= ',' . $v['id'];
			}
			$ids = substr($ids, 1, strlen($ids));
			$categoryID = $ids;
		} while (!empty($cate));
		return $array; //返回数组
	}

	/**
	 * 获取商品列表
	 */
	public function getProductList()
	{
		$limit = input('param.limit', 10);
		$page = input('param.page', 0);
		$limit = min($limit, 50);
		$id = input('param.id');
		if (!isset($id)) {
			return json_return([], 1001, '缺少参数id');
		}
		$ids = $this->getAllChildcateIds($id);
		$lists = Product::alias('p')
			->where('type', 'in', $ids)
			->order('p.create_time desc')
			->limit($limit)
			->page($page)
			->field('p.name,p.main_image,p.sub_image,p.detail,p.price,p.stock')
			->select();
		return json_return($lists, 1, '调用成功');
	}

	/**
	 * 获取商品详情
	 */
	public function getProductInfo()
	{
		$limit = input('param.limit', 10);
		$page = input('param.page', 0);
		$limit = min($limit, 50);
		$id = input('param.id');
		if (!isset($id)) {
			return json_return([], 1001, '缺少参数id');
		}
		$ids = $this->getAllChildcateIds($id);
		$lists = Product::alias('p')
			->where('type', 'in', $ids)
			->order('p.create_time desc')
			->limit($limit)
			->page($page)
			->field('p.name,p.main_image,p.sub_image,p.detail,p.price,p.stock')
			->select();
		return json_return($lists, 1, '调用成功');
	}

}
