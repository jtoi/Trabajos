<?php defined( '_VALID_ENTRADA' ) or die( 'Restricted access' );


/**
 * Inscribe a Clientes y Beneficiarios de Ais Remesas
 *
 * @author julio
*/
class insTit {
	
	//Comun
	/** Identificador desde AIS */
	var $id				= null;
	/** Nombre */
	var $nombre			= null;
	/** Primer apellido */
	var $ape1			= null;
	/** Segundo apellido */
	var $ape2			= null;
	/** Tel�fono */
	var $telf			= null;
	/** Direcci�n particular */
	var $dir			= null;
	/** Ciudad donde vive */
	var $ciudad			= null;
	/** Identificador del tipo de operaci�n a realizar (inscripci�n de clientes o beneficiarios)*/
	var $tipoTransIns	= '45';
	/** Identificador de nosotros en Tefpay */
	var $mercCode		= 'V99008980';
	
	//Clientes
	/** N�mero de identificaci�n personal */
	var $numid			= null;
	/** Fecha de vigencia del documento de identificaci�n personal */
	var $fechaDoc		= null;
	/** Correo */
	var $email			= null;
	/** Pa�s */
	var $pais			= null;
	/** Provincia */
	var $prov			= null;
	/** C�digo postal */
	var $po				= null;
	/** Pa�s de nacimiento */
	var $paisNac		= null;
	/** Fecha de nacimiento */
	var $fechaNac		= null;
	/** Sexo */
	var $sex			= null;
	/** Profesi�n */
	var $prof			= null;
	/** Salario Mensual */
	var $sal			= null;
	/** C�digo de usuario, alfanum�rico de 60 caracteres */
	var $usur			= null;
	
	//otros
	/** identificador del pa�s de nacimiento en la tabla de pa�ses */
	var $paisNace		= null;
	/** identificador del pa�s donde vive en la tabla de pa�ses */
	var $paise			= null;
	/** Si es cliente o no el usuario que est� inscibiendo */
	var $cliente 		= false;
	/** identificador */
// 	var $ide			= null;
// 	/** identificador */
// 	var $ide			= null;
// 	/** identificador */
// 	var $ide			= null;
// 	/** identificador */
// 	var $ide			= null;
// 	/** identificador */
// 	var $ide			= null;
// 	/** identificador */
// 	var $ide			= null;
// 	/** identificador */
// 	var $ide			= null;
// 	/** identificador */
// 	var $ide			= null;
// 	/** identificador */
// 	var $ide			= null;
// 	/** identificador */
// 	var $ide			= null;
// 	/** identificador */
// 	var $ide			= null;
// 	/** identificador */
// 	var $ide			= null;
// 	/** identificador */
// 	var $ide			= null;
// 	/** identificador */
// 	var $ide			= null;
// 	/** identificador */
// 	var $ide			= null;
    
    function __construct() {
		$this->corr = new correo;
    	$this->temp = new ps_DB();
    }
    
    function inicio(){
    	$this->paisNace = $this->pais($this->paisNac);
    	$this->paise = $this->pais($this->pais);
    }
    
    /**
     * Busca el identificador del pa�s en base al ISO2 
     * @param strin $codpais
     * @return integer
     */
    private function pais ($codpais) {
    	$q = "select id from tbl_paises where iso2 = '$codpais'";
    	$this->db($q);
    	return $this->temp->f('id');
    }
	
	/**
	 * Ejecuta las querys y muestra errores si los hay
	 * @param type $q
	 * @return boolean
	 */
	private function db($q) {
		$this->log .= $q."\n<br>";
		$this->temp->query($q);
		if ($this->temp->getErrorMsg()) {
			$this->log .= $this->temp->getErrorMsg()."\n<br>";
			$this->err = "Se produjo un error no especificado<br>Contacte con su comercio.";
			return false;
		}
		return TRUE;
	}
}
?>