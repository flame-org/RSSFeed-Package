<?php
/**
 * TwitterRSSReaderService.php
 *
 * @author  Jiří Šifalda <sifalda.jiri@gmail.com>
 * @package Flame
 *
 * @date    30.09.12
 */

namespace Flame\Packages\RSSFeed;

class TwitterRSSReaderService extends TwitterRSSReader implements IService
{

	/**
	 * @var int
	 */
	protected $itemsLimit = 5;

	/**
	 * @var string
	 */
	protected $cacheLimit = '+ 10 minutes';

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
	 * @param $username
	 * @return mixed
	 */
	public function load($username)
	{

		$key = 'twitter-rss-feed-' . $username . '-' . $this->itemsLimit;

		if(isset($this->cache[$key])){
			return $this->cache[$key];
		}

		$rss = $this->read($username, $this->itemsLimit);

		$this->cache->save($key, $rss, array(\Nette\Caching\Cache::EXPIRE => $this->cacheLimit));

		return $rss;
	}

}
