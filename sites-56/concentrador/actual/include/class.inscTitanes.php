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
	/** Teléfono */
	var $telf			= null;
	/** Dirección particular */
	var $dir			= null;
	/** Ciudad donde vive */
	var $ciudad			= null;
	/** Identificador del tipo de operación a realizar (inscripción de clientes o beneficiarios)*/
	var $tipoTransIns	= '45';
	/** Identificador de nosotros en Tefpay */
	var $mercCode		= 'V99008980';
	
	//Clientes
	/** Número de identificación personal */
	var $numid			= null;
	/** Fecha de vigencia del documento de identificación personal */
	var $fechaDoc		= null;
	/** Correo */
	var $email			= null;
	/** País */
	var $pais			= null;
	/** Provincia */
	var $prov			= null;
	/** Código postal */
	var $po				= null;
	/** País de nacimiento */
	var $paisNac		= null;
	/** Fecha de nacimiento */
	var $fechaNac		= null;
	/** Sexo */
	var $sex			= null;
	/** Profesión */
	var $prof			= null;
	/** Salario Mensual */
	var $sal			= null;
	/** Código de usuario, alfanumérico de 60 caracteres */
	var $usur			= null;
	
	//otros
	/** identificador del país de nacimiento en la tabla de países */
	var $paisNace		= null;
	/** identificador del país donde vive en la tabla de países */
	var $paise			= null;
	/** Si es cliente o no el usuario que está inscibiendo */
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
     * Busca el identificador del país en base al ISO2 
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