<?php
require realpath(__DIR__.'/notorm/NotORM.php');
class NotORMModel {
	public static $connection = null;
	public static $model = null;

	private function getConfig() {
		return Config::get("db");
	}
	public function setConnection($connect = null, $option = null) {
		if (!empty($connect)) {
			self::$connection = $connect;
			return $this;
		}
		$dsn = $this->getConfig();
		$con = null;
		if (empty($dsn)) {
			self::$connection = $con;
			return $this;
		} else {
			try {
				$con = new PDO("mysql:host=". $dsn['host'] .";port=". $dsn['port'].";dbname=".$dsn['db'], $dsn['username'], $dsn['password'], $option);
				//$con->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
				$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$con->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
			} catch (PDOException $e) {
				trigger_error( $e->getMessage(), E_USER_WARNING);
			}
			self::$connection = $con;
			return $this;
		}
	}

	public function getConnection() {
		return self::$connection;
	}

	public function initialize() {
		if ( empty(self::$connection) ) {
			$this->setConnection();
		}
		if ( empty(self::$model) ) {
			self::$model = new NotORM(self::$connection);
		}
		return self::$model;
	}

	public function __construct() {
		if ( empty(self::$connection) ) {
			$this->setConnection();
		}
		if ( empty(self::$model) ) {
			self::$model = new NotORM(self::$connection);
		}
		return $this;
	}
}