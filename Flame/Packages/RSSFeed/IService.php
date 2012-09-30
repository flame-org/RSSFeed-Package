<?php
/**
 * IService.php
 *
 * @author  Jiří Šifalda <sifalda.jiri@gmail.com>
 * @package Flame
 *
 * @date    30.09.12
 */

namespace Flame\Packages\RSSFeed;

interface IService
{

	/**
	 * @param \Nette\Caching\Cache $cache
	 */
	public function injectCache(\Nette\Caching\Cache $cache);

	/**
	 * @param $limit
	 */
	public function setExpiration($limit);

	/**
	 * @param $limit
	 */
	public function setItemsLimit($limit);

	/**
	 * @param $url
	 * @return array
	 */
	public function load($url);

}
