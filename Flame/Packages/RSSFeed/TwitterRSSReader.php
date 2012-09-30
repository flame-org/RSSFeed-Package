<?php
/**
 * TwitterRSSFeed.php
 *
 * @author  Jiří Šifalda <sifalda.jiri@gmail.com>
 * @package Flame
 *
 * @date    30.08.12
 */

namespace Flame\Packages\RSSFeed;

class TwitterRSSReader extends \Nette\Object
{

	protected function read($username, $limit = 10)
	{

		$url = 'http://twitter.com/statuses/user_timeline/' . $username . '.rss';
		$xml = @simplexml_load_file($url);

		if($xml){
			$r = array();
			$counter = 0;

			foreach($xml->channel->item as $item){
				if($counter >= $limit) break;

				$r[] = array(
					'date' => new \Nette\DateTime($item->pubDate),
					'link' => (string) $item->link,
					'title' => $this->activeHashTags($this->activeMetions($this->activeLinks((string) $item->title))),
				);

				$counter++;
			}

			return $r;
		}
	}

	/**
	 * @param $message
	 * @return mixed
	 */
	public function activeMetions($message)
	{

		$pattern = '/@[a-zA-Z]*/';
		preg_match_all($pattern, $message, $matches);

		if(count($matches)){
			foreach($matches as $match){
				if(count($match)){
					foreach($match as $i){
						$link = '<a href="http://twitter.com/' . str_replace('@', '', $i) . '" target="_blank">' . $i . '</a>';
						$message = str_replace($i,$link, $message);
					}
				}
			}
		}

		return $message;
	}

	/**
	 * @param $message
	 * @return mixed
	 */
	public function activeLinks($message)
	{
		$pattern = '((?:http|https)(?::\\/{2}[\\w]+)(?:[\\/|\\.]?)(?:[^\\s"]*))';
		preg_match_all($pattern, $message, $matches);

		if(count($matches)){
			foreach($matches as $match){
				if(count($match)){
					foreach($match as $i){
						$link = '<a href="' . $i . '" target="_blank">' . $i . '</a>';
						$message = str_replace($i,$link, $message);
					}
				}
			}
		}

		return $message;
	}

	public function activeHashTags($message)
	{

		$pattern = '/#[a-zA-Z]*/';
		preg_match_all($pattern, $message, $matches);


		if(count($matches)){
			foreach($matches as $match){
				if(count($match)){
					foreach($match as $i){
						$link =  '<a href="https://twitter.com/i/#!/search?q=' . str_replace('#', '%23', $i) . '" target="_blank">' . $i . '</a>';
						$message = str_replace($i,$link, $message);
					}
				}
			}
		}

		return $message;
	}


}
