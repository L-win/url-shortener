<?php
 
namespace src;
 
class Database
{
	
	private $con;
	
	public function __construct()
	{
		$this->con = $this->connect();
	}
	
	private function connect()
	{
		// return mysqli_connect('','','','');
	}
	
	public function get($shortUrl)
	{
		$shortUrl = mysqli_escape_string($this->con, $shortUrl);
		$sql = " SELECT * FROM links WHERE short = '$shortUrl' ";
		$query = mysqli_query($this->con, $sql);
		return $query;
	}
	
	public function set($originalUrl,$shortUrl)
	{
		$originalUrl = mysqli_escape_string($this->con, $originalUrl);
		$sql = " INSERT INTO links(original,short) VALUES('$originalUrl', '$shortUrl') ";
		$query = mysqli_query($this->con, $sql);
	}
	
	public function exists($originalUrl)
	{
		$originalUrl = mysqli_escape_string($this->con, $originalUrl);
		$sql = " SELECT * FROM links WHERE original = '$originalUrl' ";
		$query = mysqli_query($this->con,$sql);
		return $query;
	}
	
}
