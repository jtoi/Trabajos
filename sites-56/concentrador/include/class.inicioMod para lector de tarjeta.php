<?php
defined( '_VALID_ENTRADA' ) or die( 'Restricted access' );
/*
 * Class que trabaja todas las funciones del index
 */


/**
 * Description of newPHPClass
 *
 * @author julio
 */
class inico {
	/**pasarela*/
	var		$pasa	= '';
	/**identificador de comercio*/
	var		$comer	= '';
	/**identificador de la transaccion desde el comercio*/
	var		$tran	= '';
	/**moneda*/
	var		$mon	= '';
	/**importe*/
	var		$imp	= '';
	/**tipo de operacion D=devolucion, P=pago */
	var		$opr	= '';
	/**idioma*/
	var		$idi	= '';
	/**error*/
	var		$err	= '';
	/**log*/
	var		$log	= '';
	/**firma enviada*/
	var		$frma	= '';
	/**ip desde la que se realiza el pago*/
	var		$ip		= '';
	/**identificador del país desde el que se realiza el pago*/
	var		$idpais	= '';
	/**si la operación no viene desde un tpvv*/
	var		$tpv	= 0;
	/**identificador de la operación en el Concentrador*/
	var		$idTrn	= '';
	/*nombre de la Pasarela usada*/
	var		$pasaN = '';
	/*pago  con Amex*/
	var		$amex = 0;
	/*contador para salirse de los lazos de las pasarelas no seguras*/
	var		$cuenta = 0;
	
	/**conxión a la BD*/
	var		$temp	= '';
	/**conx al correo*/
	var		$corr	= '';
	
	/**datos del comercio*/
	var		$datCom	= array();
	/**datos de la pasarela*/
	var		$datPas	= array();
	/**datos de Ais*/
	var		$datAis = array();
	/**datos de tarjeta*/
	var		$datTar = '';
	/**arrayDatos de la tarjeta*/
	var		$arrTar = array();
	
	function __construct() {
		$this->temp = new ps_DB();
		$this->corr = new correo;
	}
	
	/**
	 * Chequea o determina la pasarela a usar
	 * @return boolean
	 */
	function cheqPas() {
		$this->log = "\n<br>Chequeo de la pasarela\n<br>";
		if ($this->datCom['estado'] == 'D') { //si el comercio está en desarrollo lo mando a la pasarela de desarrollo
			$this->pasa = 1;
			$this->log .= "Comercio en desarrollo lo pongo en la pasarela de desarrollo\n<br>";
		} else { //si el comercio no está en desarrollo
			if ($this->pasa > 0) { //el comercio envió la pasarela en los datos, revisar si está autorizada
				if (!stristr($this->datCom['pasarela'].',', $this->pasa.',') && !stristr($this->datCom['pasarelaAlMom'].',', $this->pasa.',')) {
				// reviso si el comercio está autorizado a usar esa pasarela que envía
					$this->log .= "La pasarela ".$this->pasa." no se encuentra en la cadena de pasarelas ".$this->datCom['pasarela'].", o ".
					"en la cadena ".$this->datCom['pasarelaAlMom'].",\n<br>";
					$this->err = "falla por pasarela inv&aacute;lida";
					return FALSE;
				
				}

				//chequeo si la pasarela que envían es válida
				$this->db("select idPasarela, nombre from tbl_pasarela where activo = 1 and idPasarela = ".$this->pasa);
				$this->pasaN = $this->temp->f('nombre');
				if ($this->temp->num_rows() == 0) { // la pasarela no existe o es inválida
					$this->log .= "La pasarela ".$this->pasa." no es válida o no existe\n<br>";
					$this->err = "falla por pasarela inv&aacute;lida";
					return FALSE;
				}
			} else { //el comercio no envía la pasarela hay que determinarla
// 				if (stristr($this->datCom['pasarela'], ',') ) { //chequeo si el comercio debe enviar la pasarela
// 					$this->log .= "El comercio tiene este listado de posibles pasarelas ".$this->datCom['pasarela'].", sin embargo en ".
// 									"los datos no envió la pasarela \n<br>";
// 					$this->err = "falla por pasarela inv&aacute;lida, su comercio debe especificar pasarela";
// 					return FALSE;
// 				}

				//reviso si el comercio está dentro de los comercios cuya pasarela rota por horas
				//y ahora mismo tiene una pasarela activa
				$this->db("select id from tbl_rotComPas where idcom = ".$this->datCom['id']." and activo = 1");
				$numpas = $this->temp->num_rows();
				if ($numpas > 0) {
					$this->log .= "El comercio hace cambios de pasarela por horas se escoge una\n<br>";
					//trato de buscar si hay alguna activa en este momento
					$this->db("select idpasarela from tbl_rotComPas where idcom = ".$this->datCom['id']." and activo = 1 and fecha > ".
							time());
					if ($this->temp->num_rows() > 0) {
						$this->pasa = $this->temp->f('idpasarela');
					} else { // es tiempo de cambio de pasarela
						$this->db("select id, horas, idpasarela from tbl_rotComPas where idcom = ".$this->datCom['id']." and activo = 1 ".
									"and orden = (select case when orden = $numpas then 1 else orden+1 end from tbl_rotComPas r, ".
									"tbl_comercio c where c.id = r.idcom and r.idpasarela = c.pasaRot)");
						if ($this->temp->num_rows() == 1) {
							$this->pasa = $this->temp->f('idpasarela');
							
							//modifica la fecha en dependencia de las horas que estará activa
							$this->db("update tbl_rotComPas set fecha = ".(time()+(60*60*$this->temp->f('horas')))." where id = ".
									$this->temp->f('id'));
							$this->db("update tbl_comercio set pasaRot = ".$this->pasa." where id = ".$this->datCom['id']);
						} else {
							$this->log .= "Revisar en la query anterior que no retorna un valor";
							$this->err = "Ha habido un error en la selecci&oacute;n de la pasarela de pago. Contacte a su comercio\n<br>";
							return FALSE;
						}
					}
				} else {//el comercio no hace cambios de pasarelas por horas se escoge la pasarela del comercio
					$this->pasa = $this->datCom['pasarela'];
				}
			}
		}
		$this->log .= "Revisa que la pasarela escogida no tenga cambios por monedas\n<br>";
		$this->cambPasporMon();
		if ($this->CheqLimites() == false) return false;
		$this->log .= "Pasarela = {$this->pasa}\n<br>";
		
		$this->log .= "Verificación de la combinación Pasarela - Moneda y captación de datos\n<br>";
		if ($this->opr == 'D') {
			$this->log .= "Es una devolución, se captan los datos\n<br>";
			$this->db("select c.terminal, c.clave, c.comercio, p.nombre, c.datos variant, a.tipo, p.datos datPas, p.idcenauto, m.moneda, "
					. "urlXml url, a.datos, p.comercio comNomb "
					. "from tbl_colPasarMon c, tbl_pasarela p, tbl_cenAuto a, tbl_moneda m "
					. "where m.idmoneda = c.idmoneda and c.idpasarela = p.idPasarela and a.id = p.idcenauto and c.idpasarela = ".
					$this->pasa." and c.idmoneda = '".$this->mon."'");
		} else {
			$this->db("select c.terminal, c.clave, c.comercio, p.nombre, c.datos variant, a.tipo, p.datos datPas, p.idcenauto, m.moneda, "
						. "case p.estado when 'P' then a.urlPro else a.urlDes end url, a.datos, p.comercio comNomb "
						. "from tbl_colPasarMon c, tbl_pasarela p, tbl_cenAuto a, tbl_moneda m "
						. "where m.idmoneda = c.idmoneda and c.idpasarela = p.idPasarela and a.id = p.idcenauto and c.idpasarela = ".
							$this->pasa." and c.idmoneda = '".$this->mon."'");
		}
		if ($this->temp->num_rows() > 0) {
			$arrVal = $this->temp->loadAssocList();
			$this->datPas = $arrVal[0];
// 			print_r($this->datPas);
		} else {
			$this->err = "falla por moneda en ".$this->pasaN;
			$this->log .= "falla por moneda en".$this->pasaN."\n<br>";
			return FALSE;
		}
		return TRUE;
	}
	
	/**
	 * Chequeo de los límites por pasarelas
	 * @return boolean
	 */
	private function CheqLimites($verif = true) {
		$pase = true;
		$causa = '';
		$datPas = array();
		$q = "select nombre, LimMinOper, LimMaxOper, LimDiar, LimMens, LimAnual, LimOperIpDia, LimOperTarDia";
		if ($verif) 
			$q .= ", secure";
		$q .= " from tbl_pasarela where idPasarela = ".$this->pasa;
		$this->db($q);
		$arrV = $this->temp->loadAssocList();
		$datPas = $arrV[0];
		
		//Variación para rotar todas las operaciones de pasarelas No Seguras entre las pasarelas 44 - Sabadell3, 52 - Bankia6
		//y luego que estas dos se llenen empezar a pasarlas por 20 - Caixa
		if (isset($datPas['secure']) && $datPas['secure'] == 0) {
			if ($this->pasa != 44) {
				$this->pasa = 44;
				$this->CheqLimites(false);
			}
		}

// 		Chequeo de límite mínimo por operación
		$this->log .= "Chequeo de límite mínimo por operación\n<br>";
		if (($this->imp/100) <= $datPas['LimMinOper']) {
			$mes = "Se ha producido la operación {$this->tran} con un valor de ".($this->imp/100)." menor que el límite mínimo permitido por operación"
					." para esta la pasarela".$datPas['nombre']." identificador ".$this->pasa;
			$this->corr->todo(44, 'Alerta por límites', $mes);
			$this->log .= $this->err = "La operación {$this->tran} tiene un valor por debajo del límite mínimo permitido para este TPV\n<br>";
			$pasV = $this->pasa;
			$pase = false;
			$causa = 'mínimo por operación';
			if ($datPas['secure'] == 1)	return false;
		}

// 		Chequeo de límite máximo por operación
		if ($pase) {
			$this->log .= "Chequeo de límite máximo por operación\n<br>";
			if (($this->imp/100) > $datPas['LimMaxOper']) {
				$mes = "Se ha producido la operación {$this->tran} con un valor de ".($this->imp/100)." mayor que el límite máximo permitido por operación"
						." para esta la pasarela".$datPas['nombre']." identificador ".$this->pasa;
				$this->corr->todo(44, 'Alerta por límites', $mes);
				$this->log .= $this->err = "La operación {$this->tran} tiene un valor por encima del límite máximo permitido para este TPV\n<br>";
				$pasV = $this->pasa;
				$pase = false;
				$causa = 'máximo por operación';
				if ($datPas['secure'] == 1)	return false;
			}
		}
		
// 		Chequeo de límite cantidad de transacciones por tarjeta

// 		Chequeo de límite cantidad de transacciones por Ip
		if ($pase) {
			$this->log .= "Chequeo del número de transacciones para una IP\n<br>";
			$q = "select count(t.idtransaccion) 'total'
					FROM tbl_transacciones t 
						where t.estado in ('A','V','B','R') 
							and t.tipoEntorno = 'P' 
							and t.fecha > unix_timestamp('".date('Y')."-".date('m')."-".date('d')." 00:00:00')
							and t.ip = '{$this->ip}'
							and t.pasarela = ".$this->pasa;
			$this->db($q);
			if ($this->temp->f('total') >= $datPas['LimOperIpDia']) {
				$mes = "Se ha arribado al límite máximo de operaciones por IP al día que es de {$datPas['LimOperIpDia']} para esta la pasarela"
				.$datPas['nombre']." identificador ".$this->pasa;
				$this->corr->todo(44, 'Alerta por límites', $mes);
				$this->log .= $this->err = "Alcanzado el límite máximo de operaciones para esta IP en este TPV\n<br>";
				$pasV = $this->pasa;
				$pase = false;
				$causa = 'máximo operaciones por IP';
				if ($datPas['secure'] == 1)	return false;
			}
		}

// 		Chequeo de límite diario
		if ($pase) {
			$this->log .= "Chequeo de límite diario\n<br>";
			$q = "select sum(case t.estado 
							when 'B' then if (t.fecha_mod < t.fechaPagada, (-1 * ((t.valor_inicial-t.valor)/100/t.tasa)), t.valor/100/t.tasa)
							when 'V' then if (t.fecha_mod < t.fechaPagada, (-1 * ((t.valor_inicial-t.valor)/100/t.tasa)), t.valor/100/t.tasa)
							when 'R' then if (t.fecha_mod < t.fechaPagada, (-1 * ((t.valor_inicial-t.valor)/100/t.tasa)), t.valor/100/t.tasa)
							when 'A' then (t.valor/100/t.tasa) 
							else '0.00' end) 'valor'
					FROM tbl_transacciones t 
					where t.estado in ('A','V','B','R') 
						and t.tipoEntorno = 'P' 
						and t.fecha > unix_timestamp('".date('Y')."-".date('m')."-".date('d')." 00:00:00')
						and t.pasarela = ".$this->pasa;
			$this->db($q);
			if (($this->temp->f('valor')+$this->imp/100) >= $datPas['LimDiar']) {
				$this->log .= "Alcanzado límite diario\n<br>";
				$pasV = $this->pasa;
				$pase = false;
				$causa = 'montos diarios';
				if ($datPas['secure'] == 1)	return false;
			}
		}
			
// 		Chequeo de límite mensual
		if ($pase) {
			$this->log .= "Chequeo de límite mensual\n<br>";
			$q = "select sum(case t.estado 
						when 'B' then if (t.fecha_mod < t.fechaPagada, (-1 * ((t.valor_inicial-t.valor)/100/t.tasa)), t.valor/100/t.tasa)
						when 'V' then if (t.fecha_mod < t.fechaPagada, (-1 * ((t.valor_inicial-t.valor)/100/t.tasa)), t.valor/100/t.tasa)
						when 'R' then if (t.fecha_mod < t.fechaPagada, (-1 * ((t.valor_inicial-t.valor)/100/t.tasa)), t.valor/100/t.tasa)
						when 'A' then (t.valor/100/t.tasa) 
						else '0.00' end) 'valor'
				FROM tbl_transacciones t 
				where t.estado in ('A','V','B','R') 
					and t.tipoEntorno = 'P' 
					and t.fecha > unix_timestamp('".date('Y')."-".date('m')."-01 00:00:00')
					and t.pasarela = ".$this->pasa;
			$this->db($q);
			if (($this->temp->f('valor')+$this->imp/100) >= $datPas['LimMens']) {
				$this->log .= "Alcanzado límite mensual\n<br>";
				$pasV = $this->pasa;
				$pase = false;
				$causa = 'monto mensual';
				if ($datPas['secure'] == 1)	return false;
			}
		}

// 		Chequeo de límite anual
		if ($pase) {
			$this->log .= "Chequeo de límite anual\n<br>";
			$q = "select sum(case t.estado 
						when 'B' then if (t.fecha_mod < t.fechaPagada, (-1 * ((t.valor_inicial-t.valor)/100/t.tasa)), t.valor/100/t.tasa)
						when 'V' then if (t.fecha_mod < t.fechaPagada, (-1 * ((t.valor_inicial-t.valor)/100/t.tasa)), t.valor/100/t.tasa)
						when 'R' then if (t.fecha_mod < t.fechaPagada, (-1 * ((t.valor_inicial-t.valor)/100/t.tasa)), t.valor/100/t.tasa)
						when 'A' then (t.valor/100/t.tasa) 
						else '0.00' end) 'valor'
				FROM tbl_transacciones t 
				where t.estado in ('A','V','B','R') 
					and t.tipoEntorno = 'P' 
					and t.fecha > unix_timestamp('".date('Y')."-01-01 00:00:00')
					and t.pasarela = ".$this->pasa;
			$this->db($q);
			if (($this->temp->f('valor')+$this->imp/100) >= $datPas['LimAnual']) {
				$this->log .= "Alcanzado límite anual\n<br>";
				$pasV = $this->pasa;
				$pase = false;
				$causa = 'monto anual';
				if ($datPas['secure'] == 1)	return false;
			}
		}

		//cambio la pasarela por una de Amex si el valor de amex es = 1
		if ($this->amex == 1) {
			if ($datPas['secure'] == 1) $this->pasa = 38;
			else $this->pasa = 44;
			$pase = true;
		}
		
		$this->log .= "Pase = $pase<br>\n";
		if ($pase == false) {
			if ($this->pasa == 44) $this->pasa = 53;
			elseif ($this->pasa == 53) $this->pasa = 52;
			elseif ($this->pasa == 52) $this->pasa = 56;
			elseif ($this->pasa == 56) $this->pasa = 20;
			
			if ($this->cuenta > 3) {
				$this->cuenta = 0;
				$this->err = "falla por Montos que no se ajustan a la pasarela, por favor usar pasarela Segura";
				return false;
			} else $this->cuenta++;
				
			$mes = "La pasarela ".$datPas['nombre']." identificador ".$pasV." ha llegado al tope por $causa, esta operación ({$this->tran}) pasará a "
					."la pasarela".$this->pasa;
			$this->corr->todo(44, 'Alerta por límites', $mes);
			$this->log .= "La pasarela {$datPas['nombre']} ha llegado al tope por $causa la operación {$this->tran} pasó a la pasarela {$this->pasa}\n<br>";
			$this->CheqLimites(false);
		}
		return true;
	}
	
	/**
	 * Inserta la operación en la BD
	 * @return boolean
	 */
	function operacion() {
		$idpais = 'null';
		$this->log = "\n<br>Determina el país desde donde se está realizando la operación\<br>";
		if (function_exists(geoip_country_code3_by_name)) {
			if (strlen(geoip_country_code3_by_name($this->ip)) > 0) {
				$this->db("select id from tbl_paises where iso = '".geoip_country_code3_by_name($this->ip)."'");
				$idpais = $this->temp->f('id');

				if($this->temp->num_rows() === 0){
					$accN = $this->db("insert into tbl_paises (nombre, iso) values ('".geoip_country_name_by_name($this->ip)."', '".
							geoip_country_code3_by_name($this->ip)."')");
					if ($accN === false) return FALSE;

					$accN = $this->db("select id from tbl_paises where iso = '".geoip_country_code3_by_name($this->ip)."'");
					if ($accN === false) return FALSE;
					$idpais = $this->temp->f('id');
				}
			}
		}
		
		//si se envían los datos de la tarjeta se procesan para salvarlos en la BD
		if($this->datTar) $this->tarjeta();
		
		
		if ($this->opr == 'P') {
			$this->idTrn = trIdent(true); //Genera el identificador de la transacción
			$this->log .= "\n<br>Inserta la operación\<br>";
			$accN = $this->db("insert into tbl_transacciones (idtransaccion, idcomercio, identificador, tipoOperacion, fecha, fecha_mod, ".
							"valor_inicial, tipoEntorno, moneda, estado, sesion, idioma, pasarela, ip, idpais, tpv, tarjetas) ".
						"values ('".$this->idTrn."', '{$this->comer}', '{$this->tran}', '{$this->opr}', ".time().", ".time().", ".
							"{$this->imp}, '{$this->datCom['estado']}', {$this->mon}, 'P', '{$this->frma}', '{$this->idi}', {$this->pasa}, ".
							"'{$this->ip}', $idpais, '{$this->tpv}', '{$this->arrTar[0]}')");
			if ($accN === false) return FALSE;
			//actualiza la pasarela en la tbl_reserva segun el último cambio realizado
			$this->db("update tbl_reserva set pasarela = {$this->pasa} where id_comercio = '{$this->comer}' and codigo = '{$this->tran}'");
	
			if ($this->comer == '527341458854') {
				if (!$this->db("insert into tbl_aisDato values (null, '{$this->idTrn}', '{$this->tran}', ".time().", 
					'{$this->datAis['idremitente']}', '{$this->datAis['nombremitente']}', '{$this->datAis['aperemitente']}', 
					'{$this->datAis['tipodoc']}', '{$this->datAis['numerodoc']}', '{$this->datAis['direcremitente']}', 
					'{$this->datAis['iddestin']}', '{$this->datAis['nombredestin']}', '{$this->datAis['apelldestin']}')"))
					return false;
			}
		}
		return $this->CAprueba();
	}
	
	/**
	 * Chequeo del comercio
	 * @return boolean
	 */
	function verComer() {
		$this->log = "\n<br>Verifica la validez del comercio\n<br>";
// 		if (time() >= mktime(0, 0, 1, 8, 1, 2014) and $this->comer == '527341458854') {
// 			$this->err = "Comercio inválido";
// 			$this->log .= "Ha tratado de entrar una operación de AIS";
// 			return false;
// 		}
		
		$this->db(sprintf("select id, nombre, prefijo_trans, estado, pasarela, pasarelaAlMom, url from tbl_comercio where activo = 'S' ".
							"and idcomercio = '%s'", $this->comer));
		
		if ($this->temp->num_rows() > 0) {
			$arrVal = $this->temp->loadAssocList();
			$this->datCom = $arrVal[0];
//			print_r($this->datCom);
		} else {
			$this->err = "falla por comercio";
			$this->log .= "falla por comercio\n<br>";
			return FALSE;
		}
		return TRUE;
	}
	
	/**
	 * Verifica la firma de la operación
	 * @return boolean
	 */
	function verFir() {
		$this->log = "\n<br>Verifica la firma de la operación\n<br>";
		$Calc = convierte($this->comer, $this->tran, $this->imp, $this->mon, $this->opr);
		$this->log .= "Firma recibida {$this->frma}\n<br>";
		$this->log .= "Firma generada {$Calc}\n<br>";
		if ($Calc != $this->frma) {
			$this->log .= "falla por firma\n<br>";
			$this->err = "falla por firma";
			return false;
		}
		return TRUE;
	}
	
	/**
	 * Verificación de las operaciones desde el comercio
	 * @return boolean
	 */
	function verTran() {
		$this->log = "\n<br>Verifica que la operación no se haya repetido anteriormente\n<br>";
		$this->db("select sesion, from_unixtime(fecha,'%d/%m/%y %H:%i:%s') fc from tbl_transacciones where identificador = '".$this->tran.
				"' and idcomercio = '".$this->comer."'");
		
		if ($this->temp->f('sesion') == $this->frma) {
			$this->err = "Transacci&oacute;n duplicada. P&iacute;dale a su comercio la genere nuevamente.<br>Duplicated transacction. Ask ".
				"your commerce generates it again.";
			$this->log .= "Transacción duplicada ".$this->temp->f('fc')."\n<br>";
			return FALSE;
		}
		return true;
	}
	
	/**
	 * Realiza todas las verificaciones sobre las IPs que envían pagos
	 * @return boolean
	 */
	function verIP() {
		
		$this->log = "\n<br>Verifica que la ip desde donde están pagando no está bloqueada\n<br>";
		$this->db(sprintf("select id from tbl_ipBL where ip='%s'", $this->ip));
		if ($this->temp->num_rows() !== 0) {
			$this->err = 'Su IP est&aacute; bloqueada, contacte a su comercio / Your IP is banned, contact to your e-commerce';
			$this->log .= "Intento de pago desde la IP Bloqueada: ".$this->ip;
			return FALSE;
		}
		
		$this->log .= "\n<br>Verifica que no sean hayan producido mas de ".leeSetup('cantReintentos')." intentos en menos de ".
			leeSetup('minReintento')." minutos\n<br>";
		if (!$this->ipblanca($this->ip)) {
			$this->db(sprintf("select estado from tbl_transacciones
							where idcomercio = '%s'
								and fecha_mod > %d
								and ip = '%s'
							order by fecha_mod limit 0,".leeSetup('cantReintentos'), $this->comer, (time() - (leeSetup('minReintento') * 60)), $this->ip));
			$arrDen = $this->temp->loadResultArray();
			$arrDen = (array_count_values($arrDen));
			if ($arrDen['D'] >= leeSetup('cantReintentos')){
				$this->log .= "Se han producido mas intentos de pagos que los permitidos penalizo la IP\n<br>";
				$this->db("insert into tbl_ipbloq (ip, fecha, identificador, idComercio, bloqueada, idCom) values
								('" . $this->ip . "', " . time() . ", '".$this->tran."', '".$this->comer."', 1, ".$this->datCom['id'].")");
				$mes = "Estimado Administrador \n\nUn cliente ha tratado de realizar el pago de la transacci&oacute;n ".
							"en repetidas ocasiones de forma infructuosa esto ha causado que la IP desde donde ha realizado los intentos ". 
							"({$this->ip}) se haya bloqueado.\n\nSi tiene posibilidad de contactarle, p&iacute;dale por favor que llame al ".
							"banco emisor de la tarjeta y aver&iacute;gue las causas de la negaci&oacute;n del pago.\n\nUna vez que este ".
							"problema se solucione desbloquee esta IP (men&uacute;: Reportes / Ips Bloqueadas) para que el cliente pueda ".
							"usarla nuevamente.\n\nAdminstrador de Sistemas\nAdministrador de Comercios";
				$this->corr->todo(11, 'IP bloqueada', $mes);
				$this->log .= "La ip {$this->ip} ha sido bloqueada";
				$this->err = "Su Ip ha sido bloqueada, no puede realizar mas pagos. Contacte con su comercio.<br><br>Your IP has been banned, ".
					"you can`t make more payments. Contact your commerce.";
				return FALSE;
			}
			
		}
		
		$this->log .= "\n<br>Verifica que la ip no está bloqueada por pagos denegados\n<br>";
		$this->db(sprintf("select idips from tbl_ipbloq where ip = '%s' and bloqueada = 1", $this->ip));
		if ($this->temp->num_rows() !== 0) {
			$this->err = 'Su IP est&aacute; bloqueada, contacte a su comercio / Your IP is banned, contact to your e-commerce';
			$this->log .= "Intento de pago desde la IP Bloqueada: {$this->ip}";
			return FALSE;
		}
		
		return true;
	}
	
	/**
	 * Alertas de Seguridad. Es una función que sólo envía alertas no bloquean operaciones
	 * @return boolean
	 */
	function alerSegur() {
		$this->log .= "\n<br>Verifica que el monto de la operación no está por encima del máximo de peligro\n<br>";
		if ($this->imp >= leeSetup(montoAlerta)*100) {
			$mensage .= "Se est&aacute; realizando una transacci&oacute;n por un monto de ".number_format(($this->imp/100),2,'.',' ');
			$mensage .= " correspondiente al comercio: ".$this->datCom['nombre'];
			$mensage .= "\n<br />Fecha - Hora: ".date('d')."/".date('m')."/".date('Y')." ".date('H').":".date('i').":".date('s');
			$mensage .= "\n<br /><br />";
			$this->corr->todo(10, "Alerta de vigilancia antifraude", $mensage);
			$this->log .= $mensage;
		}
		
		return TRUE;
	}
	
	/**
	 * Determina si la IP desde la que se está realizando el pago
	 * está en el listado de las IPs blancas
	 * @return boolean
	 */
	private function ipblanca() {
		$this->db(sprintf("select id from tbl_ipblancas where ip='%s'", $this->ip));
		
		if ($this->temp->num_rows() == 0) {return false;} else {return true;}
	}
	
	/**
	 * Cambio de pasarelas según la moneda
	 * @return boolean
	 */
	private function cambPasporMon() {
		$npas = 0;
		//Euros de Soy Cubano por IDirect
// 		if($this->pasa === 36 && $this->mon === '978' && $this->comer === '411691546810') {$npas = 42;}
		//Todo el EUR de Cubana lo voy rotando entre las 4 pasarelas 
		if ($this->comer === '129025985109' && $this->mon === '978'){
//			$this->db("select pasarela from tbl_transacciones where idcomercio = '129025985109' and moneda = '978' order by fecha desc limit 0,1");
			$this->log .= "Cambia pasare EUR para Cubana<br>\n";
			switch (leeSetup('pasEurCub')) {
				case '38': //si es CaixaBank2 3D
					$npas = '23'; //lo paso a Bankia3 3D DCC
					break;
				case '23': //si es Bankia3 3D DCC
					$npas = '29'; //lo paso a Sabadell Plus 3D DCC
					break;
				case '29': //si es Sabadell Plus 3D DCC
					$npas = '38'; //lo paso a CaixaBank2 3D
					break;
				default :
					$npas = '29'; //lo paso Sabadell Plus 3D DCC
			}
			actSetup($npas, 'pasEurCub');
		}
		//Todo lo de Abanca que no sea Euros pasarlo por Sabadell2 3D 20141013
// 		if ($this->pasa == 12 && $this->mon != '978') $npas = 31;
//		//Todo el EUR de bankia4 3d y bankia5 3d pasarlo a bankia DCC 20141013
//		if (($this->pasa == 32 || $this->pasa == 41) && $this->mon == '978') $npas = 23;
//		//Todo el EUR de sabadell2 3d pasarlo a sabadell DCC 20141013
//		if ($this->pasa == 31  && $this->mon == '978') $npas = 29;
//		//Todo el EUR de Caixabank pasarlo a ING 20141013
//		if ($this->pasa == 38 && $this->mon == '978') $npas = 42;
		
		//si se cambia aviso y cambio
		if ($npas > 0) {
			$this->log .= "Si, cambia de la pasarela ".$this->pasa." a la pasarela ".$npas."\n<br>";
			$this->pasa = $npas;
		}
		return true;
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
			$this->log = $this->temp->getErrorMsg()."\n<br>";
			$this->err = "Se produjo un error no especificado<br>Contacte con su comercio.";
			return false;
		}
		return TRUE;
	}
	
	/**
	 * Elabora la cadena de envío al TPV de prueba
	 * @return boolean
	 */
	private function CAprueba() {
		$this->log .= "\n<br>Entra al Centro Autorizador\n<br>";
		$forma = '';
		$est = "hidden";
		if (_MOS_CONFIG_DEBUG) {$est = "text";}
		$arrval = (array) json_decode($this->datPas['datos'],true);
		
		if ($this->opr == 'D') {
			$this->datPas['tipo'] = 'xml';
			echo $this->datPas['tipo'];
			$forma .= '<form name="envia" action=";urlPasarela;" method="post">';
			$forma .= "<input type=\"$est\" name=\"entrada\" value=\"";
			$forma .= "<DATOSENTRADA><DS_Version>0.1</DS_Version>";
// 			print_r($arrval);
			foreach ($arrval as $key => $value) {
				$forma .= "<".strtoupper($key).">\n$value\n</".strtoupper($key).">\n";
			}
		} else {
			if ($this->datPas['tipo'] == 'form') {
				$forma .= '<form name="envia" action=";urlPasarela;" method="post">';
				foreach ($arrval as $key => $value) {
					$forma .= "<input type=\"$est\" name=\"$key\" value=\"$value\"/>\n";
				}
			} elseif ($this->datPas['tipo'] == 'iframe') {//echo "entra";
	//			$i=0;
			
			if ($this->pasa == 24 && strlen($this->datTar) > 1)
				$pasito = 	"document.forms[0].elements[0].value = \'".$this->arrTar[1]."\';".
							"document.forms[0].elements[1].value = \'".$this->arrTar[2]."\';".
							"document.forms[0].elements[2].value = \'".$this->arrTar[4]."\';".
							"document.forms[0].elements[3].value = \'".$this->arrTar[3]."\';";
				
// 					$forma .= '<script src="js/jquery.js" type="text/javascript"></script><script language=\'javascript\'>$("#avisoIn").html(\'<iframe '.
// 							'title="titulo" onload="'.$pasito.'" id="ifrm" src="http://localhost/concentrador/iframeload.html?';
		
					$forma .= '<script src="js/jquery.js" type="text/javascript"></script><script language=\'javascript\'>$("#avisoIn").html(\'<iframe '.
						'title="titulo" onload="'.$pasito.'" id="ifrm" src=";urlPasarela;?';
		
				if ($this->pasa != 39) {
					foreach ($arrval as $key => $value) {
		//				echo $i++."<br>";
						$forma .= $key.'='.$value.'&';
					}
				}
				$forma = rtrim($forma, '&');
			}
		}
		$forma = $this->cambVals($forma);
// 		mail('jtoirac@gmail.com', '', $forma);

		$forma .= $this->finForm();
		$this->log .= $forma."\n<br>";
		
		return $forma;
	}
	
	private function finForm() {
		$sale = "";
		if ($this->datPas['tipo'] == 'form' || $this->datPas['tipo'] == 'xml') {
			echo $this->datPas['tipo'];
			if ($this->datPas['tipo'] == 'xml') $sale .= '</DATOSENTRADA>"/>';
			if (_MOS_CONFIG_DEBUG) {$sale .= '<input type="submit" value="Enviar" /></form>';}
			else {$sale .= '</form><script language=\'javascript\'>document.envia.submit();</script>';}
		} elseif ($this->datPas['tipo'] == 'iframe') {
			
			if ($this->pasa == 39)
				$sale .= '" width="100%" height="500" frameborder="0" marginheight="0" marginwidth="0" scrolling="no"></iframe>\');</script>';
			else
				$sale .= '" width="400" height="374" frameborder="0" marginheight="0" marginwidth="0" scrolling="no" style="border: 1px solid #000000; '.
					'padding:0; margin:0"></iframe>\');</script>';
			
// 			if ($this->pasa == 24 && strlen($this->datTar) > 1)
// 				$sale .= '<script language=\'javascript\'>window.onload = function() { var ifrm = document.getElementById(\'ifrm\');'.
// 							'var doc = ifrm.contentDocument? ifrm.contentDocument: ifrm.contentWindow.document;'.
// 							'doc.forms[0].elements[0].value = \''.$this->arrTar[1].'\';'.
// 							'doc.forms[0].elements[1].value = \''.$this->arrTar[2].'\';'.
// 							'doc.forms[0].elements[2].value = \''.$this->arrTar[4].'\';'.
// 							'doc.forms[0].elements[3].value = \''.$this->arrTar[3].'\';'.
// 							'}</script>';
		}
		
		return $sale;
	}
	
	private function cambVals($cad) {
		$this->log .= "Sustituye valores en la cadena\n<br>";
		$imp = $this->imp;
		$tr = $this->idTrn;
		if ($this->mon === '392') {$imp = $imp/100;}
		$urlcomercio = _URL_COMERCIO;
		$urldirOK = _URL_DIR . "index.php?resp=$tr" . '&est=ok';
		$urldirKO = _URL_DIR . "index.php?resp=$tr" . '&est=ko';
		$variantes = explode(',', $this->datPas['variant']);
		$encriptacion = 'SHA1';
		$idioma = $this->idi;
		
		$this->db("select nombre, servicio from tbl_reserva where codigo = '{$this->tran}' and id_comercio = '{$this->comer}'");
		if (!$this->temp->num_rows() == 0) {
			$producto = $this->temp->f('servicio');
			$titular = $this->temp->f('nombre');
		} else {
			$producto = 'Servicio Turistico';
			$titular = 'Nombre';
		}
		
		if (strpos($this->datPas['datPas'], '@')) {
			$this->log .= "entra en @\n<br>";
			$arrPas = explode('@', $this->datPas['datPas']);
			
			switch ($arrPas[0]) {
				case 'pasoA':
					($idioma == 'en') ? $idioma = '002' : $idioma = '001';
					($this->opr == 'D') ? $tipoTrans = '3' : $tipoTrans = '0';
					$message = $imp . $tr . $this->datPas['comercio'] . $this->mon . $tipoTrans . $urlcomercio . $this->datPas['clave'];
					break;
				case 'pasoB':
					$tr .= '000000000';
					$tipoTrans = 40;
					$pagoenable = 0;
					$fechapago = '990101';
					$message = $imp . $this->datPas['comercio'] . $tr . $urlcomercio . $this->datPas['clave'];
					break;
				case 'pasoC':
					$message = $this->datPas['clave'] . $this->datPas['comercio'] . $tr . $this->imp . $this->mon . $encriptacion .
					$urldirOK . $urldirKO;
					break;
				case 'pasoD':
					$ssl= 'SSL';
					($idioma == 'es') ? $idioma = '1' :  $idioma = '6';
					$message = $this->datPas['clave'] . $this->datPas['comercio'] . $variantes[0] . $this->datPas['terminal'] . $tr .
					$imp . $this->mon . $variantes[1] . $encriptacion . $urldirOK . $urldirKO;
					$this->log .= $this->datPas['clave']." . ".$this->datPas['comercio']." . ".$variantes[0]." . ".$this->datPas['terminal']." . ".$tr." . ".
					$imp." . ".$this->mon." . ".$variantes[1]." . ".$encriptacion." . ".$urldirOK." . ".$urldirKO;
					break;
				case 'pasoE':
					$operation = 1;
					$this->mon = $this->datPas['moneda'];
					$message = $this->datPas['comercio'] . $this->datPas['clave'] . $this->datPas['terminal'] . $operation . $tr . $imp .
					$this->mon . md5("{$this->datPas['variant']}");
					$this->log .= "usuario=".$this->datPas['variant']."<br>";
					$this->log .= "{$this->datPas['comercio']} . {$this->datPas['clave']} . {$this->datPas['terminal']} . $operation . $tr . $imp .
					{$this->mon} . md5(".$this->datPas['variant'].")<br>";
					break;
				case 'pasoF':
					$referencia = "M$this->mon" . "$this->imp\r\n1\r\n$tr\r\n$producto\r\n1\r\n$imp\r\n";
					break;
				case 'pasoG':
					($idioma == 'en') ? $idioma = '002' : $idioma = '001';
					$tipoTrans = '0';
					$message = $imp . $tr . $this->datPas['comercio'] . $this->mon . $tipoTrans . $this->datPas['clave'];
					break;
				case 'pasoH'://SIPAY
					$data = array(
							"username"=> $variantes[0],
							"password"=> $variantes[1],
							"apikey"=> $variantes[2],
							"module"=> $variantes[3],
							"authtype"=> $variantes[4],
							"lang"=> $variantes[5],
							"merchantid"=> $variantes[6],
							"ticket"=> $tr,
							"amount"=> complLargo($imp),
							"currency"=> $this->mon,
							"css_url"=>_ESTA_URL.$variantes[7],
							"dstpageid"=> $this->datPas['comercio']
					);

					$options = array(
							CURLOPT_RETURNTRANSFER	=> true,
							CURLOPT_SSL_VERIFYPEER	=> false,
							CURLOPT_POST			=> true,
							CURLOPT_VERBOSE			=> true,
							CURLOPT_URL				=> $this->datPas['url'],
							CURLOPT_SSLCERT			=> $variantes[8],
							CURLOPT_SSLKEY			=> $variantes[9],
							CURLOPT_POSTFIELDS		=> json_encode($data),
							CURLOPT_SSL_VERIFYHOST	=> false
					);
					
					$ch = curl_init();
					curl_setopt_array($ch , $options);
					$salida = curl_exec($ch);
// 						echo "error=".curl_errno($ch);
					if (curl_errno($ch)) $this->log .=  "Error en la resp de Sipay:".curl_strerror(curl_errno($ch))."<br>\n";
					$crlerror = curl_error($ch);
// 						echo "otroerror=".$crlerror;
					if ($crlerror) {$this->err .=  "Error en la resp de Sipay:".$crlerror."<br>\n";}
					$curl_info = curl_getinfo($ch);
					curl_close($ch);
// 						print_r($curl_info);echo "<br><br>";
					
					$arrCurl = json_decode($salida);
					$this->log .= "Datos de Sipay:<br>\n";
					foreach ($arrCurl as $key=>$value){
						$this->log .= $key." = ".$value."<br>\n";
					}
// 					print_r($arrCurl);
					
					
					if($arrCurl->idrequest) {
						$q = "insert into tbl_dataSipay (idtransaccion, idrequest, merchantid) values ('$tr','".$arrCurl->idrequest."','773')";
						$this->db($q);
						return str_replace(';urlPasarela;?', $arrCurl->iframe_src, $cad);
					}
					
					break;
			}
			$this->log .= $message."\n<br>";
				
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
			}
			$this->log .= $Digest."\n<br>";
		} else {
		
			switch ($this->pasa) {
				case '10';case '19';case '20';case '21';case '22';case '23';case '25';case '26';case '27';case '28';case '29';
				case '30';case '31';case '32';case '36';case '38';case '41';case '42';case '43';case '44':
					($idioma == 'en') ? $idioma = '002' : $idioma = '001';
					$tipoTrans = '0';
					$message = $imp . $tr . $this->datPas['comercio'] . $this->mon . $tipoTrans . $urlcomercio . $this->datPas['clave'];
					break;
				case '37':
					$tr .= '000000000';
					$tipoTrans = 40;
					$pagoenable = 0;
					$fechapago = '990101';
					$message = $imp . $this->datPas['comercio'] . $tr . $urlcomercio . $this->datPas['clave'];
					break;
				case '40':
					$message = $this->datPas['clave'] . $this->datPas['comercio'] . $tr . $this->imp . $this->mon . $encriptacion . 
					$urldirOK . $urldirKO;
					break;
				case '12';case '53':
					$ssl= 'SSL';
					($idioma == 'es') ? $idioma = '1' :  $idioma = '6';
					$message = $this->datPas['clave'] . $this->datPas['comercio'] . $variantes[0] . $this->datPas['terminal'] . $tr . 
						$imp . $this->mon . $variantes[1] . $encriptacion . $urldirOK . $urldirKO;
					break;
				case '24':
					$operation = 1;
					$this->mon = $this->datPas['moneda'];
					$message = $this->datPas['comercio'] . $this->datPas['clave'] . $this->datPas['terminal'] . $operation . $tr . $imp . 
						$this->mon . md5("{$this->datPas['variant']}");
					break;
				case '13':
					$referencia = "M$this->mon" . "$this->imp\r\n1\r\n$tr\r\n$producto\r\n1\r\n$imp\r\n";
					break;
			}
			
			switch ($this->pasa) {
				case '10';case '19';case '20';case '21';case '22';case '25';case '26';case '27';case '28';case '29';case '30';case '31';
				case '32';case '36';case '38';case '41';case '43';case '44':
					$Digest = strtoupper(sha1($message));
					break;
				case '12';case '23';case '37';case '40';case '42';case '53':
					$Digest = sha1($message);
					break;
				case '24':
					$Digest = md5($message);
					break;
			}
		}
		
		
		$arrVals = array(';importe;'=>$imp, ';moneda;'=>$this->mon, ';trans;'=>$tr, ';producto;'=>$producto, ';titular;'=>$titular, 
				';idCom;'=>  $this->datPas['comercio'], ';urlcomercio;'=>$urlcomercio, ';urlok;'=>$urldirOK, ';urlko;'=>$urldirKO, 
				';comName;'=>$this->datPas['comNomb'], ';T;'=>'T', ';idioma;'=>$idioma, ';Digest;'=>$Digest, ';terminal;'=>$this->datPas['terminal'], 
				';tipoTrans;'=>$tipoTrans, ';urlPasarela;'=>  $this->datPas['url'], ';adqbin;'=>$variantes[0], ';exponente;'=>$variantes[1], 
				';encriptacion;'=>$encriptacion, ';ssl;'=>$ssl, ';referencia;'=>$referencia, ';code;'=>$this->datPas['clave'], 
				';operation;'=>$operation, ';idremitente;'=>$this->datAis['idremitente'], ';nombremitente;'=>$this->datAis['nombremitente'], 
				';aperemitente;'=>$this->datAis['aperemitente'], ';tipodoc;'=>$this->datAis['tipodoc'], ';numerodoc;'=>$this->datAis['numerodoc'],
				';direcremitente;'=>$this->datAis['direcremitente'], ';iddestin;'=>$this->datAis['iddestin'], ';nombredestin;'=>$this->datAis['nombredestin'],
				';apelldestin;'=>$this->datAis['apelldestin'], ';fechapago;'=>$fechapago, ';pagoenable;'=>$pagoenable);
		
		foreach ($arrVals as $key => $value) {
			$cad = str_replace($key, $value, $cad);
		}
		
		return $cad;
	}
	
	/**
	 * Si los datos de la tarjeta son leídos con lector, son procesados acá
	 * para sacar el número de la misma y salvarlo en la BD
	 * @return string
	 */
	private function tarjeta() {
		$cad = str_replace('%B', '', $this->datTar);
		if (strstr($cad, '&')) $deli = '&'; else $deli = '^';
		$arrDas = explode($deli, $cad);
		$this->arrTar[] = substr($arrDas[0], 0,6).'******'.substr($arrDas[0], -4); //tarjeta ofuscada
		$this->arrTar[] = $arrDas[0]; //tarjeta completa
		$this->arrTar[] = $arrDas[1]; //nombre cliente
		$this->arrTar[] = substr($arrDas[2], 0,2); //año de vencimiento
		$this->arrTar[] = substr($arrDas[2], 2,2); //mes de vencimiento
		
		return $tar;
	}
}
