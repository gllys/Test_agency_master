<?php
/**
 * UCacheDependency类文件
 * @author $Author: chengjian $
 * @copyright Copyright &copy; 2009-2011 uuzu
 * @version $Id: UCacheDependency.php 55351 2011-10-15 09:09:46Z chengjian $
 * @package common.caching.dependencies
 * 
 * UCacheDependency实现一个缓存值是否过期依赖另一缓存值的功能
 */

/**
 * CExpressionDependency represents a dependency based on the result of a PHP expression.
 *
 * CExpressionDependency performs dependency checking based on the
 * result of a PHP {@link expression}.
 * The dependency is reported as unchanged if and only if the result is
 * the same as the one evaluated when storing the data to cache.
 *
 * @author $Author: chengjian $
 * @version $Id: UCacheDependency.php 55351 2011-10-15 09:09:46Z chengjian $
 * @package system.caching.dependencies
 * @since 1.0
 */
class UCacheDependency extends CCacheDependency
{
	/**
	 * 配置文件中的cache配置
	 *
	 * @var string
	 */
	public $cacheID='cache';
	
	public $dependencyCacheKey;
	
	private $_cache;

	/**
	 * Constructor.
	 * @param string $expression the PHP expression whose result is used to determine the dependency.
	 */
	public function __construct($dependencyCacheKey)
	{
		if($dependencyCacheKey===null) throw new CException('dependencyCacheKey不可为空');
		$this->dependencyCacheKey=$dependencyCacheKey;
	}

	/**
	 * Generates the data needed to determine if dependency has been changed.
	 * This method returns the result of the PHP expression.
	 * @return mixed the data needed to determine if dependency has been changed.
	 */
	protected function generateDependentData()
	{
		$cache = $this->getCache();	
		return $cache->get($this->dependencyCacheKey);
	}
	
	/**
	 * @return cache the CCache instance
	 * @throws CException if {@link cacheID} does not point to a valid application component.
	 */
	protected function getCache()
	{		
		if(($this->_cache=Yii::app()->getComponent($this->cacheID)) instanceof CCache)
			return $this->_cache;
		else
			throw new CException(Yii::t('yii','UCacheDependency.cacheID "{id}" is invalid. Please make sure it refers to the ID of a CCache application component.',
				array('{id}'=>$this->cacheID)));		
	}
}
