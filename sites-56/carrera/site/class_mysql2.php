<?php
// --------------------------------------------------------
// Nombre del programa					: class_mysql
// Autor								: Alejandro Diaz Cadavid.
// Email								: adiazc@gmail.com
// Fecha								: 24 de Octubre de 2006.
// Descripcion							: Clase que permite la conexion a MYSQL.
// --------------------------------------------------------
//include 'configuracion.php';
class conbd {
    var $host	= _SERVIDOR;
	var $user	= _USUARIO;
	var $pass	= _CONTRAS;
	var $bd		= _BD;
    var $con    = "";
	var $rs		= '';
	
	function conbd(){}
	
	function open () {
		$this->con = mysql_connect ($this->host, $this->user, $this->pass);
		mysql_select_db ($this->bd, $this->con);
	}
	
    function execute ($query) {
        $this->open();
//		echo "conex".$this->con;
        $this->rs = mysql_query ($query, $this->con);
//        echo $query;
        return $this->rs;
    }
	
	function lastId () {
		return mysql_insert_id($this->con);
	}
	
	function numRows() {
		return mysql_num_rows($this->rs);
	}
	
	function f() {
		return mysql_result($this->rs,0);
	}

	function close() {
    }
}
$conn=new conbd();
?>
