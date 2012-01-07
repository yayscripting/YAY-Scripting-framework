<?php
/**
 * @author YAY!Scripting
 * @package files
 */


/** Database Managment
 * 
 * This class handles the database communication
 *
 * @name Database
 * @package core
 * @subpackage Database
 */
class YS_Database
{
	
	/** MySQL link identifier
	 * 
	 * @access public
	 * @var resource
	 */
	public $db_connection;
	
	/** Query counter
	 * 
	 * @access public
	 * @var int
	 */
	public $querycount;
	
	/** Is in transaction?
	 * 
	 * @access private
	 * @var bool
	 */
	private $_transaction;
	
	/** Config data
	 * 
	 * @access private
	 * @var array
	 */
	private $_config;

	/** Constructor
	 * 
	 * Connects with database, sets default values.
	 * If parameters are null, the config file is used.
	 * 
	 * @access public
	 * @param string $server Serverhost.
	 * @param string $username Username of the mysql-server.
	 * @param string $password Password of the mysql-server.
	 * @param string $database Database name.
	 * @return YS_Database
	 * @throws DatabaseException when database is offline or could not be selected.
	 */
	public function __construct($server = null, $username = null, $password = null, $database = null) 
	{
		
		// get config
		global $_config;
		$this->_config = $_config;
		
		// basic value
		$this->transaction = false;
		
		// set connection variables
		$server		= (!is_null($server))   ? $server   : $this->_config->database->server;
		$username	= (!is_null($username)) ? $username : $this->_config->database->username;
		$password	= (!is_null($password)) ? $password : $this->_config->database->password;
		$database	= (!is_null($database)) ? $database : $this->_config->database->database;
		
		
		$this->db_connection = @mysql_connect($server, $username, $password, true);
			
		if($this->db_connection !== false) {
		
			if(mysql_select_db($database)) {
			
				// set connection charset
				mysql_set_charset('utf8'); 
				return;
			
			}
			
			throw new DatabaseException('Could not select the database: '.$database);
		
		}
		
		// error
		throw new DatabaseException('Could not connect to the database.');
	
	}
	
	/** Called on script shutdown
	 * 
	 * If transaction is still open, this function sends the COMMIT-statement.
	 * 
	 * @access public
	 * @return void
	 */
	public function __destruct()
	{
		
		// commit
		$this->commit();
		
	}
	
	/** Starts transaction
	 * 
	 * This function does only work when you are using the InnoDB storage engine.
	 * 
	 * @access public
	 * @return bool Success.
	 * @see commit
	 * @see rollback
	 */
	public function transaction()
	{
		
		// check if it is already running
		if ($this->_transaction)
			return false;
			
		// start transaction
		$this->query("START TRANSACTION");
		$this->_transaction = true;
		
		return mysql_error() == "";
		
	}
	
	/** Ends a transaction
	 * 
	 * This function is automatically called on script shutdown.
	 * This function does only work when you are using the InnoDB storage engine.
	 * 
	 * @access public
	 * @return bool success
	 * @see transaction
	 * @see rollback
	 * @see __destruct
	 */
	public function commit()
	{
		
		// check if transaction runs
		if (!$this->_transaction)
			return false;
		
		// end transaction
		$this->query("COMMIT");
		$this->_transaction = false;
		
		
		return mysql_error() == "";
		
	}
	
	/** Rollback query's which have been casted after a transaction.
	 * 
	 * This function does only work when you are using the InnoDB storage engine.
	 * 
	 * @access public
	 * @return bool Success
	 * @see transaction
	 * @see commit
	 */
	public function rollback()
	{
		
		// check if it is already running
		if (!$this->_transaction)
			return false;
			
		// rollback changes
		$this->query("ROLLBACK");
		
		return mysql_error() == "";		
		
	}
	
	/** Escapes a string
	 * 
	 * @access public
	 * @param string $string String to escape.
	 * @return string Escaped string.
	 */
	public function safe($string) 
	{
	
		if (get_magic_quotes_gpc()) {

			$string = stripslashes($string);
			
		}

		return mysql_escape_string($string);
	
	}
	
	/** Executes a query, parameters are parameterised according to {@link prepare}.
	 * 
	 * @access public
	 * @param string $sql Query.
	 * @param array $parameters Parameters.
	 * @return resource Result.
	 * @throws QueryException on error.
	 * @see prepare
	 */
	public function query($sql, $parameters = null) 
	{
	
		// querycount
		$this->querycount++;
		
		// prepare queries
		$sql = $this->prepare($sql, $parameters);
		
		// query
		$result = mysql_query($sql, $this->db_connection);
		
		// return if successfull
		if (mysql_errno($this->db_connection) == 0)
			return $result;
		
		// handle error	
		throw new QueryException('Er is een query-fout opgetreden.<br /><br />'.htmlspecialchars($sql).'<br /><br />'.htmlspecialchars(mysql_error($this->db_connection)));		
		
	}
	
	/** This function prepares a query according to the parameters, all parameters are escaped by {@link safe}
	 * 
	 * All ?'s are replaced by the corresponding string
	 * Example:
	 * <code>
	 * echo $this->sql->prepare("SELECT * FROM `?` WHERE `id`='?'", array('tableName', '25'));
	 * // echoes: "SELECT * FROM `tableName` WHERE `id`='25'"
	 * </code>
	 * 
	 * @access public
	 * @param string $sql String to parameterize
	 * @param array $parameters Parameters.
	 * @return string Parameterized string.
	 */
	public function prepare($sql, array $parameters = null)
	{
		
		// mutate string
		if (strpos($sql, "?") !== false && $parameters !== null && empty($parameters) == false && is_array($parameters)) {
			
			// default
			$prev = 0;
			
			// loop
			foreach ($parameters as $parameter) {
				
				// get pos
				$pos = strpos($sql, '?', $prev);
				if ($pos === false) break;
				
				// mutate
				$sql = substr($sql, 0, $pos) . $this->safe($parameter) . substr($sql, $pos + 1);
				
				// save
				$prev = $pos + strlen($this->safe($parameter));
								
			}			
			
		}
		
		// return
		return $sql;
		
	}
	
	/** Transforms a resource or query into a array of objects.
	 * 
	 * This function accepts a mysql resource ór a string query. A DatabaseData-object is not allowed.
	 * In case a string is used, $parameters can be used to create a parameterized query. The parameters will be parsed with {@link prepare}.
	 * 
	 * @access public
	 * @param mixed $query Query/resource.
	 * @param array $parameters Paramaters, in case of a string.
	 * @return array Array with objects.
	 */
	public function toArray($query, $parameters = null)
	{
		
		// check if it an mysql-resource
		if (!is_resource($query))
			$query = $this->query($query, $parameters);
		
		// fill array
		$return = array();
		
		while ($row = mysql_fetch_object($query)) {
			
			$return[] = $row;
			
		}
		
		return $return;
		
	}
	
	/** Inserts a row into a database
	 * 
	 * @access public
	 * @param string $table Table name.
	 * @param array $parameters All values.
	 * @return int inserted_id.
	 */
	public function insert($table, array $parameters)
	{

		// start string
		$add	 = Array();
		$add_val = Array();
		$string  = "INSERT INTO `".$this->safe($table)."` ";
		$names	 = "";
		$values	 = "";
		$index 	 = 0;
		
		// fill with values
		foreach ($parameters as $colum => $value) {
			
			if ($index >= 1) {
				
				$names  .= ",";
				$values .= ",";
				
			}
				
			$names 	.= " `".$this->safe($colum)."`";
			
			if ($value === null)
				$values .= " NULL";
			else
				$values .= " '".$this->safe($value)."'";
			
			$index++;
			
		}
		
		// compress
		$string .= "(".$names.") VALUES (".$values.")";
		
		$result = $this->query($string, $add);
		
		// query
		if ($result === false) 
			return false;
		
		return mysql_insert_id($this->db_connection);
		
	}
	
	/** Updates rows in a table
	 * 
	 * $where can be an array or an string.
	 * $limit can be an integer or an string ("270, 30" or 1).
	 * 
	 * @access public
	 * @param string $table Table name.
	 * @param array $values New values.
	 * @param mixed $where Condition values.
	 * @param mixed $limit Limit.
	 * @return int Affected_rows.
	 */
	public function update($table, array $values, $where = null, $limit = null)
	{
		
		// build limit
		$limit = ($limit === null) ? "" : " LIMIT ".$limit;
		
		// build values
		$string = "";
		foreach($values as $key => $value)
			$string .= (($string != "") ? "," : "") . "`".$this->safe($key)."`='".$this->safe($value)."'";
			
		// where
		$clause = "";
		if (is_array($where) && empty($where) == false) {
			
			foreach($where as $key => $value)
				$clause .= (($clause != "") ? " AND " : "") . "`".$this->safe($key)."`";
				$clause .= ($value === null) ? " IS NULL" : "='".$this->safe($value)."'";
				
		}else{
			
			$clause = $where;
			
		}
		
		// execute query
		$bool = $this->query("UPDATE `".$table."` SET ".$string.(!empty($clause) ? ' WHERE '.$clause : '') . (!empty($limit) ? ' LIMIT '.$limit : ''));
		
		// check for failures
		if ($bool === false)
			return false;
		
		// return number of affected rows
		return mysql_affected_rows($this->db_connection);
		
	}
	
	/** Creates a DatabaseData-object from a query or mysql resource.
	 * 
	 * This function parameterizes using {@link prepare}.
	 * 
	 * @access public
	 * @param mixed $query Query/resource to transform
	 * @param array $parameters Parameters to be parsed.
	 * @return DatabaseData The databasedata.
	 */
	public function select($query, $parameters = null)
	{
		
		// check if it an mysql-resource
		if (!is_resource($query))
			$query = $this->query($query, $parameters);
		
		// verify query
		if ($query === false)
			return false;
		
		// create new object
		return new DatabaseData($query);
	
	}
	
	/** Selects a single row from the database, based on ID.
	 * 
	 * @access public
	 * @param string $table Table name.
	 * @param int $id ID.
	 * @param string $primaryKeyField Name of the Primary Key colum.
	 * @return object Fetched_data.
	 */
	public function getFromID($table, $id, $primaryKeyField = 'id')
	{
		
		// build query
		$query = "SELECT * FROM `?` WHERE `?`='?' LIMIT 1";
		$parameters = array($table, $primaryKeyField, $id);

		// execute query
		$return = $this->query($query, $parameters);
		
		// check return value
		if ($return === false || mysql_num_rows($return) <= 0)
			return false;
			
		return mysql_fetch_object($return);
		
	}
	
}

/** Database data container
 * 
 * This class contains all data, selected by YS_Database
 *
 * @name DatabaseData
 * @package core
 * @subpackage Database
 */
class DatabaseData
{
	
	/** MySQL link identifier.
	 * 
	 * @access private
	 * @var resource
	 */
	private $resource;
	
	/** All rows
	 * 
	 * @access private
	 * @var array
	 */
	private $rows;
	
	/** Current index in $rows
	 * 
	 * @access private
	 * @var int
	 */
	private $index = -1;
	
	/** Deleted index?
	 * 
	 * @access private
	 * @var bool
	 */
 	private $changed = false;
	
	/** Number of rows selected
	 * 
	 * @access public
	 * @var int
	 */
	public $num_rows;
	
	/** Fetched data
	 * 
	 * @access public
	 * @var object
	 */	
	public $data;
	
	/** Constructor
	 * 
	 * @access public
	 * @var resource $resource Selected data
	 * @return DatabaseData
	 */
	public function __construct($resource)
	{
		
		// remember resource
		$this->resource = $resource;
		
		// retrieve other data
		$this->num_rows = mysql_num_rows($resource);
		
		// fetch rows
		$this->rows = array();
		
		while ($row = mysql_fetch_object($resource)) {
			
			$this->rows[] = $row;
			
		}
		
	}
	
	/** Returns the number of rows.
	 * 
	 * @access public
	 * @return int Number of rows.
	 * @deprecated {@link $num_rows} is used now.
	 */
	public function num_rows()
	{
		
		return $this->num_rows;
		
	}
	
	/** Fetches the next row
	 * 
	 * @access public
	 * @return bool Could this row still be fetched?
	 */
	public function fetch()
	{
		
		// save old data
		$this->processData();
		
		// check deleted rows
		if ($this->changed)
			$this->index--;
			
		$this->changed = false;
		
		// check if index exists
		if (empty($this->rows[$this->index + 1]))
			return false;
		
		
		// edit data-value
		$this->index++;
		$this->data = $this->rows[$this->index];
		
		return true;
		
	}
	
	/** Saves the the data, edited by the user
	 * 
	 * @access protected
	 * @return void
	 */
	protected function processData()
	{
		
		if ($this->index > -1) {
			
			if (empty($this->data)) {
				
				$this->num_rows--;
				unset($this->rows[$this->index]);
				sort($this->rows);
				
				$this->changed = true;
				
			} else {
			
				$this->rows[$this->index] = $this->data;
			
			}
			
		}
		
	}
	
	/** Decrypt data
	 * 
	 * Called by the models.
	 * This function will never be used in a controller.
	 * 
	 * @access public
	 * @param string $colum Colum name.
	 * @param array $parameters Encryption parameters, defined in models.
	 * @return void
	 */
	public function decrypt($colum, $parameters)
	{
		
		global $_helpers;
		
		foreach($this->rows as &$row) {
			
			if (!empty($row->$colum)) {
				
				$row->$colum = $_helpers->encryption->decrypt($row->$colum, 
						$parameters['salt'],  
						$parameters['key'], 
						empty($parameters['algorithm']) 	? MCRYPT_RIJNDAEL_256	: $parameters['algorithm'], 
						empty($parameters['mode']) 		? MCRYPT_MODE_CBC	: $parameters['mode'], 
						empty($parameters['keyGenAlgorithm'])   ? 'sha256'		: $parameters['keyGenAlgorithm'],
						empty($parameters['multiplyConstant'])  ? 16			: $parameters['multiplyConstant']
					);
					
					
			}
			
		}		
		
	}
	
	/** get JSON-formatted data according to the given row.
	 * 
	 * if $index equals null, {@link $data} is being used.
	 * 
	 * @access public
	 * @param int $index Index to format.
	 * @return string JSON_Encoded data.
	 */ 
	public function get_json_row($index = null)
	{
		
		$this->processData();
		
		if ($index == null)
			return json_encode($this->data);
			
		return json_encode($this->rows[intval($index)]);
		
	}
	
	/** Get all rows in JSON.
	 * 
	 * @access public
	 * @return string JSON_Encoded data.
	 */
	public function get_json()
	{
		
		$this->processData();
		
		return json_encode($this->rows);
		
	}
	
	/** Gets object according to index.
	 * 
	 * If $index equals null, {@link $data} is returned.
	 * 
	 * @access public
	 * @param int $index Index to return.
	 * @return object The row.
	 */
	public function get_row($index = null)
	{
		
		$this->processData();
		
		if ($index == null)
			return $this->data;
			
		return $this->rows[intval($index)];
		
	}
	
	/** Returns the current index.
	 * 
	 * @access public
	 * @return int Index
	 */
	public function pointer()
	{
		
		return $this->index;
		
	}
	
	/** Resets the current index.
	 * 
	 * @access public
	 * @return void
	 */
	public function reset()
	{
		
		$this->processData();
		$this->index = -1;
		
	}
	
	/** Sets the index.
	 * 
	 * @access public
	 * @param int $index Index.
	 * @return void
	 */
	public function set($index)
	{
		
		$this->processData();
		
		$this->index = $index;
		$this->fetch();
		$this->index--;
		
	}
	
	/** Transforms the index and values of the objects into arrays.
	 * 
	 * @access public
	 * @param int $index StartIndex.
	 * @return array Array.
	 */
	public function toArray($index = 0)
	{
		
		return $this->transformTo($index, true);
		
	}
	
	
	/** Transforms the index into arrays, but let the values remain objects.
	 * 
	 * @access public
	 * @param int $index StartIndex.
	 * @return array Array.
	 */
	public function toList($index = 0)
	{
		
		return $this->transformTo($index, false);
		
	}
	
	/** Used to transform {@link $rows} into arrays/
	 * 
	 * @access private
	 * @param int $index StartIndex.
	 * @param bool $deep Change values also to array?
	 * @return array Array.
	 * @see toList
	 * @see toArray
	 */
	private function transformTo($index = 0, $deep = true)
	{
		
		$this->processData();
		$array = array();
		
		if ($deep) {
			
			foreach($this->rows as $key => $value)
				if ($key >= $index)
					$array[$key] = (array) $value;
				
		} else {
			
			foreach($this->rows as $key => $value)
				if ($key >= $index)
					$array[$key] = (object) $value;
		
		}	
		
		return $array;	
		
	}
	
	/** Releases resource, and deletes all important variabels, used for clearing memory in big scripts.
	 * 
	 * @access public
	 * @return void
	 */
	public function Release()
	{
		
		// delete result
		mysql_free_result($this->resource);
		
		// unset data
		unset($this->index, $this->data, $this->num_rows, $this->resource, $this->rows);
		
	}
	
	/** Magic function, for ===false-checks
	 * 
	 * @access public
	 * @return string "true"
	 */
	public function __toString()
	{
		
		return "true";
			
	}
	
}
?>