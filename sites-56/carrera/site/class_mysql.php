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
    // var $host	= _SERVIDOR;
	// var $user	= _USUARIO;
	// var $pass	= _CONTRAS;
	// var $bd		= _BD;
    var $host	= SERVIDOR;
	var $user	= USUARIO;
	var $pass	= CONTRAS;
	var $bd		= BD;
    var $con    = "";
	var $rs		= '';
	
	function __construct(){}
	
	function open () {
		$this->con = mysqli_connect ($this->host, $this->user, $this->pass);
		mysqli_select_db ($this->con, $this->bd);
	}
	
    function execute ($query) {
        $this->open();
//		echo "conex".$this->con;
        $this->rs = mysqli_query ($this->con, $query);
//        echo $query;
        return $this->rs;
    }
	
	function lastId () {
		return mysqli_insert_id($this->con);
	}
	
	function numRows() {
		return mysqli_num_rows($this->rs);
	}
	
	function f() {
		$sal = mysqli_fetch_row($this->rs);
		return $sal[0];
	}

	function close() {
    }
}
$conn=new conbd();
?>
