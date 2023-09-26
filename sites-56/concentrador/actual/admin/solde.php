<?php defined( '_VALID_ENTRADA' ) or die( 'Restricted access' );
$html = new tablaHTML;
global $temp;
$ent = new entrada;
$corCreo = new correo();

$d = $_REQUEST;
$id = $ent->isEntero($d['tf'], 12);
$idtr = $ent->isEntero($d['idtr'], 12);
$devol = $ent->isNumero($d['idtr'], 12);

if (strlen($_SESSION['admin_nom']) == 0 || !isset($_SESSION['admin_nom'])) {
	echo "<script language='JavaScript'>window.open('index.php?componente=core&pag=logout', '_self')</script>";
	exit;
}

if ($id) {
	$arrayTo = array();
	
	$q = "select valor, from_unixtime(t.fecha, '%d/%m/%Y %H:%i') fecha, c.nombre, p.nombre pasarela, c.idcomercio, t.codigo, m.moneda, c.id, 
				t.identificador, t.tpv, p.comercio, t.pasarela idpasarela
			from tbl_transacciones t, tbl_comercio c, tbl_pasarela p, tbl_moneda m
			where m.idmoneda = t.moneda and t.pasarela = idPasarela and  t.idcomercio = c.idcomercio and idtransaccion = '$id'";
	$temp->query($q);
	$val = $temp->loadAssocList();
// 	print_r($val); exit();
	$val = $val[0];
	$idpass = $val['idpasarela'];
	
	$q = "select nombre, email from tbl_admin a, tbl_colAdminComer c ".
			" where c.idComerc = ".$val['id']." and c.idAdmin = a.idadmin and a.idrol = 11 and a.activo = 'S'";
	$temp->query($q);
	$arr = $temp->loadAssocList();
	foreach ($arr as $item){
		$arrayTo[] = array($item['nombre'],$item['email']);
	}
	$arrayTo[] = array($_SESSION['admin_nom'],$_SESSION['email']);

	if ($idtr) {
		$q = "select count(idtransaccion) total from tbl_transacciones where solDev = 0 and idtransaccion = $id";
		$temp->query($q);
		if ($temp->f('total') == 1) {
			//se envia correo a la persona a cargo de las devoluciones en AMF
			$text = "El administrador ".$_SESSION['admin_nom']." con correo ".$_SESSION['email']." del comercio ".$val['nombre']." con identificador ".
						$val['idcomercio'].", solicita la devolución de ".number_format($d['devol'],2)." {$val['moneda']} de la transacción número $id ".
						"que posee código de comercio ".$val['identificador']." y de autorización del banco ".$val['codigo'].
						", la misma fué realizada el día ".$val['fecha'];
			if($d['observ']) $text .= "\n\nDebe tener en cuenta \n".$d['observ'];
			
			$subject = 'Solicitud de devolución de Transacción '.$id;
			$des = true;
			foreach ($arrayTo as $todale) {
				if ($des) {
					$corCreo->to($todale[1]);
					$des = false;
				} else $corCreo->add_headers ("Cc: ".$todale[1]);
			}
			$corCreo->todo(29,$subject,$text);
			
			//se pone la operación como solicitada a devolver
			$q = "update tbl_transacciones set solDev = 1 where idtransaccion = '$id'";
			$temp->query($q);
			
			//escribe la tabla de devoluciones para insertar la solicitud
			if ($idpass == 37) {
				$q = "select descripcion from tbl_aisRazonCancel where idtitanes = ".$d['observ'];
				$temp->query($q);
				$d['observ'] = $d['observ']. "#". $temp->f('descripcion');
			}
			$q = "insert into tbl_devoluciones (id, idtransaccion, idadmin, devpor, fecha, fechaDev, valorDev, observacion) 
					values (null, '$id', '".$_SESSION['id']."', '0',' ".time()."', '0', '".$d['devol']."', '{$d['observ']}')";
			$temp->query($q);
			
			$q = "select nombre, email, idioma, codigo, servicio from tbl_reserva where id_transaccion = '$id'";
			$temp->query($q);
			$nom = $temp->f('nombre');
			$cor = $temp->f('email');
			$idi = $temp->f('idioma');

			
			($idi != 'en') ? include_once 'lang/correoes.php' : include_once 'lang/correo'.$idi.".php";if (strlen($idi) > 1);
			
			if ($nom) {
				$tes = str_replace("&date&", date("d/m/Y H:i"), str_replace("&id&", $id, str_replace("&idc&", $val['identificador'], str_replace("&dia&", $val['fecha'], str_replace("&motivo&", $d['observ'], str_replace("&comercio&", $val['nombre'], _COR_DEVCLI))))));
				
				$corCreo->todo(42, _LAB_DEVCLI, $tes);
				
				$impr = str_replace("&date&", date("d/m/Y H:i"), str_replace("&comnn&", $val['comercio'], str_replace("&serv&", $temp->f('servicio'), str_replace("&nom&", $nom, str_replace("&nom&", $nom, str_replace("&id&", $id, str_replace("&idc&", $val['identificador'], str_replace("&aut&", $val['codigo'], str_replace("&adev&", number_format($d['devol'],2), str_replace("&mon&", $val['moneda'], _IMP_DEVCLI))))))))));
				
				echo "<script language=\"JavaScript\" type=\"text/javascript\">window.open('impTick.php?text=$impr','_new')</script>";
			}
			
			echo "<script language=\"JavaScript\" type=\"text/javascript\">window.open('index.php?componente=comercio&pag=reporte','_self')</script>";
		}

	}
	
	$q = "select (valor/100) val from tbl_transacciones where idtransaccion = '$id'";
	$temp->query($q);
	$val = $temp->f('val');
	$html->idio = $_SESSION['idioma'];
	$html->tituloPag = _DEVOL_TIT;
	$html->tituloTarea = "&nbsp;";
	$html->anchoTabla = 600;
	$html->anchoCeldaI = $html->anchoCeldaD = 245;
	$html->java = "<script language=\"JavaScript\" type=\"text/javascript\">
		function verifica() {
			if (
					(checkField (document.forms[0].idtr, isInteger, 0))&&
					(checkField (document.forms[0].devol, isMoney, 0))
				) {
				var val1 = document.forms[0].comp.value * 1;
				var val2 = document.forms[0].devol.value * 1;
				//val2 = val2.substr(0,val2.indexOf('.'));
				if ((val1 * 1) >= (val2 * 1)) {
					if (val2 > 1) {
						if (confirm('Se va a proceder a devolver esta operaci\u00f3n, est\u00e1 de acuerdo?')) 
							return true;
						else return false;
					} else alert('El monto de la devoluci\u00f3n debe ser mayor que 1');
				} else alert('El monto a devolver tiene que ser igual o menor que el de la transacci\u00f3n');
			}
			return false;
		}
		</script>";
	$html->inHide($val, 'comp');
	$html->inTextb(_COMPRUEBA_TRANSACCION, $id, 'idtr');
	$html->inTextb(_INICIO_VALOR, formatea_numero($val), 'vali', null, null, "readonly=true");
	$html->inTextb(_DEVOL_MONT, '', 'devol');
	if ($idpass != 37)
		$html->inTexarea(_AVISO_OBSERVA, null, 'observ', 6, null, null, null, 17);
	else {
		$q = "select idtitanes id, descripcion nombre from tbl_aisRazonCancel order by idtitanes";
		$html->inSelect(_AVISO_OBSERVA, 'observ', 2, $q);
	}
	
	echo $html->salida($botones, $texto);
}
	
function muestraError ($textoCorreo) {
	global $correoMi, $corCreo;
	$corCreo->todo(9, 'Error subiendo Cancelación de Orden de Ais a Titanes', $textoCorreo."\n<br> ** ".$correoMi);
	// 	exit;
}
?>
