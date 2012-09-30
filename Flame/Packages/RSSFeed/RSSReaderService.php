<?php
/**
 * RSSReaderService.php
 *
 * @author  Jiří Šifalda <sifalda.jiri@gmail.com>
 * @package Flame
 *
 * @date    30.09.12
 */

namespace Flame\Packages\RSSFeed;

class RSSReaderService extends RSSReader implements IService
{

	/**
	 * @var int
	 */
	protected $itemsLimit = 10;

	/**
	 * @var string
	 */
	protected $cacheLimit = '+ 22 hours';

	/**
	 * @var \Nette\Caching\Cache $cache
	 */
	protected $cache;

	/**
	 * @param $limit
	 */
	public function setItemsLimit($limit)
	{
		if((int) $limit > 0) $this->itemsLimit = (int) $limit;
	}

	/**
	 * @param $limit
	 */
	public function setExpiration($limit)
	{
		$this->cacheLimit = (string) $limit;
	}

	/**
	 * @param \Nette\Caching\Cache $cache
	 */
	public function injectCache(\Nette\Caching\Cache $cache)
	{
		$this->cache = $cache;
	}

	/**
	 * @param $url
	 * @return array
	 */
	public function load($url)
	{

		$key = 'rss-feed-' . $url . '-' . $this->itemsLimit;

		if(isset($this->cache[$key])){
			return $this->cache[$key];
		}

		$rss = $this->read($url, $this->itemsLimit);

		$this->cache->save($key, $rss, array(\Nette\Caching\Cache::EXPIRE => $this->cacheLimit));

		return $rss;
	}

}
