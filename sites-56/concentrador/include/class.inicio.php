<?php
defined('_VALID_ENTRADA') or die('Restricted access');
/*
 * Class que trabaja todas las funciones del index
 */

/**
 * Description of newPHPClass
 *
 * @author julio
 */
class inico {
	/**
	 * Array de Cliente del comercio para el envío del correo de pago duplicado (nombre, email)
	 */
	var $arrCli = array();
	/**
	 * Array del usuario del comercio que ha puesto la invitación de pago (nombre, email)
	 */
	var $arrUsu = array();
	/**
	 * pasarela
	 */
	var $pasa = '';
	/**
	 * identificador de comercio
	 */
	var $comer = '';
	/**
	 * identificador de la transaccion de referencia para los pagos de este tipo
	 */
	var $refer = '';
	/**
	 * identificador de la transaccion desde el comercio
	 */
	var $tran = '';
	/**
	 * moneda de la operación
	 */
	var $mon = '';
	/**
	 * importe
	 */
	var $imp = '';
	/**
	 * tipo de operacion D=devolucion, P=pago, A=Preautorizacion
	 */
	var $opr = '';
	/**
	 * Tipo de pago W=web, D=diferido, P=presencial
	 */
	var $tipo = "W";
	/**
	 * idioma
	 */
	var $idi = '';
	/**
	 * error
	 */
	var $err = '';
	/**
	 * log
	 */
	var $log = '';
	/**
	 * firma enviada
	 */
	var $frma = '';
	/**
	 * ip desde la que se realiza el pago
	 */
	var $ip = '';
	/**
	 * identificador del país desde el que se realiza el pago
	 */
	var $idpais = '';
	/**
	 * si la operación no viene desde un tpvv
	 */
	var $tpv = 0;
	/**
	 * Si la operación viene desde una web
	 */
	var $pweb = 0;
	/**
	 * orden de rotación para la pasarela con 3D
	 * @var integer
	 */
	var $or3DPAS = 0;
	/**
	 * identificador de la operación en el Concentrador
	 */
	var $idTrn = '';
	/**
	 * nombre de la Pasarela usada
	 */
	var $pasaN = '';
	/**
	 * Tipo de tarjeta usada en el pago
	 */
	var $amex = 2;
	/**
	 * Pasarela segura(1) o no(0)
	 */
	var $segura = '';
	/**
	 * contador que se puede usar en cualquier lugar
	 */
	var $contador = 0;
	/**
	 * contador para salirse de los lazos de las pasarelas no seguras
	 */
	var $cuenta = 0;
	/**
	 * Array de pasarelas
	 */
	var $psArray = array ();
	/**
	 * Array ordenado de las pasarelas antes de pasarlo al psArray
	 */
	var $arrORd = array();
	/**
	 * Array de pasarelas verificadas
	 */
	var $arrPV = array();
	/**
	 * Array de motivos de denegación
	 */
	var $arrMD = array();
	/**
	 * Array de pasarelas denegadas
	 */
	var $arrPD = array();
	/**
	 * Mejor pasarela
	 */
	var $mp = '';

	/**
	 * conxión a la BD
	 */
	var $temp = '';
	/**
	 * conx al correo
	 */
	var $corr = '';
	/**
	 * conx al objeto Redsys
	 */
	var $obj = '';

	/**
	 * datos del comercio
	 */
	var $datCom = array();
	/**
	 * datos de la pasarela
	 */
	var $datPas = array();
	/**
	 * datos de Ais
	 */
	var $datAis = array();
	/**
	 * url de llegada del resultado de las operaciones sin navegador
	 */
	var $urlcomercio = '';
	/**
	 * url de llegada con navegador para las operaciones OK
	 */
	var $urldirOK = '';
	/**
	 * url de llegada con navegador para las operaciones KO
	 */
	var $urldirKO = '';
	/**
	 * Moneda en la que el comercio recibirá el pago de Bidaiondo
	 */
	var $receiveCurrency = '978';

	/**
	 * correo paguelofacil
	 */
	var $email = '';
	/**
	 * nombre paguelofacil
	 */
	var $nomb = '';
	/**
	 * apellido paguelofacil
	 */
	var $apell = '';
	/**
	 * tipo de tarjeta paguelofacil
	 */
	var $ttarj = '';
	/**
	 * tarjeta paguelofacil
	 */
	var $tarj = '';
	/**
	 * cvv2 paguelofacil
	 */
	var $secur = '';
	/**
	 * mes paguelofacil
	 */
	var $mes = '';
	/**
	 * ano paguelofacil
	 */
	var $ano = '';
	/**
	 * direcc paguelofacil
	 */
	var $direcc = '';
	/**
	 * telf paguelofacil
	 */
	var $telf = '';

	/**
	 * userid en paytpv
	 */
	var $idusr = '';
	/**
	 * tkuser en paytpv
	 */
	var $tkusr = '';

	function __construct()
	{
		$this->temp = new ps_DB();
		$this->corr = new correo();
		$this->obj = new RedsysAPI();
	}

	/**
	 * Verifica los datos del usuario que se envió y de estar aceptados los envía a PayTpv
	 * 
	 * @param string $usuario        	
	 * @return boolean
	 */
	function verfTkusr($usuario) {
		$arrUs = explode('/', $usuario);
		if (count($arrUs) == 2) {
			$this->db("select id from tbl_usuarios where idusrToken like '%$usuario%' and identificador like '%" . $this->email . "%'");
			if ($this->temp->num_rows()) {
				$this->idusr = $arrUs[0];
				$this->tkusr = $arrUs[1];
			} else
				$this->log .= "No se encontraron los datos del usuario en la base de datos, se envía la oper.\n<br>";
		} else {
			$this->log .= "No se formó correctamente el array de usuario, se envía la oper.\n<br>";
		}
		return true;
	}

	/**
	 * Verifica que el correo enviado del cliente sea válido
	 * 
	 * @return boolean
	 */
	function verfEmail() {
		$this->log .= "Verifica correo\n<br>";
		$domain = substr($this->email, stripos($this->email, '@') + 1);
		$this->log .= "Dominio=$domain\n<br>";

		if (!checkdnsrr($domain . ".", "MX")) {
			$this->log .= "El dominio del correo del Cliente no es válido\n<br>";
			$this->err = "Error en el correo del Cliente, no es v\u00E1lido";
			$this->saltosPasar($this->err);
			return false;
		}

		// 		$q = "select count(*) total from tbl_admin a, tbl_colAdminComer o
		// 				where o.idAdmin = a.idadmin and o.idComerc = " . $this->datCom ['id'] . " and a.email regexp '$domain'";
		$q = "select count(*) total from tbl_admin a, tbl_colAdminComer o
				where o.idAdmin = a.idadmin and o.idComerc = " . $this->datCom['id'] . " and a.email = '$this->email'";
		$this->db($q);
		if ($this->temp->f('total') > 0) {
			$this->log .= "El dominio del correo del Cliente es el mismo que el del comercio\n<br>";
			$this->err = "Error en el correo del Cliente, no es v\u00E1lido";
			$this->saltosPasar($this->err);
			return false;
		}

		$this->log .= "Todo bien con el correo\n<br>";
		return true;
	}

	/**
	 * Chequea o determina la pasarela a usar
	 * 
	 * @return boolean
	 */
	function cheqPas($bipayId = null, $tipoPago = null) {
		$this->log = "\n<br>Chequeo de la pasarela\n<br>";

		$this->db("select * from tbl_reserva where codigo = '{$this->tran}' and id_comercio = '{$this->comer}'");
		if ($this->temp->num_rows()) {
			$this->pweb = 0;
			$this->log .= "Operación originada en el concentrador\n<br>";
			if ($this->temp->f('pMomento') == 'N'){
				$this->log .= "Pago Diferido, chequeo si el comercio tiene permitido diferidos sin 3D<br>";
				$this->db("select permnsec from tbl_comercio where id = " . $this->datCom['id'] );
				if ($this->temp->f('permnsec') == 0) $this->segura = 1;
				$this->tipo = 'D';
			} else $this->tipo = 'P';
		} else {
			if(isset($bipayId) && isset($tipoPago)){
				if($tipoPago == 'W'){
					$this->pweb = 1;
				} else{
					$this->pweb = 0;
					$this->tipo = $tipoPago;
				}
			} else{
				$this->pweb = 1;
			}
			$this->log .= "pweb:".$this->pweb.", tipoPago:".$tipoPago.", bipayId: ".$bipayId."\n<br>";
			$this->log .= "Operación originada en la la web del comercio\n<br>";
		}

		if ($this->datCom['estado'] == 'D') { // si el comercio está en desarrollo lo mando a la pasarela de desarrollo
			$this->pasa = 1;
			$this->log .= "Comercio en desarrollo lo pongo en la pasarela de desarrollo\n<br>";
			if ($this->imp > 200000) {
				$this->log .= "Falla por monto excedido en la prueba";
				$this->err = "falla por monto excedido en la prueba";
				$this->saltosPasar($this->err);
				return false;
			}
		} else { // si el comercio no está en desarrollo
			if ($this->pasa > 0) { // el comercio envió la pasarela en los datos, revisar si está autorizada
				if ($this->pweb == 0) { //es un pago al momento

					if (!stristr($this->datCom['pasarelaAlMom'] . ',', $this->pasa . ',')) {

						// reviso si el comercio está autorizado a usar esa pasarela que envía
						$this->log .= "Comercio envía su pasarela " . $this->pasa . " pero no está autorizado a usarla  \n<br>";
						// $q = "select idPasarela from tbl_pasarela p, tbl_colComerPasar c where c.idpasarelaT = p.idPasarela and
						// 		p.secure = (select secure from tbl_pasarela where idPasarela = " . $this->pasa . ") and c.idcomercio = '{$this->comer}'
						// 		and " . time () . " between c.fechaIni and c.fechaFin";
						// $this->db ( $q );
						// $this->pasa = $this->temp->f ( 'idPasarela' );

						$this->db("select r.idpasarela from tbl_rotComPas r, tbl_pasarela p where p.idPasarela = r.idpasarela and p.activo = 1 and r.idcom = " . $this->datCom['id'] . " and r.activo = 1 and p.secure = '".$this->segura."' order by r.orden limit 0,1");
						if ($this->temp->num_rows()) {
							$this->pasa = $this->temp->f('idpasarela');
						} else {
							$this->log .= "no tiene pasarela en la tabla de rotación de pasarelas la saco por la del comercio ";
							$arrP = explode(',', $this->datCom['pasarelaAlMom'] . ',');
							$this->pasa = $arrP[0];
						}

						$this->log .= "la cambio a " . $this->pasa . " \n<br>";
					}
				} else { // es pago desde la web

					// if (!stristr($this->datCom['pasarela'] . ',', $this->pasa . ',')) {

						// reviso si el comercio está autorizado a usar esa pasarela que envía
						$this->log .= "Comercio envía su pasarela " . $this->pasa . " pero no está autorizado a usarla  \n<br>";
						if ($this->opr == 'A') $tipo = "'A'"; 
						elseif ($this->opr == 'R') $tipo = "'P','R'"; 
						else $tipo = "'A','P','R'";

						$q = "select idPasarela from tbl_pasarela p, tbl_colComerPasar c where c.idpasarelaW = p.idPasarela and p.activo = 1 and p.secure = (select secure from tbl_pasarela where idPasarela = " . $this->pasa . ") and p.idPasarela in (" . trim($this->datCom['pasarela'],',') . ") and p.tipo in ($tipo) and c.idcomercio = '{$this->comer}' and " . time() . " between c.fechaIni and c.fechaFin";
						// echo $q."<br>";
						$this->db($q);
						if ($this->temp->num_rows() == 0) {
							$this->db("select idPasarela from tbl_pasarela p, tbl_colComerPasar c where c.idpasarelaW = p.idPasarela and p.activo = 1 and p.secure = 1 and p.idPasarela in (" . trim($this->datCom['pasarela'],',') . ") and c.idcomercio = '{$this->comer}' and " . time() . " between c.fechaIni and c.fechaFin limit 0,1");
						} else {
							$mier = $this->temp->loadResultArray();
							// echo"holllllaaaaaaaaaaaaaaaa<br>";
							// var_dump($mier);
							// echo"<br>holllllaaaaaaaaaaaaaaaa<br>";
							// echo implode(',',$mier)."<br>";
							$this->datCom['pasarela'] = implode(',',$mier);
						}
						$this->pasa = $this->temp->f('idPasarela');

						$this->arrPV = $this->psArray = explode(',', $this->datCom['pasarela']);
						$this->pasa = $this->psArray[0];
						$this->log .= "la cambio a " . $this->pasa . " \n<br>";
					// }
				}

				// chequeo si la pasarela que envían es válida
				$this->db("select idPasarela, nombre from tbl_pasarela where activo = 1 and idPasarela = " . $this->pasa);
				$this->pasaN = $this->temp->f('nombre');
				if ($this->temp->num_rows() == 0) { // la pasarela no existe o es inválida
					$this->log .= "La pasarela " . $this->pasa . " no es válida o no existe\n<br>";
					$this->err = "falla por pasarela inv&aacute;lida";
					$this->saltosPasar($this->err);
					return false;
				}
			} else { // el comercio no envía la pasarela hay que determinarla
				// if (stristr($this->datCom['pasarela'], ',') ) { //chequeo si el comercio debe enviar la pasarela
				// $this->log .= "El comercio tiene este listado de posibles pasarelas ".$this->datCom['pasarela'].", sin embargo en ".
				// "los datos no envió la pasarela \n<br>";
				// $this->err = "falla por pasarela inv&aacute;lida, su comercio debe especificar pasarela";
				// return FALSE;
				// }

				// reviso si el comercio está dentro de los comercios cuya pasarela rota por horas
				// y ahora mismo tiene una pasarela activa
				$this->db("select id from tbl_rotComPas where idcom = " . $this->datCom['id'] . " and activo = 1 and horas != 0");
				$numpas = $this->temp->num_rows();
				if ($numpas > 0) { //rota por horas
					$this->log .= "El comercio hace cambios de pasarela por horas se escoge una\n<br>";
					// trato de buscar si hay alguna activa en este momento
					$this->db("select idpasarela from tbl_rotComPas where idcom = " . $this->datCom['id'] . " and activo = 1 and fecha > " . time());
					if ($this->temp->num_rows() > 0) {
						$this->pasa = $this->temp->f('idpasarela');
					} else { // es tiempo de cambio de pasarela

						$this->db("select count(id) cant from tbl_rotComPas where idcom = " . $this->datCom['id'] . " and activo = 1");
						$cantTo = $this->temp->f('cant');

						$verpos = leeSetup('ordenCub') + 1;
						if ($verpos > $cantTo) $verpos = 1;
						actSetup($verpos, 'ordenCub');

						$this->db("select id, horas, idpasarela from tbl_rotComPas where idcom = " . $this->datCom['id'] . " and activo = 1 " . "and orden = ($verpos)");

						if ($this->temp->num_rows() == 1) {
							$this->pasa = $this->temp->f('idpasarela');

							// modifica la fecha en dependencia de las horas que estará activa
							$this->db("update tbl_rotComPas set fecha = " . (mktime(date('H') + $this->temp->f('horas'), 0, 0, date('m'), date('d'), date('Y'))) . " where id = " . $this->temp->f('id'));
							$this->db("update tbl_comercio set pasaRot = " . $this->pasa . " where id = " . $this->datCom['id']);
						} else {
							$this->log .= "Revisar en la query anterior que no retorna un valor";
							$this->err = "Ha habido un error en la selecci&oacute;n de la pasarela de pago. Contacte a su comercio\n<br>";
							$this->saltosPasar($this->err);
							return false;
						}
					}
				} else { // el comercio no hace cambios de pasarelas por horas se escoge la pasarela del comercio
					$this->psArray = explode(',', $this->datCom['pasarela']);
					$this->pasa = $this->psArray[0];
				}
			}
		}

		if ($this->pasa != 1) { //Si la pasarela es la de prueba salta todo
			$this->log .= "Revisa que la pasarela escogida " . $this->pasa . " no tenga cambios por monedas\n<br>";
			$this->cambPasporMon();

			$q = "select secure from tbl_pasarela where idPasarela = " . $this->pasa;
			$this->db($q);
			$this->segura = $this->temp->f('secure');
			if ($this->comer != '122327460662' && $this->comer != '129025985109') { // si no es Prueba y no es Cubana
				if ($this->segura == 0) {//Para las pasarelas sin 3D
					//determino el psArray
					if ($this->pweb == 1) $term = "idpasarelaW";
					else $term = "idpasarelaT";
					//determina el arrayama  de las pasarelas por las que se mueve el comercio 
					$this->db("select $term pasarela from tbl_colComerPasar c, tbl_pasarela p, tbl_colTarjPasar j, tbl_colPasarMon m where j.idPasar = p.idPasarela and m.idpasarela = p.idPasarela and m.idmoneda = ".$this->mon." and p.activo = 1 and m.estado = 1 and $term = p.idPasarela and c.idcomercio = " . $this->comer . " and (" . time() . " between c.fechaIni and c.fechaFin) and p.secure = 0 and j.idTarj = " . $this->amex);
					//$this->db("select idPasarela from tbl_pasarela where idPasarela in ({$this->datCom ['pasarelaAlMom']}) and secure = 0");
					$this->psArray = $this->temp->loadResultArray();
					if (count($this->psArray) == 0) {
						$this->log .= "Falló al obtener las pasarelas de este comercio (paso1)";
						$this->err = "Fall&oacute; al obtener los datos de su comercio(1), contacte a Comercial";
						$this->saltosPasar($this->err);
						return false;
					} else if (count($this->psArray) == 1) $this->log .= "ALERTA SÓLO TIENE UNA PASARELA";

					//reordena el array de pasarelas y toma la primera pasarela de ese array
					$this->reordPasar();

					// array_shift($this->psArray);
					$this->log .= "psArray=".implode(",",$this->psArray)."<br>";
					$this->log .= "Pasarela sin 3D en la nueva rotación escogida: {$this->pasa}\n<br>";
				} else {
					// if ($this->pweb == 0) { //si no es pago desde web busco el array 
						$this->log .= "Es una pasarela con 3D se busca el array de pasarelas por el que circularía la oper y se toma como la primera para que inicie la verificación\n<br>";

						//Si la oper es con 3D busca el array de pasarelas por los que deberá circular el comercio
						//y fija como la pasarela la primera que tenga
						if ($this->opr == 'A') $tipo = "'A'"; 
						elseif ($this->opr == 'R') $tipo = "'P','R'"; 
						else $tipo = "'A','P','R'";
						$this->db("select r.idPasarela from tbl_rotComPas r, tbl_colTarjPasar j, tbl_colPasarMon m, tbl_pasarela p where p.idPasarela = r.idPasarela and p.tipo in ($tipo) and j.idPasar = r.idPasarela and m.idpasarela = r.idPasarela and m.idmoneda = ".$this->mon."  and r.idcom = " . $this->datCom['id'] . " and p.secure = 1 and r.activo = 1 and r.horas = 0 and j.idTarj = " . $this->amex ." order by r.orden");
						if ($this->temp->num_rows() > 0) {
							$this->psArray = $this->temp->loadResultArray();

							//reordena el array de pasarelas y toma la primera pasarela de ese array
							$this->reordPasar();

						} else if ($this->temp->num_rows()  == 1){
							$this->log .= "ALERTA SÓLO TIENE UNA PASARELA(2)";
						} else {
							$this->log .= "Falló al obtener las pasarelas de este comercio (paso2)";
							$this->err = "Fall&oacute; al obtener los datos de su comercio(2), contacte a Comercial";
							$this->saltosPasar($this->err);
							return false;
						}

						//} else {
						//$this->log .= "Es una pasarela con 3D y pago desde la web se busca el array de pasarelas por el que circularía la operación \n<br>";
						//$this->db("select pasarelaWeb from tbl_comercio where id = '{$this->datCom['id']}'");
						//$this->psArray = $this->temp->loadResultArray();
						//$this->pasa = $this->psArray[0];
					// }
				}
			}


			//adiciona al array de pasarelas las pasarelas autorizadas usd amex si el comercio está autorizado
			if ($this->datCom['usdxamex'] == 1 && $this->amex == 1 && $this->mon == 840) {
				//busco las pasarelas que tienen permitido esta variande de usd - amex
				$this->db("select idPasarela from tbl_pasarela where usdxamex = 1 and activo = 1 and secure = " . $this->segura);
				if ($this->temp->num_rows() > 0) {
					$pasAmex = $this->temp->loadResultArray(0);
					$this->psArray = array_diff($this->psArray, $pasAmex);
					$this->psArray = array_merge($pasAmex, $this->psArray);
					$this->pasa = $this->psArray[0];
				}
			}

			//adiciona las pasarelas necesarias de acuerdo al método de pago seleccionado
			//chequeo si es un método de pago
			$this->db("select count(*) total from tbl_tarjetas where id = '".$this->amex."' and tipo = 'M'");
			if ($this->temp->f('total') > 0 ) {
				// Lo enviado en amex es un método de pago
				$this->log .= "enviaron un metodo de pago<br>";

				if ($this->pweb == 0) $term = 'idpasarelaT'; else $term = 'idpasarelaW';
					$this->log .= "Tipo pasarela $term<br>";

				// busco si una o varias de las pasarelas asignadas al comercio tienen el método de pago enviado
				$this->db("select $term 'idPasarela' from tbl_colComerPasar c, tbl_pasarela p, tbl_colTarjPasar j, tbl_colPasarMon m where j.idPasar = p.idPasarela and m.idpasarela = p.idPasarela and m.idmoneda = ".$this->mon." and p.activo = 1 and m.estado = 1 and $term = p.idPasarela and c.idcomercio = " . $this->comer . " and (" . time() . " between c.fechaIni and c.fechaFin) and j.idTarj = " . $this->amex);

				if ($this->temp->num_rows() > 0) {
					$pasAmex = $this->temp->loadResultArray(0);
					$this->psArray = array_diff($this->psArray, $pasAmex);
					$this->psArray = array_merge($pasAmex, $this->psArray);
					$this->pasa = $this->psArray[0];
				}
			}
			$this->log .= "pasarela antes del chequeo de límites ".$this->pasa." <br>";

			if ($this->CheqLimites() == false)
				return false;
		}

		$this->log .= "Pasarela = {$this->pasa}\n<br>";

		$this->log .= "Verificaci&oacute;n de la combinaci&oacute;n Pasarela - Moneda y captaci&oacute;n de datos\n<br>";
		if ($this->opr == 'D') {
			$this->log .= "Es una devoluci&oacute;n, se captan los datos\n<br>";
			$this->db("select c.terminal, c.clave, c.comercio, p.nombre, c.datos variant, a.tipo, p.datos datPas, p.idcenauto, m.moneda, p.secure, urlXml url, a.datos, p.comercio comNomb, p.tipo tipoP from tbl_colPasarMon c, tbl_pasarela p, tbl_cenAuto a, tbl_moneda m where m.idmoneda = c.idmoneda and c.idpasarela = p.idPasarela and p.activo = 1 and a.id = p.idcenauto and c.idpasarela = " . $this->pasa . " and c.idmoneda = '" . $this->mon . "'");
		} else {
			if ($this->opr == 'A') $tipo = "'A'"; 
			elseif ($this->opr == 'R') $tipo = "'P','R'"; 
			else $tipo = "'A','P','R'";
			$this->db("select c.terminal, c.clave, c.comercio, p.nombre, c.datos variant, a.tipo, p.datos datPas, p.idcenauto, m.moneda, p.secure, case p.estado when 'P' then a.urlPro else a.urlDes end url, a.datos, p.comercio comNomb, p.tipo tipoP, j.idTarj  from tbl_colPasarMon c, tbl_pasarela p, tbl_cenAuto a, tbl_moneda m, tbl_colTarjPasar j where m.idmoneda = c.idmoneda and p.activo = 1 and c.idpasarela = p.idPasarela and a.id = p.idcenauto and p.tipo in ($tipo) and c.idpasarela = " . $this->pasa . " and c.idmoneda = '" . $this->mon . "'" . " and j.idTarj = ". $this->amex);
		}

		if ($this->temp->num_rows() > 0) {
			$arrVal = $this->temp->loadAssocList();
			$this->datPas = $arrVal[0];
			// print_r($this->datPas);
		} else {
			$this->err = "falla por moneda en " . $this->pasaN;
			$this->saltosPasar($this->err);
			$this->log .= "falla por moneda en" . $this->pasaN . "\n<br>";
			return false;
		}

		return true;
	}

	/**
	 * Reordena el array de pasarelas teniendo en cuenta la pasarela por la que pasó la última operación de ese comercio
	 *
	 * @return void
	 */
	private function reordPasar() {
		$this->log .= "Array desordenado de las pasarelas->". implode(",",$this->psArray)."<br>";
		//verifica la pasarela de la última operación de este comercio
		$this->db("select pasarela from tbl_transacciones where idcomercio = '" . $this->comer . "' and pasarela in (" . implode(",", $this->psArray) . ") order by fecha desc limit 0,1");
		//cambio el orden de las pasarelas en el array
		if ($this->temp->num_rows() > 0) { //si anteriormente el comercio tuvo alguna transacción por estas pasarelas
			$ultpas = $this->temp->f(pasarela);
			$this->log .= "Última pasarela: $ultpas\n<br>";
			if ($ultpas !== $this->psArray[count($this->psArray) - 1]) { //chequeo si la última pasarela no es el último elemento del array

				//Determino la posición en el array de la última pasarela por la que transitó la última operación
				for ($i = 0; $i < count($this->psArray); $i++) {
					if ($this->psArray[$i] == $ultpas) {
						if (($i + 1) > count($this->psArray)) {
							$elmIni = 0;
						} else {
							$elmIni = ($i + 1);
						}
						break;
					}
				}
				$this->log .= "Orden en el array de la próxima pasarela: $elmIni\n<br>";
				//ordeno el array para que la próxima pasarela sea la primera
				$arrORd = array();
				$this->log .= "psArray=".implode(",",$this->psArray)."<br>";

				for ($i = 0; count($arrORd) < count($this->psArray); $i++) {
					if (($elmIni + $i) >= count($this->psArray)) {
						$elmIni = $i = 0;
					}
					$arrORd[] = $this->psArray[$elmIni + $i];
				}

				$this->log .= "arrORd=".implode(",",$arrORd)."<br>";
				$this->psArray = $arrORd;
			}

			//PONER ACA LA PASARELA POR LA QUE DEBE TRANSITAR EL CLIENTE PARA LOS PAGOS RECURRENTES
			//DE PRIMERA EN EL ORDENAMIENTO DE LAS PASARELAS
			if ($this->opr == 'R') {
				//evaluar si el código enviado es válido
				$this->log .= "<br />Entra a pagos Recurrentes<br />refer='$this->refer'<br />";
				if (strlen($this->refer) == 40) {
					//buscamos en la tabla referencia por el código que nos manda el comercio el TPV correspondiente
					$this->db(sprintf ("select r.idpasarela, r.idtransaccion, t.codigo, t.idcomercio, t.identificador from tbl_referencia r, tbl_transacciones t where t.idtransaccion = r.idtransaccion and codConc = '%s'", $this->refer));

					if ($this->temp->num_rows()) {
						$pasRef 	= $this->temp->f('idpasarela');
						$ideRef		= $this->temp->f('identificador');
						$comRef 	= $this->temp->f('idcomercio');
						$codBanc 	= $this->temp->f('codigo');
						$orden		= $this->temp->f('idtransaccion');

						$this->log .= "($orden.$codBanc.$comRef.$ideRef)<br>";
						$this->log .= $this->refer." == ".hash("sha1",$orden.$codBanc.$comRef.$ideRef)."<br>";

						if ($this->refer == hash("sha1",$orden.$codBanc.$comRef.$ideRef)){
							//quito la pasarela de donde esté
							if (($key = array_search($pasRef, $this->psArray)) !== false) {
								unset($this->psArray[$key]);
							}
							// unset($this->psArray[$pasRef]);
							array_unshift($this->psArray, $pasRef);

						} else $this->refer = ''; //la referencia enviada no es válida

					} else $this->refer = ''; //la referencia enviada no está en la BD
				}  else $this->refer = ''; //la referencia enviada no tiene la longitud requerida

			} elseif ($this->opr == 'Q') {//nos piden cambio de la tarjeta que tenía la referencia
				//borramos en la tabla referencia por el código que nos manda el comercio el TPV correspondiente
				$this->db(sprintf ("delete from tbl_referencia where codConc = '%s'", $this->refer));

				//ponemos la operación como pago por referencia para pedirle al banco nos envíe la nueva referencia
				$this->opr = 'P';
			}
			
						// if ($this->datAis['idremitente'] == '262') { //quitar estas tres líneas completa
						// 	array_unshift($this->psArray, '188');
						// 	$this->log .= "Cambio el ordennnnnn".implode(",",$this->psArray)."<br>";
						// }

		}
		$this->log .= "Array de pasarelas ordenado -". implode(",",$this->psArray)."<br>";
		$this->pasa = $this->psArray[0];
	}

	/**
	 * Chequeo de los límites de Fincimex y Tocopay
	 *
	 * @return void
	 */
	private function ChequeoAis() {
		$this->log .= "\n<br>Chequeo del l&iacute;mite trimestral en Cimex\n<br>";
		$vlim = leeSetup('trim');
		$this->log .= "limite trimestral=$vlim<br>";
		$usrPerm = leeSetup('clientePerm');
		if (strlen($usrPerm) == 0) $usrPerm = 0;

		$arrCli = explode(',', leeSetup('clientePerm'));
		$this->db("select b.idcimex from tbl_aisBeneficiario b, tbl_aisClienteBeneficiario r, tbl_aisCliente c where c.id = r.idcliente and b.id = r.idbeneficiario and c.idcimex in ($usrPerm)");
		$arrBen = $this->temp->loadResultArray();
		$this->log .= "arrBen1: ".json_encode($arrBen)."<br>";
		// $arrBen = explode(',', leeSetup('benefPerm'));
		// $this->log .= "arrBen2: ".json_encode($arrBen)."<br>";

		if (date('n') >= 1 && date('n') <= 3) { // fechas del primer trimestre
			$fec1Ais = mktime(0, 0, 0, 01, 01, date("Y"));
			$fec2Ais = mktime(0, 0, 0 - 1, 04, 01, date("Y"));
			$this->log .= "Primer Trimestre\n<br>";
		} elseif (date('n') >= 4 && date('n') <= 6) { // fechas del segundo trimestre
			$fec1Ais = mktime(0, 0, 0, 04, 01, date("Y"));
			$fec2Ais = mktime(0, 0, 0 - 1, 07, 01, date("Y"));
			$this->log .= "Segundo Trimestre\n<br>";
		} elseif (date('n') >= 7 && date('n') <= 9) { // fechas del tercer trimestre
			$fec1Ais = mktime(0, 0, 0, 07, 01, date("Y"));
			$fec2Ais = mktime(0, 0, 0 - 1, 10, 01, date("Y"));
			$this->log .= "Tercer Trimestre\n<br>";
		} elseif (date('n') >= 10 && date('n') <= 12) { // fechas del cuarto trimestre
			$fec1Ais = mktime(0, 0, 0, 10, 01, date("Y"));
			$fec2Ais = mktime(0, 0, 0 - 1, 01, 01, date("Y") + 1);
			$this->log .= "Cuarto Trimestre\n<br>";
		}

		$q = "select concat(c.nombre,' ',c.papellido,' ',c.sapellido, ' (',c.usuario,') ',c.correo) cliente, bloq from tbl_aisCliente c where c.idcimex = ".$this->datAis['idremitente'];
		$this->db($q);
		$clienteDt = $this->temp->f('cliente');
		$this->log .= "Cliente: $clienteDt<br>";
		if ($this->temp->f('bloq') == 1) {
			$this->log .= "Cliente inhabilitado de por vida";
			$causa = $this->err = "Estimado Cliente $clienteDt, Ud. está inhabilitado para enviar dinero por nuestro sitio, si lo desea puede ponerse en contacto con <a href='mailto:info@tocopay.com'>info@tocopay.com</a>";
			$this->saltosPasar($this->err);
			return false;
		}

		$q = "select concat(c.nombre,' ',c.papellido,' ',c.sapellido) beneficiario, bloq from tbl_aisBeneficiario c where c.idcimex = ".$this->datAis['iddestin'];
		$this->db($q);
		$beneficiarioDt = $this->temp->f('beneficiario');
		$this->log .= "Beneficiario: $beneficiarioDt<br>";
		if ($this->temp->f('bloq') == 1) {
			$this->log .= "Beneficiario inhabilitado de por vida";
			$causa = $this->err = "Estimado Cliente $clienteDt, el beneficiario de esta operacion $beneficiarioDt, está inhabilitado para recibir dinero por nuestro sitio, si lo desea puede ponerse en contacto con <a href='mailto:info@tocopay.com'>info@tocopay.com</a>";
			$this->saltosPasar($this->err);
			return false;
		}

		//Chequeo de límite trimestral para el Remitente
		$q = "select sum(o.recibe) total1, sum(t.valor) total2 from tbl_aisOrden o, tbl_transacciones t, tbl_aisCliente c
				where o.idtransaccion = t.idtransaccion and o.idcliente = c.id and t.estado = 'A'
					and t.fecha between $fec1Ais and $fec2Ais and c.idcimex = " . $this->datAis['idremitente'];
		//me parece que el tema va por el monto enviado creo que sin importar moneda
		// $q = "select sum(t.valor) total from tbl_aisOrden o, tbl_transacciones t, tbl_aisCliente c
		// 		where o.idtransaccion = t.idtransaccion and o.idcliente = c.id and t.estado = 'A'
		// 			and t.fecha between $fec1Ais and $fec2Ais and c.idcimex = " . $this->datAis ['idremitente'];
		$this->db($q);
		($this->temp->f('total1') >= $this->temp->f('total2')) ? $total = ($this->temp->f('total1')) : $total = ($this->temp->f('total2'));
		$this->log .= $total . " + " . $this->imp . " = " . ($total + $this->imp) . " > " . ($vlim * 100) . " / " . $total . " + " . $this->imp . " = " . ($total + $this->imp) . " > " . ($vlim * 100) . "\n<br>";

		if (($total + $this->imp) > ($vlim * 100)) {//si va a pasar mas del límite 
			if (!in_array($this->datAis['idremitente'], $arrCli)) { //pero no está dentro de los Clientes permitidos
				$this->err = $causa = "Estimado Cliente $clienteDt, usted tiene un acumulado en envios de " . number_format($total / 100, 2) . ", con esta operacion de " . number_format($this->imp / 100, 2) . " llega al limite para un trimestre natural. Por favor, pongase en contacto con <a href='mailto:info@tocopay.com'>info@tocopay.com</a> para realizar el envio:";
				$this->log .= $causa;
				$this->saltosPasar($causa);
				return false;
			}
		}
		//Chequeo de 5000
		if (($total + $this->imp) > 499900) {
			$this->err = $causa = "Estimado Cliente $clienteDt, usted tiene un acumulado en envios de " . number_format($total / 100, 2) . ", con esta operacion de " . number_format($this->imp / 100, 2) . " llega al tope que es de " . number_format(5000, 2) . " para un trimestre natural.";
			$this->log .= $causa;
			$this->saltosPasar($causa);
			return false;
		}

		//TODO ver el TODO anterior que se aplica acá también
		//Chequeo de límite trimestral para el Beneficiario
		$q = "select sum(o.recibe) total1, sum(t.valor) total2 from tbl_aisOrden o, tbl_transacciones t, tbl_aisBeneficiario c
			where o.idtransaccion = t.idtransaccion and o.idbeneficiario = c.id and t.estado = 'A'
				and t.fecha between $fec1Ais and $fec2Ais and c.idcimex = " . $this->datAis['iddestin'];
		//me parece que el tema va por el monto enviado creo que sin importar moneda
		// $q = "select sum(t.valor) total from tbl_aisOrden o, tbl_transacciones t, tbl_aisBeneficiario c
		// 	where o.idtransaccion = t.idtransaccion and o.idbeneficiario = c.id and t.estado = 'A'
		// 		and t.fecha between $fec1Ais and $fec2Ais and c.idcimex = " . $this->datAis ['iddestin'];
		$this->db($q);
		($this->temp->f('total1') >= $this->temp->f('total2')) ? $total = ($this->temp->f('total1')) : $total = ($this->temp->f('total2'));
		$this->log .= $total . " + " . $this->imp . " = " . ($total + $this->imp) . " > " . ($vlim * 100) . " / " . $total . " + " . $this->imp . " = " . ($total + $this->imp) . " > " . ($vlim * 100) . "\n<br>";

		if (!in_array($this->datAis['iddestin'], $arrBen)) {
			if (($total + $this->imp) > ($vlim * 100)) {
				$this->err = $causa = "Estimado Cliente $clienteDt, el beneficiario $beneficiarioDt de esta operacion tiene acumulado " . number_format($total / 100, 2) . " con esta operacion de " . number_format($this->imp / 100, 2) . " llega al tope que es de " . number_format($vlim, 2) . " para un trimestre natural. Debe ponerse en contacto con <a href='mailto:info@tocopay.com'>info@tocopay.com</a> para realizar el envio:";
				$this->log .= $causa;
				$this->saltosPasar($causa);
				return false;
			}
		}

		// Chequeo de los 5000
		if (($total + $this->imp) > 499900) {
			$this->err = $causa = "Estimado Cliente / Dear Customer $clienteDt:<br><br> El beneficiario $beneficiarioDt de esta operacion tiene acumulado " . number_format($total / 100, 2) . " con esta operacion de " . number_format($this->imp / 100, 2) . " llega al tope que es de " . number_format(5000, 2) . " para un trimestre natural.";
			$this->log .= $causa;
			$this->saltosPasar($causa);
			return false;
		}

		//Chequeo si el Beneficiario existe en la BD
		$q = "select idtitanes from tbl_aisBeneficiario where idcimex = {$this->datAis['iddestin']}";
		$this->db($q);
		if ($this->temp->getNumRows() == 0) {
			$this->db("select concat(nombre,' ',papellido,' ',sapellido,' (',usuario, ')') cliente from tbl_aisCliente where idcimex = ".$this->datAis['idremitente']);
			$this->err = $causa = "El Beneficiario ".$this->datAis['iddestin']." no se encuenta en la BD, el Cliente es ".$this->temp->f('cliente');
			$this->log .= $causa;
			$this->saltosPasar($causa);
			return false;

		}
		//Chequeo si el Beneficiario tiene idTitanes en la BD
		if (strlen($this->temp->f('idtitanes')) < 4) {
			//include(benRequest.php);
			//if (!buscaBen($this->datAis['idremitente'])) {
				$this->db("select concat(nombre,' ',papellido,' ',sapellido,' (',usuario, ')') cliente from tbl_aisCliente where idcimex = ".$this->datAis['idremitente']);
				$this->err = $causa = "El Beneficiario ".$this->datAis['iddestin']." no tiene IDTitanes, el Cliente es ".$this->temp->f('cliente');
				$this->log .= $causa;
				$this->saltosPasar($causa);
				return false;
			//}
		}

		//Chequeo de operación repetida
		$iniDia = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
		$this->db("select count(t.idtransaccion) total from tbl_transacciones t, tbl_aisOrden o, tbl_aisCliente c, tbl_aisBeneficiario b where o.idtransaccion = t.idtransaccion and b.id = o.idbeneficiario and c.id = o.idcliente and t.estado = 'A' and t.fecha > $iniDia and c.idcimex = {$this->datAis['idremitente']} and b.idcimex = {$this->datAis['iddestin']} and valor_inicial = {$this->imp}");
		if ($this->temp->f('total') > 0) {
			$this->err = $causa = "Estimado Cliente / Dear Customer $clienteDt:<br><br>
			Usted no puede enviar remesa a un mismo beneficiario el mismo monto en un periodo menor de 24 horas. Por favor, intentelo nuevamente usando una cantidad diferente o pruebe a volver enviar en el dia de hoy despues de las 6.00 p.m. hora de Cuba. / You can not send remittances to the same beneficiary, the same amount in less than 24 hours. Please, try again using a different amount or try to send again today after 6.00 p.m. according to Cuba time.<br><br>";
			$this->log .= $causa;
			$this->saltosPasar($causa);
			return false;
		}

		//Chequeo de datos del Cliente
		$q = "select count(*) total from tbl_aisCliente where idcimex = " . $this->datAis['idremitente'];
		$this->db($q);
		if ($this->temp->f('total') == 0) {
			$this->err = $causa = "Estimado Cliente / Dear Customer $clienteDt:<br><br>Usted debe tener un registro mas actualizado que este con el que esta intentando realizar este envio, emplee ese registro / You should have a more up-to-date record of the one you are trying to send with, use that record.<br><br>";
			$this->log .= $causa;
			$this->saltosPasar($causa);
			return false;
		}
		$q = "select count(*) total from tbl_aisCliente where fechaDocumento > " . time() . " and idcimex = " . $this->datAis['idremitente'];
		$this->db($q);
		if ($this->temp->f('total') == 0) {
			$this->err = $causa = "Estimado Cliente / Dear Customer $clienteDt:<br><br>La fecha de vencimiento del Documento de Identidad ha caducado. Debe subir el nuevo documento. / Your Identity Document has expired, please upload the new one.<br><br>";
			$this->log .= $causa;
			$this->saltosPasar($causa);
			return false;
		}

		return true;
	}

    /**
     * Chequeo de los límites de Vidaipay
     *
     * @return void
     */
    private function ChequeoVpay() {
        $this->log .= "\n<br>Chequeo del l&iacute;mite trimestral en Vidaipay\n<br>";
        $vlim = leeSetup('trimVpay');
        $this->log .= "limite trimestral=$vlim<br>";
        $usrPerm = leeSetup('clientePerm');
        if (strlen($usrPerm) == 0) $usrPerm = 0;

        $arrCli = explode(',', leeSetup('clientePerm'));
        $this->db("select b.idcimex from tbl_aisBeneficiario b, tbl_aisClienteBeneficiario r, tbl_aisCliente c where c.id = r.idcliente and b.id = r.idbeneficiario and c.idcimex in ($usrPerm)");
        $arrBen = $this->temp->loadResultArray();
        $this->log .= "arrBen1: ".json_encode($arrBen)."<br>";
        // $arrBen = explode(',', leeSetup('benefPerm'));
        // $this->log .= "arrBen2: ".json_encode($arrBen)."<br>";

        if (date('n') >= 1 && date('n') <= 3) { // fechas del primer trimestre
            $fec1Ais = mktime(0, 0, 0, 01, 01, date("Y"));
            $fec2Ais = mktime(0, 0, 0 - 1, 04, 01, date("Y"));
            $this->log .= "Primer Trimestre\n<br>";
        } elseif (date('n') >= 4 && date('n') <= 6) { // fechas del segundo trimestre
            $fec1Ais = mktime(0, 0, 0, 04, 01, date("Y"));
            $fec2Ais = mktime(0, 0, 0 - 1, 07, 01, date("Y"));
            $this->log .= "Segundo Trimestre\n<br>";
        } elseif (date('n') >= 7 && date('n') <= 9) { // fechas del tercer trimestre
            $fec1Ais = mktime(0, 0, 0, 07, 01, date("Y"));
            $fec2Ais = mktime(0, 0, 0 - 1, 10, 01, date("Y"));
            $this->log .= "Tercer Trimestre\n<br>";
        } elseif (date('n') >= 10 && date('n') <= 12) { // fechas del cuarto trimestre
            $fec1Ais = mktime(0, 0, 0, 10, 01, date("Y"));
            $fec2Ais = mktime(0, 0, 0 - 1, 01, 01, date("Y") + 1);
            $this->log .= "Cuarto Trimestre\n<br>";
        }

        $q = "select concat(c.nombre,' ',c.papellido,' ',c.sapellido, ' (',c.usuario,') ',c.correo) cliente, bloq from tbl_aisCliente c where c.idcimex = ".$this->datAis['idremitente'];
        $this->db($q);
        $clienteDt = $this->temp->f('cliente');
        $this->log .= "Cliente: $clienteDt<br>";
        if ($this->temp->f('bloq') == 1) {
            $this->log .= "Cliente inhabilitado de por vida";
            $causa = $this->err = "Estimado Cliente $clienteDt, Ud. está inhabilitado para enviar dinero por nuestro sitio, si lo desea puede ponerse en contacto con <a href='mailto:info@vidaipay.com'>info@vidaipay.com</a>";
            $this->saltosPasar($this->err);
            return false;
        }

        $q = "select concat(c.nombre,' ',c.papellido,' ',c.sapellido) beneficiario, bloq from tbl_aisBeneficiario c where c.idcimex = ".$this->datAis['iddestin'];
        $this->db($q);
        $beneficiarioDt = $this->temp->f('beneficiario');
        $this->log .= "Beneficiario: $beneficiarioDt<br>";
        if ($this->temp->f('bloq') == 1) {
            $this->log .= "Beneficiario inhabilitado de por vida";
            $causa = $this->err = "Estimado Cliente $clienteDt, el beneficiario de esta operacion $beneficiarioDt, está inhabilitado para recibir dinero por nuestro sitio, si lo desea puede ponerse en contacto con <a href='mailto:info@vidaipay.com'>info@vidaipay.com</a>";
            $this->saltosPasar($this->err);
            return false;
        }

        //Chequeo de límite trimestral para el Remitente
        $q = "select sum(o.recibe) total1, sum(t.valor) total2 from tbl_aisOrden o, tbl_transacciones t, tbl_aisCliente c
				where o.idtransaccion = t.idtransaccion and o.idcliente = c.id and t.estado = 'A'
					and t.fecha between $fec1Ais and $fec2Ais and c.idcimex = " . $this->datAis['idremitente'];
        //me parece que el tema va por el monto enviado creo que sin importar moneda
        // $q = "select sum(t.valor) total from tbl_aisOrden o, tbl_transacciones t, tbl_aisCliente c
        // 		where o.idtransaccion = t.idtransaccion and o.idcliente = c.id and t.estado = 'A'
        // 			and t.fecha between $fec1Ais and $fec2Ais and c.idcimex = " . $this->datAis ['idremitente'];
        $this->db($q);
        ($this->temp->f('total1') >= $this->temp->f('total2')) ? $total = ($this->temp->f('total1')) : $total = ($this->temp->f('total2'));
        $this->log .= $total . " + " . $this->imp . " = " . ($total + $this->imp) . " > " . ($vlim * 100) . " / " . $total . " + " . $this->imp . " = " . ($total + $this->imp) . " > " . ($vlim * 100) . "\n<br>";

        if (($total + $this->imp) > ($vlim * 100)) {//si va a pasar mas del límite
            if (!in_array($this->datAis['idremitente'], $arrCli)) { //pero no está dentro de los Clientes permitidos
                $this->err = $causa = "Estimado Cliente $clienteDt, usted tiene un acumulado en envios de " . number_format($total / 100, 2) . ", con esta operacion de " . number_format($this->imp / 100, 2) . " llega al limite para un trimestre natural. Por favor, pongase en contacto con <a href='mailto:info@vidaipay.com'>info@vidaipay.com</a> para realizar el env&iacute;o:";
                $this->log .= $causa;
                $this->saltosPasar($causa);
                return false;
            }
        }
        //Chequeo de 5000
        if (($total + $this->imp) > 499900) {
            $this->err = $causa = "Estimado Cliente $clienteDt, usted tiene un acumulado en envios de " . number_format($total / 100, 2) . ", con esta operacion de " . number_format($this->imp / 100, 2) . " llega al tope que es de " . number_format(5000, 2) . " para un trimestre natural.";
            $this->log .= $causa;
            $this->saltosPasar($causa);
            return false;
        }

        //TODO ver el TODO anterior que se aplica acá también
        //Chequeo de límite trimestral para el Beneficiario
        $q = "select sum(o.recibe) total1, sum(t.valor) total2 from tbl_aisOrden o, tbl_transacciones t, tbl_aisBeneficiario c
			where o.idtransaccion = t.idtransaccion and o.idbeneficiario = c.id and t.estado = 'A'
				and t.fecha between $fec1Ais and $fec2Ais and c.idcimex = " . $this->datAis['iddestin'];
        //me parece que el tema va por el monto enviado creo que sin importar moneda
        // $q = "select sum(t.valor) total from tbl_aisOrden o, tbl_transacciones t, tbl_aisBeneficiario c
        // 	where o.idtransaccion = t.idtransaccion and o.idbeneficiario = c.id and t.estado = 'A'
        // 		and t.fecha between $fec1Ais and $fec2Ais and c.idcimex = " . $this->datAis ['iddestin'];
        $this->db($q);
        ($this->temp->f('total1') >= $this->temp->f('total2')) ? $total = ($this->temp->f('total1')) : $total = ($this->temp->f('total2'));
        $this->log .= $total . " + " . $this->imp . " = " . ($total + $this->imp) . " > " . ($vlim * 100) . " / " . $total . " + " . $this->imp . " = " . ($total + $this->imp) . " > " . ($vlim * 100) . "\n<br>";

        if (!in_array($this->datAis['iddestin'], $arrBen)) {
            if (($total + $this->imp) > ($vlim * 100)) {
                $this->err = $causa = "Estimado Cliente $clienteDt, el beneficiario $beneficiarioDt de esta operacion tiene acumulado " . number_format($total / 100, 2) . " con esta operacion de " . number_format($this->imp / 100, 2) . " llega al tope que es de " . number_format($vlim, 2) . " para un trimestre natural. Debe ponerse en contacto con <a href='mailto:info@vidaipay.com'>info@vidaipay.com</a> para realizar el envio:";
                $this->log .= $causa;
                $this->saltosPasar($causa);
                return false;
            }
        }

        // Chequeo de los 5000
        if (($total + $this->imp) > 499900) {
            $this->err = $causa = "Estimado Cliente / Dear Customer $clienteDt:<br><br> El beneficiario $beneficiarioDt de esta operaci&oacute;n tiene acumulado " . number_format($total / 100, 2) . " con esta operacion de " . number_format($this->imp / 100, 2) . " llega al tope que es de " . number_format(5000, 2) . " para un trimestre natural.";
            $this->log .= $causa;
            $this->saltosPasar($causa);
            return false;
        }

        //Chequeo si el Beneficiario existe en la BD
        $q = "select idtitanes from tbl_aisBeneficiario where idcimex = {$this->datAis['iddestin']}";
        $this->db($q);
        if ($this->temp->getNumRows() == 0) {
            $this->db("select concat(nombre,' ',papellido,' ',sapellido,' (',usuario, ')') cliente from tbl_aisCliente where idcimex = ".$this->datAis['idremitente']);
            $this->err = $causa = "El Beneficiario ".$this->datAis['iddestin']." no se encuenta en la BD, el Cliente es ".$this->temp->f('cliente');
            $this->log .= $causa;
            $this->saltosPasar($causa);
            return false;

        }
        //Chequeo si el Beneficiario tiene idTitanes en la BD
        if (strlen($this->temp->f('idtitanes')) < 4) {
            //include(benRequest.php);
            //if (!buscaBen($this->datAis['idremitente'])) {
            $this->db("select concat(nombre,' ',papellido,' ',sapellido,' (',usuario, ')') cliente from tbl_aisCliente where idcimex = ".$this->datAis['idremitente']);
            $this->err = $causa = "El Beneficiario ".$this->datAis['iddestin']." no tiene IDTitanes, el Cliente es ".$this->temp->f('cliente');
            $this->log .= $causa;
            $this->saltosPasar($causa);
            return false;
            //}
        }

        //Chequeo de operación repetida
        $iniDia = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
        $this->db("select count(t.idtransaccion) total from tbl_transacciones t, tbl_aisOrden o, tbl_aisCliente c, tbl_aisBeneficiario b where o.idtransaccion = t.idtransaccion and b.id = o.idbeneficiario and c.id = o.idcliente and t.estado = 'A' and t.fecha > $iniDia and c.idcimex = {$this->datAis['idremitente']} and b.idcimex = {$this->datAis['iddestin']} and valor_inicial = {$this->imp}");
        if ($this->temp->f('total') > 0) {
            $this->err = $causa = "Estimado Cliente / Dear Customer $clienteDt:<br><br>
			Usted no puede enviar remesa a un mismo beneficiario el mismo monto en un per&iacute;odo menor de 24 horas. Por favor, int&eacute;ntelo nuevamente usando una cantidad diferente o pruebe a volver enviar en el d&iacute;a de hoy despu&eacute;s de las 6.00 p.m. hora de Cuba. / You can not send remittances to the same beneficiary, the same amount in less than 24 hours. Please, try again using a different amount or try to send again today after 6.00 p.m. according to Cuba time.<br><br>Gracias por escoger Vidaipay para sus env&iacute;os de dinero a Cuba!";
            $this->log .= $causa;
            $this->saltosPasar($causa);
            return false;
        }

        //Chequeo de datos del Cliente
        $q = "select count(*) total from tbl_aisCliente where idcimex = " . $this->datAis['idremitente'];
        $this->db($q);
        if ($this->temp->f('total') == 0) {
            $this->err = $causa = "Estimado Cliente / Dear Customer $clienteDt:<br><br>Usted debe tener un registro mas actualizado que este con el que esta intentando realizar este envio, emplee ese registro / You should have a more up-to-date record of the one you are trying to send with, use that record.<br><br>Gracias por escoger Vidaipay para sus envios de dinero a Cuba!";
            $this->log .= $causa;
            $this->saltosPasar($causa);
            return false;
        }
        $q = "select count(*) total from tbl_aisCliente where fechaDocumento > " . time() . " and idcimex = " . $this->datAis['idremitente'];
        $this->db($q);
        if ($this->temp->f('total') == 0) {
            $this->err = $causa = "Estimado Cliente / Dear Customer $clienteDt:<br><br>La fecha de vencimiento del Documento de Identidad ha caducado. Debe subir el nuevo documento. / Your Identity Document has expired, please upload the new one.<br><br>Gracias por escoger Vidaipay para sus envios de dinero a Cuba!";
            $this->log .= $causa;
            $this->saltosPasar($causa);
            return false;
        }

        return true;
    }

	/**
	 * Chequeo de los límites por comercio
	 *
	 * @return void
	 */
	private function CheqLimComercios () {
		
		$this->db("select limxoper, limxdia, limadia, limxmes, limxano, cantxdia, cantxmes, cantxano from tbl_colComerLim where idcomercio = ".$this->datCom['id']);
		$arrLimCom = $this->temp->loadAssocList();
		$arrLimCom = $arrLimCom[0];

		if (count($arrLimCom) > 0 ) {
			//verifico lim por operacion
			if ($arrLimCom['limxoper'] > -1) {
				$this->log .= "Chequeo de límite máximo por operación para el comercio\n<br>" . ($this->imp / 100) . " > " . $arrLimCom['limxoper'] . "\n<br>";
				if (($this->imp / 100) > $arrLimCom['limxoper']) {
					$mes = "La operación {$this->tran} con un valor de " . ($this->imp / 100) . " mayor que el l&iacute;mite m&iacute;nimo permitido por operación para este comercio " . $this->datCom[1] . " que es de " . $arrLimCom['limxoper'];
					
					$this->log .= $mes . "\n<br>";
					$this->arrMD[] = $this->err = "M&aacute;ximo por operaci&oacte;n con esta (".number_format(($this->imp / 100),2).") y el comercio tiene permitido{2}:".number_format($arrLimCom['limxoper'],2);
					saltosPasar($this->err);
					return false;
				}
			}

			//verifico el limite diario
			if ($arrLimCom['limxdia'] > -1) {
				$this->log .= "Chequeo de limite maximo por operación para el comercio\n<br>" . ($this->imp / 100) . " > " . $arrLimCom['limxdia'] . "\n<br>";
				$this->db("select sum(t.valor_inicial/100/t.tasa) 'valor'
				FROM tbl_transacciones t 
				where t.estado in ('A','V','B','R') and t.tipoEntorno = 'P' and t.fecha > unix_timestamp('" . date('Y') . "-" . date('m') . "-" . date('d') . " 00:00:00') and t.idcomercio = ".$this->comer);
				
				if (($this->temp->f('valor') + $this->imp / 100) >= $arrLimCom['limxdia']) {
					$mes = "La operación {$this->tran} con un valor de " . ($this->imp / 100) . " mayor que el l&iacute;mite m&iacute;nimo permitido por día para este comercio " . $this->datCom[1] . " que es de " . $arrLimCom['limxdia'];
					
					$this->log .= $mes . "\n<br>";
					$this->arrMD[] = $this->err = "M&aacute;ximo por d&iacute;a, con esta operaci&oacute;n (".number_format(($this->temp->f('valor') + $this->imp / 100),2)."), el comercio sobrepas&oacute; lo permitido para el d&iacute;a{4}:".number_format($arrLimCom['limxdia'],2);
					saltosPasar($this->err);
					return false;
				}
			}
			
			//verifico el limite diario acumulado
			if ($arrLimCom['limadia'] > -1) {
				$this->db("select sum(t.valor_inicial/100/t.tasa) 'valor'
				FROM tbl_transacciones t 
				where t.estado in ('A','V','B','R') and t.tipoEntorno = 'P' and t.fecha > unix_timestamp('" . date('Y') . "-" . date('m') . "-01 00:00:00') and t.idcomercio = ".$this->comer);
				$this->log .= "Chequeo de limite diario acumulado para el comercio\n<br>" . ($this->temp->f('valor') + $this->imp / 100) . " >= " .($arrLimCom['limadia'] * date(j)) . "\n<br>";
				
				if (($this->temp->f('valor') + $this->imp / 100) >= ($arrLimCom['limadia'] * date(j))) {
					$mes = "La operación {$this->tran} con un valor de " . ($this->imp / 100) . " sobrepasa el límite máximo acumulado por día ".($arrLimCom['limadia'] * date(j))." para este comercio " . $this->datCom[1] . " que es de " . $arrLimCom['limadia']." ";
					
					$this->log .= $mes . "\n<br>";
					$this->arrMD[] = $this->err = "M&aacute;ximo acumulado por d&iacutea, hasta el d&iacute;a de hoy acumula el comercio (".number_format(($this->temp->f('valor') + $this->imp / 100),2).") tiene situado para hoy{4}:".number_format(($arrLimCom['limadia'] * date(j)),2);
					saltosPasar($this->err);
					return false;
				}
			}
			
			//verifico el limite mensual
			if ($arrLimCom['limxmes'] > -1) {
				$this->log .= "Chequeo de limite diario máximo para el comercio\n<br>" . ($this->imp / 100) . " > " . $arrLimCom['limxmes'] . "\n<br>";
				$this->db("select sum(t.valor_inicial/100/t.tasa) 'valor'
				FROM tbl_transacciones t 
				where t.estado in ('A','V','B','R') and t.tipoEntorno = 'P' and t.fecha > unix_timestamp('" . date('Y') . "-" . date('m') . "-01 00:00:00') and t.idcomercio = ".$this->comer);
				
				if (($this->temp->f('valor') + $this->imp / 100) >= $arrLimCom['limxmes']) {
					$mes = "La operación {$this->tran} con un valor de " . ($this->imp / 100) . " mayor que el l&iacute;mite m&iacute;nimo permitido por mes para este comercio " . $this->datCom[1] . " que es de " . $arrLimCom['limxmes'];
					
					$this->log .= $mes . "\n<br>";
					$this->arrMD[] = $this->err = "M&aacute;ximo por mes, el comercio con esta operaci&oacute;n llega a ".number_format(($this->temp->f('valor') + $this->imp / 100),2)." y tiene situado{5}:".number_format($arrLimCom['limxmes'],2);
					saltosPasar($this->err);
					return false;
				}
			}
			
			//verifico el limite anual
			if ($arrLimCom['limxano'] > -1) {
				$this->log .= "Chequeo de limite diario máximo para el comercio\n<br>" . ($this->imp / 100) . " > " . $arrLimCom['limxano'] . "\n<br>";
				$this->db("select sum(t.valor_inicial/100/t.tasa) 'valor'
				FROM tbl_transacciones t 
				where t.estado in ('A','V','B','R') and t.tipoEntorno = 'P' and t.fecha > unix_timestamp('" . date('Y') . "-01-01 00:00:00') and t.idcomercio = ".$this->comer);
				
				if (($this->temp->f('valor') + $this->imp / 100) >= $arrLimCom['limxano']) {
					$mes = "La operación {$this->tran} con un valor de " . ($this->imp / 100) . " mayor que el l&iacute;mite m&iacute;nimo permitido por año para este comercio " . $this->datCom[1] . " que es de " . $arrLimCom['limxano'];
					
					$this->log .= $mes . "\n<br>";
					$this->arrMD[] = $this->err = 'M&aacute;ximo por a&ntilde;o, el comercio con esta operaci&oacute;n llega a '.number_format(($this->temp->f('valor') + $this->imp / 100),2). ' y tiene permitido'."{6}:".number_format($arrLimCom['limxano'],2);
					saltosPasar($this->err);
					return false;
				}
			}
			
			//verifico el limite por cantidad de operaciones en el día
			if ($arrLimCom['cantxdia'] > -1) {
				$this->log .= "Chequeo de la cantidad máxima de operaciones al día para el comercio\n<br>" . $arrLimCom['cantxdia'] . "\n<br>";

				$this->db("select count(t.idtransaccion) 'valor'
				FROM tbl_transacciones t 
				where t.estado in ('A','V','B','R') and t.tipoEntorno = 'P' and t.fecha > unix_timestamp('" . date('Y') . "-" . date('m') . "-" . date('d') . " 00:00:00') and t.idcomercio = ".$this->comer);
				
				if ($this->temp->f('valor') >= $arrLimCom['cantxdia']) {
					$mes = "Con la operación {$this->tran} este comercio " . $this->datCom[1] . " ha arribado al l&iacute;mite diario m&aacute;ximo de operaciones que es de " . $arrLimCom['cantxdia'];
					
					$this->log .= $mes . "\n<br>";
					$this->arrMD[] = $this->err = 'M&aacute;ximo de operaciones por d&iacute;a, el comercio acumula hoy '.$this->temp->f('valor'). ' y tiene permitido'."{7}:".($arrLimCom['cantxdia']);
					saltosPasar($this->err);
					return false;
				}
			}
			
			//verifico el limite por cantidad de operaciones en el mes
			if ($arrLimCom['cantxmes'] > -1) {
				$this->log .= "Chequeo de la cantidad máxima de operaciones al mes para el comercio\n<br>" . $arrLimCom['cantxmes'] . "\n<br>";
				$this->db("select count(t.idtransaccion) 'valor'
				FROM tbl_transacciones t 
				where t.estado in ('A','V','B','R') and t.tipoEntorno = 'P' and t.fecha > unix_timestamp('" . date('Y') . "-" . date('m') . "-01 00:00:00') and t.idcomercio = ".$this->comer);
				
				if ($this->temp->f('valor') >= $arrLimCom['cantxmes']) {
					$mes = "Con la operación {$this->tran} este comercio " . $this->datCom[1] . " ha arribado al l&iacute;mite mensual m&aacute;ximo de operaciones que es de " . $arrLimCom['cantxmes'];
					
					$this->log .= $mes . "\n<br>";
					$this->arrMD[] = $this->err = 'M&aacute;ximo de operaciones por mes, el comercio acumula hasta hoy '.$this->temp->f('valor'). ' y tiene permitido'."{8}:".$arrLimCom['cantxmes'];
					saltosPasar($this->err);
					return false;
				}
			}
			
			//verifico el limite por cantidad de operaciones en el año
			if ($arrLimCom['cantxano'] > -1) {
				$this->log .= "Chequeo de la cantidad máxima de operaciones al año para el comercio\n<br>" . $arrLimCom['cantxano'] . "\n<br>";
				$this->db("select count(t.idtransaccion) 'valor'
				FROM tbl_transacciones t 
				where t.estado in ('A','V','B','R') and t.tipoEntorno = 'P' and t.fecha > unix_timestamp('" . date('Y') . "-01-01 00:00:00') and t.idcomercio = ".$this->comer);
				
				if ($this->temp->f('valor') >= $arrLimCom['cantxano']) {
					$mes = "Con la operación {$this->tran} este comercio " . $this->datCom[1] . " ha arribado al l&iacute;mite anual m&aacute;ximo de operaciones que es de " . $arrLimCom['cantxano'];
					
					$this->log .= $mes . "\n<br>";
					$this->arrMD[] = $this->err = 'M&aacute;ximo de operaciones por a&ntilde;o, el comercio acumula hasta hoy '.$this->temp->f('valor'). ' y tiene permitido'."{9}:".$arrLimCom['cantxano'];
					saltosPasar($this->err);
					return false;
				}
			}
	}
		

		return true;
	}

    function saltosPasar($motivo) {
        if (strlen($motivo)) {
			$motivo = strip_tags($motivo);
			if (strlen($this->datCom['id']) > 2)
            $this->db("insert into tbl_saltosPasar (identificador, idpasarela, idcomercio, idmoneda, fecha, motivo, idcliente) values ('".$this->tran."', '".$this->pasa."', '".$this->datCom['id']."', '".$this->mon."', unix_timestamp(), '$motivo', '".$this->datAis['idremitente']."')");
        }
    }

	/**
	 * Chequeo de los límites por pasarelas
	 * 
	 * @return boolean
	 */
	private function CheqLimites($verif = true) {
		$pase = 1;
		$causa = '';
		$datPas = array();
		$incPorc = (leeSetup('porcTPVs') + 100) / 100;
		//if ($verif)
		//this->log = '';
		$this->log .= "<br>";

		//Chequeo de usuario y correo para pasarela Moneytigo
		if ($this->pasa == 218) {
			$this->log .= "Entra en verificacion de Moneytigo<br>";
			if (
				strlen($this->email) < 5 ||
				strlen($this->nomb) < 3 ||
				strlen($this->apell) < 3 
			) {//si no vienen los datos de los usuarios los busco en reserva

				$this->log .= "No viene usuario y correo de la web<br>";
				$q = "select nombre, email from tbl_reserva where id_comercio = '{$this->comer}' and codigo = '{$this->tran}'";
				$this->db($q);
				if ($this->temp->getNumRows() == 1) {
					$arrNom = explode(' ', $this->temp->f('nombre'));
					$this->nomb = $arrNom[0];
					$this->apell = $arrNom[1];
					$this->email = $this->temp->f('email');
				} else {
					$this->log .= $causa = "Faltan datos de Cliente para pasarela Moneytigo". $this->pasa;
					$pase = 0;
				}
			}
		}

		// Variación para rotar todas las operaciones de pasarelas No Seguras entre las pasarelas 44 - Sabadell3, 52 - Bankia6
		// y luego que estas dos se llenen empezar a pasarlas por 20 - Caixa
		if (isset($datPas['secure']) && $datPas['secure'] == 0) {
			//if ($this->pasa != 44 && $this->comer != '122327460662') { // Si la pasarela no es Sabadel y no es el comercio Prueba
			//$this->pasa = 44;
			// if ($this->pasa != 20) {
			// if ($this->pasa != 20) {
			// $this->pasa = 20;
			$this->segura = 0;
			unset($datPas);
			$this->CheqLimites(false);
			//}
		} elseif (isset($datPas['secure']) && $datPas['secure'] == 1)
			$this->segura = 1;

		//Chequeo de las pasarelas permitidas al comercio
		//si el comercio es cubana me lo salto porque tiene rotación especial
		if ($this->comer != '129025985109' && $this->pasa != 1) {

			//reviso si la operación es de un sitio web o está originada en el Concentrador
			//$this->db("select * from tbl_reserva where id_comercio = ". $this->comer ." and codigo = '".$this->tran."'");
			($this->pweb == 0) ? $mq = "pasarelaAlMom" : $mq = "pasarela";

			//if (_MOS_CONFIG_DEBUG) $mq = "pasarelaAlMom";

			//cargo las pasarelas que tiene admitida el comercio
			$this->db("select $mq from tbl_comercio where idcomercio = '" . $this->comer . "'");
			$arrPa = explode(',', $this->temp->loadResult());
			if (_MOS_CONFIG_DEBUG) var_dump($this->temp->loadResult());

			//Si no está dentro de las pasarelas asignadas por el comercio pido el cambio
			if (!in_array($this->pasa, $arrPa)) {
				$this->log .= $causa = "Cambio de pasarela desde la Web, no est&aacute; asignada al comercio:".$this->pasa;
				$pase = 0;
			}
		}

		//Chequeo que la pasarela esté activa
		if ($this->comer != '122327460662') {
			if ($this->opr == 'A') $tipo = "'A'"; 
			elseif ($this->opr == 'R') $tipo = "'P','R'"; 
			else $tipo = "'A','P','R'";
			$this->db("select count(idPasarela) total from tbl_pasarela where activo = 1 and tipo in ($tipo) and idPasarela = '" . $this->pasa . "'");
			if ($this->temp->f('total') == 0) {
				$this->log .= $causa = "Cambio de pasarela obligado pasarela no activa{1}:". $this->pasa;
				$pase = 0;
			}
		}

		//Chequeo de las operaciones cada 6 horas
		//$this->log .= "<br>select count(idtransaccion) total from tbl_transacciones where idcomercio = '". $this->comer ."' and estado = 'A' and pasarela = ". $this->pasa ." and fecha > ".(time()-60*60*6);
		$this->db("select count(idtransaccion) total from tbl_transacciones where idcomercio = '" . $this->comer . "' and estado = 'A' and pasarela = " . $this->pasa . " and  fecha > " . (time() - 60 * 60 * 0.5));
		$oprR = $this->temp->f('total');
		if ($this->comer != '122327460662') {
			$arrCom = explode(',', leeSetup('com6Horas' . $this->pasa)); //El listado de los comercios está en tbl_setup
			if ($this->pasa == 44 || $this->pasa == 52) { // Si es de Sabadell3 o Bankia6 y además de alguno de los comercios
				if (in_array($this->comer, $arrCom) && $oprR > 0) {
						$this->log .= $causa = "Cambio de pasarela obligado operaci&oacte;n repetida en menos de 0.5 horas{1}:";
						$pase = 0;
					}
			}
		}

		//Chequeo de restricciones de IP por Sabadell3
		if ($this->comer != '122327460662') {
			if (
				$this->pasa == 44 && ( // Si es de Sabadell3 y además de alguna de estas IP
					$this->ip == '200.55.188.130' 		// Hotel Nacional
					|| $this->ip == '200.55.183.72' 	// Hotel Nacional
					|| $this->ip == '200.68.72.41' 		// Havanatur Argentina
					|| $this->ip == '190.179.230.61' 	// Outdoor Argentina
					|| $this->ip == '186.158.140.251' 	// Outdoor Argentina
					|| $this->ip == '186.13.7.203' 		// Outdoor Argentina 
					|| $this->ip == '186.13.2.213' 		// Outdoor Argentina
					|| $this->ip == '187.157.255.10' 	// Palco VI
					|| $this->ip == '200.55.138.170' 	// Hotel Saratoga
					|| $this->ip == '190.6.92.42' 		// Hotel Saratoga
					|| $this->ip == '190.15.155.106' 	// Amistur
				)
			) {
				$this->log .= $causa = "Cambio de pasarela obligado Sabadell3{1}:";
				$pase = 0;
			}
		}

		//Chequeo de restricciones por IP para Abanca2
		if ($this->comer != '122327460662') {
			if (
				$this->pasa == 53 && ( // Si es de Abanca2 y además de alguna de estas IP
					$this->ip != '200.55.188.130' 		// Hotel Nacional
					// $this->ip == '200.55.165.58' || 	//Gaviota Tours
					&& $this->ip != '190.179.230.61') 	// Outdoor Argentina
			) {
				$this->log .= $causa = "Cambio de pasarela obligado Abanca2{1}:";
				$pase = 0;
			}
		}

		// Chequeo de restricciones por países
		if ($this->comer != '122327460662') {
			if ($pase && $this->ip != '127.0.0.1') {
				$q = "select id from tbl_colPaisPasarelDeng where idpasarela = " . $this->pasa . " and idpais = " . $this->damepais();
				$this->db($q);
				if ($this->temp->f('id')) {
					$this->log .= $causa = "Cambio de pasarela por pa&iacute;s prohibido{1}:".$this->damepais();
					$pase = 0;
				}
			}
		}

		// Chequeo de límites para Cimex y Tocopay
		if ($this->comer == '527341458854' || $this->comer == '144172448713' || $this->comer == '163430526040' ||
			$this->comer == '169453189889') {
			if ($this->ChequeoAis() == false) {
				$pase = 0;
				$causa = $this->err;
			}
		}

        // Chequeo de límites para Vidaipay
		if ($this->comer == '166975114294' || $this->comer == '167707944853') {
			if ($this->ChequeoVpay() == false) {
				$pase = 0;
				$causa = $this->err;
			}
		}

		//Chequeo que el correo del cliente que exista para las pasarelas Navarrap
		if ($pase) {
			if (
				$this->pasa == 71
				|| $this->pasa == 72
			) {
				if (strlen($this->email) < 4) { //Si no se envió el correo del cliente lo busco en la BD
					$this->log .= "El Cliente viene sin correo y la oper va por un Navarrap\n<br>";
					$this->db("select email from tbl_reserva where id_comercio = '{$this->comer}' and codigo = '{$this->tran}' ");
					$this->email = $this->temp->f('email');
					if (strlen($this->email) < 4) {
						$this->log .= "El Cliente no tiene correo y se cancela este TPV\n<br>" . ($this->comer) . "\n<br>";
						$pase = 0;
						$causa = 'Cliente sin correo{1} por Navarrap';
					}
				}
			}
		}

		//buscando los límites
		if (!_CAMB_LIM) {//verifico si el cambio de limites por moneda está habilitado
			$q = "select nombre, LimMinOper, LimMaxOper, LimDiar, LimMens, LimAnual, LimOperIpDia, LimOperDia, pasarLim";
			if ($verif)
				$q .= ", secure";
			$q .= " from tbl_pasarela where idPasarela = " . $this->pasa;
		
			$this->db($q);
			$arrV = $this->temp->loadAssocList();
			$datPas = $arrV[0];
		} else {
			$q = "select p.nombre, p.pasarLim, c.valor ";
			if ($verif)
				$q .= ", p.secure";
			$q .= " from tbl_pasarela p, tbl_colPasarLimite c where c.idPasar = p.idPasarela and c.idmoneda = '".$this->mon."' and p.idPasarela = '" . $this->pasa. "' order by idLimite";
			$this->db($q);
			$arrV = $this->temp->loadAssocList();
				
			$datPas['nombre'] = $arrV[0]['nombre'];
			$datPas['pasarLim'] = $arrV[0]['pasarLim'];
			$q = "select nombre from tbl_limites order by id";
			$this->db($q);
			$arrLim = $this->temp->loadResultArray();
			// print_r($arrLim);
			// echo "<br>";
			if ($verif) $datPas['secure'] = $arrV[0]['secure'];
			for ($i=0; $i<count($arrV); $i++) {
				$datPas[$arrLim[$i]] = $arrV[$i]['valor'];
			}
			// print_r($arrV);

		}

		foreach ($datPas as $key => $value) {
			$this->log .= "$key => $value / ";
		}
		$this->log .= "\n<br>";
			// echo "<br>".$this->log; exit;

		//selecciona pasarelas iguales para límites
		if (strlen($datPas['pasarLim'])>0) {
			$this->db("select idPasarela from tbl_pasarela where pasarLim = " . $datPas['pasarLim']);
			$arrPasSum = implode(",", $this->temp->loadResultArray());
		}
		$this->log .= "pasarelas para limites $arrPasSum \n<br>";

		// Chequeo por la última pasarela que cursó
		// if ($this->comer != '129025985109' //Cubana
		// 	&& $this->comer != '152295812637' // Soy Cubano IT
		// 	&& $this->comer != '527341458854' //Fincimex
		// 	&& ($this->amex == 2 || $this->amex == 3) 
		// 	// && $this->comer != '140784511377' //y el H. Saratoga
		// 	// && $this->comer != '145918467582' //y el FCBC
		// 	&& $this->pweb != 1 //para los pagos que no sean de la Web
		// 	){
		// 		if ($pase) {
		// 			error_log("entra");
		// 			// if ($this->segura == 0 || $this->comer == '147145461846') { // que sean sin 3D o que el comercios sea SMD
		// 				$q = "select pasarela from tbl_transacciones where idcomercio = '" . $this->comer . "' order by fecha desc limit 0,1";
		// 				$this->db($q);
		// 				$this->log .= "Chequeo de la &uacute;ltima pasarela del comercio\n<br>" . ($this->comer) . "\n<br>";
		// 				$this->log .= $this->temp->f('pasarela') . " == " . $this->pasa . " ?\n<br>";
		// 				if ($this->temp->f('pasarela') == $this->pasa) {
		// 					$mes = "Esta pasarela " . $datPas['nombre'] . " identificador " . $this->pasa . " ha cursado la operación anterior";
		// 					//$this->corr->todo ( 44, 'Alerta por límites', $mes );
		// 					$this->log .= $this->err = $mes . "\n<br>";
		// 					$pasV = $this->pasa;
		// 					$pase = 0;
		// 					$causa = 'Ultima pasarela usada';
		// 				}
		// 			// }
		// 		}
		// 	}

		// Chequeo del tipo de moneda
		if ($pase) {
			$q = "select count(id) 'total' " . "FROM tbl_colPasarMon " . "where idmoneda = '" . $this->mon . "' " . "and estado = 1 " . "and idpasarela = " . $this->pasa;
			$this->db($q);
			$this->log .= "Chequeo del tipo de moneda\n<br>" . ($this->mon) . "\n<br>";
			if ($this->temp->f('total') == 0) {
				$mes = "Esta pasarela " . $datPas['nombre'] . " identificador " . $this->pasa . " no soporta la moneda " . $this->mon . " enviada";
				//$this->corr->todo ( 44, 'Alerta por límites', $mes );
				$this->log .= $this->err = $mes . "\n<br>";
				$pasV = $this->pasa;
				$pase = 0;
				$causa = 'Moneda que no est&aacute; en esta pasarela{1}:'.$this->mon;
				// if ($datPas['secure'] == 1) return false;
			}
		}

		// Chequeo de límite mínimo por operación
		if ($pase) {
			$this->log .= "Chequeo de limite minimo por operación\n<br>";
			$this->log .= ($this->imp / 100) . " < " . $datPas['LimMinOper'] . "\n<br>";
			if (($this->imp / 100) < $datPas['LimMinOper']) {
				$mes = "La operación {$this->tran} con un valor de " . ($this->imp / 100) . " menor que el l&iacute;mite m&iacute;nimo permitido por operación" . " para esta la pasarela " . $datPas['nombre'] . " identificador " . $this->pasa . " que es de " . $datPas['LimMinOper'];
				//$this->corr->todo ( 44, 'Alerta por límites', $mes );
				$this->log .= $this->err = $mes . "\n<br>";
				$pasV = $this->pasa;
				$pase = 0;
				$causa = 'M&iacute;nimo por operaci&oacute;n{3}:'.number_format($datPas['LimMinOper'],2);
				// if ($datPas['secure'] == 1) return false;
			}
		}

		// Chequeo de límite máximo por operación
		if ($pase) {
			$this->log .= "Chequeo de limite maximo por operación\n<br>";
			$this->log .= ($this->imp / 100) . " > " . $datPas['LimMaxOper'] . "\n<br>";
			if (($this->imp / 100) > $datPas['LimMaxOper']) {
				$mes = "La operación {$this->tran} con un valor de " . ($this->imp / 100) . " mayor que el l&iacute;mite m&aacute;ximo permitido por operación" . " para esta la pasarela " . $datPas['nombre'] . " identificador " . $this->pasa . " que es de " . $datPas['LimMaxOper'];
				//$this->corr->todo ( 44, 'Alerta por límites', $mes );
				$this->log .= $this->err = $mes . "\n<br>";
				$pasV = $this->pasa;
				$pase = 0;
				$causa = 'M&aacute;ximo por operaci&oacute;n{2}:'.number_format($datPas['LimMaxOper'],2);
				// if ($datPas['secure'] == 1) return false;
			}
		}

		// Chequeo de límite cantidad de transacciones por Ip
		if ($pase) {
			$this->log .= "Chequeo del n&uacute;mero de transacciones para una IP\n<br>";
			$q = "select count(t.idtransaccion) 'total'
					FROM tbl_transacciones t 
						where t.tipoEntorno = 'P' 
							and from_unixtime(t.fecha, '%d%m%y') = '" . date('d') . date('m') . date('y') . "'
							and t.ip = '{$this->ip}'
							and t.pasarela in (" . $arrPasSum . ")";
			$this->db($q);
			$this->log .= $this->temp->f('total') . " >= " . $datPas['LimOperIpDia'] . "\n<br>";
			if ($this->temp->f('total') >= $datPas['LimOperIpDia']) {
				$mes = "Se ha arribado al l&iacute;mite m&aacute;ximo de operaciones por IP al d&iacute;a que es de {$datPas['LimOperIpDia']} 
							para esta la pasarela " . $datPas['nombre'] . " identificador " . $this->pasa;
				//$this->corr->todo ( 44, 'Alerta por límites', $mes );
				$this->log .= $this->err = $mes . "\n<br>";
				$pasV = $this->pasa;
				$pase = 0;
				$causa = 'M&aacute;ximo operaciones por IP{1}:'. $datPas['LimOperIpDia'];
				// if ($datPas['secure'] == 1) return false;
			}
		}

		// Chequeo de límite diario
		if ($pase) {
			$this->log .= "Chequeo de l&iacute;mite diario\n<br>";
			$q = "select sum(t.valor_inicial/100/t.tasa) 'valor'
					FROM tbl_transacciones t 
					where t.estado in ('A','V','B','R') 
						and t.tipoEntorno = 'P' 
						and t.fecha > unix_timestamp('" . date('Y') . "-" . date('m') . "-" . date('d') . " 00:00:00')
						and t.pasarela in (" . $arrPasSum . ")";
			$this->db($q);
			$this->log .= ($this->temp->f('valor') + $this->imp / 100) . " >= " . ($datPas['LimDiar'] * $incPorc) . "\n<br>";
			if (($this->temp->f('valor') + $this->imp / 100) >= ($datPas['LimDiar'] * $incPorc)) {
				$mes = "Con la operación {$this->tran} de un valor de " . ($this->imp / 100) . " se ha arribado al l&iacute;mite diario m&aacute;ximo por montos que es de {$datPas['LimDiar']} para esta la pasarela " . $datPas['nombre'] . " identificador " . $this->pasa;
				$this->log .= $this->err = $mes . "\n<br>";
				$pasV = $this->pasa;
				$pase = 0;
				$causa = 'L&iacute;mite diarios{1}:'.number_format(($datPas['LimDiar'] * $incPorc),2);
				// if ($datPas['secure'] == 1) return false;
			}
		}

		// Chequeo de límite mensual
		if ($pase) {
			$this->log .= "Chequeo de l&iacute;mite mensual\n<br>";
			$q = "select sum(t.valor_inicial/100/t.tasa) 'valor'
				FROM tbl_transacciones t 
				where t.estado in ('A','V','B','R') 
					and t.tipoEntorno = 'P' 
					and t.fecha > unix_timestamp('" . date('Y') . "-" . date('m') . "-01 00:00:00')
					and t.pasarela in (" . $arrPasSum . ")";
			$this->db($q);
			$this->log .= ($this->temp->f('valor') + $this->imp / 100) . " >= " . $datPas['LimMens'] . "\n<br>";
			if (($this->temp->f('valor') + $this->imp / 100) >= $datPas['LimMens']) {
				$mes = "Con la operación {$this->tran} de un valor de " . ($this->imp / 100) . " se ha arribado al l&iacute;mite mensual m&aacute;ximo por 
						montos que es de {$datPas['LimMens']} para esta la pasarela " . $datPas['nombre'] . " identificador " . $this->pasa;
				$this->log .= $this->err = $mes . "\n<br>";
				$pasV = $this->pasa;
				$pase = 0;
				$causa = 'L&iacute;mite mensual{1}:'.number_format($datPas['LimMens'],2);
				// if ($datPas['secure'] == 1) return false;
			}
		}

		// Chequeo de límite anual
		if ($pase) {
			$this->log .= "Chequeo de l&iacute;mite anual\n<br>";
			$q = "select sum(t.valor_inicial/100/t.tasa) 'valor'
				FROM tbl_transacciones t 
				where t.estado in ('A','V','B','R') 
					and t.tipoEntorno = 'P' 
					and t.fecha > unix_timestamp('" . date('Y') . "-01-01 00:00:00')
					and t.pasarela in (" . $arrPasSum . ")";
			$this->db($q);
			$this->log .= ($this->temp->f('valor') + $this->imp / 100) . " >= " . $datPas['LimAnual'] . "\n<br>";
			if (($this->temp->f('valor') + $this->imp / 100) >= $datPas['LimAnual']) {
				$mes = "Con la operación {$this->tran} de un valor de " . ($this->imp / 100) . " se ha arribado al l&iacute;mite anual m&aacute;ximo por 
						montos que es de {$datPas['LimAnual']} para esta la pasarela " . $datPas['nombre'] . " identificador " . $this->pasa;
				$this->log .= $this->err = $mes . "\n<br>";
				$pasV = $this->pasa;
				$pase = 0;
				$causa = 'L&iacute;mite anual{1}:'.number_format($datPas['LimAnual'],2);
				// if ($datPas['secure'] == 1) return false;
			}
		}

		// Chequeo de límite de cantidad de operaciones al día
		if ($pase) {
			$this->log .= "Chequeo de cantidad de operaciones al d&iacute;a\n<br>";
			$q = "select count(t.idtransaccion) 'valor'
				FROM tbl_transacciones t ".
				// "where t.estado in ('A','V','B','R','D') ".
				"where t.estado in ('A','V','B','R') ".
				"	and t.tipoEntorno = 'P' 
					and t.fecha > unix_timestamp('" . date('Y') . "-" . date('m') . "-" . date('d') . " 00:00:00')
					and t.pasarela in (" . $arrPasSum . ")";
			$this->db($q);
			$this->log .= $this->temp->f('valor') . " >= " . $datPas['LimOperDia'] . "\n<br>";
			if ($this->temp->f('valor') > $datPas['LimOperDia']) {
				$mes = "Con la operación {$this->tran} se ha arribado al l&iacute;mite diario m&aacute;ximo por operaciones que es de 
						{$datPas['LimOperDia']} para esta la pasarela " . $datPas['nombre'] . " identificador " . $this->pasa;
				$this->log .= $this->err = $mes . "\n<br>";
				$pasV = $this->pasa;
				$pase = 0;
				$causa = 'Cantidad de operaciones diarias{1}:'.$datPas['LimOperDia'];
				// if ($datPas['secure'] == 1) return false;
			}
		}

		// chequeo del tipo de tarjeta usado en el pago
		if ($pase) {
			$q = "select count(*) tot from tbl_colTarjPasar where idPasar = {$this->pasa} and idTarj = " . $this->amex;
			$this->db($q);
			if ($this->temp->f('tot') != 1) {
				$this->db("select nombre from tbl_tarjetas where id = " . $this->amex);
				$this->log .= "Solicitado el pago con " . $this->temp->f('nombre') . " pero la pasarela " . $this->pasa . " no lo tiene\n<br>";
				//if ($this->segura == 1)
				//$this->pasa = 41;
				//else
				//$this->pasa = 44; //Sabadell3
				//$pase = 1;
				//$this->log .= "Solicitado ".$this->temp->f('nombre').", se va a la pasarela " . $this->pasa . "\n<br>";
				$pase = 0;
				$causa = "Pasarela no tiene la tarjeta{1}:".$this->temp->f('nombre');
			}
		}

		//$pase = 0;
		$this->log .= "Pase = $pase<br>\n";
		$this->log .= "Segura = $this->segura<br>\n";
		$this->log .= "Causa = $causa<br>\n";
		$this->arrMD[] = $causa;
		$this->arrPD[] = $this->pasa;
        $this->saltosPasar($causa);


		$this->log .= "psArrayB=".implode(",",$this->psArray)."<br>";
// echo "catArr=".$this->datCom['pasarelaAlMom'];
		//if (_MOS_CONFIG_DEBUG) $pase = 0;

		if ($pase == 0 && $this->comer != '122327460662') { // si ha saltado algún límite.. y el comercio no es Prueba
			$this->log .= "Necesita cambio de pasarela\n<br>";
			
			if ($this->comer == '527341458854' || $this->comer == '144172448713' || $this->comer == '163430526040'
                || $this->comer == '166975114294' || $this->comer == '167707944853' || $this->comer == '169453189889')
					return false; // Si el comercio es Cimex no hay cambio de pasarela que valga y retorna error

			if (count($this->psArray) == 0 && $this->mp == '') {
				// entra por primera vez determina los elementos y el orden del array de pasarelas seguras
				// basados en el comportamiento que han tenido los últimos 7 días

				return false;

				/* Reina
				if (count($this->psArray) == 0)
					return true; // si no aparece ninguna pasarela */
			} elseif (count($this->psArray) == 0 && $this->mp != '') { // si recorre todo el array y no tiene ninguna pasarela seleccionada...

				// $this->pasa = $this->mps; //toma la mejor y se encarga a Dios
				$this->corr->todo(44, "Operación posible a Denegar", "Esta operación superó algún límite la causa fué: $causa");
				return false; // Retorna falso y dá error
			} elseif (count($this->psArray) == 1) {
				//no quedan más pasarelas en el array escojo retorno esta última y aviso

				//retorno falso si no quedan mas pasarelas
				$this->corr->todo(44, "Problema con la pasarela escogida", $this->log);
				return false;

				$sal = '';
				
				for($i = 0; $i < count($this->arrPV); $i++) {
					$sal .= "La pasarela ".$this->arrPV[$i]." saltó por ".$this->arrMD[$i]."<br>";
				}
				$this->log .= "<br><br>problema con la pasarela escogida ".$this->arrMD[$i].$sal;
//				$this->corr->todo(44, "Problema con la pasarela escogida", $this->log);

				//si la tarjeta es Amex y la moneda USD verifico que la operación se vaya por Navarra Amex 3D
				//que es la única autorizada a cobrar usd
				if ($this->mon == '840' && $this->amex == '1') {
					return $this->usdxamex();
				} else 
					return true;
			}
			$this->log .= "El psArray tiene " . count($this->psArray) . " elementos\n<br>";
			array_shift($this->psArray);
			$this->pasa = $this->psArray[0];

			$mes .= " Pasar&aacute; a la pasarela" . $this->pasa;
			//$this->corr->todo ( 44, 'Alerta por límites', $mes );
			$this->log .= "La pasarela {$datPas['nombre']} ha llegado al tope por $causa la operación {$this->tran} pasó a la pasarela {$this->pasa}
							contador = $this->contador\n<br>\n<br>";
			$this->contador++;
			return $this->CheqLimites(false);
		} else
			return true;
	}

	/**
	 * Verifica que el comercio tenga la(s) pasarela(2) autorizadas para las operaciones
	 * con Amex en usd, si no lo tuviera retorna error y la operación no se efectúa
	 *
	 * @return boolean
	 */
	function usdxamex(){
		//array de pasarelas que pueden procesar Amex con usd
		$arrAmexusd = array(
			'93' //Navarra Amex 3D
		);

		echo count($arrAmexusd)."<br>";
		echo count($this->psArray)."<br>";
		$resul = array_intersect($arrAmexusd, $this->arrORd);
		var_dump($arrAmexusd);
		var_dump($this->arrORd);
		var_dump($resul);
		exit;

		if (count($resul) > 0) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Chequea el número de la tarjeta
	 * 
	 * @param integer $number        	
	 * @return boolean
	 */
	function luhn($number)
	{
		$odd = true;
		$sum = 0;

		$ver = $this->check_cc($number);
		if ($this->pasa == 57 && ($ver != 'VISA' && $ver != 'MC'))
			return false;
		$this->ttarj = $ver;

		foreach (array_reverse(str_split($number)) as $num) {
			$sum += array_sum(str_split(($odd = !$odd) ? $num * 2 : $num));
		}

		return (($sum % 10 == 0) && ($sum != 0));
	}

	/**
	 * Chequea el tipo de tarjeta que se está procesando
	 * 
	 * @param string $cc        	
	 * @return boolean
	 */
	private function check_cc($cc) {
		$cards = array(
			"visa" => "(4\d{12}(?:\d{3})?)",
			"amex" => "(3[47]\d{13})",
			"jcb" => "(35[2-8][89]\d\d\d{10})",
			"maestro" => "((?:5020|5038|6304|6579|6761)\d{12}(?:\d\d)?)",
			"solo" => "((?:6334|6767)\d{12}(?:\d\d)?\d?)",
			"mastercard" => "(5[1-5]\d{14})",
			"switch" => "(?:(?:(?:4903|4905|4911|4936|6333|6759)\d{12})|(?:(?:564182|633110)\d{10})(\d\d)?\d?)"
		);
		$names = array(
			"VISA",
			"American Express",
			"JCB",
			"Maestro",
			"Solo",
			"MC",
			"Switch"
		);
		$matches = array();
		$pattern = "#^(?:" . implode("|", $cards) . ")$#";
		$result = preg_match($pattern, $cc, $matches);
		return ($result > 0) ? $names[sizeof($matches) - 2] : false;
	}

	/**
	 * Busca el país desde donde se está haciendo la operación
	 * 
	 * @return string
	 */
	function damepais() {
		$idpais = 'null';
		$this->log .= "\n<br>Determina el país desde donde se está realizando la operación\<br>";
		if (function_exists(geoip_country_code3_by_name)) {
			if (strlen(geoip_country_code3_by_name($this->ip)) > 0) {
				$this->db("select id from tbl_paises where iso = '" . geoip_country_code3_by_name($this->ip) . "'");
				$idpais = $this->temp->f('id');

				if ($this->temp->num_rows() === 0) {
					$accN = $this->db("insert into tbl_paises (nombre, iso) values ('" . geoip_country_name_by_name($this->ip) . "', '" . geoip_country_code3_by_name($this->ip) . "')");
					if ($accN === false)
						return false;

					$accN = $this->db("select id from tbl_paises where iso = '" . geoip_country_code3_by_name($this->ip) . "'");
					if ($accN === false)
						return false;
					$idpais = $this->temp->f('id');
				}
			}
		}
		return $idpais;
	}

	/**
	 * Inserta la operación en la BD
	 * 
	 * @return boolean
	 */
	function operacion($bipayId = null) {
		$this->log .= "Determina la moneda con la que Bidaiondo pagara al comercio<br>";
		$this->db("select idmoneda from tbl_colComerPasaMon where idcomercio = '".$this->datCom['id']."' and idpasarela = '".$this->pasa."'");
		$this->receiveCurrency = $this->temp->f('idmoneda');
		$this->log .= "El comercio recibirá el resultado de esta operación en {$this->receiveCurrency}<br>";

        $this->log .= "tipo de operacion: ".$this->opr."<br>";
		if ($this->opr == 'P' || $this->opr == 'A' || $this->opr == 'R') {
			$this->idTrn = trIdent(true); // Genera el identificador de la transacción
			
			$this->log .= "\n<br>Inserta la operación\<br>";
			if(isset($bipayId)){
				$accN = $this->db("insert into tbl_transacciones (idtransaccion,idcomercio,identificador,tipoOperacion,fecha,fecha_mod,"."valor_inicial,tipoEntorno,moneda,estado, sesion, idioma, pasarela, ip, idpais, tpv, id_tarjeta, tipoPago, bipayId) values ('".$this->idTrn."','{$this->comer}','{$this->tran}','{$this->opr}',".time().",".time().","."{$this->imp},'{$this->datCom['estado']}',{$this->mon},'P','{$this->frma}','{$this->idi}',{$this->pasa},"."'{$this->ip}','".$this->damepais()."','{$this->tpv}','{$this->amex}','{$this->tipo}','$bipayId')");
			} else{
				$accN = $this->db("insert into tbl_transacciones (idtransaccion,idcomercio,identificador,tipoOperacion,fecha,fecha_mod,"."valor_inicial,tipoEntorno,moneda,estado, sesion, idioma, pasarela, ip, idpais, tpv, id_tarjeta, tipoPago) values ('" . $this->idTrn . "', '{$this->comer}', '{$this->tran}', '{$this->opr}', " . time() . ", " . time() . ", " . "{$this->imp}, '{$this->datCom['estado']}', {$this->mon}, 'P', '{$this->frma}', '{$this->idi}', {$this->pasa}, " . "'{$this->ip}', '" . $this->damepais() . "', '{$this->tpv}', '{$this->amex}', '{$this->tipo}')");
			}
			if ($accN === false)
				return false;
			// actualiza la pasarela en la tbl_reserva segun el último cambio realizado
			$this->db("update tbl_reserva set pasarela = {$this->pasa} where id_comercio = '{$this->comer}' and codigo = '{$this->tran}'");

			// if ($this->comer == '527341458854' || $this->comer == '144172448713' || $this->comer == '163430526040') {
			// 	if (!$this->db("insert into tbl_aisDato (idtransaccion, fecha, idCliente, idBeneficiario) 
			// 				values ('{$this->idTrn}', " . time() . ", '{$this->datAis['idremitente']}', '{$this->datAis['iddestin']}')"))
			// 		return false;
			// }
		}
		return $this->CAprueba();
	}

	/**
	 * Chequeo del comercio
	 * 
	 * @return boolean
	 */
	function verComer() {
		$this->log = "\n<br>Verifica la validez del comercio\n<br>";
		// if (time() >= mktime(0, 0, 1, 8, 1, 2014) and $this->comer == '527341458854') {
		// $this->err = "Comercio inválido";
		// $this->log .= "Ha tratado de entrar una operación de AIS";
		// return false;
		// }

		$this->db(sprintf("select id, nombre, prefijo_trans, estado, pasarela, pasarelaAlMom, url, usdxamex from tbl_comercio where activo = 'S' " . "and idcomercio = '%s'", $this->comer));

		if ($this->temp->num_rows() > 0) {
			$arrVal = $this->temp->loadAssocList();
			$this->datCom = $arrVal[0];
			$this->log .= "Comercio = " . $arrVal[0]['nombre'] . "\n<br>";
			// print_r($this->datCom);
		} else {
			$this->err = "falla por comercio";
			$this->log .= "falla por comercio\n<br>";
			$this->saltosPasar($this->err);
			return false;
		}

		if ($this->CheqLimComercios() == false) return false;


		$this->log .= "moneda=".$this->mon." \n<br>";
		$this->log .= "usdxamex=".$this->datCom['usdxamex']." \n<br>";
		$this->log .= "amex=".$this->amex." \n<br>";
		// Chequeo de restricción por la autorización de usar Amex con otra divisa que no sea EUR
		if ($this->amex == 1) {
			if ($this->mon != 978 && $this->mon != 840) {
				$pase = 0;
				$this->err = "Las Amex no tienen permitido pagos que no sean en USD o EUR";
				$this->log .= $this->err." \n<br>";
				$this->saltosPasar($this->err);
				return false;
			}
			if ($this->mon != 978 && $this->datCom['usdxamex'] == 0) {
				$pase = 0;
				$this->err = "El comercio no tiene permitidos pagos con Amex que no sean en Euros";
				$this->log .= $this->err." \n<br>";
				$this->saltosPasar($this->err);
				return false;
			} elseif ($this->datCom['usdxamex'] == 2) {
				$pase = 0;
				$this->err = "El comercio no tiene permitidos pagos con Amex";
				$this->log .= $this->err." \n<br>";
				$this->saltosPasar($this->err);
				return false;
			}
		}
		return true;
	}

	/**
	 * Verifica la firma de la operación
	 * 
	 * @return boolean
	 */
	function verFir() {
		$this->log = "\n<br>Verifica la firma de la operación\n<br>";
		$this->log .= "firma $this->comer . $this->tran . $this->imp . $this->mon . $this->opr\n<br>";
		if (strlen($this->frma) == 32) 
			$Calc = convierte($this->comer, $this->tran, $this->imp, $this->mon, $this->opr);
		elseif (strlen($this->frma) > 32) 
			$Calc = convierte256($this->comer, $this->tran, $this->imp, $this->mon, $this->opr);
		$this->log .= "Firma recibida {$this->frma}\n<br>";
		$this->log .= "Firma generada {$Calc}\n<br>";
		if ($Calc != $this->frma) {
			$this->log .= "falla por firma\n<br>";
//			$this->err = "Firma recibida {$this->frma}\n<br>Firma generada {$Calc}\n<br>falla por firma";
			$this->err = "Firma recibida {$this->frma}\n<br>";
			$this->saltosPasar($this->err);
			return false;
		}
		return true;
	}

	/**
	 * Verificación de las operaciones desde el comercio
	 * 
	 * @return boolean
	 */
	function verTran() {
		$referencia = leeSetup('refOpPruebas');
		if($this->tran === $referencia){
			// Se verifica si la operacion es de un linl de pruebas
		} else {
			$this->log = "\n<br>Verifica que la operación no se haya repetido anteriormente\n<br>";
			$this->db("select idtransaccion, sesion, from_unixtime(fecha,'%d/%m/%y %H:%i:%s') fc from tbl_transacciones where identificador = '" . $this->tran . "' and idcomercio = '" . $this->comer . "'");

			if ($this->temp->f('sesion') == $this->frma) {
				$idtt = $this->temp->f('idtransaccion');
				$this->db("select r.email ecliente, r.nombre cliente, a.nombre, a.email from tbl_admin a, tbl_reserva r where r.id_admin = a.idadmin and r.id_comercio = '" . $this->comer . "' and r.codigo = '" . $this->tran . "'");
				if ($this->temp->num_rows()) {
					$this->arrCli[0] = $this->temp->f('cliente');
					$this->arrCli[1] = $this->temp->f('ecliente');
					$this->arrCli[2] = $idtt;
					$this->arrUsu[0] = $this->temp->f('nombre');
					$this->arrUsu[1] = $this->temp->f('email');
					$this->log .= "Se crean los arrays arrCli y arrUsu con " . count($this->arrCli) . " y " . count($this->arrCli) . "elementos respectivamente.\n<br>";
				}
				$this->err = "Transacci&oacute;n duplicada. P&iacute;dale a su comercio la genere nuevamente.<br>Duplicated transacction. Ask " . "your commerce generates it again.<br>Transazione doppia. Ha cliccato due volte sul collegamento (link). Ne richieda uno nuovo.";
				$this->log .= "Transacción duplicada " . $this->temp->f('fc') . "\n<br>";
				$this->saltosPasar("Transacci&oacute;n duplicada" . $this->temp->f('fc') . "\n<br>");
				return false;
			}
		}
		return true;
	}

	/**
	 * Realiza todas las verificaciones sobre las IPs que envían pagos
	 * 
	 * @return boolean
	 */
	function verIP() {
		$this->log .= "\n<br>Verifica que no sean hayan producido mas de " . leeSetup('cantReintentos') . " intentos en menos de " . leeSetup('minReintento') . " minutos\n<br>";
		if (!$this->ipblanca($this->ip)) {
			$reinten = leeSetup('cantReintentos');
			if ($reinten == '' || $reinten == 0) $reinten = 5;
			$this->db(sprintf("select estado from tbl_transacciones where idcomercio = '%s' and fecha_mod > %d and ip = '%s' order by fecha_mod limit 0,$reinten", $this->comer, (time() - (leeSetup('minReintento') * 60)), $this->ip));
			$arrDen = $this->temp->loadResultArray();
			$arrDen = (array_count_values($arrDen));
			if ($arrDen['D'] >= leeSetup('cantReintentos')) {
				$this->log .= "Se han producido mas intentos de pagos que los permitidos penalizo la IP\n<br>";
				$this->db("insert into tbl_ipbloq (ip, fecha, identificador, idComercio, bloqueada, idCom) values
								('" . $this->ip . "', " . time() . ", '" . $this->tran . "', '" . $this->comer . "', 1, " . $this->datCom['id'] . ")");
				$mes = "Estimado Administrador \n\nUn cliente ha tratado de realizar el pago de la transacci&oacute;n " . "en repetidas ocasiones de forma infructuosa esto ha causado que la IP desde donde ha realizado los intentos " . "({$this->ip}) se haya bloqueado.\n\nSi tiene posibilidad de contactarle, p&iacute;dale por favor que llame al " . "banco emisor de la tarjeta y aver&iacute;gue las causas de la negaci&oacute;n del pago.\n\nUna vez que este " . "problema se solucione desbloquee esta IP (men&uacute;: Reportes / Ips Bloqueadas) para que el cliente pueda " . "usarla nuevamente.\n\nAdminstrador de Sistemas\nAdministrador de Comercios";
				$this->corr->todo(11, 'IP bloqueada', $mes);
				$this->log .= "La ip {$this->ip} ha sido bloqueada";
				$this->err = "Su Ip ha sido bloqueada, no puede realizar mas pagos. Contacte con su comercio.<br><br>Your IP has been banned, " . "you can`t make more payments. Contact your commerce.";
				$this->saltosPasar($this->err);
				return false;
			}
		} else return true;

		$this->log .= "\n<br>Verifica que la ip desde donde están pagando no está bloqueada\n<br>";
        /* Reina - 26-01-2023
        $this->db(sprintf("select id from tbl_ipBL where ip='%s' and cuenta >= 5", $this->ip));
        if ($this->temp->num_rows() !== 0) {
            $this->err = 'Su IP '.$this->ip.' est&aacute; bloqueada, contacte a su comercio / Your IP '.$this->ip.' is banned, contact to your e-commerce';
            $this->log .= "Intento de pago desde la IP Bloqueada: " . $this->ip;
            $this->saltosPasar($this->err);
            return false;
        }*/

        $this->db(sprintf("SELECT cuenta FROM tbl_ipBL WHERE ip='%s'", $this->ip));
        if ($this->temp->num_rows() > 0) {
            $cuenta = $this->temp->f('cuenta');
            if( $cuenta > 0){
                $newCuenta = floor($cuenta / 2);
                if($newCuenta > 5) {
                    $this->err = 'Su IP '.$this->ip.' est&aacute; bloqueada, contacte a su comercio / Your IP '.$this->ip.' is banned, contact to your e-commerce';
                    $this->log .= "Intento de pago desde la IP Bloqueada: " . $this->ip;
                    $this->saltosPasar($this->err);
                    return false;
                }
            }
        }

		$this->log .= "\n<br>Verifica que la ip no está bloqueada por pagos denegados\n<br>";
		$this->db(sprintf("select idips from tbl_ipbloq where ip = '%s' and bloqueada = 1", $this->ip));
		if ($this->temp->num_rows() !== 0) {
			$this->err = 'Su IP est&aacute; bloqueada, contacte a su comercio / Your IP is banned, contact to your e-commerce';
			$this->log .= "Intento de pago desde la IP Bloqueada: {$this->ip}";
			$this->saltosPasar($this->err);
			return false;
		}

		return true;
	}
	
    /**
     * Envíos de SMS desde la plataforma Esendex
     *
     * @param [int] $id identificador del mensaje
     * @param [varchar] $mens texto del mensaje
     * @return void
     */
    function envioSMSen($id, $mens) {
        include "Esendex/autoload.php";
        global $temp;
        $sale = '';

        $q = "select a.telefono from tbl_admin a, tbl_colSmsAdmin c where c.idadmin = a.idadmin and c.idsms = $id";
        $temp->query($q);
        $arrUsr = $temp->loadResultArray();

        foreach ($arrUsr as $telefono) {

            $message = new \Esendex\Model\DispatchMessage(
                "Bidaiondo", // Send from
                $telefono, // Send to any valid number
                $mens,
                \Esendex\Model\Message::SmsType
            );
            $authentication = new \Esendex\Authentication\LoginAuthentication(
                "EX0304912", // Your Esendex Account Reference
                "serv.tecnico@bidaiondo.com", // Your login email address
                "Bidaiondo#50" // Your password
            );
            $service = new \Esendex\DispatchService($authentication);
            $result = $service->send($message);
            
            if (strlen($result->id()) > 5) {
                error_log("mensaje enviado a $telefono");
            } else return "Hubo error en el envío de los SMS";
        }
        error_log("SMS enviado");
        return true;
    }

	/**
	 * Alertas de Seguridad.
	 * Es una función que sólo envía alertas no bloquean operaciones
	 * 
	 * @return boolean
	 */
	function alerSegur() {
		$this->db("select a.nombre from tbl_admin a, tbl_reserva r where r.id_admin = a.idadmin and r.codigo = '".$this->tran."' and r.id_comercio = '".$this->comer."'");
		($this->temp->num_rows() == 0) ? $admin = '' : $admin = $this->temp->f('nombre');
		$this->db("select moneda from tbl_moneda where idmoneda = '".$this->mon."'");
		($this->temp->num_rows() == 0) ? $mone = '' : $mone = $this->temp->f('moneda');
		$mensage = "";
		$this->log = "\n<br>Verifica que el monto de la operación no está por encima del máximo de peligro\n<br>";
		if ($this->imp >= leeSetup('montoAlerta') * 100) {
			$mensage .= "Se est&aacute; realizando una transacci&oacute;n por un monto de " . number_format(($this->imp / 100), 2, '.', ' ');
			$mensage .= " $mone correspondiente al comercio: " . $this->datCom['nombre']. " impuesto por el Usuario: ".$admin;
			$mensage .= "\n<br />Fecha - Hora: " . date('d') . "/" . date('m') . "/" . date('Y') . " " . date('H') . ":" . date('i') . ":" . date('s');
			// $mensage .= "\n<br /><br />";
			$this->corr->todo(10, "Alerta de vigilancia antifraude", $mensage);
			$this->log .= $mensage;
			$mensage = str_replace("&oacute;","o",str_replace("&aacute;","a",str_replace("<br />", "", $mensage)));
			if (!envioSMS(2,$mensage)) error_log("Error en el envío de los sms de límites");
		}

		return true;
	}

	/**
	 * Determina si la IP desde la que se está realizando el pago
	 * está en el listado de las IPs blancas
	 * 
	 * @return boolean
	 */
	private function ipblanca() {
		$this->db(sprintf("select id from tbl_ipblancas where ip='%s'", $this->ip));

		if ($this->temp->num_rows() == 0) {
			return false;
		} else {
			$this->db(sprintf("update tbl_ipblancas set fecha = ".time()." where ip='%s'", $this->ip));
			return true;
		}
	}

	/**
	 * Cambio de pasarelas según la moneda
	 * 
	 * @return boolean
	 */
	private function cambPasporMon() {
		$this->log .= "<br>\nRevisa si hay cambio de pasarela por moneda<br>\n";
		$npas = 0;
		// Euros de Soy Cubano por IDirect
		// if($this->pasa === 36 && $this->mon === '978' && $this->comer === '411691546810') {$npas = 42;}
		// Todo el EUR de Cubana lo voy rotando entre las pasarelas, la rotación de los USD se cambia en la tbl_rotComPas
		$this->db("select count(*) total from tbl_rotPasarOperac r, tbl_comercio c where c.id = r.idcomercio and r.activo = 1 and c.idcomercio = '".$this->comer."' and r.idmoneda = '".$this->mon."'");
		$cantPas = $this->temp->f('total');
		if ($cantPas > 0) {
			$this->log .= "<br>\nCambia pasarela por número de operaciones y moneda<br>\n";
			$this->db("select pasarela from tbl_transacciones where moneda = '".$this->mon."' and idcomercio = '".$this->comer."' order by fecha desc limit 0,1");
			$ultpasUsa = $this->temp->f('pasarela');
			$this->log .= "Ultima pasarela usada: $ultpasUsa<br>\n";

			$this->db("select orden, cantOperac from tbl_rotPasarOperac r, tbl_comercio c where c.id = r.idcomercio and c.idcomercio = '".$this->comer."' and r.idmoneda = '".$this->mon."' and idpasarela = $ultpasUsa");
			$ultOrden = $this->temp->f('orden');
			$cantOper = $this->temp->f('cantOperac');
			$this->log .= "Orden de la ultima pasarela usada: $ultOrden<br>\n";
			$this->log .= "Cantidad de operaciones de la ultima pasarela usada: $cantOper<br>\n";

			$this->log .= "VERIFICA -".strlen($ultOrden)."-".($ultOrden > 0)."-<br>\n";
			if (strlen($ultOrden) > 0 && $ultOrden > 0) {
				$this->log .= "Entra aca<br>";
				// rechequeo de la ultima pasarela usada en base a la cantidad de operaciones
				$this->db("select pasarela from tbl_transacciones where moneda = '".$this->mon."' and idcomercio = '".$this->comer."' order by fecha desc limit 0,$cantOper");
				// $arrVals = $this->temp->loadAssocList();
				$arrVals = $this->temp->loadResultArray();
				//chequeo si todos los valores del array son iguales
				if (count(array_unique($arrVals)) !== 1) {//hay mas de una pasarela por lo que no ha terminado con la $ultpasUsa
					$npas = $ultpasUsa;
					$this->log .= "Debe continuar por la ultima pasarela: $ultpasUsa<br>\n";
				} else {
					$this->log .= "hace cambio de pasarela pasarela<br>\n";
					//toca hacer el cambio de pasarela

					while ($npas == 0) {
						if ($ultOrden == $cantPas) {
							$ultOrden = 1;
						} elseif ($ultOrden > $cantPas) {
							$ultOrden = $cantPas;
						} else {
							$ultOrden++;
						}

						$this->db("select idpasarela from tbl_rotPasarOperac r, tbl_comercio c where c.id = r.idcomercio and r.activo = 1 and c.idcomercio = '".$this->comer."' and r.idmoneda = '".$this->mon."' and orden = $ultOrden");
						$npas = $this->temp->f('idpasarela');
					}
				}
			} else {
				$this->log .= "Entra acullá<br>";
				for ($i = 1; $i<$cantPas; $i++) {
					$q = "select idpasarela from tbl_rotPasarOperac r, tbl_comercio c where c.id = r.idcomercio and c.idcomercio = '".$this->comer."' and r.idmoneda = '".$this->mon."' and orden = $i limit 0,1";
					$this->db($q);
					if ($this->temp->num_rows() > 0) {
						$npas = $this->temp->f('idpasarela');
						break;
					}
				}
			}
		}

		// si se cambia aviso y cambio
		if ($npas > 0) {
			$this->log .= "Si, cambia de la pasarela " . $this->pasa . " a la pasarela " . $npas . "\n<br>";
			$this->pasa = $npas;
		}
		return true;
	}

	/**
	 * Ejecuta las querys y muestra errores si los hay
	 * 
	 * @param string $q        	
	 * @return boolean
	 */
	private function db($q) {
		$this->log .= $q . "\n<br>";
		$this->temp->query($q);
		if ($this->temp->getErrorMsg()) {
			$this->log .= $this->temp->getErrorMsg() . "\n<br>";
			$this->err = "Se produjo un error no especificado<br>Contacte con su comercio.";
			return false;
		}
		return true;
	}

	/**
	 * Elabora la cadena de envío al TPV de prueba
	 * 
	 * @return boolean
	 */
	private function CAprueba() {
		$this->log .= "\n<br>Entra al Centro Autorizador\n<br>";
		$forma = '';
		$est = "hidden";
		if (_MOS_CONFIG_DEBUG) {
			$est = "text";
		}
		$arrval = ( array )json_decode($this->datPas['datos'], true);

		if ($this->opr == 'D') {
			$this->log .= "entra en xml<br>";
			$this->datPas['tipo'] = 'xml';
			// echo $this->datPas['tipo'];
			$forma .= '<form name="envia" action=";urlPasarela;" method="post">';
			$forma .= "<input type=\"$est\" name=\"entrada\" value=\"";
			$forma .= "<DATOSENTRADA><DS_Version>0.1</DS_Version>";
			// print_r($arrval);
			foreach ($arrval as $key => $value) {
				$forma .= "<" . strtoupper($key) . ">\n$value\n</" . strtoupper($key) . ">\n";
			}
		} else {
            if ($this->datPas['tipo'] == 'get') {
				$this->log .= "entra en get<br>";

                $forma .= '<form name="envia" action=";urlPasarela;" method="get">';
                $this->db("select concat(a.url, '/paid.php') url from tbl_agencias a, tbl_pasarela p where p.idagencia = a.id and p.idPasarela = " . $this->pasa);
                $this->datPas['url'] = $this->temp->f('url');

                $this->log .= "url=".$this->datPas['url']."\n<br>";

                foreach ($arrval as $key => $value) {
                    if ($this->datPas['datPas'] != 'pasoA@firmaX') {
                        $forma .= "<input type=\"$est\" name=\"$key\" value=\"$value\"/>\n";
                    } else {
                        $forma .= "$key=$value" . '¿';
                    }
                }

            } elseif ($this->datPas['tipo'] == 'form' || $this->datPas['tipo'] == 'melt') {
				$this->log .= "entra en form<br>";
				$forma .= '<form name="envia" action=";urlPasarela;" method="post">';
				if ($this->datPas['tipo'] == 'melt') {
					$this->db("select concat(a.url, '/paid.php') url from tbl_agencias a, tbl_pasarela p where p.idagencia = a.id and p.idPasarela = " . $this->pasa);
					$this->datPas['url'] = $this->temp->f('url');
				}
				$this->log .= "url=".$this->datPas['url']."\n<br>";

				foreach ($arrval as $key => $value) {
					if ($this->datPas['datPas'] != 'pasoA@firmaX') {
						$forma .= "<input type=\"$est\" name=\"$key\" value=\"$value\"/>\n";
					} else {
						$forma .= "$key=$value" . '¿';
					}
				}
			} elseif ($this->datPas['tipo'] == 'iframe') { // echo "entra";
				$this->log .= "entra en iframe<br>";
				// $i=0;
				$forma .= '<script src="js/jquery.js" type="text/javascript"></script><script language=\'javascript\'>$("#avisoIn").html(\'<iframe ' . 'title="titulo" src=";urlPasarela;?';
				if ($this->pasa != 39) {
					foreach ($arrval as $key => $value) {
						// echo $i++."<br>";
						$forma .= $key . '=' . $value . '&';
					}
				}
				$forma = rtrim($forma, '&');
			} elseif ($this->datPas['tipo'] == 'curl') {
				$this->log .= "entra en curl<br>";
				$forma = $this->datPas['datos'];
			}
		}

		if (!$preform = $this->cambVals($forma)) return false;
		//echo $preform;

		if ($this->datPas['tipo'] == 'melt') {
			$preform .= '<input type="hidden" name="id" value="' . $this->idTrn . '" />';
		}

		//error_log("preform=".$preform);
		$forma = $preform . $this->finForm();
		error_log("FORMA=".$forma);

		return $forma;
	}

	/**
	 * Finaliza el formulario de envío
	 * @return string
	 */
	private function finForm() {
		$sale = "";
		if ($this->datPas['tipo'] == 'form' || $this->datPas['tipo'] == 'melt' || $this->datPas['tipo'] == 'xml' || $this->datPas['tipo'] == 'get') {
			// echo $this->datPas['tipo'];
			if ($this->datPas['tipo'] == 'xml')
				$sale .= '</DATOSENTRADA>"/>';
			if (_MOS_CONFIG_DEBUG) {
				$sale .= '<input type="submit" value="Enviar" /></form>';
			} else {
				$sale .= '</form><script language=\'javascript\'>document.envia.submit();</script>';
			}
		} elseif ($this->datPas['tipo'] == 'iframe') {
			if ($this->pasa == 39)
				$sale .= '" width="100%" height="500" frameborder="0" marginheight="0" marginwidth="0" scrolling="no"></iframe>\');</script>';
			else
				$sale .= '" width="400" height="374" frameborder="0" marginheight="0" marginwidth="0" scrolling="no" style="border: 1px solid #000000; ' . 'padding:0; margin:0"></iframe>\');</script>';
		}

		return $sale;
	}

	/**
	 * Sustituye los valores de las variables a enviar
	 * @param string $cad
	 * @return boolean|mixed|string
	 */
	private function cambVals($cad) {
		$this->log .= "Sustituye valores en la cadena\n<br>";
		$this->log .= "Pasarela de envío: {$this->pasa} - {$this->datPas['nombre']}\n<br>";
        //$this->saltosPasar("Transacci&oacute;n enviada al banco");
		$imp = $this->imp;
		$tr = $this->idTrn;

		//factor de mult para pesos chilenos y Yenes
		$this->db("select factmult from tbl_moneda where idmoneda = '{$this->mon}'");
		$imp = $imp / $this->temp->f('factmult');
		//if ($this->mon === '392') {
		//$imp = $imp / 100;
		//}			
		error_log("TIOP=" . $this->datPas['tipo']);
		if ($this->datPas['tipo'] == 'melt') {
			$urlB = str_replace("paid.php", "", $this->datPas['url']);
			$urlcomercio = $this->urlcomercio		= str_replace("paid.php", "llegada.php", $this->datPas['url']);
			// if (strlen($id) < 10) 
				$id = $tr;
			$urldirOK = $this->urldirOK		= str_replace("paid.php", "", $this->datPas['url']) . "ver.php?resp=$id" . '&est=ok';
			$urldirKO = $this->urldirKO		= str_replace("paid.php", "", $this->datPas['url']) . "ver.php?resp=$id" . '&est=ko';
			$urlOri = $this->datPas['url'];
		} else {
			$urlcomercio = $this->urlcomercio	= _URL_COMERCIO;
			$urldirOK = $this->urldirOK		= _URL_DIR . "index.php?resp=$tr" . '&est=ok';
			$urldirKO = $this->urldirKO		= _URL_DIR . "index.php?resp=$tr" . '&est=ko';
			$urlOri = _URL_DIR;
		}
		$variantes = explode(',', $this->datPas['variant']);
		$encriptacion = 'SHA1';
		$idioma = $this->idi;
		$producto = 'Servicio Turistico';
		$titular = 'Nombre';

		$this->db("select nombre, servicio from tbl_reserva where codigo = '{$this->tran}' and id_comercio = '{$this->comer}'");
		if (!$this->temp->num_rows() == 0) {
			//$producto = $this->temp->f ( 'servicio' );
			$titular = $this->temp->f('nombre');
		}

		if (strpos($this->datPas['datPas'], '@')) {
			$arrPas = explode('@', $this->datPas['datPas']);
			$this->log .= "entra en ".$arrPas[0]."@\n<br>";

			switch ($arrPas[0]) {
				case 'pasoA': //Redsys
					($idioma == 'es') ? $idioma = '1' : $idioma = '2';
					if ($this->opr == 'D') $tipoTrans = '3'; else $tipoTrans = '0';
					$message = $imp . $tr . $this->datPas['comercio'] . $this->mon . $tipoTrans . $urlcomercio . $this->datPas['clave'];

					// if (isset($this->datAis['idremitente']) && $this->datAis['idremitente'] > 10) { //Para la pasarela de Redsys que sacó Titanes
					// 	$this->db("select idtitanes, fechaDocumento from tbl_aisCliente where idcimex = " . $this->datAis['idremitente']);
					// 	if (!$this->temp->f('idtitanes') > 0) {
					// 		$this->err = "El cliente no existe en la Base de datos";
					// 		return false;
					// 	}
					// 	if ($this->temp->f('fechaDocumento') < time()) {
					// 		$this->err = "Ha caducado el documento de identificaci&oacute;n deber&aacute; renovarlo";
					// 		return false;
					// 	}
					// 	$this->db("select id from tbl_aisBeneficiario where idcimex = '" . $this->datAis['iddestin'] . "'");
					// 	if ($this->temp->num_rows() == 0) {
					// 		$this->err = "El beneficiario no existe en la Base de datos";
					// 		return false;
					// 	}
					// 	$this->db("insert into tbl_aisOrden (idtransaccion, idcliente, idbeneficiario, envia, recibe, comision, subida, idrazon, monedaRecibe) values ('$tr',
					// 				(select id from tbl_aisCliente where idcimex = '" . $this->datAis['idremitente'] . "' order by id desc limit 0,1),
					// 				(select id from tbl_aisBeneficiario where idcimex = '" . $this->datAis['iddestin'] . "' order by id desc limit 0,1),
					// 				{$this->datAis['importenvia']}, {$this->datAis['importerecive']}, {$this->datAis['comision']}, 0, {$this->datAis['rason']}, '{$this->datAis['monrecibe']}')");
					// }
					break;
				case 'pasoAN': //Redsys con AFT
					($idioma == 'es') ? $idioma = '1' : $idioma = '2';
					if ($this->opr == 'D') $tipoTrans = '3'; else $tipoTrans = '0';
					$message = $imp . $tr . $this->datPas['comercio'] . $this->mon . $tipoTrans . $urlcomercio . $this->datPas['clave'];

					if($tipoTrans == '0'){
						$this->db("select id, nombre from tbl_aisCliente where idcimex = '" . $this->datAis['idremitente'] . "'");
						if ($this->temp->num_rows() == 0) {
							$this->err = "El cliente no existe en la Base de datos";
							$this->saltosPasar($this->err);
							return false;
						} else{
							$sendName = $this->temp->f('nombre');
							if(strlen($sendName) > 30){
								$sendName = substr($sendName, 0, 30);
							}
						}
						$this->db("select id, nombre, papellido, pais, direccion, ciudad from tbl_aisBeneficiario where idcimex = '" . $this->datAis['iddestin'] . "'");
						if ($this->temp->num_rows() == 0) {
							$this->err = "El beneficiario no existe en la Base de datos";
							$this->saltosPasar($this->err);
							return false;
						} else{
							$rcvName = $this->temp->f('nombre');
							if(strlen($rcvName) > 30){
								$rcvName = substr($rcvName, 0, 30);
							}
							$rcvLastName = $this->temp->f('papellido');
							if(strlen($rcvLastName) > 35){
								$rcvLastName = substr($rcvLastName, 0, 35);
							}
							$rcvAddress = $this->temp->f('direccion');
							if(strlen($rcvAddress) > 35){
								$rcvAddress = substr($rcvAddress, 0, 35);
							}
							$rcvCity = $this->temp->f('ciudad');
							if(strlen($rcvCity) > 25){
								$rcvCity = substr($rcvCity, 0, 25);
							}
							$this->db("select iso from tbl_paises where id = '" . $this->temp->f('pais') . "'");
							if ($this->temp->num_rows() == 0) {
								$this->err = "El pais del beneficiario no existe en la Base de datos";
								$this->saltosPasar($this->err);
								return false;
							} else{
								$rcvCountry = $this->temp->f('iso');
							}
						}
						$this->db("select b.ica from tbl_bancos b, tbl_pasarela p where b.id = p.idbanco and p.idpasarela = '" . $this->pasa . "'");
						if ($this->temp->num_rows() == 0) {
							$ica = str_repeat('0', 6);
						} else{
							$ica = $this->temp->f('ica');
						}
						$year = date('y');
						$julDate = date("z");
                        if(strlen($julDate) < 3){
                            $julDate = str_pad($julDate,3,"0",STR_PAD_LEFT);
                        }
						$time = date('His');
						$sec = rand(1,99);
						if(strlen($sec) < 2){
							$sec = str_pad($sec,2,"0",STR_PAD_LEFT);
						}
						$utr = '0'.$ica.substr($year, 1, 1).$julDate.$time.$sec;

						$aft = json_encode(
							array(
								"RcvFirstName"  => $rcvName,
								"RcvLastName"   => $rcvLastName,
								"RcvCountry"    => $rcvCountry,
								"RcvAddress"    => $rcvAddress,
								"RcvCity"       => $rcvCity,
								"RcvAccountNumber"  => $this->datAis['accountNumber'],
								"RcvAccountNumberType"  => $this->datAis['accountNumberType'],
								"UTR"           => $utr,
								"BAI"           => "AA",    // Account to account
								"SndName"       => $sendName
							)
						);
					}
					break;
				case 'pasoB': // Tefpay
					$this->db("select id from tbl_aisCliente where idcimex = '" . $this->datAis['idremitente'] . "'");
					if ($this->temp->num_rows() == 0) {
						$this->err = "El cliente no existe en la Base de datos";
						$this->saltosPasar($this->err);
						return false;
					}
					$this->db("select id from tbl_aisBeneficiario where idcimex = '" . $this->datAis['iddestin'] . "'");
					if ($this->temp->num_rows() == 0) {
						$this->err = "El beneficiario no existe en la Base de datos";
						$this->saltosPasar($this->err);
						return false;
					}

					$this->db("insert into tbl_aisOrden (idtransaccion, idcliente, idbeneficiario, envia, recibe, comision, subida, idrazon, monedaRecibe) values ('$tr',
								(select id from tbl_aisCliente where idcimex = '" . $this->datAis['idremitente'] . "' order by id desc limit 0,1),
								(select id from tbl_aisBeneficiario where idcimex = '" . $this->datAis['iddestin'] . "' order by id desc limit 0,1),
								{$this->datAis['importenvia']}, {$this->datAis['importerecive']}, {$this->datAis['comision']}, 0, {$this->datAis['rason']}, '{$this->datAis['monrecibe']}')
								");
					$tr .= '000000000';
					// $tipoTrans = 1;
					$tipoTrans = 46;
					$paisbenef = 'CU';
					$monedacuc = 'CUC';
					$delivery = '4';
					$orderef = 'A6969696';
					$corresp = 'T086';
					$subcorresp = '1';
					$branch = 'T0860001';
					$tipocta = '3';
					$this->db("select idtitanes, fechaDocumento from tbl_aisCliente where idcimex = " . $this->datAis['idremitente']);
					if (!$this->temp->f('idtitanes') > 0) {
						$this->err = "El cliente no se ha inscrito";
						$this->saltosPasar($this->err);
						return false;
					}
					if ($this->temp->f('fechaDocumento') < time()) {
						$this->err = "Ha caducado el documento de identificaci&oacute;n deber&aacute; renovarlo";
						$this->saltosPasar($this->err);
						return false;
					}
					$idremitente = $this->temp->f('idtitanes');
					$this->db("select ciudad, idtitanes from tbl_aisBeneficiario where idcimex = " . $this->datAis['iddestin']);
					if (!$this->temp->f('idtitanes') > 0) {
						$this->err = "El beneficiario no se ha inscrito";
						$this->saltosPasar($this->err);
						return false;
					}
					$citybenef = $this->temp->f('ciudad');
					$idbenef = $this->temp->f('idtitanes');
					$rason = $this->datAis['rason'];
					$this->db("select razon from tbl_aisRazones where id = $rason");
					$mdescr = $this->temp->f('razon');
					$message = $imp . $this->datPas['comercio'] . $tr . $urlcomercio . $this->datPas['clave'];
					break;
				case 'pasoC':
					$message = $this->datPas['clave'] . $this->datPas['comercio'] . $tr . $this->imp . $this->mon . $encriptacion . $urldirOK . $urldirKO;
					break;
				case 'pasoD':
					$ssl = 'SSL';
					$encriptacion = 'SHA2';
					($idioma == 'es') ? $idioma = '1' : $idioma = '6';
					$message = $this->datPas['clave'] . $this->datPas['comercio'] . $variantes[0] . $this->datPas['terminal'] . $tr . $imp . $this->mon . $variantes[1] . $encriptacion . $urldirOK . $urldirKO;
					$this->log .= $this->datPas['clave'] . " . " . $this->datPas['comercio'] . " . " . $variantes[0] . " . " . $this->datPas['terminal'] . " . " . $tr . " . " . $imp . " . " . $this->mon . " . " . $variantes[1] . " . " . $encriptacion . " . " . $urldirOK . " . " . $urldirKO;
					break;
				case 'pasoE': // paytpv viejo
					$operation = 1;
					$this->mon = $this->datPas['moneda'];
					$message = $this->datPas['comercio'] . $this->datPas['clave'] . $this->datPas['terminal'] . $operation . $tr . $imp . $this->mon . md5("{$this->datPas['variant']}");
					$this->log .= "usuario=" . $this->datPas['variant'] . "<br>";
					$this->log .= "{$this->datPas['comercio']} . {$this->datPas['clave']} . {$this->datPas['terminal']} . $operation . $tr . $imp .
					{$this->mon} . md5(" . $this->datPas['variant'] . ")<br>";
					break;
				case 'pasoF':
					$referencia = "M$this->mon" . "$this->imp\r\n1\r\n$tr\r\n$producto\r\n1\r\n$imp\r\n";
					break;
				case 'pasoG':
					($idioma == 'es') ? $idioma = '001' : $idioma = '002';
					$tipoTrans = '0';
					$message = $imp . $tr . $this->datPas['comercio'] . $this->mon . $tipoTrans . $this->datPas['clave'];
					break;
				case 'pasoH': // SIPAY
					$data = array(
						"username" => $variantes[0],
						"password" => $variantes[1],
						"apikey" => $variantes[2],
						"module" => $variantes[3],
						"authtype" => $variantes[4],
						"lang" => $variantes[5],
						"merchantid" => $variantes[6],
						"ticket" => $tr,
						"amount" => complLargo($imp),
						"currency" => $this->mon,
						"css_url" => _ESTA_URL . $variantes[7],
						"dstpageid" => $this->datPas['comercio']
					);

					$options = array(
						CURLOPT_RETURNTRANSFER => true,
						CURLOPT_SSL_VERIFYPEER => false,
						CURLOPT_POST => true,
						CURLOPT_VERBOSE => true,
						CURLOPT_URL => $this->datPas['url'],
						CURLOPT_SSLCERT => $variantes[8],
						CURLOPT_SSLKEY => $variantes[9],
						CURLOPT_POSTFIELDS => json_encode($data),
						CURLOPT_SSL_VERIFYHOST => false
					);

					$ch = curl_init();
					curl_setopt_array($ch, $options);
					$salida = curl_exec($ch);
					// echo "error=".curl_errno($ch);
					if (curl_errno($ch))
						$this->log .= "Error en la resp de Sipay:" . curl_strerror(curl_errno($ch)) . "<br>\n";
					$crlerror = curl_error($ch);
					// echo "otroerror=".$crlerror;
					if ($crlerror) {
						$this->err .= "Error en la resp de Sipay:" . $crlerror . "<br>\n";
						$this->saltosPasar($this->err);
					}
					$curl_info = curl_getinfo($ch);
					curl_close($ch);
					// print_r($curl_info);echo "<br><br>";

					$arrCurl = json_decode($salida);
					$this->log .= "Datos de Sipay:<br>\n";
					foreach ($arrCurl as $key => $value) {
						$this->log .= $key . " = " . $value . "<br>\n";
					}
					// print_r($arrCurl);

					if ($arrCurl->idrequest) {
						$q = "insert into tbl_dataSipay (idtransaccion, idrequest, merchantid) values ('$tr','" . $arrCurl->idrequest . "','773')";
						$this->db($q);
						return str_replace(';urlPasarela;?', $arrCurl->iframe_src, $cad);
					}

					break;
				case 'pasoI':
					$imp = $imp / 100;
					$urlLogo = _ESTA_URL . "/admin/logos/blanco.jpg"; // . $this->comer .".jpg";
					$shopId = '';
					$urlser = _ESTA_URL . '/pagServ.php?cod=' . $this->tran . '&com=' . $this->comer;
					$producto = "Order: " . $this->tran . " - " . $producto;
					$duplCheck = 'no';
					if ($idioma == 'es')
						$displayText = "Gracias por escoger nuestros servicios.";
					else
						$displayText = "Thank you very much for choosing our services.";
					$shopname_customParameter1 = $tr;
					$shopname_customParameter2 = "shopname_customParameter2";
					$tipoTrans = 'CCARD';
					$fgprtord = "customerId,shopId,amount,currency,orderDescription,customerStatement,orderReference,duplicateRequestCheck," . "successUrl,cancelUrl,failureUrl,pendingUrl,serviceUrl,confirmUrl,language,displayText,imageUrl,shopname_customParameter1," . "shopname_customParameter2,requestFingerprintOrder,secret";
					$ret = $this->datPas['comercio'] . $shopId . $imp . $this->mon . $producto . $titular . $tr . $duplCheck . $urldirOK . $urldirKO . $urldirKO . $urldirKO . $urlser . $urlcomercio . $idioma . $displayText . $urlLogo . $shopname_customParameter1 . $shopname_customParameter2 . $fgprtord . $this->datPas['clave'];
					break;
				case 'pasoJ': //PayTPV
					$operation = 1;
					$this->mon = $this->datPas['moneda'];
					$message = $this->datPas['comercio'] . $this->datPas['terminal'] . $operation . $tr . $imp . $this->mon . md5("{$this->datPas['clave']}");
					$this->log .= "usuario=" . $this->datPas['variant'] . "<br>";
					$this->log .= "{$this->datPas['comercio']} . {$this->datPas['terminal']} . $operation . $tr . $imp . {$this->mon} .
						md5(" . $this->datPas['clave'] . ")<br>";
					$urldirOK = "ver.php?resp=$tr" . '&est=ok';
					$urldirKO = "ver.php?resp=$tr" . '&est=ko';

					break;
				case 'pasoK': // Tefpay
					$tr .= '000000000';

					if($this->datPas['idTarj'] == 17){  // Criptomoneda
						$tipoTrans = 84;
						$this->db("select moneda from tbl_moneda where idmoneda = '{$this->receiveCurrency}'");
						$receiveCurrency = $this->temp->f('moneda');
					} else{
						if ($this->segura == 1)	$tipoTrans = 1;
						else {
							if ($this->pasa == 89 || $this->pasa == 161) {
								$tipoTrans = 27; //sólo iberoTef2 e iberoTef2 TIENDAS
							} else $tipoTrans = 22; //para el resto de los Tef
						}
					}

					/* Reina - se comenta se comenta porque se identifica por el medio de pago 17
					if ($this->segura == 1)	$tipoTrans = 1;
					else if ($this->pasa == 194) { //criptomonedas
						$tipoTrans = 84;
						$this->db("select moneda from tbl_moneda where idmoneda = '{$this->receiveCurrency}'");
						$receiveCurrency = $this->temp->f('moneda');

					} else {
						if ($this->pasa == 89 || $this->pasa == 161) {
							$tipoTrans = 27; //sólo iberoTef2 e iberoTef2 TIENDAS
						} else $tipoTrans = 22; //para el resto de los Tef
					}*/
					if ($this->opr == 'A') $tipoTrans = 2;
					
					$this->log .= "tipo operacion: ".$this->opr."<br>tipoTrans:$tipoTrans<br>";
					//$tipoTrans = 22;
					$message = $imp . $this->datPas['comercio'] . $tr . $urlcomercio . $this->datPas['clave'];
					break;
                case 'pasoKN': // Tefpay REST
                    $tr .= '000000000';
//					if ($this->segura == 1)	$tipoTrans = 1; Reina - cambios a REST

                    // Reina - Busco el id de la pasarela original
                    $pasarelaOriginal = 0;
                    $this->db("select pasarLim from tbl_pasarela where idPasarela = {$this->pasa}");
                    if ($this->temp->num_rows() > 0) {
                        $pasarelaOriginal = $this->temp->f('pasarLim');
                    }
                    if($pasarelaOriginal > 0 && $pasarelaOriginal === 225){  // KUTXABANKTEF 3D
                        $tipoTrans = 201;
                        $termAuth = $this->datPas['terminal'];
                    } else{
	                    if($this->datPas['idTarj'] == 17){  // Criptomoneda
		                    $tipoTrans = 84;
		                    $this->db("select moneda from tbl_moneda where idmoneda = '{$this->receiveCurrency}'");
		                    $receiveCurrency = $this->temp->f('moneda');
	                    } else {
		                    if ($this->segura == 1){
			                    $tipoTrans = 201;
			                    $termAuth = $this->datPas['terminal'];
		                    } else {
			                    if ($this->pasa == 89 || $this->pasa == 161) {
				                    $tipoTrans = 27; //sólo iberoTef2 e iberoTef2 TIENDAS
			                    } else $tipoTrans = 22; //para el resto de los Tef
		                    }
	                    }

                        /* Reina - se comenta porque se identifica por el medio de pago 17
                        if ($this->segura == 1){
			                    $tipoTrans = 201;
			                    $termAuth = $this->datPas['terminal'];
		                } else if ($this->pasa == 194) { //criptomonedas
                            $tipoTrans = 84;
                        } else {
                            if ($this->pasa == 89 || $this->pasa == 161) {
                                $tipoTrans = 27; //sólo iberoTef2 e iberoTef2 TIENDAS
                            } else $tipoTrans = 22; //para el resto de los Tef
                        } */
                        if ($this->opr == 'A') $tipoTrans = 2;
                    }

                    $this->log .= "tipo operacion: ".$this->opr."<br>tipoTrans:$tipoTrans<br>\n";
                    //$tipoTrans = 22;
                    $message = $imp . $this->datPas['comercio'] . $tr . $urlcomercio . $this->datPas['clave'];
                    break;
				case 'pasoL': //Xilema
					$imp = $imp / 100;

					$this->db('select count(*) total from tbl_colTarjPasar where idTarj = 16 and idPasar = '.$this->pasa);
					if ($this->temp->f('total') > 0) {
						$interfase = 'paypal';
						$style = 'bidaiondo_paypal';
					} else {
						$interfase = 'form';
						$style = 'bidaiondo_paypal_form';
					}

					if ($this->opr == 'A') $this->datPas['url'] .= "startPreauthorization";
					if ($this->opr == 'P') $this->datPas['url'] .= "startPayment";

                    $this->db("select moneda from tbl_moneda where idmoneda = '{$this->mon}'");
                    $monedaStr = $this->temp->f('moneda');
					
					$this->log .= "URL= ".$this->datPas['url']."<br>";
					($this->datPas['secure'] == 1) ? $xSqre = true : $xSqre = false;
					$json = json_encode(
						array(
							"merchant" => array(
								"clientId"			=> $this->datPas['comercio'],
								"clientSecret"		=> $this->datPas['clave']
							),
							"trx" => array(
								"reference"			=> $tr,
								"source"			=> 'eCommerce',
								"datetime"			=> date(DATE_ISO8601),
								"amount"			=> number_format($imp, 2, '.', ''),
								"allow3DSecure"		=> $xSqre,
								"paypalInterface"	=> $interfase,
								"currency" => array(
									"numCode"		=> $this->mon,
									"alphaCode"		=> "$monedaStr"
								)
							),
							"additionals" => array(
								"configuration" => array(
									"language"		=> $idioma,
									"styleName"		=> $style,
									"callbackUrl"	=> $urlcomercio,
									"successUrl"	=> $urldirOK,
									"errorUrl"		=> $urldirKO,
									"cancelUrl"		=> $urldirKO
								)
							)
						)
					);
					$this->log .= $json . "\n<br>";
					$options = array(
						CURLOPT_RETURNTRANSFER	=> true,
						CURLOPT_SSL_VERIFYPEER	=> false,
						CURLOPT_SSL_VERIFYHOST  => false,
						CURLOPT_POST			=> true,
						CURLOPT_VERBOSE			=> true,
						//CURLOPT_PROXY			=> '127.0.0.1',
						//CURLOPT_PROXYPORT		=> '23462',
						//CURLOPT_PROXYTYPE		=> CURLPROXY_SOCKS5,
						CURLOPT_FAILONERROR		=> true,
						CURLOPT_URL				=> $this->datPas['url'],
						CURLOPT_POSTFIELDS		=> $json,
						CURLOPT_CUSTOMREQUEST	=> 'POST',
						CURLOPT_HTTPHEADER		=> array(
							'Content-Type: application/json',
							'Content-Length: ' . strlen($json)
						)
					);
					$ch = curl_init();
					curl_setopt_array($ch, $options);
					$output = curl_exec($ch);
					if (curl_error($ch)) $this->log .= "Error en la resp de Xilema:" . curl_error($ch) . "<br>\nurl de envío: " . $this->datPas['url'];

					$curl_info = curl_getinfo($ch);
					curl_close($ch);
					$arrayXil = json_decode($output);
					$this->log .= "<br><br>salida=$output<br><br>";

					break;
				case 'pasoM': // Papam
					$source = $urldirKO;
					$idCommande = $abonnement =	$porteur = $codeTemplate = $panier = '';
					$this->mon = strtoupper($this->datPas['moneda']);
					$tipoTrans = "D";
//					$urldirOK = "https://www.administracomercios.com/retour.php?estado=ok";
//					$urldirKO = "https://www.administracomercios.com/retour.php?estado=ko";
//					$urlcomercio = "https://www.administracomercios.com/ipn.php";
					$imp = $imp/100;

					$cadenita = "$imp|{$this->datPas['comercio']}|$tr|$idCommande|{$this->mon}|$idioma|$producto|$source|$urldirOK|$urldirKO|$tipoTrans|$tr|".base64_encode(json_encode($porteur))."|".base64_encode(json_encode($abonnement))."|$codeTemplate|$panier|$urlcomercio|{$this->datPas['clave']}";
					$this->log .= "<br>$cadenita<br>";
					$message = base64_encode($cadenita);
				break;
				case 'pasoN': //BiPay
					$tipoTrans = 0;// compra
					$message = $imp . $tr . $this->datPas['comercio'] . $this->mon . $this->segura . $this->datPas['clave'];
				break;
				case 'pasoO': //Sabadell Marca blanca
					$tipoTrans = 1;// compra
                    $this->db("select moneda from tbl_moneda where idmoneda = '{$this->mon}'");
                    $this->mon = $this->temp->f('moneda');
					$message = $this->datPas['comercio'] . $this->datPas['terminal'] . $tipoTrans . $tr . $imp  . $this->mon . md5($this->datPas['clave']);
				break;
				case 'pasoP': //Stripe
                    $this->db("select moneda from tbl_moneda where idmoneda = '{$this->mon}'");
                    $this->mon = strtolower($this->temp->f('moneda'));
					$message = $tr . $this->mon . $imp . $this->datPas['clave'];
				break;
				case 'pasoQ': //Moneytigo
					$imp = $imp / 100;

                    $this->db("select moneda from tbl_moneda where idmoneda = '{$this->mon}'");
                    $monedaStr = $this->temp->f('moneda');
					
					$this->log .= "URL= ".$this->datPas['url']."<br>";

					// $this->db("select concat(a.url, '/paid.php') url from tbl_agencias a, tbl_pasarela p where p.idagencia = a.id and p.idPasarela = " . $this->pasa);
					// $agenciaurl = $this->temp->f('url');

					$urlcom		= str_replace("paid.php", "llegada.php", $agenciaurl);
					$urlok		= str_replace("paid.php", "", $agenciaurl) . "ver.php?resp=$id" . '&est=ok';
					$urlko		= str_replace("paid.php", "", $agenciaurl) . "ver.php?resp=$id" . '&est=ko';

					($this->datPas['secure'] == 1) ? $xSqre = true : $xSqre = false;
					$params = array(
							"MerchantKey"			=> $this->datPas['comercio'],
							"amount"				=> number_format($imp, 2, '.', ''),
							"RefOrder"				=> $tr,
							"Customer_Email"		=> $this->email,
							"Customer_Name"			=> $this->apell,
							"Customer_FirstName"	=> $this->nomb,
							"lang"					=> $idioma,
							"urlIPN"				=> $urlcom,
							"urlOK"					=> $urlok,
							"urlKO"					=> $urlko
					);

					foreach ($params as $key => $value) {
						$beforesign .= $value."!";
					}

					$beforesign .= $this->datPas['clave'];
					$this->log .= $beforesign . "<br>";

					$Digest = hash("sha512", base64_encode($beforesign."|".$this->datPas['clave']));
					// $params['SHA'] = $message;

					// $this->log .= json_encode($params) . "\n<br>";
					$this->log .= "enviado->".$beforesign."|".$this->datPas['clave'] . "******** $Digest\n<br>";
					// $options = array(
					// 	CURLOPT_RETURNTRANSFER	=> true,
					// 	CURLOPT_POST			=> true,
					// 	CURLOPT_URL				=> $this->datPas['url'],
					// 	CURLOPT_POSTFIELDS		=>  http_build_query($params),
					// 	CURLOPT_CUSTOMREQUEST	=> 'POST',
					// 	CURLOPT_HTTPHEADER		=> array(
					// 		'Content-Type: application/x-www-form-urlencoded'
					// 	)
					// );
					// $ch = curl_init();
					// curl_setopt_array($ch, $options);
					// // $output = curl_exec($ch);
					// if (curl_error($ch)) $this->log .= "Error en la resp de Moneytigo:" . curl_error($ch) . "<br>\nurl de envío: " . $this->datPas['url'];

					// $curl_info = curl_getinfo($ch);
					// curl_close($ch);
					// $arrayXil = json_decode($output);
					// $this->log .= "<br><br>salida=$output<br><br>";

					break;
				case 'pasoR':
					include_once("eurocoinpay/api/eurocoinpay-class.php"); 

					$ecpc = new EurocoinPayClass();

					$q = "select terminal, clave, comercio FROM tbl_colPasarMon where idmoneda = '" . $this->mon . "' and estado = 1 and idpasarela = " . $this->pasa;
					$this->db($q);
					$codi = $this->temp->f('clave');
					$term = $this->temp->f('terminal');
					$comercio = $this->temp->f('comercio');

					$this->log .= "URL->".$this->datPas['url'] . "<br>";
					$this->log .= "urlOri=" . $urlOri . "llegada.php<br>";
					$this->log .= "terminal=" . $this->temp->f('terminal') . "<br>clave=" . $this->temp->f('clave') . "<br>";

					$q = "select moneda FROM tbl_moneda where idmoneda = " . $this->mon;
					$this->db($q);
					$moneda = $this->temp->f('moneda');

					$this->log .= "moneda->".$moneda. "<br>";

					//TODO: Set here your payment terminal parameters, provided by EurocoinPay
					$ecpc->eurocoinpay_customer_number = $comercio;
					$ecpc->eurocoinpay_terminal_number = $term;
					$ecpc->eurocoinpay_encryption_key = $codi;
					$ecpc->eurocoinpay_real_mode = 'test'; // real or test payments
					$ecpc->eurocoinpay_shop_name = 'Caribbean Travels'; // The name of your shop to be displayed
					$ecpc->eurocoinpay_log_enabled = 'false'; // Only activate this setting if instructed by EurocoinPay


					$cur_page_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
					$pos1 = strrpos($cur_page_url,"/");
					$cur_page_dir = substr($cur_page_url,0,$pos1+1);
					
					$this->log .= "cur_page_dir = " . $cur_page_dir . "<br>";

					$ecpc->eurocoinpay_url_ok = $urldirOK;
					$ecpc->eurocoinpay_url_fail = $urldirKO;
					$ecpc->eurocoinpay_url_notif = $urlOri . "llegada.php";
					$this->log .= "ecpc->eurocoinpay_customer_number = " . $ecpc->eurocoinpay_customer_number . "<br>";
					$this->log .= "ecpc->eurocoinpay_terminal_number = " . $ecpc->eurocoinpay_terminal_number . "<br>";
					$this->log .= "ecpc->eurocoinpay_encryption_key = " . $ecpc->eurocoinpay_encryption_key . "<br>";
					$this->log .= "ecpc->eurocoinpay_real_mode = " . $ecpc->eurocoinpay_real_mode . "<br>";
					$this->log .= "ecpc->eurocoinpay_shop_name = " . $ecpc->eurocoinpay_shop_name . "<br>";
					$this->log .= "ecpc->eurocoinpay_log_enabled = " . $ecpc->eurocoinpay_log_enabled . "<br>";
					$this->log .= "ecpc->eurocoinpay_url_notif = " . $ecpc->eurocoinpay_url_notif . "<br>";
					$this->log .= "ecpc->eurocoinpay_url_ok = " . $ecpc->eurocoinpay_url_ok . "<br>";
					$this->log .= "ecpc->eurocoinpay_url_fail = " . $ecpc->eurocoinpay_url_fail . "<br>";
					$this->log .= "transaccion = " . $tr . "<br>";
					$this->log .= "importe = " . ($imp / 100) . "<br>";
					$this->log .= "moneda = " . $moneda . "<br>";
					$sndData = $ecpc->prepareDataForEcpServer($tr ,($imp / 100),  $moneda );

					$this->log .= "salida = " . $sndData . "<br>";

					$this->datPas['url'] = $sndData['srvUrl'];
					$data = $sndData['data'];
					$sig = $sndData['sig'];
					break;
			}

			if (isset($this->datAis['idremitente']) && $this->datAis['idremitente'] > 10) { //Para la pasarela de Redsys que sacó Titanes

				$this->db("select idtitanes, fechaDocumento from tbl_aisCliente where idcimex = " . $this->datAis['idremitente']);
				if (!$this->temp->f('idtitanes') > 0) {
					$this->err = "El cliente no existe en la Base de datos";
					$this->saltosPasar($this->err);
					return false;
				}
				if ($this->temp->f('fechaDocumento') < time()) {
					$this->err = "Ha caducado el documento de identificaci&oacute;n deber&aacute; renovarlo";
					$this->saltosPasar($this->err);
					return false;
				}
				$this->db("select id from tbl_aisBeneficiario where idcimex = '" . $this->datAis['iddestin'] . "'");
				if ($this->temp->num_rows() == 0) {
					$this->err = "El beneficiario no existe en la Base de datos";
					$this->saltosPasar($this->err);
					return false;
				}
				$this->db("insert into tbl_aisOrden (idtransaccion, idcliente, idbeneficiario, envia, recibe, comision, subida, idrazon, monedaRecibe) values ('$this->idTrn',
							(select id from tbl_aisCliente where idcimex = '" . $this->datAis['idremitente'] . "' order by id desc limit 0,1),
							(select id from tbl_aisBeneficiario where idcimex = '" . $this->datAis['iddestin'] . "' order by id desc limit 0,1),
							{$this->datAis['importenvia']}, {$this->datAis['importerecive']}, {$this->datAis['comision']}, 0, {$this->datAis['rason']}, '{$this->datAis['monrecibe']}')");
			}

			$this->log .= $message . "\n<br>";

			switch ($arrPas[1]) {
				case 'firmaA':
					$Digest = strtoupper(sha1($message));
					break;
				case 'firmaB':
					$Digest = sha1($message);
					break;
				case 'firmaC':
					$Digest = md5($message);
					break;
				case 'firmaD':
					$Digest = hash_hmac("sha512", $ret, $this->datPas['clave']);
				break;
				case 'firmaE':
					$Digest = hash('sha256', $message);
					break;
				case 'firmaF':
					$Digest = hash("sha512", $message);
				break;
			}
			$this->log .= $Digest . "\n<br>";
		} else {

			switch ($this->pasa) {
				case '10':
				case '19':
				case '20':
				case '21':
				case '22':
				case '23':
				case '25':
				case '26':
				case '27':
				case '28':
				case '29':
				case '30':
				case '31':
				case '32':
				case '36':
				case '38':
				case '41':
				case '42':
				case '43':
				case '44':
					($idioma == 'es') ? $idioma = '001' : $idioma = '002';
					$tipoTrans = '0';
					$message = $imp . $tr . $this->datPas['comercio'] . $this->mon . $tipoTrans . $urlcomercio . $this->datPas['clave'];
					break;
				case '57':
					$company = 'Caribbean Online';
					$logo = "LogosEmp/logo172.png";
					$acreditar = "";
					$IDUu = 0;
					$Login = "Invitado";
					$returnurl = _ESTA_URL . "/rep/";
					$imp = $imp;
					break;
				case '37':
					$tr .= '000000000';
					$tipoTrans = 40;
					$pagoenable = 0;
					$fechapago = '990101';
					$message = $imp . $this->datPas['comercio'] . $tr . $urlcomercio . $this->datPas['clave'];
					break;
				case '40':
					$message = $this->datPas['clave'] . $this->datPas['comercio'] . $tr . $this->imp . $this->mon . $encriptacion . $urldirOK . $urldirKO;
					break;
				case '12':
				case '53':
					$ssl = 'SSL';
					($idioma == 'es') ? $idioma = '1' : $idioma = '6';
					$message = $this->datPas['clave'] . $this->datPas['comercio'] . $variantes[0] . $this->datPas['terminal'] . $tr . $imp . $this->mon . $variantes[1] . $encriptacion . $urldirOK . $urldirKO;
					break;
				case '24':
					$operation = 1;
					$this->mon = $this->datPas['moneda'];
					$message = $this->datPas['comercio'] . $this->datPas['clave'] . $this->datPas['terminal'] . $operation . $tr . $imp . $this->mon . md5("{$this->datPas['variant']}");
					break;
				case '13':
					$referencia = "M$this->mon" . "$this->imp\r\n1\r\n$tr\r\n$producto\r\n1\r\n$imp\r\n";
					break;
			}

			switch ($this->pasa) {
				case '10':
				case '19':
				case '20':
				case '21':
				case '22':
				case '25':
				case '26':
				case '27':
				case '28':
				case '29':
				case '30':
				case '31':
				case '32':
				case '36':
				case '38':
				case '41':
				case '43':
				case '44':
				case '88':
					$Digest = strtoupper(sha1($message));
					break;
				case '12':
				case '23':
				case '37':
				case '40':
				case '42':
				case '53':
					$Digest = sha1($message);
					break;
				case '24':
					$Digest = md5($message);
					break;
			}
		}

		$arrVals = array(
			';data;' => $data,
			';sig;' => $sig,
			';importe;' => $imp,
			';moneda;' => $this->mon,
			';trans;' => $tr,
			';producto;' => $producto,
			';titular;' => $titular,
			';idCom;' => $this->datPas['comercio'],
			';urlcomercio;' => $urlcomercio,
			';urlok;' => $urldirOK,
			';urlko;' => $urldirKO,
			';comName;' => $this->datPas['comNomb'],
			';T;' => 'T',
			';idioma;' => $idioma,
			';Digest;' => $Digest,
			';terminal;' => $this->datPas['terminal'],
			';tipoTrans;' => $tipoTrans,
			';receiveCurrency;' => $receiveCurrency,
			';urlPasarela;' => $this->datPas['url'],
			';adqbin;' => $variantes[0],
			';idCommande;' => $idCommande,
			';porteur;' => base64_encode(json_encode($porteur)),
			';abonnement;' => base64_encode(json_encode($abonnement)),
			';codeTemplate;' => $codeTemplate,
			';panier;' => $panier,
			';exponente;' => $variantes[1],
			';encriptacion;' => $encriptacion,
			';ssl;' => $ssl,
			';referencia;' => $referencia,
			';code;' => $this->datPas['clave'],
			';operation;' => $operation,
			';fechapago;' => $fechapago,
			';source;' => $source,
			';pagoenable;' => $pagoenable,
			';company;' => $company,
			';identificador;' => $identificador,
			';logo;' => $logo,
			';acreditar;' => $acreditar,
			';numerotarjeta;' => $this->tarj,
			';tipotarjeta;' => $this->ttarj,
			';expmes;' => $this->mes,
			';securitycode;' => $this->secur,
			';usr;' => $this->email,
			';nombres;' => $this->nomb,
			';apellidos;' => $this->apell,
			';direccion;' => $this->direcc,
			';telf;' => $this->telf,
			';expano;' => $this->ano,
			';returnurl;' => $returnurl,
			// ';direcremitente;'=>$this->datAis['direcremitente'], ';nombredestin;'=>$this->datAis['nombredestin'],
			// ';apelldestin;'=>$this->datAis['apelldestin'], ';aperemitente;'=>$this->datAis['aperemitente'], ';tipodoc;'=>$this->datAis['tipodoc'],
			// ';numerodoc;'=>$this->datAis['numerodoc'], ';nombremitente;'=>$this->datAis['nombremitente'],
			';importenvia;' => $this->datAis['importenvia'],
			';idremitente;' => $idremitente,
			';idbenef;' => $idbenef,
			';importerecive;' => $this->datAis['importerecive'],
			';paisbenef;' => $paisbenef,
			';citybenef;' => $citybenef,
			';comision;' => $this->datAis['comision'],
			';monedaenv;' => $this->mon,
			';monedacuc;' => $monedacuc,
			';delivery;' => $delivery,
			';orderef;' => $orderef,
			';rason;' => $rason,
			';mdescr;' => $mdescr,
			';corresp;' => $corresp,
			';subcorresp;' => $subcorresp,
			';branch;' => $branch,
			';tipocta;' => $tipocta,
			';numcta;' => $this->datAis['numcta'],
			';urlLogo;' => $urlLogo,
			';shopId;' => $shopId,
			';duplCheck;' => $duplCheck,
			';displayText;' => $displayText,
			';shopname_customParameter1;' => $shopname_customParameter1,
			';shopname_customParameter2;' => $shopname_customParameter2,
			';fgprtord;' => $fgprtord,
			';urlser;' => $urlser,
//			';T;' => 'T',
			';pasar;' => $this->pasa,
			';secu;' => $this->datPas['secure'],
			';jetid;' => $this->datPas['variant'],
			';cenauto;' => $this->datPas['idcenauto'],
			';idusr;' => $this->idusr,
			';tkusr;' => $this->tkusr,
			';email;' => $this->email,
			';comnomb;' => $this->datCom['nombre'],
			';isoDate;' => date(DATE_ISO8601),
			';Xitoken;' => $arrayXil->token,
			';segura;' => $this->segura,
            ';terminalAuth;' => $termAuth,
//			';aft;' => $aft
		);

		if ($arrayXil->tpvUrl) {
			$arrVals[';urlPasarela;'] = $arrayXil->tpvUrl;
		}
		if ($arrPas[1] != 'firmaX') {
			foreach ($arrVals as $key => $value) {
				$cad = str_replace($key, $value, $cad);
			}
		} else {
			$cad = $this->varRedsys($aft);
		}
		if (is_array($cad)) {
			foreach ($cad as $key => $value) {
				$this->log .= $key . " -> " . $value;
			}
		}

		$this->log .= "<br>Valores enviados: <br>". str_replace("&quot;", '', str_replace("'", '', str_replace('"', '', str_replace("<input type=", '', str_replace("hidden", '', str_replace("/>", '<br>', str_replace("name=", '', str_replace(" value", '', $cad)))))))) ."<br>";
		return $cad;
	}

	/**
	 * Función para la búsqueda inteligente de la próxima pasarela ********** OBSOLETA ************
	 */
	private function arrpasar() {
		$dias = 7; // número de días hacia atrás para buscar datos
		$this->log .= "Cambio de pasarela inteligente\n<br>";
		$restpasa = '';
		$inser = ' ';

		// Cubana rotará por las siguientes pasarelas para las divisas:
		// Sabadell 2, Bankia 4, Caixa bank 2, Bankia 5, Navarra 2, Ibercaja
		// Para los EUR por:
		// Ibercaja, Sabadell DCC, Bankia DCC, Navarra DCC, Laboral 3D, Abanca 3D
		$q = "select group_concat(idpasarela separator',') pasarela from tbl_restrcPasarela where idcomercio = " . $this->datCom['id'] . " and idmoneda = '" . $this->mon . "'";
		$this->db($q);
		$restpasa = $this->temp->f('pasarela');

		// situar los entornos de montos
		if ($this->imp > 0 && $this->imp <= 25000) {
			$minop = 0;
			$maxop = 25000;
		} // entre 0 y 200
		elseif ($this->imp > 25000 && $this->imp <= 50000) {
			$minop = 25001;
			$maxop = 50000;
		} // entre 200 y 500
		elseif ($this->imp > 50000 && $this->imp <= 100000) {
			$minop = 50001;
			$maxop = 100000;
		} // entre 500 y 1000
		elseif ($this->imp > 100000 && $this->imp <= 250000) {
			$minop = 100001;
			$maxop = 250000;
		} // entre 1000 y 2500
		elseif ($this->imp > 250000 && $this->imp <= 500000) {
			$minop = 250001;
			$maxop = 500000;
		}  // entre 2500 y 5000
		else {
			$minop = 500001;
			$maxop = 5000000000;
		} // más de 5000
		$this->log .= "$minop - $maxop\n<br>";

		if ($this->comer == '147145461846') //servicios médicos docentes
			$inser .= 'and p.idPasarela in (59,63,12,45,46) ';
			
		if ($this->opr == 'A') $tipo = "'A'"; 
		elseif ($this->opr == 'R') $tipo = "'P','R'"; 
		else $tipo = "'A','P','R'";

		$q = "select t.pasarela, (((select count(*) from tbl_transacciones r where r.pasarela = t.pasarela 
					and r.estado = 'A'
					and r.moneda = " . $this->mon . "
					and r.fecha > " . (time() - (60 * 60 * 24 * $dias)) . "
					and r.valor_inicial between $minop and $maxop
					and r.tipoEntorno = 'P'
					)*100/count(*))*p.coefImporta) total
				from tbl_transacciones t, tbl_pasarela p, tbl_colEmpresasComercios e, tbl_comercio c
				where t.pasarela = p.idPasarela 
					and p.idempresa = e.idempresa
					and e.idcomercio = c.id
					and c.idcomercio = " . $this->comer . "
					and p.secure = " . $this->segura . "
					and t.moneda = " . $this->mon . "
					and t.estado in ('A','B','V','D')
					and t.fecha > " . (time() - (60 * 60 * 24 * $dias)) . "
					and p.tipo in ($tipo)";

		// si hay restricciones de pasarela
		if (strlen($restpasa) > 0)
			$q .= " and t.pasarela in ($restpasa)";
		// eliminar wirecard WDCP de la rotación
		$q .= " and t.pasarela not in (64)";

		$q .= "		and p.activo = 1
					and p.estado = 'P' " . $inser . "
					and t.valor_inicial between $minop and $maxop
					and t.tipoEntorno = 'P'
				group by t.pasarela order by total desc";
		$this->db($q);
		$this->log .= $q . "<br>\n";
		$this->psArray = $this->temp->loadResultArray(0);
		if (count($this->psArray) == 0)
			$this->psArray[0] = $this->pasa;
		$this->mp = $this->psArray[0];
		$this->log .= "this->mp={$this->mp}<br>\n";
		return;
	}

	/**
	 * Variación de datos a Redsys con el cálculo de la firma SHA256
	 * 
	 * @return string
	 */
	private function varRedsys($aft = null) {

		//afecta el monto según la moneda para yenes y pesos chilenos
		$this->db("select factmult from tbl_moneda where idmoneda = '{$this->mon}'");
		$amount = $this->imp / $this->temp->f('factmult');
		$id = $this->idTrn;
		$fuc = $this->datPas['comercio'];
		$moneda = $this->mon;
		$trs = '0';
        ($this->idi == 'es') ? $idioma = '1' : $idioma = '2';
		
		if ($this->amex == 12) {
			$met = 'z';
		} else {
			$met = 'T';
		}
		$termi = $this->datPas['terminal'];

		if ($this->opr == 'R') { //Pago por Referencia
			$identificador = 'REQUIRED';
			$this->log .= "<br><br>Pago por referencia ";
			$this->log .= "<br>Cliente=".$this->datAis['idremitente'];
			$this->log .= "<br>Referencia=".$this->refer;
			if (isset($this->refer) && strlen($this->refer) > 10) {
				$this->log .= "<br>select codBanco from tbl_referencia where codConc = '" . $this->refer . "' or codBanco = '" . $this->refer . "'";
				$this->db("select codBanco from tbl_referencia where codConc = '" . $this->refer . "' or codBanco = '" . $this->refer . "'");
				if (strlen($this->temp->f('codBanco')) > 10) {
					$identificador = $this->temp->f('codBanco');
					$trs = '0';
                }
            }
        }

		if ($this->datPas['tipo'] == 'melt') {
			$urlcom		= str_replace("paid.php", "llegada.php", $this->datPas['url']);
//			$urlcom		= _URL_COMERCIO;
			$urlok		= str_replace("paid.php", "", $this->datPas['url']) . "ver.php?resp=$id" . '&est=ok';
			$urlko		= str_replace("paid.php", "", $this->datPas['url']) . "ver.php?resp=$id" . '&est=ko';
		} else {
			$urlcom = _URL_COMERCIO;
			$urlok = _URL_DIR . "index.php?resp=$id" . '&est=ok';
			$urlko = _URL_DIR . "index.php?resp=$id" . '&est=ko';
		}

		$this->log .= "<br>DS_MERCHANT_AMOUNT=$amount";
		$this->log .= "<br>DS_MERCHANT_ORDER=" . strval($id);
		$this->log .= "<br>DS_MERCHANT_MERCHANTCODE=$fuc";
		$this->log .= "<br>DS_MERCHANT_CURRENCY=$moneda";
		$this->log .= "<br>DS_MERCHANT_TRANSACTIONTYPE=$trs";
		$this->log .= "<br>DS_MERCHANT_TERMINAL=$termi";
		$this->log .= "<br>DS_MERCHANT_MERCHANTURL=$urlcom";
		$this->log .= "<br>DS_MERCHANT_URLOK=$urlok";
		$this->log .= "<br>DS_MERCHANT_URLKO=$urlko";
        $this->log .= "<br>DS_MERCHANT_PAYMETHODS=$met";
        $this->log .= "<br>DS_MERCHANT_CONSUMERLANGUAGE=$idioma";
        if ($this->opr == 'R') {
            $this->log .= "<br>DS_MERCHANT_IDENTIFIER= $identificador";
            $this->log .= "<br>DS_MERCHANT_DIRECTPAYMENT= false";
        }
        if(isset($aft)){
            $this->log .= "<br>DS_MERCHANT_AFT= {";
            $aftArray = json_decode($aft, true);
            foreach ($aftArray as $key => $value) {
                $this->log .= "<br>" . $key . "=" . $value;
            }
            $this->log .= "<br>}";
        }

		$this->obj->setParameter("DS_MERCHANT_AMOUNT", $amount);
		$this->obj->setParameter("DS_MERCHANT_ORDER", strval($id));
		$this->obj->setParameter("DS_MERCHANT_MERCHANTCODE", $fuc);
		$this->obj->setParameter("DS_MERCHANT_CURRENCY", $moneda);
		$this->obj->setParameter("DS_MERCHANT_TRANSACTIONTYPE", $trs);
		$this->obj->setParameter("DS_MERCHANT_TERMINAL", $termi);
		$this->obj->setParameter("DS_MERCHANT_MERCHANTURL", $urlcom);
		$this->obj->setParameter("DS_MERCHANT_URLOK", $urlok);
		$this->obj->setParameter("DS_MERCHANT_URLKO", $urlko);
		$this->obj->setParameter("DS_MERCHANT_PAYMETHODS", $met);
		$this->obj->setParameter("DS_MERCHANT_CONSUMERLANGUAGE", $idioma);
        if ($this->opr == 'R') {
            $this->obj->setParameter("DS_MERCHANT_IDENTIFIER", $identificador);
            $this->obj->setParameter("DS_MERCHANT_DIRECTPAYMENT", "false");
        }
        if(isset($aft)){
            $this->obj->setJsonParameter("DS_MERCHANT_AFT", $aft);
        }

		$params = $this->obj->createMerchantParameters();
		$signature = $this->obj->createMerchantSignature($this->datPas["clave"]);
		// echo $params."<br>";
		// echo $signature."<br>";

		$salida = '<form name="envia" action="' . $this->datPas['url'] . '" method="post">' . '<input type="hidden" name="Ds_SignatureVersion" value="HMAC_SHA256_V1"/>' . "\n" . '<input type="hidden" name="Ds_MerchantParameters" value="' . $params . '"/>' . "\n" . '<input type="hidden" name="Ds_Signature" value="' . $signature . '"/>';
		// echo "\n".$salida;
		// print_r($arrVals);
		return $salida;
	}
}
