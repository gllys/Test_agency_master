<?php
/**
 * 权限通常有两人维度（动作和操作对象），比如说“删除《七十二变官网》文章”这个权限有两个条件：1、特指删除动作，而不是查看、新增动作;2、特指七二变网官这个对象
 * 此接口表示的就是操作对象
 *
 */
interface UIResources{
	/**
	 * 获取所有资源
	 *
	 */
	public function getAllResources();
	
	/**
	 * 授权时回调
	 *
	 * @param string $userId
	 */
	public function afterAssign($resourceId);
	
	/**
	 * 取消授权时回调
	 *
	 * @param string $resourceId
	 */
	public function afterRevoke($resourceId);
}