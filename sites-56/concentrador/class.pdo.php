<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


/**
 * Description of class
 *
 * @author julio
 */
class pdos {

	private		$db = "";
	private		$dbconn;
	private		$link;
	var			$row = 0;
	var			$rowset;
	var			$_query=null;
	var			$called = false;
	var			$erStr = '';
	
	function __construct($hostname,$usr,$pass,$db) {
		$this->db = $db;
		static $con;
        $options = array(
            PDO::ATTR_TIMEOUT => 30,
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,        		
        );
		if (!class_exists('PDO')) {echo "Error: No existe la clase PDO";return;}
		$con = new PDO('mysql:host='.$hostname.';dbname='.$db, $usr, $pass, $options);
		if(!$con) {
			$this->erStr.="Problem connecting to database ";
			return false;
		} else $con->prepare("SET NAMES 'utf8'")->execute();
		$this->dbconn = $con;
	}
	
	/**
	 * Ejecuta la query
	 * @param type $query
	 * @return type
	 */
	public function query($query) {
		$this->_query = $query;
//		$this->called = false;
		$this->link = $this->dbconn->prepare($this->_query);
		$this->link->execute();
		return $this->link;
	}
	
	public function index($link, $database)
	{
		$obj = $link->query('SHOW TABLES');
		$results = $this->results($obj);
		foreach($results as $key => $value) {
			if (isset($value['Tables_in_'.$database])) {
				$this->query('REPAIR TABLE '.$value['Tables_in_'.$database]);
				$this->query('OPTIMIZE TABLE '.$value['Tables_in_'.$database]);
			}
		}
	}
	
	/**pendiente*/
	public function next_record() {
		echo $this->row."<br>";
		if ($this->row < $this->num_rows()) {
			$this->query($this->_query);
//			if ($this->called) 
				$this->row++;
//			else 
//				$this->called = true;
			return true;
//			return $this->loadRow();
		} else return false;
	}
	
	/**pendiente*/
	public function prev_record() {
		if ($this->row > 0) {
			$this->query($this->_query);
			$this->row--;
			return true;
		} else return false;
	}
	
	
	function loadRow() {
		$this->row = -1;
		if (!$this->error()) $arr = $this->link->fetchAll(PDO::FETCH_NUM);
		return $arr[$this->row];
	}
	
	/**
	 * Retorna el valor del campo dado f=field
	 * @param string $nom nombre del campo
	 * @return type
	 */
	public function f($nom, $tripslashes=true) {
//		print_r($this->link->fetch(PDO::FETCH_ASSOC));
		if (!$this->error()) $arr = $this->link->fetch(PDO::FETCH_ASSOC);
		echo $this->row." - ";
//		$this->prev_record();
//		echo $this->row;
		return $arr[$nom];
	}
	
	/**
	 * Retorna la cantidad de record de la última query
	 * @return int número de records
	 */
	public function num_rows() {
//		echo $this->link;
		return $this->link->rowCount();
	}
	
	/**
	 * Retorna true si es el último record
	 * @return boolean
	 */
	public function is_last_record() {
		return ($this->row+1 >= $this->num_rows());
	}
	
	/**
	 * Retorna el error de la última query
	 * @return string tipo de error
	 */
	public function error()
	{
		$err = $this->dbconn->errorInfo();
		return $err[2];
	}
	
	
	public function results()
	{
		if (!$this->error()) return $this->link->fetchAll(PDO::FETCH_ASSOC);
		else return false;
	}
	
//	public function __destruct()
//	{
////		$this->index($this->dbconn, $this->db);
//		$this->dbconn = null;
//	}

}
