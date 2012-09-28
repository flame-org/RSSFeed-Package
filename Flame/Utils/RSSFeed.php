<?php
/**
 * RSSFeedControlFactory.php
 *
 * @author  Jiří Šifalda <sifalda.jiri@gmail.com>
 * @package Flame
 *
 * @date    30.08.12
 */

namespace Flame\Utils;

/**
 * RSS for PHP - small and easy-to-use library for consuming an RSS Feed
 */
class RSSFeed extends \Nette\Object
{

	/**
	 * @var int
	 */
	protected $limit = 15;

	/**
	 * @var \Nette\Caching\Cache $cache
	 */
	protected $cache;

	/**
	 * @param $limit
	 */
	public function setLimit($limit)
	{
		if((int) $limit > 0) $this->limit = (int) $limit;
	}

	/**
	 * @param \Nette\Caching\Cache $cache
	 */
	public function injectCache(\Nette\Caching\Cache $cache)
	{
		$this->cache = $cache;
	}

	/**
	* @param $content
	* @return mixed
	*/
	protected function findImage($content){
		$pattern = '/<img[^>]+src[\\s=\'"]';
		$pattern .= '+([^"\'>\\s]+)/is';

		if(preg_match($pattern,$content,$match)){
			return $match;
		}
	}

	/**
	 * @param $url
	 * @return array
	 */
	protected function load($url)
	{

		//$xml = @simplexml_load_file($url);
		$xml = @simplexml_load_file($url, 'SimpleXMLElement', LIBXML_NOCDATA);
		//$content = file_get_contents($url);
		//$xml = new \SimpleXmlElement($content);

		if($xml){
			$r = array();
			$counter = 0;

			foreach($xml->channel->item as $item){
				if($counter >= $this->limit) break;

				$namespaces = $item->getNameSpaces(true);

				if(isset($namespaces['content'])){
					$content = (string) $item->children($namespaces['content']);
				}else{
					$content = (string) $item->description;
				}

				$r[] = array(
					'date' => new \Nette\DateTime($item->pubDate),
					'link' => (string) $item->link,
					'title' => (string) $item->title,
					'description' => (string) $item->description,
					'category' => (string) $item->category,
					'content' => $content,
					'image' => $this->findImage($content),
				);

				$counter++;
			}

			return $r;
		}
	}

	/**
	 * @param $url
	 * @return \Nette\ArrayHash
	 */
	public function loadRss($url)
	{

		$key = 'rss-feed-' . $url . '-' . $this->limit;

		if(isset($this->cache[$key])){
			return $this->cache[$key];
		}

		$rss = $this->load($url);

		$this->cache->save($key, $rss, array(\Nette\Caching\Cache::EXPIRE => '+ 22 hours'));

		return $rss;
	}
	
}