<?php
/**
* @version		$Id: mysqli.php 16385 2010-04-23 10:44:15Z ian $
* @package		Joomla.Framework
* @subpackage	Database
* @copyright	Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// Check to ensure this file is within the rest of the framework
defined( '_VALID_ENTRADA' ) or die( 'Restricted access' );

/**
 * MySQLi database driver
 *
 * @package		Joomla.Framework
 * @subpackage	Database
 * @since		1.0
 */
class ps_DB {
	/**
	 *  The database driver name
	 *
	 * @var string
	 */
	var $name			= 'mysqli';
	var $_nullDate		= '0000-00-00 00:00:00';
	var $_nameQuote		= '`';
	var $_sql			= '';
	var $_sqlo			= '';
	var $_cursor		= null;
	var $_resource		= '';
	var $_errorNum		= '';
	var $_errorMsg		= '';
	var $row			= '';
	var $_debug			= false;
	var $record			= null;
	var $called			= false;
	var $log			= '';

	/**
	* Database object constructor
	*
	* @access	public
	* @param	array	List of options used to configure the connection
	* @since	1.5
	* @see		JDatabase
	*/
	function __construct( $options = null) {
		include_once 'correo.php';
		//include_once '../admin/adminis.func.php';
		global $host, $user, $pass, $db;
		$select = true;

		// Unlike mysql_connect(), mysqli_connect() takes the port and socket
		// as separate arguments. Therefore, we have to extract them from the
		// host string.
		$port	= NULL;
		$socket	= NULL;
		$targetSlot = substr( strstr( $host, ":" ), 1 );
		if (!empty( $targetSlot )) {
			// Get the port number or socket name
			if (is_numeric( $targetSlot ))
				$port	= $targetSlot;
			else
				$socket	= $targetSlot;

			// Extract the host name only
			$host = substr( $host, 0, strlen( $host ) - (strlen( $targetSlot ) + 1) );
			// This will take care of the following notation: ":3306"
			if($host == '')
				$host = 'localhost';
		}

		// perform a number of fatality checks, then return gracefully
		if (!function_exists( 'mysqli_connect' )) {
			$this->_errorNum = 1;
			$this->_errorMsg = 'The MySQL adapter "mysqli" is not available.';
			return;
		}

		// connect to the server
		if (!($this->_resource = @mysqli_connect($host, $user, $pass, NULL, $port, $socket))) {
			$this->_errorNum = 2;
			$this->_errorMsg = 'Could not connect to MySQL';
			return;
		}
		
		
		// finalize initialization
		$this->_utf = $this->hasUTF();

		//Set charactersets (needed for MySQL 4.1.2+)
		if ($this->_utf){
			$this->setUTF();
		}

		// select the database
		if ( $select ) {
			$this->select($db);
		}
	}

	/**
	 * Database object destructor
	 *
	 * @return boolean
	 * @since 1.5
	 */
	function __destruct() {
		$return = false;
		if (is_resource($this->_resource)) {
			$return = mysqli_close($this->_resource);
		}
		return $return;
	}

	/**
	 * Test to see if the MySQLi connector is available
	 *
	 * @static
	 * @access public
	 * @return boolean  True on success, false otherwise.
	 */
	function test() {
		return (function_exists( 'mysqli_connect' ));
	}

	/**
	 * Sets the SQL query string for later execution.
	 *
	 * This function replaces a string identifier <var>$prefix</var> with the
	 * string held is the <var>_table_prefix</var> class variable.
	 *
	 * @access public
	 * @param string The SQL query
	 * @param string The offset to start selection
	 * @param string The number of results to return
	 * @param string The common table prefix
	 */
	function setQuery( $sql, $offset = 0, $limit = 0 ) {
		$this->_limit	= (int) $limit;
		$this->_offset	= (int) $offset;
		$this->_cursor = null;
// 		$this->_resource = null;
		$this->record = null;
		$this->row = null;
// 		$this->__destruct();
	}

	/**
	 * Determines if the connection to the server is active.
	 *
	 * @access	public
	 * @return	boolean
	 * @since	1.5
	 */
	function connected() {
		return $this->_resource->ping();
	}

	/**
	 * Select a database for use
	 *
	 * @access	public
	 * @param	string $database
	 * @return	boolean True if the database has been successfully selected
	 * @since	1.5
	 */
	function select($database) {
		if ( ! $database ) {
			return false;
		}

		if ( !mysqli_select_db($this->_resource, $database)) {
			$this->_errorNum = 3;
			$this->_errorMsg = 'Could not connect to database';
			return false;
		}

		// if running mysql 5, set sql-mode to mysql40 - thereby circumventing strict mode problems
		if ( strpos( $this->getVersion(), '5' ) === 0 ) {
			$this->query("SET sql_mode = 'MYSQL40'");
		}

		return true;
	}
	
	/**
         * Returns the number of rows in the RecordSet from a query de un select.
         * @return int
         */
	function num_rows() {
		return ( $this->getNumRows() );
	}

	/**
	 * Determines UTF support
	 *
	 * @access public
	 * @return boolean True - UTF is supported
	 */
	function hasUTF() {
		$verParts = explode( '.', $this->getVersion() );
		return ($verParts[0] == 5 || ($verParts[0] == 4 && $verParts[1] == 1 && (int)$verParts[2] >= 2));
	}

	/**
	 * Custom settings for UTF support
	 *
	 * @access public
	 */
	function setUTF()
	{
		mysqli_query( $this->_resource, "SET NAMES 'utf8'" );
		// mysqli_query( $this->_resource, "SET NAMES 'iso-8859-1'" );
	}

	/**
	 * Get a database escaped string
	 *
	 * @param	string	The string to be escaped
	 * @param	boolean	Optional parameter to provide extra escaping
	 * @return	string
	 * @access	public
	 * @abstract
	 */
	function getEscaped( $text, $extra = false ) {
		$result = mysqli_real_escape_string( $this->_resource, $text );
		if ($extra) {
			$result = addcslashes( $result, '%_' );
		}
		return $result;
	}
	/**
	* Execute the query
	*
	* @access public
	* @return mixed A database resource if successful, FALSE if not.
	*/
	function query($sql=null) {
		// $sql = str_replace("\n\r", " ", str_replace("\n", " ", str_replace("\r", " ", str_replace("	", " ", $sql))));
		if ($sql) $this->_sql = preg_replace( "/\r|\n/", " ", preg_replace("/\t/", ' ', $sql));
		
		$this->setQuery($this->_sql);
		if ($this->_sqlo != $this->_sql) {
			$this->log .= $this->_sql."<br>";
			$this->_sqlo = $this->_sql;
		}
		
		if (!is_object($this->_resource)) {
			return false;
		}

		// Take a local copy so that we don't modify the original query and cause issues later
		if ($this->_limit > 0 || $this->_offset > 0) {
			$sql .= ' LIMIT ' . max($this->_offset, 0) . ', ' . max($this->_limit, 0);
		}
		if ($this->_debug) {
			$this->_ticker++;
			$this->_log[] = $sql;
		}
		
		$this->row = 0;
		$this->_errorNum = 0;
		$this->_errorMsg = '';
		$this->_cursor = mysqli_query($this->_resource, $this->_sql);
//		print_r($this->_cursor);

		if (!$this->_cursor) {
			$this->_errorNum = mysqli_errno( $this->_resource );
			$this->_errorMsg = mysqli_error( $this->_resource )." SQL=$this->_sql";
			$this->log .= '<span style="color:red">MySQL::queryError: '.preg_replace("/\t/", '', $this->_errorNum.' - '.$this->_errorMsg)."</span><br>";
			
			if ($this->_debug) {
				echo 'MySQL::queryError: '.$this->_errorNum.' - '.$this->_errorMsg ;exit;
			}
// 			print_r($_SERVER);
			$datcorr = "<br>\nDatos:<br>\n";
			foreach ($_REQUEST as $key => $value) {
				$datcorr .= "$key = $value \n<br>";
			}
error_log('MySQL::queryError: '. $this->_errorNum.' - '.$this->_errorMsg.$datcorr);
			sendTelegram("Error de Mysql: Página: ".$_SERVER['HTTP_REFERER'].' \n<br>MySQL::queryError: '. $this->_errorNum.' - '.$this->_errorMsg.$datcorr, null);
			$corr = new correo();
			$corr->todo(43, "Error de Mysql", 'Página: '.$_SERVER['HTTP_REFERER'].' \n<br>MySQL::queryError: '. $this->_errorNum.' - '.$this->_errorMsg.$datcorr);
			return false;
		}
		
// 		if (strtoupper(substr( ltrim($this->_sql) , 0, 6 )) == "SELECT" ) {
// 			$this->record = $this->loadObjectList();
// 		}
		return $this->_cursor;
	}

	/**
	 * Description
	 *
	 * @access public
	 * @return int The number of affected rows in the previous operation
	 * @since 1.0.5
	 */
	function getAffectedRows()
	{
		return mysqli_affected_rows( $this->_resource );
	}

	/**
	* Execute a batch query
	*
	* @access public
	* @return mixed A database resource if successful, FALSE if not.
	*/
	function queryBatch( $abort_on_error=true, $p_transaction_safe = false)
	{
		$this->_errorNum = 0;
		$this->_errorMsg = '';
		if ($p_transaction_safe) {
			$this->_sql = rtrim($this->_sql, "; \t\r\n\0");
			$si = $this->getVersion();
			preg_match_all( "/(\d+)\.(\d+)\.(\d+)/i", $si, $m );
			if ($m[1] >= 4) {
				$this->_sql = 'START TRANSACTION;' . $this->_sql . '; COMMIT;';
			} else if ($m[2] >= 23 && $m[3] >= 19) {
				$this->_sql = 'BEGIN WORK;' . $this->_sql . '; COMMIT;';
			} else if ($m[2] >= 23 && $m[3] >= 17) {
				$this->_sql = 'BEGIN;' . $this->_sql . '; COMMIT;';
			}
		}
		$query_split = $this->splitSql($this->_sql);
		$error = 0;
		foreach ($query_split as $command_line) {
			$command_line = trim( $command_line );
			if ($command_line != '') {
				$this->_cursor = mysqli_query( $this->_resource, $command_line );
				if ($this->_debug) {
					$this->_ticker++;
					$this->_log[] = $command_line;
				}
				if (!$this->_cursor) {
					$error = 1;
					$this->_errorNum .= mysqli_errno( $this->_resource ) . ' ';
					$this->_errorMsg .= mysqli_error( $this->_resource )." SQL=$command_line <br />";
					if ($abort_on_error) {
						return $this->_cursor;
					}
				}
			}
		}
		return $error ? false : true;
	}

	/**
	 * Diagnostic function
	 *
	 * @access public
	 * @return	string
	 */
	function explain()
	{
		$temp = $this->_sql;
		$this->_sql = "EXPLAIN $this->_sql";

		if (!($cur = $this->query())) {
			return null;
		}
		$first = true;

		$buffer = '<table id="explain-sql">';
		$buffer .= '<thead><tr><td colspan="99">'.$this->getQuery().'</td></tr>';
		while ($row = mysqli_fetch_assoc( $cur )) {
			if ($first) {
				$buffer .= '<tr>';
				foreach ($row as $k=>$v) {
					$buffer .= '<th>'.$k.'</th>';
				}
				$buffer .= '</tr>';
				$first = false;
			}
			$buffer .= '</thead><tbody><tr>';
			foreach ($row as $k=>$v) {
				$buffer .= '<td>'.$v.'</td>';
			}
			$buffer .= '</tr>';
		}
		$buffer .= '</tbody></table>';
		mysqli_free_result( $cur );

		$this->_sql = $temp;

		return $buffer;
	}

	/**
	 * Description
	 *
	 * @access public
	 * @return int The number of rows returned from the most recent query.
	 */
	function getNumRows( $cur=null ) {
		return mysqli_num_rows( $cur ? $cur : $this->_cursor );
	}

	/**
	* This method loads the first field of the first row returned by the query.
	*
	* @access public
	* @return The value returned in the query or null if the query failed.
	*/
	function loadResult() {
		if (!($cur = $this->query())) {
			return null;
		}
		$ret = null;
		if ($row = mysqli_fetch_row( $cur )) {
			$ret = $row[0];
		}
		mysqli_free_result( $cur );
		return $ret;
	}

	/**
	* Load an array of single field results into an array
	*
	* @access public
	*/
	function loadResultArray($numinarray = 0)
	{
		if (!($cur = $this->query())) {
			return null;
		}
		$array = array();
		while ($row = mysqli_fetch_row( $cur )) {
			$array[] = $row[$numinarray];
		}
		mysqli_free_result( $cur );
		return $array;
	}

	/**
	* Fetch a result row as an associative array
	*
	* @access public
	* @return array
	*/
	function loadAssoc()
	{
		if (!($cur = $this->query())) {
			return null;
		}
		$ret = null;
		if ($array = mysqli_fetch_assoc( $cur )) {
			$ret = $array;
		}
		mysqli_free_result( $cur );
		return $ret;
	}

	/**
	* Load a assoc list of database rows
	*
	* @access public
	* @param string The field name of a primary key
	* @return array If <var>key</var> is empty as sequential list of returned records.
	*/
	function loadAssocList( $key='' )
	{
		if (!($cur = $this->query())) {
			return null;
		}
		$array = array();
		while ($row = mysqli_fetch_assoc( $cur )) {
			if ($key) {
				$array[$row[$key]] = $row;
			} else {
				$array[] = $row;
			}
		}
		mysqli_free_result( $cur );
		return $array;
	}

	/**
	* This global function loads the first row of a query into an object
	*
	* @access public
	* @return object
	*/
	function loadObject( )
	{
		if (!($cur = $this->query())) {
			return null;
		}
		$ret = null;
		if ($object = mysqli_fetch_object( $cur )) {
			$ret = $object;
		}
		mysqli_free_result( $cur );
		return $ret;
	}

	/**
	* Load a list of database objects
	*
	* If <var>key</var> is not empty then the returned array is indexed by the value
	* the database key.  Returns <var>null</var> if the query fails.
	*
	* @access public
	* @param string The field name of a primary key
	* @return array If <var>key</var> is empty as sequential list of returned records.
	*/
	function loadObjectList( $key='' ) {
		$array = array();
		if (!$this->_cursor->current_field) $this->query ();
		
		while ($row = mysqli_fetch_object($this->_cursor)) {
			if ($key) {
				$array[$row->$key] = $row;
			} else {
				$array[] = $row;
			}
		}
// 		mysqli_free_result($this->_cursor );
		return $array;
	}

	/**
	 * Description
	 *
	 * @access public
	 * @return The first row of the query.
	 */
	function loadRow() {
		// Initialise variables.
		$ret = null;

		// Execute the query and get the result set cursor.
		if (!($cursor = $this->query())){
			return null;
		}
		
		// Get the first row from the result set as an array.
		if ($row = $this->fetchArray($cursor)){
			$ret = $row;
		}

		// Free up system resources and return.
		$this->freeResult($cursor);

		return $ret;
	}

	/**
	* Load a list of database rows (numeric column indexing)
	*
	* If <var>key</var> is not empty then the returned array is indexed by the value
	* the database key.  Returns <var>null</var> if the query fails.
	*
	* @access public
	* @param string The field name of a primary key
	* @return array If <var>key</var> is empty as sequential list of returned records.
	*/
	function loadRowList( $key=null )
	{
		if (!($cur = $this->query())) {
			return null;
		}
		$array = array();
		while ($row = mysqli_fetch_row( $cur )) {
			if ($key !== null) {
				$array[$row[$key]] = $row;
			} else {
				$array[] = $row;
			}
		}
		mysqli_free_result( $cur );
		return $array;
	}

	/**
	 * Inserts a row into a table based on an objects properties
	 *
	 * @access public
	 * @param	string	The name of the table
	 * @param	object	An object whose properties match table fields
	 * @param	string	The name of the primary key. If provided the object property is updated.
	 */
	function insertObject( $table, &$object, $keyName = NULL )
	{
		$fmtsql = 'INSERT INTO '.$this->nameQuote($table).' ( %s ) VALUES ( %s ) ';
		$fields = array();
		foreach (get_object_vars( $object ) as $k => $v) {
			if (is_array($v) or is_object($v) or $v === NULL) {
				continue;
			}
			if ($k[0] == '_') { // internal field
				continue;
			}
			$fields[] = $this->nameQuote( $k );
			$values[] = $this->isQuoted( $k ) ? $this->Quote( $v ) : (int) $v;
		}
		$this->setQuery( sprintf( $fmtsql, implode( ",", $fields ) ,  implode( ",", $values ) ) );
		if (!$this->query()) {
			return false;
		}
		$id = $this->insertid();
		if ($keyName && $id) {
			$object->$keyName = $id;
		}
		return true;
	}

	/**
	 * Description
	 *
	 * @access public
	 * @param [type] $updateNulls
	 */
	function updateObject( $table, &$object, $keyName, $updateNulls=true )
	{
		$fmtsql = 'UPDATE '.$this->nameQuote($table).' SET %s WHERE %s';
		$tmp = array();
		foreach (get_object_vars( $object ) as $k => $v) {
			if( is_array($v) or is_object($v) or $k[0] == '_' ) { // internal or NA field
				continue;
			}
			if( $k == $keyName ) { // PK not to be updated
				$where = $keyName . '=' . $this->Quote( $v );
				continue;
			}
			if ($v === null)
			{
				if ($updateNulls) {
					$val = 'NULL';
				} else {
					continue;
				}
			} else {
				$val = $this->isQuoted( $k ) ? $this->Quote( $v ) : (int) $v;
			}
			$tmp[] = $this->nameQuote( $k ) . '=' . $val;
		}
		$this->setQuery( sprintf( $fmtsql, implode( ",", $tmp ) , $where ) );
		return $this->query();
	}

	/**
	 * Description
	 *
	 * @access public
	 */
	function last_insert_id()
	{
		return mysqli_insert_id( $this->_resource );
	}

	/**
	 * Description
	 *
	 * @access public
	 */
	function getVersion() {
		return mysqli_get_server_info( $this->_resource );
	}

	/**
	 * Description
	 *
	 * @access public
	 * @return array A list of all the tables in the database
	 */
	function getTableList()
	{
		$this->setQuery( 'SHOW TABLES' );
		return $this->loadResultArray();
	}

	/**
	 * Shows the CREATE TABLE statement that creates the given tables
	 *
	 * @access	public
	 * @param 	array|string 	A table name or a list of table names
	 * @return 	array A list the create SQL for the tables
	 */
	function getTableCreate( $tables )
	{
		settype($tables, 'array'); //force to array
		$result = array();

		foreach ($tables as $tblval)
		{
			$this->setQuery( 'SHOW CREATE table ' . $this->getEscaped( $tblval ) );
			$rows = $this->loadRowList();
			foreach ($rows as $row) {
				$result[$tblval] = $row[1];
			}
		}

		return $result;
	}

	/**
	 * Retrieves information about the given tables
	 *
	 * @access	public
	 * @param 	array|string 	A table name or a list of table names
	 * @param	boolean			Only return field types, default true
	 * @return	array An array of fields by table
	 */
	function getTableFields( $tables, $typeonly = true )
	{
		settype($tables, 'array'); //force to array
		$result = array();

		foreach ($tables as $tblval)
		{
			$this->setQuery( 'SHOW FIELDS FROM ' . $tblval );
			$fields = $this->loadObjectList();

			if($typeonly)
			{
				foreach ($fields as $field) {
					$result[$tblval][$field->Field] = preg_replace("/[(0-9)]/",'', $field->Type );
				}
			}
			else
			{
				foreach ($fields as $field) {
					$result[$tblval][$field->Field] = $field;
				}
			}
		}

		return $result;
	}
	/**
	 * @return int The error number for the most recent query
	 */
	function getErrorNum() {
		return $this->_errorNum;
	}
	/**
	* @return string The error message for the most recent query
	*/
	function getErrorMsg() {
		return str_replace( array( "\n", "'" ), array( '\n', "\'" ), $this->_errorMsg );
	}


	/**
  *  Returns the value of the given field name for the current
  *  record in the RecordSet. 
  * f == field
  * @param string  The field name
  * @param boolean Strip slashes from the data?
  * @return string the value of the field $field_name in the recent row of the record set
  */
	function f($field_name, $stripslashes=true) {
//		echo $field_name;
//		echo $this->row;
		if (!$this->record) 
			$this->record = $this->loadObjectList();
//		print_r($this->record);
		if (isset($this->record[$this->row]->$field_name)) {
			if($stripslashes) {
				return( stripslashes( $this->record[$this->row]->$field_name ) );
			}
			else {
				return( $this->record[$this->row]->$field_name );
			}
		}
	}

	/**
	 * Returns the next row in the RecordSet for the last query run.  
	 *
	 * @return boolean False if RecordSet is empty or the pointer is at the end.
	 */
	function next_record() {
		if (!$this->record) $this->record = $this->loadObjectList();
		
		if ( empty( $this->_sql ) ) {
			if ($this->_debug) echo " called with no query pending.";
			return false;
		}
		if ( $this->called ) {
			$this->row++;
		}
		else {
			$this->called = true;
		}

		if ($this->row < sizeof( $this->record ) ) {
			return true;
		}
		else {
			$this->row--;
			return false;
		}
	}

	/**
	 * returns true when the actual row is the last record in the record set
	 * otherwise returns false
	 *
	 * @return boolean
	 */
	function is_last_record() {
		return ($this->row+1 >= $this->num_rows());
	}

	/**
	 * Method to free up the memory used for the result set.
	 * 
	 * @param   mixed  $cursor  The optional result set cursor from which to fetch the row.
	 * @return  void
	 */
	protected function freeResult($cursor = null) {
		$cursor ? $cursor : $cursor = $this->_cursor;
		$cursor->free();
	}

	/**
	 * Method to fetch a row from the result set cursor as an array.
	 * 
	 * @param   mixed  $cursor  The optional result set cursor from which to fetch the row.
	 * @return  mixed  Either the next row from the result set or false if there are no more rows.
	 */
	protected function fetchArray($cursor = null){
		$cursor ? $cursor : $cursor = $this->_cursor;
		return $cursor->fetch_array(MYSQLI_NUM);
	}
}
