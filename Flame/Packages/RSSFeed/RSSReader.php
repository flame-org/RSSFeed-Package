<?php
/**
 * RSSReader.php
 *
 * @author  Jiří Šifalda <sifalda.jiri@gmail.com>
 * @package Flame
 *
 * @date    30.08.12
 */

namespace Flame\Packages\RSSFeed;

/**
 * RSS for PHP - small and easy-to-use library for consuming an RSS Feed
 */
class RSSReader extends \Nette\Object
{

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
	 * @param int $limit
	 * @return array
	 */
	protected function read($url, $limit = 5)
	{

		//$xml = @simplexml_load_file($url);
		$xml = @simplexml_load_file($url, 'SimpleXMLElement', LIBXML_NOCDATA);
		//$content = file_get_contents($url);
		//$xml = new \SimpleXmlElement($content);

		if($xml){
			$r = array();
			$counter = 0;

			foreach($xml->channel->item as $item){
				if($counter >= $limit) break;

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
	
}