<?php
interface UIUser{
	/**
	 * 获取用户列表
	 *
	 */
	public function getUserList();
	
	/**
	 * 授权时回调
	 *
	 * @param string $userId
	 */
	public function afterAssign($userId);
	
	/**
	 * 取消授权时回调
	 *
	 * @param string $resourceId
	 */
	public function afterRevoke($userId);
}