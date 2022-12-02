<?php declare(strict_types=1);


use Dibi\Connection;

/**
 * Yadal interface for the Dibi database type
 *
 * @package Yadal
 */


/**
 * class YadalDibi
 *
 * Yadal - Yet Another Database Abstraction Layer
 * Dibi class
 *
 */
class YadalDibi extends Yadal
{
	use Cz\PHPUnit\MockDibi\MockTrait;
	
	private function _Conn() : Connection
	{
		return $this->_conn;
	}
	/**
     * YadalDibi::YadalDibi()
     *
     * Constructor: set the database we should be using
     *
     */
	public function __construct()
	{
		parent::__construct();
		$this->_quoteNumbers = true;
		$this->_nameQuote = '';
	}

	/**
     * YadalDibi::connect()
     *
     * Make a connection with the database and
     * select the database.
     *
     * @param string host: the host to connect to
     * @param string username: the username which should be used to login
     * @param string password: the password which should be used to login
     * @return resource: The connection resource
     * @access public
     */
	public function connect( $host = 'localhost', $username = '', $password = '' )
	{
        return $this->_conn;
	}


	/**
     * YadalDibi::close()
     *
     * Close the connection
     *
     * @return bool
     * @access public
     */
	public function close()
	{
		if( $this->_isConnected )
			$this->_isConnected = false;

		return true;
	}

	/**
     * YadalDibi::query()
     *
     * Execute the query
     *
     * @param string $query: the query which should be executed
     * @return resource
     * @access public
     */
	public function query( $query )
	{

		$query = SqlFormatter::compress($query);
		
		$this->_lastQuery = $query;

		return $this->_Conn()->query($query);
	}

	/**
     * YadalDibi::getInsertId()
     *
     * Get the id of the last inserted record
     *
     * @return int
     * @access public
     */
	public function getInsertId()
	{
		return $this->_Conn()->getInsertId();
	}

	/**
     * YadalDibi::result()
     *
     * Return a specific result of a sql resource
     *
     * @param resource $sql: The sql where you want to get a result from
     * @param int $row: The row where you want a result from
     * @param string $field: The field which result you want
     * @return string
     * @access public
     */
	public function result( $sql, $row = 0, $field = 0 )
	{
		throw new Exception("not implemented");
	}

	/**
     * YadalDibi::getError()
     *
     * Return the last error
     *
     * @return string
     * @access public
     */
	public function getError()
	{
		throw new Exception("not implemented");
	}

	/**
     * YadalDibi::getErrorNo()
     *
     * Return the error number
     *
     * @return int
     * @access public
     */
	public function getErrorNo()
	{
		throw new Exception("not implemented");
	}

	/**
     * YadalDibi::recordCount()
     *
     * Return the number of records found by the query
     *
     * @param resource $sql: The resource which should be counted
     * @return int
     * @access public
     */
	public function recordCount( $sql )
	{
		return $sql->getRowCount();
	}

	/**
     * YadalDibi::getRecord()
     *
     * Fetch a record in assoc mode and return it
     *
     * @param resource $sql: The resource which should be used to retireve a record from
     * @return assoc array or false when there are no records left
     * @access public
     */
	public function getRecord( $sql )
	{
		$t = $sql->fetch();

		if ($t)
			return (array)$t;
		
		return false;
	}

	/**
     * YadalDibi::getFieldNames()
     *
     * Return the field names of the table
     *
     * @param string $table: The table where the field names should be collected from
     * @return array
     * @access public
     */
	public function getFieldNames($table )
	{
		$t = strtolower($table);

		// return the data from the cache if it exists
		if( isset( $this->_cache['fields'][$t] ) )
		{
			return $this->_cache['fields'][$t];
		}

		$result = array();

		// try to get a record and fetch the field names..
		$sql = $this->query( 'DESCRIBE ' . $this->quote( $table ) );

		if (!$sql)
			throw new Exception("query not successful");

		$result = array();
		while($r = $this->getRecord($sql))
			$result[] = $r['Field'];
	
		// save the result in the cache
		$this->_cache['fields'][$t] = $result;

		return $result;
	}

	/**
     * YadalDibi::getTables()
     *
     * Return the tables from the database
     *
     * @return array
     * @access public
     */
	public function getTables()
	{		
		// return the data from the cache if it exists
		if( isset( $this->_cache['tables'] ) )
		{
			return $this->_cache['tables'];
		}
		$sql = $this->query('SHOW TABLES');
	
		$result = $this->_Conn()->fetch();
		
		if (!is_array($result))
			throw new Exception("result must be an array");

		// save the result in the cache
		$this->_cache['tables'] = $result;

		return $result;
	}

	/**
     * YadalDibi::getNotNullFields()
     *
     * Retrieve the fields that can not contain NULL
     *
     * @param string $table: The table which fields we should retrieve
     * @return array
     * @access public
     */
	public function getNotNullFields ( $table )
	{
		$t = strtolower($table);

		// return the data from the cache if it exists
		if( isset( $this->_cache['notnull'][$t] ) )
		{
			return $this->_cache['notnull'][$t];
		}

		$sql = $this->query('DESCRIBE '.$this->quote( $table ) );

		if (!$sql)
			throw new Exception("query not successful");

		$result = array();
		while($r = $this->getRecord($sql))
		{
			if( $r['Null'] == 'NO' || empty($r['Null']) )
			{
				$result[] = $r['Field'];
			}
		}
			
		
		if (!is_array($result))
			throw new Exception("result must be an array");

		// save the result in the cache
		$this->_cache['notnull'][$t] = $result;

		return $result;
	}

	/**
     * YadalDibi::getFieldTypes()
     *
     * Retrieve the field types of the given table
     *
     * @param string $table: The table where we should fetch the fields and their types from
     * @return array
     * @access public
     * @author Teye Heimans
     */
	public function getFieldTypes( $table )
	{
		$t = strtolower($table);

		// return the data from the cache if it exists
		if( isset( $this->_cache['fieldtypes'][$t] ) )
		{
			return $this->_cache['fieldtypes'][$t];
		}

		// Get the default values for the fields
		$sql = $this->query("DESCRIBE " . $this->quote($table));

		if (!$sql)
			throw new Exception("query not successfull");
			
		$result = array();
		while( $row = $this->getRecord($sql))
		{
			// split the size from the type
			if( preg_match('/^(.*)\((\d+)\)$/', $row['Type'], $match) )
			{
				$type = $match[1];
				$length = $match[2];
			}
			else
			{
				$type   = $row['Type'];
				$length = null;
			}

			$result[ $row['Field'] ] = array(
			$type,
			$length,
			$row['Default']
			);
		}

		// save the result in the cache
		$this->_cache['fieldtypes'][$t] = $result;

		return $result;
	}

	/**
     * YadalDibi::escapeString()
     *
     * Escape the string we are going to save from dangerous characters
     *
     * @param string $string: The string to escape
     * @return string
     * @access public
     */
	public function escapeString( $string )
	{
		return $string;
	}

	/**
     * YadalDibi::getPrKeys()
     *
     * Fetch the keys from the table
     *
     * @param string $table: The table where we should fetch the keys from
     * @return array of the keys which are found
     * @access public
     */
	function getPrKeys( $table )
	{
		$t = strtolower($table);

		// return the data from the cache if it exists
		if( isset( $this->_cache['keys'][$t] ) ) {
			return $this->_cache['keys'][$t];
		}

		$sql = $this->query("SHOW KEYS FROM {$table}");

		if (!$sql)
			throw new Exception("query not successfull");

		$keys = array();
		while( $r = $this->getRecord($sql) )
		{
			if ( $r['Key_name'] == 'PRIMARY' ) {
				$keys[] = $r['Column_name'];
			}
		}
			// save the result in the cache
		$this->_cache['keys'][$t] = $keys;

		return $keys;
	}

	/**
     * YadalDibi::getUniqueFields()
     *
     * Fetch the unique fields from the table
     *
     * @param string $table: The table where the unique-value-field should be collected from
     * @return array: multidimensional array of the unique indexes on the table
     * @access public
     * @author Teye Heimans
     */
	public function getUniqueFields( $table )
	{
		$t = strtolower( $table );

		// return the data from the cache if it exists
		if( isset( $this->_cache['unique'][$t] ) )
		{
			return $this->_cache['unique'][$t];
		}

		// get the keys
		$sql = $this->query("SHOW KEYS FROM " . $this->quote($table) );

		$unique = array();

		// save all keys which have to be unique
		while( $r = $this->getRecord($sql) )
		{
			if ( $r['Non_unique'] == 0 )
			{
				$unique[$r['Key_name']][] = $r['Column_name'];
			}
		}

		// save the result in the cache
		$this->_cache['unique'][$t] = $unique;

		return $unique;
	}
}
?>