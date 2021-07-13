<?php
/**
 * Model class : connect and query in database
 */

abstract class DBModel {
	protected	$primeKey 	= 'id';
	protected	$fillable 	= [];
	protected	$hidden 	= [];
	protected	$tableName 	= '';
	protected	$db 		= '';
	private 	$query 		= '';
	private 	$whereArray = [];
	private 	$orArray 	= [];
	private 	$limit 		= 10;
	private 	$offset 	= 0;
	private 	$orderArray = [];

	public static	$connection = null;

	private function getConfig() {
		return Config::get("db");
	}

	public function __construct() {
		$order = [
			['field' => $this->primeKey, 'type' => 1]
		]; 
		$this->orderArray = $order;
		if ( empty(self::$connection) ) {
			$this->setConnection();
		}
		return $this;
	}

	public function strLimit($str, $limit = 50) {
		if (strlen($str) <= $limit) {
			return $str;
		} else {
			return substr($str, 0, $limit)."...";
		}
	}

	public function initialize() {
		if (empty(self::$connection)) {
			$this->setConnection();
		}
		$this->whereArray = [];
		$this->orArray = [];
		$this->query = '';
		$this->limit = 10;
		$this->offset = 0;
		$this->orderArray = [
			['field' => $this->primeKey, 'type' => 1]
		];
		return $this;
	}

	/**
	 * [setFillable mảng các cột có thể thao tác với db]
	 * @param [type] $this
	 */
	public function setFillable($fillable) {
		if (is_array($fillable)) {
			$this->fillable = $fillable;
		}
		return $this;
	}

	/**
	 * [setHidden mảng các thuộc tính ẩn đi khi lấy data]
	 * @param [type] $this
	 */
	public function setHidden($hidden) {
		if (is_array($hidden)) {
			$this->hidden = $hidden;
		}
		return $this;
	}

	/**
	 * [setTableName tên bảng dữ liệu]
	 * @param [type] $this
	 */
	public function setTableName($tableName) {
		if (is_string($tableName)) {
			$this->tableName = $tableName;
		}
		return $this;
	}

	/**
	 * [setDb tên database]
	 * @param [type] $this
	 */
	public function setDb($db) {
		if (is_string($db)) {
			$this->db = $db;
		}
		return $this;
	}

	public function getDb() {
		return $this->db;
	}

	public function getTableName() {
		return $this->tableName;
	}

	/**
	 * [setConnection connection sử dụng]
	 */
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
				$option[PDO::MYSQL_ATTR_INIT_COMMAND] = "SET NAMES 'utf8mb4';";
				$con = new PDO("mysql:host=". $dsn['host'] .";port=". $dsn['port'].";charset=utf8mb4", $dsn['username'], $dsn['password'], $option);
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

	/**
	 * [checkConnection description]
	 * @return [type] [description]
	 */
	private function checkConnection() {
		if (empty(self::$connection)) {
			trigger_error('Connection is empty', E_USER_WARNING);
			return false;
		}
		return true;
	}

	/**
	 * [checkQuery description]
	 * @return [type] [description]
	 */
	private function checkQuery() {
		if(!$this->checkConnection()) {
			return false;
		}
		if (empty($this->fillable)) {
			trigger_error('Fillable is empty', E_USER_WARNING);
			return false;
		}
		if (empty($this->db)) {
			trigger_error('Dbname is empty', E_USER_WARNING);
			return false;
		}
		if (empty($this->tableName)) {
			trigger_error('tableName is empty', E_USER_WARNING);
			return false;
		}
		if (empty($this->query)) {
			trigger_error('query is empty', E_USER_WARNING);
			return false;
		}
		return true;
	}

	/**
	 * [execute description]
	 * @param  array  $params [description]
	 * @return [type]         [description]
	 */
	public function execute($params = []) {
		if($this->checkQuery()) {
			try {
				$stmt = self::$connection->prepare($this->query);
				$stmt->execute($params);
				return $stmt;
			} catch (PDOException $e) {
				trigger_error("DATABASE QUERY ERROR !!! {$this->query} ".$e->getMessage(), E_USER_NOTICE );
				return null;
			}
		} else {
			return null;
		}
	}

	/**
	 * [setQuery description]
	 * @param [type] $query [description]
	 */
	public function setQuery($query) {
		if ( $this->checkConnection() ) {
			$this->query = $query;
		}
		return $this;
	}

	/**
	 * [get description]
	 * @return [type] [description]
	 */
	public function get($fields = []) {
		return $this->makeQuerySelect($fields)->execute();
	}

	public function all($fields = []) {
		$stmt = $this->makeQuerySelect($fields)->execute();
		if (!empty($stmt)) {
			return $stmt->fetchAll();
		} else {
			return [];
		}
	}

	public function first($fields = []) {
		$stmt = $this->makeQuerySelect($fields)->setLimit(1)->execute();
		if (!empty($stmt)) {
			return $stmt->fetch();
		} else {
			return null;
		}
	}

	private function buildStrCondition($arrCondition, $type) {
		$query = [];
		foreach ($arrCondition as $key => $value) {
			if(is_string($value['value'])) {
				array_push($query, "{$value['field']} {$value['operator']} ".self::$connection->quote($value['value']));
			} else if (is_null($value['value'])) {
				array_push($query, "{$value['field']} {$value['operator']} NULL");
			} else if (is_array($value['value'])){
				foreach ($value['value'] as $k => $v) {
					if (is_string($v)) {
						$value['value'][$k] = "'".$v."'";
					}
				}
				$tmp = implode(',', $value['value']);
				array_push($query, "{$value['field']} {$value['operator']} ({$tmp})");
			} else {
				array_push($query, "{$value['field']} {$value['operator']} {$value['value']}");
			}
		}
		return implode(" {$type} ", $query);
	}

	/**
	 * [buildCondition description]
	 * @return [type] [description]
	 */
	private function buildCondition() {
		if (!empty($this->whereArray) && empty($this->orArray)) {
			return $this->buildStrCondition($this->whereArray, "AND");
		}
		if (empty($this->whereArray) && !empty($this->orArray)) {
			return $this->buildStrCondition($this->orArray, "OR");
		}
		if (empty($this->whereArray) && empty($this->orArray)) {
			return "1";
		}
		if (!empty($this->whereArray) && !empty($this->orArray)) {
			$query = [];
			array_push($query, "(".$this->buildStrCondition($this->whereArray, "AND").")");
			array_push($query, "(".$this->buildStrCondition($this->orArray, "OR").")");
			return implode(" AND ", $query);
		}
	}

	private function buildOrder() {
		$query = [];
		foreach ($this->orderArray as $key => $value) {
			if($value['type'] == 0) {
				array_push($query, "{$value['field']} DESC");
			} else if($value['type'] == 1) {
				array_push($query, "{$value['field']} ASC");
			}
		}
		return implode(", ", $query);
	}

	/**
	 * [makeQuerySelect description]
	 * @return [type] [description]
	 */
	public function makeQuerySelect($fields) {
		$list_field = [];
		$arr_fields = empty($fields) ? $this->fillable : $fields;
		foreach ($arr_fields as $key => $value) {
			if ( !in_array($value, $this->hidden) ) {
				array_push($list_field, "`".$value."`");
			}
		}
		$list_field = implode(',', $list_field);
		$query = "SELECT {$list_field} FROM {$this->db}.{$this->tableName}";
		$query .= " WHERE " . $this->buildCondition();
		if (!empty($this->orderArray)) {
			$query .= " ORDER BY ". $this->buildOrder();
		}
		$query .= " LIMIT {$this->limit}";
		$query .= " OFFSET {$this->offset}";
		$query .= ";";
		$this->query = $query;
		return $this;
	}

	/**
	 * [makeQueryCount description]
	 * @return [type] [description]
	 */
	public function makeQueryCount() {
		$query = "SELECT COUNT({$this->primeKey}) as counter FROM {$this->db}.{$this->tableName}";
		$query .= " WHERE " . $this->buildCondition();
		$query .= ";";
		$this->query = $query;
		return $this;
	}

	public function setWhereCondition($where) {
		$this->whereArray = $where;
		return $this;
	}

	public function setOrCondition($where) {
		$this->orArray = $where;
		return $this;
	}

	public function resetCondition() {
		$this->orArray = [];
		$this->whereArray = [];
		return $this;
	}

	/**
	 * [where description]
	 * @param  [type] $field    [description]
	 * @param  [type] $operator [description]
	 * @param  [type] $value    [description]
	 * @return [type]           [description]
	 */
	public function where($field, $operator, $value) {
		$this->whereArray = [];
		array_push($this->whereArray, compact('field', 'operator', 'value'));
		return $this;
	}

	/**
	 * [andWhere description]
	 * @param  [type] $field    [description]
	 * @param  [type] $operator [description]
	 * @param  [type] $value    [description]
	 * @return [type]           [description]
	 */
	public function andWhere($field, $operator, $value) {
		array_push($this->whereArray, compact('field', 'operator', 'value'));
		return $this;
	}

	/**
	 * [orWhere description]
	 * @param  [type] $field    [description]
	 * @param  [type] $operator [description]
	 * @param  [type] $value    [description]
	 * @return [type]           [description]
	 */
	public function orWhere($field, $operator, $value) {
		array_push($this->orArray, compact('field', 'operator', 'value'));
		return $this;
	}

	/**
	 * [setLimit description]
	 * @param [type] $limit [description]
	 */
	public function setLimit($limit) {
		$this->limit = intval($limit);
		return $this;
	}

	/**
	 * [setOffset description]
	 * @param [type] $offset [description]
	 */
	public function setOffset($offset) {
		$this->offset = intval($offset);
		return $this;
	}

	/**
	 * [setOrderBy description]
	 * @param [type] $arr [description]
	 */
	public function setOrderBy($arr) {
		$this->orderArray = $arr;
		return $this;
	}

	/**
	 * [findById description]
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function findById($id) {
		$list_field = [];
		foreach ($this->fillable as $key => $value) {
			if ( !in_array($value, $this->hidden) ) {
				array_push($list_field, $value);
			}
		}
		$this->orArray = [];
		$this->whereArray = [
			["field" => $this->primeKey, "operator" => '=', "value" => $id]
		];
		$list_field  = implode(',', $list_field);
		$query       = "SELECT {$list_field} FROM {$this->db}.{$this->tableName}";
		$query       = $query . " WHERE " . $this->buildCondition();
		$query       = $query . " LIMIT 1;";
		$this->query = $query;
		return $this->execute()->fetch();
	}

	/**
	 * [insert description]
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	public function insert($data, $return_record_inserted = true) {
		$list_field = [];
		$list_value = [];
		$params     = [];
		foreach ($data as $key => $value) {
			if ( in_array($key, $this->fillable) && $key != $this->primeKey) {
				array_push($list_field, $key);
				array_push($list_value, ':'.$key);
				$params[$key] = $value;
			}
		}
		$list_field  = implode(',', $list_field);
		$list_value  = implode(',', $list_value);
		$query       = "INSERT INTO {$this->db}.{$this->tableName} ({$list_field}) VALUES ({$list_value});";
		$this->query = $query;
		$stmt        = $this->execute($params);
		if(!empty($stmt)) {
			$last_id     = self::$connection->lastInsertId();
			if($return_record_inserted) {
				return $this->findById($last_id);
			} else {
				return $last_id;
			}
		} else {
			return null;
		}
		
	}

	public function update($data) {
		$list_field_update = [];
		$params     = [];
		foreach ($data as $key => $value) {
			if ( in_array($key, $this->fillable) && $key != $this->primeKey) {
				array_push($list_field_update, $key.' = :'.$key);
				$params[$key] = $value;
			}
		}
		$list_field_update = implode(',', $list_field_update);
		$query = "UPDATE {$this->db}.{$this->tableName} SET {$list_field_update}";
		$query = $query. " WHERE ".$this->buildCondition();
		$query = $query. ";";
		$this->query = $query;
		return $this->execute($params);
	}

	public function count() {
		$res = $this->makeQueryCount()->execute()->fetch();
		return !empty($res['counter']) ? intval($res['counter']) : 0;
	}

	public function delete() {
		$query = "DELETE FROM {$this->db}.{$this->tableName}";
		$query = $query. " WHERE ".$this->buildCondition();
		$query = $query . " LIMIT {$this->limit}";
		$query = $query. ";";
		$this->query = $query;
		return $this->execute([]);
	}

	private function convertCondition($field, $operator, $value) {
		if(is_string($value)) {
			return "{$field} {$operator} '{$value}'";
		} else if (is_null($value)) {
			return "{$field} {$operator} NULL";
		} else if (is_array($value)) {
			$str_tmp = [];
			foreach ($value as $k => $v) {
				if (is_string($v)) {
					$str_tmp[] = "'".$v."'";
				} else if(is_null($v)) {
					$str_tmp[] = "NULL";
				} else {
					$str_tmp[] = $v;
				}
			}
			$value_tmp = implode(',', $str_tmp);
			return "{$field} {$operator} ({$value_tmp})";
		} else {
			return "{$field} {$operator} {$value}";
		}
	}

	public function search($fields, $condition, $limit, $offset) {
		$query = [];
		for($i = 0; $i < count($condition); $i++) {
			foreach ($condition[$i] as $key => $value) {
				if(strval($key) != strval('or_condition')) {
					array_push($query, $this->convertCondition($condition[$i][$key][0], $condition[$i][$key][1], $condition[$i][$key][2]));
				} else {
					$query_tmp = [];
					for ($j=0; $j < count($condition[$i]['or_condition']); $j++) { 
						array_push($query_tmp, $this->convertCondition($condition[$i]['or_condition'][$j][0], $condition[$i]['or_condition'][$j][1], $condition[$i]['or_condition'][$j][2]));
					}
					array_push($query, "(".implode(" OR ", $query_tmp).")");
				}
			}
		}
		$conditions = implode(" AND ", $query);

		$query_count = "SELECT COUNT({$this->primeKey}) as counter FROM {$this->db}.{$this->tableName}";
		$query_count .= " WHERE " . $conditions;
		$query_count .= ";";
		$res = $this->setQuery($query_count)->execute()->fetch();
		$total = !empty($res['counter']) ? intval($res['counter']) : 0;

		$list_field = [];
		$arr_fields = empty($fields) ? $this->fillable : $fields;
		foreach ($arr_fields as $key => $value) {
			if ( !in_array($value, $this->hidden) ) {
				array_push($list_field, "`".$value."`");
			}
		}
		$list_field = implode(',', $list_field);
		$query_select = "SELECT {$list_field} FROM {$this->db}.{$this->tableName}";
		$query_select .= " WHERE " . $conditions;
		if (!empty($this->orderArray)) {
			$query_select .= " ORDER BY ". $this->buildOrder();
		}
		$query_select .= " LIMIT {$limit}";
		$query_select .= " OFFSET {$offset}";
		$query_select .= ";";
		$this->query = $query_select;
		$data = $this->execute()->fetchAll();
		return compact('data', 'total');
	}
}
?>