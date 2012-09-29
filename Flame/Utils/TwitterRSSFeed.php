<?php
/**
 * TwitterRSSFeed.php
 *
 * @author  Jiří Šifalda <sifalda.jiri@gmail.com>
 * @package Flame
 *
 * @date    30.08.12
 */

namespace Flame\Utils;

class TwitterRSSFeed extends RSSFeed
{

	protected function load($username)
	{
		$url = 'http://twitter.com/statuses/user_timeline/' . $username . '.rss';
		return parent::load($url);
	}

	public function loadRss($username)
	{

		$key = 'twitter-rss-feed-' . $username . '-' . $this->limit;

		if(isset($this->cache[$key])){
			return $this->cache[$key];
		}

		$rss = $this->load($username);

		if(count($rss)){

			foreach($rss as $item){
				if(isset($item['title'])){
					$item['title'] = $this->activeHashTags($this->activeMetions($this->activeLinks($item['title'])));
				}
			}
		}

		$this->cache->save($key, $rss, array(\Nette\Caching\Cache::EXPIRE => '+ 10 minutes'));

		return $rss;
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
