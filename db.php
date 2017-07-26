<?php

require_once ('config.php');

class DB
{
	private static $DB = null;

	private function __construct() { }

	public static function getInstance()
	{
		if($DB == null) {
			try {
				$dsn = DB . ":host=". HOST. ";dbname=". DB_NAME. "";
				self::$DB = new PDO($dsn, USERNAME, PASSWORD);
				self::$DB->exec("SET NAMES utf8");
			}
			catch (Exception $e) {
				throw $e;
			}
			
		}
		return self::$DB;
	}
}