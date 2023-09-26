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
	 * identificador de la transaccion desde el comercio
	 */
	var $tran = '';
	/**
	 * moneda
	 */
	var $mon = '';
	/**
	 * importe
	 */
	var $imp = '';
	/**
	 * tipo de operacion D=devolucion, P=pago
	 */
	var $opr = '';
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
	var $amex = 0;
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
			$this->log .= "El dominio no tiene registros de correo\n<br>";
			$this->err = "Error en el correo";
			return false;
		}

		// 		$q = "select count(*) total from tbl_admin a, tbl_colAdminComer o
		// 				where o.idAdmin = a.idadmin and o.idComerc = " . $this->datCom ['id'] . " and a.email regexp '$domain'";
		$q = "select count(*) total from tbl_admin a, tbl_colAdminComer o
				where o.idAdmin = a.idadmin and o.idComerc = " . $this->datCom['id'] . " and a.email = '$this->email'";
		$this->db($q);
		if ($this->temp->f('total') > 0) {
			$this->log .= "El dominio corresponde al comercio\n<br>";
			$this->err = "Error en el correo";
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
	function cheqPas() {
		$this->log = "\n<br>Chequeo de la pasarela\n<br>";

		$this->db("select * from tbl_reserva where codigo = '{$this->tran}' and id_comercio = '{$this->comer}'");
		if ($this->temp->num_rows()) {
			$this->pweb = 0;
			$this->log .= "Operación originada en el concentrador\n<br>";
		} else {
			$this->pweb = 1;
			$this->log .= "Operación originada en la la web del comercio\n<br>";
		}

		if ($this->datCom['estado'] == 'D') { // si el comercio está en desarrollo lo mando a la pasarela de desarrollo
			$this->pasa = 1;
			$this->log .= "Comercio en desarrollo lo pongo en la pasarela de desarrollo\n<br>";
		} else { // si el comercio no está en desarrollo
			if ($this->pasa > 0) { // el comercio envió la pasarela en los datos, revisar si está autorizada
				if ($this->pweb == 0) { //es un pago al momento

					if (!stristr($this->datCom['pasarelaAlMom'] . ',', $this->pasa . ',')) {

						// reviso si el comercio está autorizado a usar esa pasarela que envía
						$this->log .= "Comercio envía su pasarela " . $this->pasa . " pero no está autorizado a usarla  \n<br>";
// 						$q = "select idPasarela from tbl_pasarela p, tbl_colComerPasar c where c.idpasarelaT = p.idPasarela and
// 								p.secure = (select secure from tbl_pasarela where idPasarela = " . $this->pasa . ") and c.idcomercio = '{$this->comer}'
// 								and " . time () . " between c.fechaIni and c.fechaFin";
// 						$this->db ( $q );
// 						$this->pasa = $this->temp->f ( 'idPasarela' );

						$this->db("select idpasarela from tbl_rotComPas where idcom = " . $this->datCom['id'] . " and activo = 1 and orden = 1");
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

					if (!stristr($this->datCom['pasarela'] . ',', $this->pasa . ',')) {

						// reviso si el comercio está autorizado a usar esa pasarela que envía
						$this->log .= "Comercio envía su pasarela " . $this->pasa . " pero no está autorizado a usarla  \n<br>";

						$q = "select idPasarela from tbl_pasarela p, tbl_colComerPasar c where c.idpasarelaW = p.idPasarela and p.secure = (select secure from tbl_pasarela where idPasarela = " . $this->pasa . ") and p.idPasarela in (" . $this->datCom['pasarela'] . ") and c.idcomercio = '{$this->comer}' and " . time() . " between c.fechaIni and c.fechaFin limit 0,1";
						$this->db($q);
						if ($this->temp->num_rows() == 0) {
							$this->db("select idPasarela from tbl_pasarela p, tbl_colComerPasar c where c.idpasarelaW = p.idPasarela and p.secure = 1 and p.idPasarela in (" . $this->datCom['pasarela'] . ") and c.idcomercio = '{$this->comer}' and " . time() . " between c.fechaIni and c.fechaFin limit 0,1");
						}
						$this->pasa = $this->temp->f('idPasarela');

						$this->psArray = explode(',', $this->datCom['pasarela']);
						$this->pasa = $this->psArray[0];
						$this->log .= "la cambio a " . $this->pasa . " \n<br>";
					}
				}

				// chequeo si la pasarela que envían es válida
				$this->db("select idPasarela, nombre from tbl_pasarela where activo = 1 and idPasarela = " . $this->pasa);
				$this->pasaN = $this->temp->f('nombre');
				if ($this->temp->num_rows() == 0) { // la pasarela no existe o es inválida
					$this->log .= "La pasarela " . $this->pasa . " no es válida o no existe\n<br>";
					$this->err = "falla por pasarela inv&aacute;lida";
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
			if ($this->comer != '122327460662') { // si no es Prueba
				if ($this->segura == 0) {//Para las pasarelas sin 3D
					//determino el psArray
					if ($this->pweb == 1) $term = "idpasarelaW";
					else $term = "idpasarelaT";
					//determina el array de las pasarelas por las que se mueve el comercio 
					$this->db("select $term pasarela from tbl_colComerPasar c, tbl_pasarela p, tbl_colTarjPasar j where j.idPasar = p.idPasarela and $term = p.idPasarela and c.idcomercio = " . $this->comer . " and (c.fechaIni <=" . time() . " and c.fechaFin >= " . time() . ") and p.secure = 0 and j.idTarj = " . $this->amex);
					//$this->db("select idPasarela from tbl_pasarela where idPasarela in ({$this->datCom ['pasarelaAlMom']}) and secure = 0");
					$this->psArray = $this->temp->loadResultArray();

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
							for ($i = 0; count($this->arrORd) < count($this->psArray); $i++) {
								if (($elmIni + $i) >= count($this->psArray)) {
									$elmIni = $i = 0;
								}
								$this->arrORd[] = $this->psArray[$elmIni + $i];
							}
							$this->psArray = $this->arrORd;
						}
					}

					$this->pasa = $this->psArray[0];
					array_shift($this->psArray);
					$this->log .= "Pasarela sin 3D en la nueva rotación escogida: {$this->pasa}\n<br>";
				} else {
					($this->pweb == 0) ? $tabla = 'tbl_rotComPas' : $tabla = 'tbl_rotComPasWeb';

					$this->log .= "Es una pasarela con 3D y pago desde el concentrador se busca el array de pasarelas por el que circularía la oper y se toma como la primera para que inicie la verificación\n<br>";

					//Si la oper es con 3D busca el array de pasarelas por los que deberá circular el comercio
					//y fija como la pasarela la primera que tenga
					$this->db("select idPasarela from $tabla where idcom = " . $this->datCom['id'] . " and activo = 1 and horas = 0 order by orden");
					if ($this->temp->num_rows() > 0) {
						$this->psArray = $this->temp->loadResultArray();
						$this->pasa = $this->psArray[0];
						$this->arrORd = $this->psArray;
					}

						//} else {
						//$this->log .= "Es una pasarela con 3D y pago desde la web se busca el array de pasarelas por el que circularía la operación \n<br>";
						//$this->db("select pasarelaWeb from tbl_comercio where id = '{$this->datCom['id']}'");
						//$this->psArray = $this->temp->loadResultArray();
						//$this->pasa = $this->psArray[0];
					// }
				}

				//adiciona al array de pasarelas las pasarelas autorizadas usd amex si el comercio está autorizado
				if ($this->datCom['usdxamex'] == 1 && $this->amex == 1 && $this->mon == 840) {
					//busco las pasarelas que tienen permitido esta variande de usd - amex
					$this->db("select idPasarela from tbl_pasarela where usdxamex = 1 and secure = " . $this->segura);
					if ($this->temp->num_rows() > 0) {
						$pasAmex = $this->temp->loadResultArray(0);
						$this->psArray = array_diff($this->psArray, $pasAmex);
						$this->psArray = array_merge($pasAmex, $this->psArray);
						$this->pasa = $this->psArray[0];
					}
				}
			}
			if ($this->CheqLimites() == false)
				return false;
		}

		$this->log .= "Pasarela = {$this->pasa}\n<br>";

		$this->log .= "Verificaci&oacute;n de la combinaci&oacute;n Pasarela - Moneda y captaci&oacute;n de datos\n<br>";
		if ($this->opr == 'D') {
			$this->log .= "Es una devoluci&oacute;n, se captan los datos\n<br>";
			$this->db("select c.terminal, c.clave, c.comercio, p.nombre, c.datos variant, a.tipo, p.datos datPas, p.idcenauto, m.moneda, p.secure, " . "urlXml url, a.datos, p.comercio comNomb " . "from tbl_colPasarMon c, tbl_pasarela p, tbl_cenAuto a, tbl_moneda m " . "where m.idmoneda = c.idmoneda and c.idpasarela = p.idPasarela and a.id = p.idcenauto and c.idpasarela = " . $this->pasa . " and c.idmoneda = '" . $this->mon . "'");
		} else {
			$this->db("select c.terminal, c.clave, c.comercio, p.nombre, c.datos variant, a.tipo, p.datos datPas, p.idcenauto, m.moneda, p.secure, " . "case p.estado when 'P' then a.urlPro else a.urlDes end url, a.datos, p.comercio comNomb " . "from tbl_colPasarMon c, tbl_pasarela p, tbl_cenAuto a, tbl_moneda m " . "where m.idmoneda = c.idmoneda and c.idpasarela = p.idPasarela and a.id = p.idcenauto and c.idpasarela = " . $this->pasa . " and c.idmoneda = '" . $this->mon . "'");
		}

		if ($this->temp->num_rows() > 0) {
			$arrVal = $this->temp->loadAssocList();
			$this->datPas = $arrVal[0];
			// print_r($this->datPas);
		} else {
			$this->err = "falla por moneda en " . $this->pasaN;
			$this->log .= "falla por moneda en" . $this->pasaN . "\n<br>";
			return false;
		}

		return true;
	}

	private function ChequeoAis() {
		$this->log .= "\n<br>Chequeo del l&iacute;mite trimestral en Cimex\n<br>";
		$vlim = 3000;

		$arrCli = explode(',', leeSetup('clientePerm'));
		$arrBen = explode(',', leeSetup('benefPerm'));

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

		//Chequeo de límite trimestral para el Remitente
		if (!in_array($this->datAis['idremitente'], $arrCli)) { //habilita el límite trimestral para que los Clientes suban documentación
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

			if (($total + $this->imp) > ($vlim * 100)) {
				$this->err = $causa = "Estimado Cliente, usted tiene un acumulado en env&iacute;os de " . number_format($total / 100, 2) . ", con esta operaci&oacute;n de " . number_format($this->imp / 100, 2) . " llega al tope que es de " . number_format($vlim, 2) . " para un trimestre natural. Debe ponerse en contacto con <a href='mailto:info@aisremesascuba.com'>info@aisremesascuba.com</a> para realizar el env&iacute;o\n<br>";
				$this->log .= $causa;
				return 0;
			}
		} elseif (!in_array($this->datAis['iddestin'], $arrBen)) {
			//		TODO ver el TODO anterior que se aplica acá también
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

			if (($total + $this->imp) > ($vlim * 100)) {
				$this->err = $causa = "Estimado Cliente, el beneficiario de esta operaci&oacute;n tiene acumulado " . number_format($total / 100, 2) . " con esta operaci&oacute;n de " . number_format($this->imp / 100, 2) . " llega al tope que es de " . number_format($vlim, 2) . " para un trimestre natural. Debe ponerse en contacto con <a href='mailto:info@aisremesascuba.com'>info@aisremesascuba.com</a> para realizar el env&iacute;o\n<br>";
				$this->log .= $causa;
				return 0;
			}
		}

		//Chequeo de operación repetida
		$iniDia = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
		$this->db("select count(t.idtransaccion) total from tbl_transacciones t, tbl_aisOrden o, tbl_aisCliente c, tbl_aisBeneficiario b where o.idtransaccion = t.idtransaccion and b.id = o.idbeneficiario and c.id = o.idcliente and t.estado = 'A' and t.fecha > $iniDia and c.idcimex = {$this->datAis['idremitente']} and b.idcimex = {$this->datAis['iddestin']} and valor_inicial = {$this->imp}");
		if ($this->temp->f('total') > 0) {
			$this->err = $causa = "Hoy se ha realizado otra operaci&oacute;n Aceptada con el mismo monto para el mismo Beneficiario, deber&aacute; esperar a ma&ntilde;ana para cursar otra operaci&oacute;n igual\n<br>";
			$this->log .= $causa;
			return 0;
		}

		//Chequeo de datos del Cliente
		$q = "select count(*) total from tbl_aisCliente where fechaDocumento > " . time() . " and idcimex = " . $this->datAis['idremitente'];
		$this->db("select count(*) total from tbl_aisCliente where fechaDocumento > " . time() . " and idcimex = " . $this->datAis['idremitente']);
		if ($this->temp->f('total') == 0) {
			$this->err = $causa = "La fecha de vencimiento del Documento de Identidad ha caducado. Debe subir el nuevo documento.\n<br>";
			$this->log .= $causa;
			return 0;
		}

		return 1;
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
		$this->log .= "\n<br>";

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
				$this->log .= $causa = "Cambio de pasarela {$this->pasa} la pasarela no es válida para el comercio\n<br>";
				$pase = 0;
			}
		}

		//Chequeo que la pasarela esté activa
		if ($this->comer != '122327460662') {
			$this->db("select count(idPasarela) total from tbl_pasarela where activo = 1 and tipo = 'P' and idPasarela = '" . $this->pasa . "'");
			if ($this->temp->f('total') == 0) {
				$this->log .= $causa = "Cambio de pasarela obligado pasarela no activa\n<br>";
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
						$this->log .= $causa = "Cambio de pasarela obligado operación repetida en menos de 6 horas\n<br>";
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
				$this->log .= $causa = "Cambio de pasarela obligado Sabadell3\n<br>";
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
				$this->log .= $causa = "Cambio de pasarela obligado Abanca2\n<br>";
				$pase = 0;
			}
		}

		// Chequeo de restricciones por países
		if ($this->comer != '122327460662') {
			if ($pase && $this->ip != '127.0.0.1') {
				$q = "select id from tbl_colPaisPasarelDeng where idpasarela = " . $this->pasa . " and idpais = " . $this->damepais();
				$this->db($q);
				if ($this->temp->f('id')) {
					$this->log .= $causa = "Cambio de pasarela por país prohibido\n<br>";
					$pase = 0;
				}
			}
		}

		// Chequeo de límites para Cimex
		if ($this->comer == '527341458854' || $this->comer == '144172448713') {
			$pase = $this->ChequeoAis();
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
						$causa = 'Cliente sin correo';
					}
				}
			}
		}

		//buscando los límites
		$q = "select nombre, LimMinOper, LimMaxOper, LimDiar, LimMens, LimAnual, LimOperIpDia, LimOperTarDia, LimOperDia, pasarLim";
		if ($verif)
			$q .= ", secure";
		$q .= " from tbl_pasarela where idPasarela = " . $this->pasa;
		$this->db($q);
		$arrV = $this->temp->loadAssocList();
		$datPas = $arrV[0];
		foreach ($datPas as $key => $value) {
			$this->log .= "$key => $value / ";
		}
		$this->log .= "\n<br>";

		//selecciona pasarelas iguales para límites
		$this->db("select idPasarela from tbl_pasarela where pasarLim = " . $datPas['pasarLim']);
		$arrPasSum = implode(",", $this->temp->loadResultArray());
		$this->log .= "pasarelas para limites $arrPasSum \n<br>";

		// Chequeo por la última pasarela que cursó
		if ($this->comer != '129025985109' //Cubana
			&& $this->comer != '152295812637' // Soy Cubano IT
			&& $this->comer != '527341458854' //Fincimex
			&& ($this->amex == 2 || $this->amex == 3) 
			// && $this->comer != '140784511377' //y el H. Saratoga
			// && $this->comer != '145918467582' //y el FCBC
			&& $this->pweb != 1 //para los pagos que no sean de la Web
			){
				if ($pase) {
					$q = "select pasarela from tbl_transacciones where idcomercio = '" . $this->comer . "' order by fecha desc limit 0,1";
					$this->db($q);
					$this->log .= "Chequeo de la &uacute;ltima pasarela del comercio\n<br>" . ($this->comer) . "\n<br>";
					$this->log .= $this->temp->f('pasarela') . " == " . $this->pasa . " ?\n<br>";
					if ($this->temp->f('pasarela') == $this->pasa) {
						$mes = "Esta pasarela " . $datPas['nombre'] . " identificador " . $this->pasa . " ha cursado la operación anterior";
						$this->log .= $this->err = $mes . "\n<br>";
						$pasV = $this->pasa;
						$pase = 0;
						$causa = 'Ultima pasarela usada';
					}
				}
			}

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
				$causa = 'Moneda que no est&aacute; en esta pasarela';
				// if ($datPas['secure'] == 1) return false;
			}
		}

		// Chequeo de límite mínimo por operación
		if ($pase) {
			$this->log .= "Chequeo de l&iacute;mite m&iacute;nimo por operación\n<br>" . ($this->imp / 100) . " <= " . $datPas['LimMinOper'] . "\n<br>";
			if (($this->imp / 100) <= $datPas['LimMinOper']) {
				$mes = "La operación {$this->tran} con un valor de " . ($this->imp / 100) . " menor que el l&iacute;mite m&iacute;nimo permitido por operación" . " para esta la pasarela " . $datPas['nombre'] . " identificador " . $this->pasa . " que es de " . $datPas['LimMinOper'];
				//$this->corr->todo ( 44, 'Alerta por límites', $mes );
				$this->log .= $this->err = $mes . "\n<br>";
				$pasV = $this->pasa;
				$pase = 0;
				$causa = 'm&iacute;nimo por operación';
				// if ($datPas['secure'] == 1) return false;
			}
		}

		// Chequeo de límite máximo por operación
		if ($pase) {
			$this->log .= "Chequeo de l&iacute;mite m&aacute;ximo por operación\n<br>" . ($this->imp / 100) . " > " . $datPas['LimMaxOper'] . "\n<br>";
			if (($this->imp / 100) > $datPas['LimMaxOper']) {
				$mes = "La operación {$this->tran} con un valor de " . ($this->imp / 100) . " mayor que el l&iacute;mite m&aacute;ximo permitido por operación" . " para esta la pasarela " . $datPas['nombre'] . " identificador " . $this->pasa . " que es de " . $datPas['LimMaxOper'];
				//$this->corr->todo ( 44, 'Alerta por límites', $mes );
				$this->log .= $this->err = $mes . "\n<br>";
				$pasV = $this->pasa;
				$pase = 0;
				$causa = 'm&aacute;ximo por operación';
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
				$causa = 'm&aacute;ximo operaciones por IP';
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
				$mes = "Con la operación {$this->tran} de un valor de " . ($this->imp / 100) . " se ha arribado al l&iacute;mite diario m&aacute;ximo por montos que 
						es de {$datPas['LimDiar']} para esta la pasarela " . $datPas['nombre'] . " identificador " . $this->pasa;
				$this->log .= $this->err = $mes . "\n<br>";
				$pasV = $this->pasa;
				$pase = 0;
				$causa = 'montos diarios';
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
				$causa = 'monto mensual';
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
				$causa = 'monto anual';
				// if ($datPas['secure'] == 1) return false;
			}
		}

		// Chequeo de límite de cantidad de operaciones al día
		if ($pase) {
			$this->log .= "Chequeo de cantidad de operaciones al d&iacute;a\n<br>";
			$q = "select count(t.idtransaccion) 'valor'
				FROM tbl_transacciones t 
				where t.estado in ('A','V','B','R','D') 
					and t.tipoEntorno = 'P' 
					and t.fecha > unix_timestamp('" . date('Y') . "-" . date('m') . "-" . date('d') . " 00:00:00')
					and t.pasarela in (" . $arrPasSum . ")";
			$this->db($q);
			$this->log .= $this->temp->f('valor') . " >= " . $datPas['LimOperDia'] . "\n<br>";
			if ($this->temp->f('valor') >= $datPas['LimOperDia']) {
				$mes = "Con la operación {$this->tran} se ha arribado al l&iacute;mite diario m&aacute;ximo por operaciones que es de 
						{$datPas['LimOperDia']} para esta la pasarela " . $datPas['nombre'] . " identificador " . $this->pasa;
				$this->log .= $this->err = $mes . "\n<br>";
				$pasV = $this->pasa;
				$pase = 0;
				$causa = 'cantidad de operaciones diarias';
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
				$causa = "pasarela no tiene la tarjeta";
			}
		}

		//$pase = 0;
		$this->log .= "Pase = $pase<br>\n";
		$this->log .= "Segura = $this->segura<br>\n";
		$this->log .= "Causa = $causa<br>\n";

		// if ($this->pasa == 56 && ( //si es la Caixa3 y además es de alguna de estas ip
		// $this->ip == '200.55.165.58' //Gaviota Tours
		// // || $this->ip == '200.55.132.205' //Soy Cubano
		// || $this->ip == '200.55.188.130' //Hotel Nacional
		// // || $this->ip == '190.179.230.61' //Outdoor Argentina190.15.144.236
		// // || $this->ip == '190.15.144.236' //Servicios Global
		// )) {
		// $this->log .= "Cambio de pasarela super obligado\n<br>";
		// // $this->pasa = 53; //Cambio obligado a Abanca2
		// $this->pasa = 44; //Cambio obligado a Sabadell3
		// $pase = 1;
		// }


		//if (_MOS_CONFIG_DEBUG) $pase = 0;


		if ($pase == 0 && $this->comer != '122327460662') { // si ha saltado algún límite.. y el comercio no es Prueba
			$this->log .= "Necesita cambio de pasarela\n<br>";
			// if ($pase == false) {//si ha saltado algún límite.. y el comercio no es Prueba
			//if ($this->segura == 0) { // cuando la pasarela sea no segura roto entre esto
			//$this->log .= "cuenta = ".$this->cuenta."\n<br>";
			//// Contador de pasarelas no segura
			////if ($this->cuenta > 4) {
			////$this->cuenta = 0;
			////$this->err = "Alcanzado n&uacute;mero m&aacute;ximo de operaciones No Seguras, por favor usar pasarela Segura";
			////return false;
			////} else
			////$this->cuenta ++;
			//
			//if ($this->pasa == 44) // si va por Abanca3 lo paso a Bankia6
			//$this->pasa = 52;
			//elseif ($this->pasa == 52) // si va por Bankia6 lo paso a Abanca3
			//$this->pasa = 68;
			//elseif ($this->pasa == 68) // si va por Abanca3 lo paso a Navarrap
			//$this->pasa = 71;
			//else {// si va por la última le devuelvo error
			//$this->err = "Alcanzado n&uacute;mero m&aacute;ximo de pasarelas No Seguras, por favor usar pasarela Segura";
			//return false;
			//}
			//} else { // cuando la pasarela sea segura
			// if (count($this->psArray) == 0 && $this->mp == '') {
			// 	// entra por primera vez determina los elementos y el orden del array de pasarelas seguras
			// 	// basados en el comportamiento que han tenido los últimos 7 días

			// 	if ($this->comer == '527341458854' || $this->comer == '144172448713')
			// 		return false; // Si el comercio es Cimex no hay cambio de pasarela que valga y retorna error

			// 	//$this->arrpasar ();

			// 	if (count($this->psArray) == 0)
			// 		return true; // si no aparece ninguna pasarela
			// } elseif (count($this->psArray) == 0 && $this->mp != '') { // si recorre todo el array y no tiene ninguna pasarela seleccionada...
			if (count($this->psArray) == 1 ) { // si recorre todo el array y no tiene ninguna pasarela seleccionada...

				// $this->pasa = $this->mps; //toma la mejor y se encarga a Dios
				$this->corr->todo(44, "Operación posible a Denegar", "Esta operación superó algún límite la causa fué: $causa");
				if ($causa == "pasarela no tiene la tarjeta") {
					$this->log .= "La última pasarela no tiene la tarjeta así que lo mando al primer elemento del array";
					$this->pasa = $this->arrORd[0];
				}
				return true; // sigue con la última seleccionada que es la más mala para que deniegue al usuario
			}
			$this->log .= "El psArray tiene " . count($this->psArray) . " elementos\n<br>";
			array_shift($this->psArray);
			$this->pasa = $this->psArray[0];
			//}

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
	function operacion() {
		if ($this->opr == 'P') {
			$this->idTrn = trIdent(true); // Genera el identificador de la transacción
			$this->log .= "\n<br>Inserta la operación\<br>";
			$accN = $this->db("insert into tbl_transacciones (idtransaccion, idcomercio, identificador, tipoOperacion, fecha, fecha_mod, " . "valor_inicial, tipoEntorno, moneda, estado, sesion, idioma, pasarela, ip, idpais, tpv, id_tarjeta) " . "values ('" . $this->idTrn . "', '{$this->comer}', '{$this->tran}', '{$this->opr}', " . time() . ", " . time() . ", " . "{$this->imp}, '{$this->datCom['estado']}', {$this->mon}, 'P', '{$this->frma}', '{$this->idi}', {$this->pasa}, " . "'{$this->ip}', '" . $this->damepais() . "', '{$this->tpv}', '{$this->amex}')");
			if ($accN === false)
				return false;
			// actualiza la pasarela en la tbl_reserva segun el último cambio realizado
			$this->db("update tbl_reserva set pasarela = {$this->pasa} where id_comercio = '{$this->comer}' and codigo = '{$this->tran}'");

			if ($this->comer == '527341458854' || $this->comer == '144172448713') {
				if (!$this->db("insert into tbl_aisDato (idtransaccion, fecha, idCliente, idBeneficiario) 
							values ('{$this->idTrn}', " . time() . ", '{$this->datAis['idremitente']}', '{$this->datAis['iddestin']}')"))
					return false;
			}
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
			return false;
		}


		$this->log .= "moneda=".$this->mon." \n<br>";
		$this->log .= "usdxamex=".$this->datCom['usdxamex']." \n<br>";
		$this->log .= "amex=".$this->amex." \n<br>";
		// Chequeo de restricción por la autorización de usar Amex con otra divisa que no sea EUR
		if ($this->amex == 1) {
			if ($this->mon != 978 && $this->mon != 840) {
				$pase = 0;
				$this->err = "Las Amex no tienen permitido pagos que no sean en USD o EUR";
				$this->log .= $this->err." \n<br>";
				return false;
			}
			if ($this->mon != 978 && $this->datCom['usdxamex'] == 0) {
				$pase = 0;
				$this->err = "El comercio no tiene permitidos pagos con Amex que no sean en Euros";
				$this->log .= $this->err." \n<br>";
				return false;
			} elseif ($this->datCom['usdxamex'] == 2) {
				$pase = 0;
				$this->err = "El comercio no tiene permitidos pagos con Amex";
				$this->log .= $this->err." \n<br>";
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
		if (strlen($this->frma) == 32) 
			$Calc = convierte($this->comer, $this->tran, $this->imp, $this->mon, $this->opr);
		elseif (strlen($this->frma) > 32) 
			$Calc = convierte256($this->comer, $this->tran, $this->imp, $this->mon, $this->opr);
		$this->log .= "Firma recibida {$this->frma}\n<br>";
		$this->log .= "Firma generada {$Calc}\n<br>";
		if ($Calc != $this->frma) {
			$this->log .= "falla por firma\n<br>";
			$this->err = "falla por firma";
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
			return false;
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
			$this->db(sprintf("select estado from tbl_transacciones where idcomercio = '%s' and fecha_mod > %d and ip = '%s' order by fecha_mod limit 0," . leeSetup('cantReintentos'), $this->comer, (time() - (leeSetup('minReintento') * 60)), $this->ip));
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
				return false;
			}
		} else return true;


		$this->log .= "\n<br>Verifica que la ip desde donde están pagando no está bloqueada\n<br>";
		$this->db(sprintf("select id from tbl_ipBL where ip='%s' and cuenta >= 5", $this->ip));
		if ($this->temp->num_rows() !== 0) {
			$this->err = 'Su IP est&aacute; bloqueada, contacte a su comercio / Your IP is banned, contact to your e-commerce';
			$this->log .= "Intento de pago desde la IP Bloqueada: " . $this->ip;
			return false;
		}

		$this->log .= "\n<br>Verifica que la ip no está bloqueada por pagos denegados\n<br>";
		$this->db(sprintf("select idips from tbl_ipbloq where ip = '%s' and bloqueada = 1", $this->ip));
		if ($this->temp->num_rows() !== 0) {
			$this->err = 'Su IP est&aacute; bloqueada, contacte a su comercio / Your IP is banned, contact to your e-commerce';
			$this->log .= "Intento de pago desde la IP Bloqueada: {$this->ip}";
			return false;
		}

		return true;
	}

	/**
	 * Alertas de Seguridad.
	 * Es una función que sólo envía alertas no bloquean operaciones
	 * 
	 * @return boolean
	 */
	function alerSegur() {
		$mensage = "";
		$this->log = "\n<br>Verifica que el monto de la operación no está por encima del máximo de peligro\n<br>";
		if ($this->imp >= leeSetup(montoAlerta) * 100) {
			$mensage .= "Se est&aacute; realizando una transacci&oacute;n por un monto de " . number_format(($this->imp / 100), 2, '.', ' ');
			$mensage .= " correspondiente al comercio: " . $this->datCom['nombre'];
			$mensage .= "\n<br />Fecha - Hora: " . date('d') . "/" . date('m') . "/" . date('Y') . " " . date('H') . ":" . date('i') . ":" . date('s');
			$mensage .= "\n<br /><br />";
			$this->corr->todo(10, "Alerta de vigilancia antifraude", $mensage);
			$this->log .= $mensage;
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
		// Todo el EUR de Cubana lo voy rotando entre las pasarelas
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
		}
		//antigua rotación
		// if ($this->comer === '129025985109' && $this->mon === '978') {

		// 	// $this->db("select pasarela from tbl_transacciones where idcomercio = '129025985109' and moneda = '978' order by fecha desc limit 0,1");
		// 	$this->log .= "<br>\nCambia pasarela EUR para Cubana<br>\n";
		// 	$pasEurCub = leeSetup('pasEurCub');
		// 	$this->log .= "pasEurCub = $pasEurCub<br>\n";
		// 	switch ($pasEurCub) {
		// 		case '12': // si es Abanca 3D
		// 			$npas = '23'; // lo paso a Bankia3 3D DCC
		// 			break;
		// 		case '23': // si es Bankia3 3D DCC
		// 			$npas = '51'; // lo paso a LabKutxa 3D
		// 			break;
		// 		case '51': // si es LabKutxa 3D
		// 			$npas = '29'; // lo paso a SabadellPlus DCC
		// 			break;
		// 		case '29': // si es SabadellPlus DCC			Vuelto a poner en rotación
		// 			//$npas = '50'; // lo paso a Ibercaja 3D		el 19/2/19
		// 			// case '29' : // si es SabadellPlus DCC
		// 			$npas = '58'; // lo paso a Navarra DCC
		// 			break;
		// 		case '50': // si es Ibercaja 3D
		// 			$npas = '58'; // lo paso a Navarra DCC
		// 			break;
		// 		case '58': // si es Navarra DCC
		// 			$nuM = 2; // número de operaciones
		// 			$this->db("select pasarela from tbl_transacciones where moneda = '978' and idcomercio = '129025985109' order by fecha desc limit 0,$nuM");
		// 			$arrVals = $this->temp->loadAssocList();
		// 			// print_r($arrVals);
		// 			$j = 0; // cant de veces que pasa -1
		// 			for ($i = 0; $i < count($arrVals); $i++) {
		// 				$this->log .= "arrVals[i]['pasarela'] = " . $arrVals[$i]['pasarela'] . " i = $i\n<br>";
		// 				if ($arrVals[$i]['pasarela'] != '58')
		// 					break;
		// 				$j++;
		// 			}
		// 			if ($j == $nuM) {
		// 				$npas = '63'; // lo paso a CaixaGeral 3D
		// 			} else {
		// 				$npas = '58'; // lo dejo en Navarra
		// 			}
		// 			break;
		// 		case '63': // si es CaixaGeral 3D
		// 			$npas = '67'; // lo paso a BancaMarch 3D DCC !!!!Se quita este TPv de la rotación de Euros y Divisas
		// 			//$npas = '73'; // lo paso a Triodos
		// 			break;
		// 		case '67': // si es BancaMarch 3D DCC
		// 			$nuM = 2; // número de operaciones
		// 			$this->db("select pasarela from tbl_transacciones where moneda = '978' and idcomercio = '129025985109' order by fecha desc limit 0,$nuM");
		// 			$arrVals = $this->temp->loadAssocList();
		// 			// print_r($arrVals);
		// 			$j = 0; // cant de veces que pasa -1
		// 			for ($i = 0; $i < count($arrVals); $i++) {
		// 				$this->log .= "arrVals[i]['pasarela'] = " . $arrVals[$i]['pasarela'] . " i = $i\n<br>";
		// 				if ($arrVals[$i]['pasarela'] != '67')
		// 					break;
		// 				$j++;
		// 			}
		// 			if ($j == $nuM) {
		// 				//$npas = '12'; // lo paso a Abanca 3D
		// 				$npas = '73'; // lo paso a Triodos 3D
		// 			} else {
		// 				$npas = '67'; // lo dejo en BancaMarch 3D DCC
		// 			}
		// 			break;
		// 		case '73': // si es Triodos 3D
		// 			$npas = '92'; // lo paso a Xilema 3D
		// 			break;
		// 		case '92': // si es Xilema 3D
		// 		$npas = '12'; // lo paso a Abanca 3D
		// 		$npas = '23'; //  lo paso a Bankia3 3D DCC se saca abanca de la rotación
		// 			break;
		// 		default:
		// 			$npas = '29'; // lo paso Sabadell Plus 3D DCC
		// 	}
		// 	$this->log .= "npas = " . $npas . "\n<br>";
		// 	// trigger_error ("Cambia de la pasarela $pasEurCub a la $npas ".$this->log, E_USER_WARNING);
		// 	actSetup($npas, 'pasEurCub');
		// }

		// Todo lo de Abanca que no sea Euros pasarlo por Sabadell2 3D 20141013
		// if ($this->pasa == 12 && $this->mon != '978') $npas = 31;
		// //Todo el EUR de bankia4 3d y bankia5 3d pasarlo a bankia DCC 20141013
		// if (($this->pasa == 32 || $this->pasa == 41) && $this->mon == '978') $npas = 23;
		// //Todo el EUR de sabadell2 3d pasarlo a sabadell DCC 20141013
		// if ($this->pasa == 31 && $this->mon == '978') $npas = 29;
		// //Todo el EUR de Caixabank pasarlo a ING 20141013
		// if ($this->pasa == 38 && $this->mon == '978') $npas = 42;

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
			$this->datPas['tipo'] = 'xml';
			echo $this->datPas['tipo'];
			$forma .= '<form name="envia" action=";urlPasarela;" method="post">';
			$forma .= "<input type=\"$est\" name=\"entrada\" value=\"";
			$forma .= "<DATOSENTRADA><DS_Version>0.1</DS_Version>";
			// print_r($arrval);
			foreach ($arrval as $key => $value) {
				$forma .= "<" . strtoupper($key) . ">\n$value\n</" . strtoupper($key) . ">\n";
			}
		} else {
			if ($this->datPas['tipo'] == 'form' || $this->datPas['tipo'] == 'melt') {
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
		//error_log("FORMA=".$forma);

		return $forma;
	}

	/**
	 * Finaliza el formulario de envío
	 * @return string
	 */
	private function finForm() {
		$sale = "";
		if ($this->datPas['tipo'] == 'form' || $this->datPas['tipo'] == 'melt' || $this->datPas['tipo'] == 'xml') {
			echo $this->datPas['tipo'];
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
			$this->log .= "entra en @\n<br>";
			$arrPas = explode('@', $this->datPas['datPas']);

			switch ($arrPas[0]) {
				case 'pasoA':
					($idioma == 'es') ? $idioma = '001' : $idioma = '002';
					($this->opr == 'D') ? $tipoTrans = '3' : $tipoTrans = '0';
					$message = $imp . $tr . $this->datPas['comercio'] . $this->mon . $tipoTrans . $urlcomercio . $this->datPas['clave'];

					if ($this->pasa == 91 && isset($this->datAis['idremitente'])) { //Para la pasarela de Redsys que sacó Titanes

						$this->db("select idtitanes, fechaDocumento from tbl_aisCliente where idcimex = " . $this->datAis['idremitente']);
						if (!$this->temp->f('idtitanes') > 0) {
							$this->err = "El cliente no existe en la Base de datos";
							return false;
						}
						if ($this->temp->f('fechaDocumento') < time()) {
							$this->err = "Ha caducado el documento de identificaci&oacute;n deber&aacute; renovarlo";
							return false;
						}
						$this->db("select id from tbl_aisBeneficiario where idcimex = '" . $this->datAis['iddestin'] . "'");
						if ($this->temp->num_rows() == 0) {
							$this->err = "El beneficiario no existe en la Base de datos";
							return false;
						}
						$this->db("insert into tbl_aisOrden (idtransaccion, idcliente, idbeneficiario, envia, recibe, comision, subida, idrazon) values ('$tr',
									(select id from tbl_aisCliente where idcimex = '" . $this->datAis['idremitente'] . "'),
									(select id from tbl_aisBeneficiario where idcimex = '" . $this->datAis['iddestin'] . "'),
									{$this->datAis['importenvia']}, {$this->datAis['importerecive']}, {$this->datAis['comision']}, 0, {$this->datAis['rason']})");
					}
					break;
				case 'pasoB': // Tefpay Fincimex
					$this->db("select id from tbl_aisCliente where idcimex = '" . $this->datAis['idremitente'] . "'");
					if ($this->temp->num_rows() == 0) {
						$this->err = "El cliente no existe en la Base de datos";
						return false;
					}
					$this->db("select id from tbl_aisBeneficiario where idcimex = '" . $this->datAis['iddestin'] . "'");
					if ($this->temp->num_rows() == 0) {
						$this->err = "El beneficiario no existe en la Base de datos";
						return false;
					}

					$this->db("insert into tbl_aisOrden (idtransaccion, idcliente, idbeneficiario, envia, recibe, comision, subida, idrazon) values ('$tr',
								(select id from tbl_aisCliente where idcimex = '" . $this->datAis['idremitente'] . "'),
								(select id from tbl_aisBeneficiario where idcimex = '" . $this->datAis['iddestin'] . "'),
								{$this->datAis['importenvia']}, {$this->datAis['importerecive']}, {$this->datAis['comision']}, 0, {$this->datAis['rason']})
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
						return false;
					}
					if ($this->temp->f('fechaDocumento') < time()) {
						$this->err = "Ha caducado el documento de identificaci&oacute;n deber&aacute; renovarlo";
						return false;
					}
					$idremitente = $this->temp->f('idtitanes');
					$this->db("select ciudad, idtitanes from tbl_aisBeneficiario where idcimex = " . $this->datAis['iddestin']);
					if (!$this->temp->f('idtitanes') > 0) {
						$this->err = "El beneficiario no se ha inscrito";
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
				case 'pasoJ':
					$operation = 1;
					$this->mon = $this->datPas['moneda'];
					$message = $this->datPas['comercio'] . $this->datPas['terminal'] . $operation . $tr . $imp . $this->mon . md5("{$this->datPas['clave']}");
					$this->log .= "usuario=" . $this->datPas['variant'] . "<br>";
					$this->log .= "{$this->datPas['comercio']} . {$this->datPas['terminal']} . $operation . $tr . $imp . {$this->mon} .
						md5(" . $this->datPas['clave'] . ")<br>";
					$urldirOK = "ver.php?resp=$tr" . '&est=ok';
					$urldirKO = "ver.php?resp=$tr" . '&est=ko';

					$this->db("select count(*) total from tbl_reserva where id_comercio = '{$this->comer}' and codigo = '{$this->tran}'");
					if ($this->temp->f('total') == 0) // si la operación viene de una web externa la inserto en la pag usuarios para un futuro pago recurrente
						$this->db("insert into tbl_usuarios values (null, '$tr', unix_timestamp(), '" . $this->email . "', null)");
					break;
				case 'pasoK': // Tefpay
					$tr .= '000000000';
					if ($this->segura == 1)	$tipoTrans = 1;
					else $tipoTrans = 22;
					//$tipoTrans = 22;
					$message = $imp . $this->datPas['comercio'] . $tr . $urlcomercio . $this->datPas['clave'];
					break;
				case 'pasoL': //Xilema
					$imp = $imp / 100;
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
								"currency" => array(
									"numCode"		=> $this->mon
								)
							),
							"additionals" => array(
								"configuration" => array(
									"language"		=> $idioma,
									"styleName"		=> "bidaiondo",
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
			}
			$this->log .= $message . "\n<br>";

			switch ($arrPas[1]) {
				case 'firmaA':
					$Digest = strtoupper(sha1($message));
					break;
				case 'firmaB':
					$Digest = sha1($message);
					break;
				case 'firmaE':
					$Digest = hash('sha256', $message);
					break;
				case 'firmaC':
					$Digest = md5($message);
					break;
				case 'firmaD':
					$Digest = hash_hmac("sha512", $ret, $this->datPas['clave']);
				case 'firmaF':
					$Digest = hash("sha512", $message);
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
			';T;' => 'T',
			';pasar;' => $this->pasa,
			';secu;' => $this->datPas['secure'],
			';jetid;' => $this->datPas['variant'],
			';cenauto;' => $this->datPas['idcenauto'],
			';idusr;' => $this->idusr,
			';tkusr;' => $this->tkusr,
			';email;' => $this->email,
			';comnomb;' => $this->datCom['nombre'],
			';isoDate;' => date(DATE_ISO8601),
			';Xitoken;' => $arrayXil->token
		);

		if ($arrayXil->tpvUrl) {
			$arrVals[';urlPasarela;'] = $arrayXil->tpvUrl;
		}
		if ($arrPas[1] != 'firmaX') {
			foreach ($arrVals as $key => $value) {
				$cad = str_replace($key, $value, $cad);
			}
		} else {
			$cad = $this->varRedsys();
		}
		if (is_array($cad)) {
			foreach ($cad as $key => $value) {
				$this->log .= $key . " -> " . $value;
			}
		}
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
					and p.tipo = 'P'";

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
	private function varRedsys() {

		$amount = $this->imp;
		$id = $this->idTrn;
		$fuc = $this->datPas['comercio'];
		$moneda = $this->mon;
		$trs = '0';
		$termi = $this->datPas['terminal'];

		if ($this->datPas['tipo'] == 'melt') {
			$urlcom		= str_replace("paid.php", "llegada.php", $this->datPas['url']);
			$urlok		= str_replace("paid.php", "", $this->datPas['url']) . "ver.php?resp=$id" . '&est=ok';
			$urlko		= str_replace("paid.php", "", $this->datPas['url']) . "ver.php?resp=$id" . '&est=ko';
		} else {
			$urlcom = _URL_COMERCIO;
			$urlok = _URL_DIR . "index.php?resp=$id" . '&est=ok';
			$urlko = _URL_DIR . "index.php?resp=$id" . '&est=ko';
		}

		$this->log .= "<br>\nDS_MERCHANT_AMOUNT=$amount";
		$this->log .= "<br>\DS_MERCHANT_ORDER=" . strval($id);
		$this->log .= "<br>\DS_MERCHANT_MERCHANTCODE=$fuc";
		$this->log .= "<br>\DS_MERCHANT_CURRENCY=$moneda";
		$this->log .= "<br>\DS_MERCHANT_TRANSACTIONTYPE=$trs";
		$this->log .= "<br>\DS_MERCHANT_TERMINAL=$termi";
		$this->log .= "<br>\DS_MERCHANT_MERCHANTURL=$urlcom";
		$this->log .= "<br>\DS_MERCHANT_URLOK=$urlok";
		$this->log .= "<br>\DS_MERCHANT_URLKO=$urlko";
		$this->log .= "<br>\DS_MERCHANT_PAYMETHODS=T";

		$this->obj->setParameter("DS_MERCHANT_AMOUNT", $amount);
		$this->obj->setParameter("DS_MERCHANT_ORDER", strval($id));
		$this->obj->setParameter("DS_MERCHANT_MERCHANTCODE", $fuc);
		$this->obj->setParameter("DS_MERCHANT_CURRENCY", $moneda);
		$this->obj->setParameter("DS_MERCHANT_TRANSACTIONTYPE", $trs);
		$this->obj->setParameter("DS_MERCHANT_TERMINAL", $termi);
		$this->obj->setParameter("DS_MERCHANT_MERCHANTURL", $urlcom);
		$this->obj->setParameter("DS_MERCHANT_URLOK", $urlok);
		$this->obj->setParameter("DS_MERCHANT_URLKO", $urlko);
		$this->obj->setParameter("DS_MERCHANT_PAYMETHODS", 'T');
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
