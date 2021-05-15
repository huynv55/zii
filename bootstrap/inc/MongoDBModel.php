<?php
/**
 * Model class : connect and query in database
 * link doc mongo client : https://docs.mongodb.com/php-library/current/tutorial/crud/
 */

abstract class MongoDBModel {
    protected   $collection = '';
    protected   $db         = '';
    private 	$query 		= '';
	private 	$whereArray = [];
	private 	$orArray 	= [];
	private 	$limit 		= 10;
	private 	$offset 	= 0;
    private 	$orderArray = [];

    private     $mongoCollection = null;
    public static	$connection = null;

	private function getConfig() {
		return Config::get("mongo");
    }
    
    public function __construct() {
		if ( empty(self::$connection) ) {
			$this->setConnection();
		}
		return $this;
    }
    
    public function setConnection($connect = null, $option = []) {
        if(!empty($connect)) {
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
                if(!empty($dsn['user']) && !empty($dsn['password'])) {
                    if(empty($dsn['authDB'])) {
                        $dsn['authDB'] = 'admin';
                    }
                    $url_con = "mongodb://".$dsn['user'].":".$dsn['password']."@". $dsn['host'] .":". $dsn['port']."?authSource=".$dsn['authDB'];
                } else {
                    $url_con = "mongodb://". $dsn['host'] .":". $dsn['port'];
                }
                $con = new \MongoDB\Client($url_con, $option);
                
			} catch (Exception $e) {
				trigger_error( $e->getMessage(), E_USER_WARNING);
			}
			self::$connection = $con;
			return $this;
        }
    }

	public function setCollection($col) {
		if (is_string($col)) {
			$this->collection = $col;
		}
		return $this;
    }

    public function getCol() {
        return $this->mongoCollection;
    }
    
	public function setDb($db) {
		if (is_string($db)) {
			$this->db = $db;
		}
		return $this;
	}

    public function initialize($option = array('typeMap' => array('root' => 'array', 'document' => 'array', 'array' => 'array'))) {
		if (empty(self::$connection)) {
			$this->setConnection();
        }
        if (empty($this->mongoCollection)) {
            $mongo = self::$connection->selectCollection($this->db, $this->collection, $option);
            $this->mongoCollection = $mongo;
        }
		$this->whereArray = [];
		$this->orArray = [];
		$this->query = '';
		$this->limit = 10;
		$this->offset = 0;
		$this->orderArray = [];
		return $this;
    }

    public function check() {
        if (empty(self::$connection)) {
			trigger_error('Connection mongo is empty', E_USER_WARNING);
			return false;
		}
		if (empty($this->mongoCollection)) {
			trigger_error('Collection is empty', E_USER_WARNING);
			return false;
        }
        return true;
    }
    
    public function findAll() {
        return $this->prepare()->find()->toArray();
    }

    public function prepare() {
        return $this->initialize()->getCol();
    }
}
?>