<?php

class Logger
{
	private static $mode = 1;			// 0 - silent, 1 - normal
	private static $log_file = 'log.txt';

	public static function apiError($data, $user_id = null)
	{
		if(is_string($data)) {
			self::toFile($data);
			if(self::$mode == 1) {
				$user_id ? self::print($data . ", user_id: $user_id") : self::print($data);
			}
			
		}
		else {
			self::toFile($data->error_msg);
			if(self::$mode == 1) {
				$user_id ? self::print($data->error_msg . ", user_id: $user_id") : self::print($data->error_msg);
			}

		}
	}

	public static function print($str, $newLine = true)
	{
		self::toFile($str);
		if(self::$mode == 1) {
			echo $newLine ? $str . "\n" : $str;
		}
	}

	public static function setMode($mode = 1)
	{
		self::$mode = $mode;
	}

	public static function printTable($data)
	{
		Logger::print(str_repeat("-", exec('tput cols')));
		if(!is_array($data)) {
			Logger::print($data);
		}
		else {
			foreach ($data as $row) {
				if(!is_array($row)) {
					Logger::print($row . "\t", false);
				}
				else {
					foreach ($row as $val) {
						Logger::print($val . "\t", false);
					}
					
				}
				Logger::print("");
			}
		}
		
		Logger::print(str_repeat("-", exec('tput cols')));	
	}

	private static function toFile($msg)
	{
		if($f = fopen(self::$log_file, 'a')) {
			fwrite($f, date('d/m H:i:s', time()) . " - " . $msg . "\n");
			fclose($f);
		}
	}

	public static function printHelp()
	{
		self::print("Parameters:

	Required (one of them):
	-u <user_id> - get all photos of user with <user_id>
	-f <list.csv> - open csv file with list of user_ids and to get all their photos.You can find sample file in repo.
	-l - show list of users, already downloaded into DB
	-s <user_id> - find user and all his photos and albums by id

	Optional:
	-q - quiet mode - do not output anything to command line
	-d - direct connection. By default it uses proxy, but you can disable it by -d flag. You can change proxy server in apiManager.php file");
	}
}