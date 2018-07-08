<?php

namespace src;

class App
{
	
	private $db;
	
	public function __construct()
	{
		$this->db = new Database();
	}
	
	public function boot()
	{
		$array = explode('/',$_SERVER[ 'REQUEST_URI' ]);
		if (in_array('u',$array)) {
			$this->runRedirect();
		} else {
			return $this->runShortener();
		}
	}
	
	private function runShortener()
	{
		if ($this->isUrlSet()) {
			$originalUrl = $this->isUrlSet();
			$exists = $this->db->exists($originalUrl);
			if (mysqli_num_rows($exists) == 0) {
				$shortUrl = $this->makeShort();
				$this->db->set($originalUrl, $shortUrl);
				$result = ['short'=>$shortUrl];
				return $result;
			} else {
				$result = mysqli_fetch_assoc($exists);
				return $result;
			}
		}
	}
	
	private function isUrlSet()
	{
		if (isset($_POST['originalUrl']) and !empty(isset($_POST['originalUrl']))) {
			$originalUrl = $_POST['originalUrl'];
			$originalUrl = str_replace('http://','',$originalUrl);
			$originalUrl = str_replace('https://','',$originalUrl);
			return $originalUrl;
		} else {
			return false;
		}
	}
					
	private function makeShort()
	{
		$shortUrl = substr(md5(microtime()),rand(0,26),5);
		return $shortUrl;
	}
	
	private function runRedirect()
	{
		$uri = $_SERVER[ 'REQUEST_URI' ];
		$route = ltrim($uri, '/');
		$route = rtrim($route, '/');
		$route = htmlentities($route);
		$route = explode('/', $route);
		if (in_array('u',$route)) {
			$shortUrl = array_pop($route);
			$result = $this->db->get($shortUrl);
			if (mysqli_num_rows($result) == 1) {
				$originalUrl = mysqli_fetch_assoc($result);
				$originalUrl = $originalUrl['original'];
				header('Location: http://'. $originalUrl);
			}
		}
	}
	
}
