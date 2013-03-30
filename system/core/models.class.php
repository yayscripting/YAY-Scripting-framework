<?php
/**
 * @author YAY!Scripting
 * @package files
 */


/** Models
 * 
 * This class loads all models
 * 
 */
class YS_Models extends YS_Singleton
{
	
	/**
	 * @access private
	 * @var array $config Config
	 */
	private $config;
			
	/** 
	 * @access private	 
	 * @var array $models All models
	 */
 	private $models = null;
 	
 	/** Database-pointer
 	 * 
 	 * @access private
 	 * @var object
 	 */
 	 private $sql;
 	
	/** Constructor
	 * 
	 * load config and helpers
	 * 
	 * @access public
	 * @return void
	 */
	public function __construct()
	{
		
		// config
		$this->config = YS_Config::Load();
		
	}
	
	/** Magic function - selects a model
	 * 
	 * If the class still needs to be created, the event 'loadModel' will be called.
	 * 
	 * @access public
	 * @param string $model Name of the model.
	 * @throws ModelException with errorcode 1 when the model is not loaded.
	 * @return YS_ModelController(or child)
	 */
	public function __get($model)
	{
	
		if (empty($this->sql)) {
			
			require_once 'system/core/database.class.php';
			$this->sql = YS_Database::Load();
			
		}
		
		$model = strtolower($model);
		
		// load helper
		if (empty($this->models->$model)) {
			
			YS_Events::Load()->fire('loadModel', $model);
			
			require_once('application/models/'.strtolower($model).'.class.php');
			
			$class = 'YS_'.ucfirst(strtolower($model));
			$this->models->{strtolower($model)} = new $class ($this->sql);	
			
		}
		
		// call helper
		if (is_object($this->models->$model))
			return $this->models->$model;
			
			
		throw new ModelException(1, 'Couldn\'t load the model: '.$model.'.');
		
	}
	
	/** Access the DB-object
	 * 
	 * Returns the database-object, without a model interface.
	 * 
	 * @access public
	 * @return YS_Database
	 */
 	public function getSQL()
 	{
 		
 		if (empty($this->sql)) {
			
			require_once 'system/core/database.class.php';
			$this->sql = YS_Database::Load();
			
		}
		
		return $this->sql;
 		
	}
	
}					

/** ModelController
 * 
 * This class is the parent of every model.
 *
 * @name Model
 * @package core
 * @subpackage Database
 */
class YS_ModelController
{
	
	/** Name of primary key
	 * 
	 * @access private
	 * @var string $primaryKeyField
	 */
	private $primaryKeyField;
	
	/** Encryption data
	 * 
	 * @access private
	 * @var array $encryption
	 */
	private $encryption;
	
	/** Column names
	 * 
	 * @access private
	 * @var array $fields
	 */
	private $fields;
	
	
	/** Database-object
	 * 
	 * @access protected
	 * @var YS_Database $sql
	 */
	protected $sql;
	
	/** Helpers
	 * 
	 * @access protected
	 * @var YS_Helpers $helpers
	 */
	protected $helpers;
	
	/** Config
	 * 
	 * @access protected
	 * @var array $config
	 */
	protected $config;
	
	/** Tablename
	 * 
	 * @access public
	 * @var string $table
	 */
	public $table;
	
	/** Constructor
	 * 
	 * @access public
	 * @param YS_Database $sql Database object.
	 * @param array $parameters All parameters.
	 * @return YS_ModelController
	 */ 
	public function __construct($sql, $parameters)
	{
		
		// sql
		$this->sql = $sql;
		
		// helpers/config
		$this->helpers = YS_Helpers::Load();
		$this->config  = YS_Config::Load();
		
		// other parameters
		$this->table		= $parameters['table'];
		$this->primaryKeyField	= empty($parameters['primaryKeyField']) ? null : $parameters['primaryKeyField'];
		$this->fields		= $parameters['fields'];
		$this->encryption	= empty($parameters['encryption']) ? null : $parameters['encryption'];
		
	}
	
	/** Selects a row by ID
	 * 
	 * @access public
	 * @param int $id ID
	 * @param array $fields Fields to select
	 * @return object Fetched_result.
	 * @see getBy
	 */
	public function getById($id, array $fields = null)
	{
		
		// reference to DATABASE-class
		$data = $this->select($fields, array($this->primaryKeyField => intval($id)), null, 1);
		
		// check for failure
		if ($data->num_rows == 0)
			return false;
		
		// return
		$data->fetch();
		return $data->data;
		
	}
	
	/** Executes a query
	 * 
	 * @access public
	 * @param string $sql Query
	 * @return Resource Mysql Resource
	 */
	final public function query($sql)
	{
		
		return $this->sql->query($sql);
		
	}
	
	/** Deletes a row by id, or uses a query.
	 * 
	 * id is accepted, array with values is accepted, even a string is accepted
	 * 
	 * Example:
	 * <code>
	 * $this->client->delete(5);
	 * // equals
	 * $this->client->delete("DELETE FROM `table` WHERE `id`='5'");
	 * // equals
	 * $this->client->delete(array('id' => '5'));
	 * </code>
	 * 
	 * @access public
	 * @param mixed $id ID to delete.
	 * @return bool Success
	 */
	final public function delete($id)
	{
		
		if (is_object($id)) $id = (array) $id;
		
		if (is_numeric($id) == false && is_array($id) == false) {
			
			return ($this->sql->query($id) !== false);
			
		} else if (is_array($id)) {
			
			$where = "WHERE ";
				
			foreach($id as $key => $value) {
					
				if (!empty($this->encryption[$key]))
					$value = $this->encode($key, $value);
					
				$where .= (($where == "WHERE ") ? "" : " AND ") . "`".$this->safe($key)."`".(($value === null) ? " IS NULL" : " ='" . $this->safe($value) . "'");
					
			}
					
			return($this->delete("DELETE FROM `".$this->safe($this->table)."` ".$where));
			
		} else {
			
			return ($this->sql->query("DELETE FROM `?` WHERE `?` = '?'", array($this->table, $this->primaryKeyField, $id)) !== false);
		
		}
		
	}
	
	/** Selects data
	 * 
	 * If $fields a a string, and all other parameters are equal to null, the string is handled as it is a query.
	 * order example: array('title') == 'DESC' or array('-title') == 'ASC' 
	 * if $where or $order is an string, it is just placed after the WHERE or ORDER BY-tag, without parsing.
	 * Limit can be a string, integer or an array ("270, 30", 1 or [270, 30]).
	 * Examples:
	 * <code>
	 * $this->client->select("SELECT * FROM clients"); // works
	 * $this->client->select(array('id', 'name'), array('id' => '5'), array('id', '-name'), 1);
	 * // selects id and name, where id=5, orders by id DESC, name ASC, limit 1
	 * 
	 * // if you want to select a row with the id NOT being 5, it works this way:
	 * $this->client->select(array('id', 'name'), array('!id' => '5'));
	 * </code>
	 * 
	 * @access public
	 * @param mixed $fields Select what fields?
	 * @param mixed $where What parameters are required
	 * @param mixed $order Order by what?
	 * @param mixed $limit What limit
	 * @param string $suffix A Suffix, this string is pasted behind the select-statement
	 * @return DatabaseData Data-object
	 */
	final public function select($fields = null, $where = null, $order = null, $limit = null, $suffix = null)
	{
		
		$avarage = ($where == null && $order == null && $limit == null && $suffix == null && is_string($fields)) == false;
		
		if (is_object($fields)) $fields = (array)$fields;
		if (is_object($where)) $where = (array)$where;
		if (is_object($order)) $order = (array)$order;
		if (is_object($limit)) $limit = (array)$limit;
		
		// avarage query?
		if ($avarage) {
		
			// default fields
			if ($fields == null)
				$fields = $this->fields;
			
			// build fields
			$index = 0;
			$select	= "";
			
			if (is_array($fields)) {
				
				foreach ($fields as $field) {
					
					$select .= ($index > 0) ? ',' . '`'.$this->sql->safe($field).'`' : '`'.$this->sql->safe($field).'`';
					$index++;
					
				}
			
			} else {
				
				$select = $fields;
				
			}
			
			// build where
			if ($where == null) {
				
				$where = "";
				
			}else if (is_array($where)) {
				
				$string = "WHERE ";
				
				foreach($where as $key => $value) {
					
					// check for comparison prefix
					$command = substr($key, 0, 1);
					if ($command == '!')
						$key = substr($key, 1);
						
					$type = ($command == '!' ? '<>' : '=');
					
					if (!empty($this->encryption[$key]))
						$value = $this->encode($key, $value);
					
					$string .= (($string == "WHERE ") ? "" : " AND ") . "`".$this->safe($key)."`".$type."'".$this->safe($value)."'";
					
				}
					
				$where = $string;
				
			} else {
				
				$where = " WHERE ".$where;
				
			}
			
			
			// build order
			if ($order == null || empty($order)) {
				
				$order = "";
				
			}else if (is_array($order)) {
				
				$string = "ORDER BY ";
				
				foreach($order as $value) {
					
					$value = (substr($value, 0, 1) == "-") ? '`'.$this->safe(substr($value, 1)) . '` DESC' : '`'.$this->safe($value) . '` ASC';
					$string .= (($string == "ORDER BY ") ? "" : ",") . $value;
					
				}
					
				$order = $string;
				
			} else {
				
				$order = "ORDER BY ".$order;
				
			}
			
			// build limit
			if (is_array($limit))
				$limit = $limit[0] . ', ' . $limit[1];
			
			// build limit
			$limit = ($limit == null) ? "" : " LIMIT " . $limit;
				
			// build query
			$query = "SELECT " . $select . " FROM `" . $this->sql->safe($this->table) . "`" . $where . $order . $limit . (($suffix === null) ? '' : $suffix);
		
		// string given
		} else {
			
			$query = $fields;
			
		}
		
		// reference to DATABASE-class
		$data = $this->sql->select($query);
		
		// need to decrypt?
		if ((empty($fields) == false && $avarage) || $avarage == false)
			foreach ($this->fields as $field) {
				
				if (!empty($this->encryption[$field]))
					$data->decrypt($field, $this->encryption[$field]);
				
				
			};
			
		return $data;
		
	}
	
	/** Checks if a ID exists
	 * 
	 * @access public
	 * @param int $id
	 * @return bool
	 */
	final public function exists($id)
	{
		
		return ($this->getById($id, array('id')) !== false);
		
	}
	
	/** Updates a table
	 * 
	 * If $values is a string, and Where is null and $limit is null, $value is imprented as a query.
	 * $limit can be a int or a string("270, 30" or 1).
	 * example:
	 * <code>
	 * $this->client->update("UPDATE `clients` SET `name`='test' WHERE `id`='5'");
	 * // equals
	 * $this->client->update(array('name'=>'test'), array('id' => '5'));
	 * </code>
	 * 
	 * @param mixed $values Values to update.
	 * @param mixed $where Conditions.
	 * @param mixed $limit Limit.
	 * @return int Affected rows.
	 */
	final public function update($values = null, $where = null, $limit = null)
	{
		
		// avarage?
		$avarage = ($where == null && $limit == null && is_string($values)) == false;
		
		if (is_object($values)) $values = (array)$values;
		if (is_object($where)) $where = (array)$where;
		
		if ($avarage) {
			
			// need encryption?
			if (!empty($values))
				foreach($values as $key => &$value) {
					
					if (!empty($this->encryption[$key]))
						$value = $this->encode($key, $value);		
						
					
				};
			
			if (is_array($where) && empty($where) == false) {
				
				// need encryption?
				foreach($where as $key => &$value) {
					
					if (!empty($this->encryption[$key]))
						$value = $this->encode($key, $value);		
						
					
				}
				
			}
			
			// REFERENCES
			return $this->sql->update($this->table, $values, $where, $limit);
			
		} else {
			
			if (!$this->sql->query($values))
				return false;
				
			return mysql_affected_rows($this->sql->db_connection);
			
		}
		
		
	}
	
	/** Gets rows by checking the value of 1 specific colum.
	 * 
	 * @access public
	 * @param string $colum Column name.
	 * @param string $value Column value.
	 * @return DatabaseData Data-object.
	 * @see getById
	 */
	final public function getBy($colum, $value)
	{
		
		return ($this->select(null, array($colum => $value), null));
		
	}
	
	/** Counts the amount of rows with specific parameters
	 * 
	 * $where can either be an array or a string.
	 * e.g.
	 * <code>
	 * $this->clients->count(array('activated' => '1'))
	 * // equals
	 * $this->clients->count("`activated`='1'");
	 * </code>
	 * 
	 * @access public
	 * @param mixed $where Values to insert.
	 * @return int Amount of rows.
	 */
 	final public function count($where = "") 
 	{
 		
 		if (is_object($where)) $where = (array)$where;
 		
 		// encryption?
 		if (is_array($where)) {
				
			$temp = '';
			
			// glue
			foreach($where as $key => &$value) {
				
				// need encryption?
				if (!empty($this->encryption[$key]))
					$value = $this->encode($key, $value);		
					
				$temp .= (($temp != "") ? " AND " : "") . "`".$this->safe($key)."`";
				$temp .= ($value === null) ? " IS NULL" : "='".$this->safe($value)."'";
				
			}
			
			$where = $temp;
			
		}
		
		if ($where != "")
			$where = " WHERE " . $where;
		
		$res = mysql_fetch_row($this->sql->query("SELECT COUNT(*) FROM `".$this->table."`".$where));
 		
 		return $res[0];
 		
	}
	
	/** Inserts data into the table.
	 * 
	 * $values can either be an array, or a string.
	 * e.g.
	 * <code>
	 * $this->client->insert("INSERT INTO `table` (`name`) VALUES ('name')");
	 * // equals
	 * $this->client->insert(array('name'=>'name'));
	 * </code>
	 * 
	 * @access public
	 * @param mixed $values Values to insert.
	 * @return int Inserted ID, false on error.
	 */
	final public function insert($values)
	{
		
		if (is_object($values)) $values = (array)$values;
		
		// avarage?
		if (is_array($values)) {
			
			// need encryption?
			if (!empty($values))
				foreach($values as $key => &$value) {
					
					if (!empty($this->encryption[$key]))
						$value = $this->encode($key, $value);		
						
					
				};
		
			// reference to DATABASE-class	
			return $this->sql->insert($this->table, $values);
		
		// string given	
		} else {
			
			if ($this->sql->query($values) === false)
				return false;
				
			return mysql_insert_id($this->sql->db_connection);
			
		}
		
	}
	
	/** Escapes a string
	 * 
	 * This function does only exist for fast escaping in the models
	 * 
	 * @access protected
	 * @param string $string String to escape.
	 * @return string Escaped string.
	 */
	final protected function safe($string)
	{
		
		// reference to DATABASE-class
		return $this->sql->safe($string);	
		
	}
	
	/** Encode data
	 * 
	 * @access protected
	 * @param string $key Colum name.
	 * @param string $value String to encode.
	 * @return Encoded data.
	 * @see decode
	 */
	final public function encode($key, $value) 
	{
		
		// no need to encrypt
		if (empty($this->encryption[$key]) || empty($value))
			return $value;
		
		// encrypt
		return $this->helpers->encryption->encrypt($value, 
				$this->encryption[$key]['salt'], 
				$this->encryption[$key]['key'], 
				empty($this->encryption[$key]['algorithm']) ? MCRYPT_RIJNDAEL_256 : $this->encryption[$key]['algorithm'], 
				empty($this->encryption[$key]['mode']) ? MCRYPT_MODE_CBC : $this->encryption[$key]['mode'], 
				empty($this->encryption[$key]['keyGenAlgorithm']) ? 'sha256' : $this->encryption[$key]['keyGenAlgorithm'],
				empty($this->encryption[$key]['iv']) ? null : $this->encryption[$key]['iv'],
				empty($this->encryption[$key]['multiplyConstant']) ? 16 : $this->encryption[$key]['multiplyConstant']
			);
		
	}
	
	/** Decode data
	 * 
	 * @access protected
	 * @param string $key Colum name.
	 * @param string $value String to decode.
	 * @return Decoded data.
	 * @see encode
	 */
	final protected function decode($key, $value) 
	{
		
		// no need to encrypt
		if (empty($this->encryption[$key]) || empty($value))
			return $value;
		
		// encrypt
		return $this->helpers->encryption->decrypt($value, 
				$this->encryption[$key]['salt'], 
				$this->encryption[$key]['key'], 
				empty($this->encryption[$key]['algorithm']) ? MCRYPT_RIJNDAEL_256 : $this->encryption[$key]['algorithm'], 
				empty($this->encryption[$key]['mode']) ? MCRYPT_MODE_CBC : $this->encryption[$key]['mode'], 
				empty($this->encryption[$key]['keyGenAlgorithm']) ? 'sha256' : $this->encryption[$key]['keyGenAlgorithm'],
				empty($this->encryption[$key]['multiplyConstant']) ? 16 : $this->encryption[$key]['multiplyConstant']
			);
		
	}
	
}
