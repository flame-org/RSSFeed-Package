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

		if(is_array($rss) and count($rss)){

			$_this = $this;
			$rss = array_map(function ($item) use ($_this){
				if(isset($item['title'])){
					$item['title'] = $_this->activeMetions($_this->activeLinks($item['title']));
				}
				return $item;
			}, $rss);
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
			$links = array_map(function ($match) {
				if(is_array($match) and count($match)){

					$match = array_map(function ($i) {
						return '<a href="http://twitter.com/' . str_replace('@', '', $i) . '" target="_blank">' . $i . '</a>';
					}, $match);

				}

				return $match;
			}, $matches);

			if(is_array($links) and count($links)){
				foreach($links as $k =>$link){
					$message = str_replace($matches[$k], $link, $message);
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
		$pattern = '/http.^ */';
		$re1='((?:http|https)(?::\\/{2}[\\w]+)(?:[\\/|\\.]?)(?:[^\\s"]*))';
		preg_match_all($re1, $message, $matches);

		if(count($matches)){
			$links = array_map(function ($match) {
				if(is_array($match) and count($match)){


					$match = array_map(function ($i) {
						return '<a href="' . $i . '" target="_blank">' . $i . '</a>';
					}, $match);

				}

				return $match;
			}, $matches);

			if(is_array($links) and count($links)){
				foreach($links as $k =>$link){
					$message = str_replace($matches[$k], $link, $message);
				}
			}
		}

		return $message;
	}

}
