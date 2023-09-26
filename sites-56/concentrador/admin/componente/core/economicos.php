<?php defined( '_VALID_ENTRADA' ) or die( 'Restricted access' );
//Lee el c&oacute;digo HTML y lo prepara para la sustituci&oacute;n de valores
global $temp;
global $send_m;
$html = new tablaHTML;
$d = $_REQUEST;

if (_MOS_CONFIG_DEBUG) var_dump($d);

if ($d['cambio']) {
	if ($d['nomb']){
		if ($d['email']) {
			$q = "update tbl_economicos set idcomercio = {$d['comm']}, nombre = '{$d['nomb']}', email = '{$d['email']}', fecha = unix_timestamp() where id = ".$d['cambio'];
			$temp->query($q);
		} else {
			echo "<div style=\"text-align:center;margin-top:20px;color:red;font-family:Arial sans-serif;font-size:11px\">Falta el correo del contacto</div>";
		}
	}
} else {
	if ($d['nomb']){
		if ($d['email']) {
			$q = "select * from tbl_economicos where email = '{$d['email']}'";
			$temp->query($q);
			if ($temp->num_rows() == 0){
				$q = "insert into tbl_economicos values (null, {$d['comm']}, '{$d['nomb']}', '{$d['email']}', unix_timestamp())";
				$temp->query($q);
			} else {
				echo "<div style=\"text-align:center;margin-top:20px;color:red;font-family:Arial sans-serif;font-size:11px\">Ya se encuentra el correo en la BD</div>";
			
			} 
		} else {
			echo "<div style=\"text-align:center;margin-top:20px;color:red;font-family:Arial sans-serif;font-size:11px\">Falta el correo del contacto</div>";
		}
	}
}

if ($d['borrar']){
	$q = "delete from tbl_economicos where id = ".$d['borrar'];
	$temp->query($q);
}


/* Construye el formulario de Buscar */
$html->java = "<style>#bsccc span{font-size:12px;font-weight:bold;line-height:23px;}</style>";

$html->idio = $_SESSION['idioma'];
$html->tituloPag = 	_MENU_ADMIN_ECON;
$html->tituloTarea = 'Insertar Destinatario';
$html->anchoTabla = 700;
$html->anchoCeldaI = $html->anchoCeldaD = 345;

if ($d['cambiar']) {
	$html->inHide($d['cambiar'], 'cambio');
	$q = "select nombre, email, idcomercio from tbl_economicos where id = ".$d['cambiar'];
	$temp->query($q);
	$nomb = $temp->f('nombre');
	$email = $temp->f('email');
	$d['comb'] = $temp->f('idcomercio');
}

$html->inTextb('Nombre del destinatario', $nomb, 'nomb');
$html->inTextb('Correo', $email, 'email');
$comq = "select id, nombre from tbl_comercio where activo = 'S' order by nombre";
$html->inSelect('Comercio', 'comm', 2, $comq, $d['comb']);

$html->inTextoL('Buscar','bsccc');

$html->inHide('', 'buca');
$temp->query($comq);
// $arrCom = $temp->loadRowList();
$arrIni = array(array('', 'Escoja'));
$arrCom = array_merge($arrIni,$temp->loadRowList());
$html->inSelect('Comercio', 'comb', 3, $arrCom, $d['comb']);
if ($d['comb']) {
	$mn = "select u.idadmin, concat(u.nombre,' ',u.email) nombre from tbl_admin u, tbl_colAdminComer c where u.idadmin = c.idAdmin and u.idcomercio != 'todos' and c.idComerc = ".$d['comb'];
	$temp->query($mn);
	$arrIni = array(array('', 'Escoja'));
	$arrUs = array_merge($arrIni,$temp->loadRowList());
	$html->inSelect('Usuario', 'usus', 3, $arrUs);
}
echo $html->salida();
if (!$d['comb']) $d['comb'] = $_SESSION['idcomStr'];
$vista = "select a.id, a.nombre, a.email, c.nombre comercio, from_unixtime(a.fecha, '%d/%m/%Y') fecha_visita from tbl_economicos a, tbl_comercio c ";

$where = ' where c.id = a.idcomercio and a.idcomercio in ('.$d['comb'].')' ;

$orden = ' a.fecha desc';

$colEsp = array(
		array("e", "Editar Datos", "css_edit", "Editar"),
		array("b", "Borrar Económico", "css_borra", "Borrar Económico"));

$busqueda = array();

$columnas = array(
		array(_FORM_NOMBRE, "nombre", "120", "center", "left" ),
		array(_MENU_ADMIN_COMERCIO, "comercio", "100", "center", "left" ),
		array(_FORM_CORREO, "email", "", "center", "left"),
		array(_USUARIO_FECHA_ULTIMA,"fecha_visita","","center","center"));

$ancho = 900;

echo "<div style='float:left; width:100%' ><table class='total1' width=\"$ancho\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\">
<tr>
<td><span style='float:right' class='css_x-office-document' onclick='document.exporta.submit()' onmouseover='this.style.cursor=\"pointer\"' alt='"._REPORTE_CSV."' title='"._REPORTE_CSV."'></span></td>
		</tr>
	</table></div>";

// echo $vista.$where." order by ".$orden;
tabla( $ancho, 'E', $vista, $orden, $where, $colEsp, $busqueda, $columnas );
?>

<script type="text/javascript">
$("#comb").change(function(){
	$("#buca").val('1');
	$('form:first').submit();
});

$("#usus").change(function() {
	$.post('componente/core/ejec.php',{
		fun: 'econ',
		usr: $("#usus :selected").val()
	},function(data){
		var datos = eval('(' + data + ')');
		if (datos.cont.length > 0) {
			$("#nomb").val(datos.cont[0]);
			$("#email").val(datos.cont[1]);
		}
	});
});

function verifica(){
	if ($("#nomb").val().lenght > 0) {
		
	}
}
</script>