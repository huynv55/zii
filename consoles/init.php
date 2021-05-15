<?php
require realpath(__DIR__.'/../bootstrap/inc/config.php');

//This function will take a given $file and execute it directly in php.
//This code is for use within a codeigntier framework application

//It tries them in that order and checks to make sure they WILL work based on various requirements of those options
class Init
{
	public static function execute_sql($file)
	{
		//1st method; directly via mysql
		$mysql_paths = array();

		//use mysql location from `which` command.
		$mysql = trim('which mysql');

		if (is_executable($mysql))
		{
			array_unshift($mysql_paths, $mysql);
		}

		//Default paths
		$mysql_paths[] = '/Applications/MAMP/Library/bin/mysql';  //Mac Mamp
		$mysql_paths[] = 'c:\xampp\mysql\bin\mysql.exe';//XAMPP

		$mysql_paths[] = '/usr/bin/mysql';  //Linux
		$mysql_paths[] = '/usr/local/mysql/bin/mysql'; //Mac
		$mysql_paths[] = '/usr/local/bin/mysql'; //Linux
		$mysql_paths[] = '/usr/mysql/bin/mysql'; //Linux

		$db_hostname = escapeshellarg( getenv('MYSQL_HOST') );
		$db_port = escapeshellarg(getenv('MYSQL_PORT'));
		$db_username= escapeshellarg(getenv('MYSQL_USER'));
		$db_password = escapeshellarg(getenv('MYSQL_PASSWORD'));

		$file_to_execute = escapeshellarg($file);
		foreach($mysql_paths as $mysql)
		{
			if (is_executable($mysql))
			{
					$execute_command = "\"$mysql\" --host=$db_hostname --port=$db_port --user=$db_username --password=$db_password < $file_to_execute";
					$status = false;
					system($execute_command, $status);
					return $status == 0;
			}
		}					
		return FALSE;
	}
}

$result = Init::execute_sql( realpath(__DIR__."/init.sql") );
if ($result) {
	echo "Success execute_sql ".realpath(__DIR__."/init.sql");
} else {
	echo "Error execute_sql ".realpath(__DIR__."/init.sql");
}
?>