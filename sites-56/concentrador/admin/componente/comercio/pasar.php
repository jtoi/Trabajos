<?php defined( '_VALID_ENTRADA' ) or die( 'Restricted access' );
//Lee el c&oacute;digo HTML y lo prepara para la sustituci&oacute;n de valores
global $temp;
$html = new tablaHTML;

$d = $_REQUEST;

if ($d['buscar']) {
	$tira = explode('and', $d['buscar']);
	$fecha1 = date('d/m/Y', substr($tira[3], strlen($tira[3])-11));
} else {
	$fecha1 = date('d/m/Y', mktime(0, 0, 0, date("m"), date("d"), date("Y")));
	if ($d['fecha1']) $fecha1 = $d['fecha1'];
}


$query = "select idcomercio from tbl_comercio where id in (".$_SESSION['idcomStr'].")";
$temp->query($query);
$comercios = implode(",", $temp->loadResultArray());

$comer = $comercios;

if (isset($d['comercio'])) $comercId = str_replace ("'", "", $d['comercio']);
else {
	if ($comer == 'todos') $comercId = $comercios;
	else $comercId = $comer;
}

if(is_array($comercId)) $comercId = implode(',', $comercId);
$comercId = str_replace ("'", "", $comercId);

$modoVal = "'P'";
$nombreVal = '';

//echo $d['moneda'];
if ($d['modo']) $modoVal = stripslashes($d['modo']);

$html->idio = $_SESSION['idioma'];
$html->tituloPag = _MENU_ADMIN_PASARELA;
$html->tituloTarea = _REPORTE_TASK;
$html->hide = true;
$html->anchoTabla = 500;
$html->anchoCeldaI = 243;
$html->anchoCeldaD = 250;
$query = "select idPasarela from tbl_pasarela where idPasarela not in (5,6,7)";
$temp->query($query);
$listPasar = implode(", ", $temp->loadResultArray()).", 0";
$d['pasarela']? $pasarelaid = $d['pasarela']:$pasarelaid = $listPasar;

if ($comer == 'todos') {
	$query = "select idcomercio id, nombre from tbl_comercio where activo = 'S' order by nombre";
	$html->inSelect(_COMERCIO_TITULO, 'comercio', 5, $query,  str_replace(",", "', '", $comercId), null, null, "multiple size='5'");
} elseif (strpos ($comer, ",")) {
	$query = "select idcomercio id, nombre from tbl_comercio where idcomercio in (".$comer.") and activo = 'S' order by nombre";
	//		echo $query;
	$html->inSelect(_COMERCIO_TITULO, 'comercio', 5, $query,  str_replace(",", "', '", $comercId), null, null, "multiple size='5'");
} else $html->inHide ($comercId, 'comercio');
$query = "select idPasarela id, nombre from tbl_pasarela where idPasarela not in (5,6,7) order by nombre";
$temp->query($query);
$arrP = $temp->loadAssocList();
$arrIdP = $temp->loadResultArray();
$arrIdP[] = (0);
$arrPasar = array();
$arrPasar[] = array(implode(',', $arrIdP), _REPORTE_TODOS);
for($i=0; $i<count($arrP); $i++) {
    $arrPasar[] = array($arrP[$i]['id'], $arrP[$i]['nombre']);
}
$html->inSelect(_COMERCIO_PASARELA, 'pasarela', 3, $arrPasar, $pasarelaid);
$html->inCheckBox('Activar fecha', 'fechAct', 5, 1);
$html->inFecha(_REPORTE_FECHA, $fecha1, 'fecha1', null, null);

echo $html->salida();


$fecha = to_unix($fecha1)+(date('s')*1+date('i')*60+date('H')*3600);

$vista = "select distinct c.nombre, (select pp.nombre from tbl_pasarela pp where pp.idPasarela = o.idpasarelaT) pasarelaT, ".
                "(select p.nombre from tbl_pasarela p where p.idPasarela = o.idpasarelaW) pasarelaW, o.fechaIni fechaIn, ".
                "case when o.fechaFin = 2863700400 then ' - ' else o.fechaFin end fechaFi ".
         "from tbl_comercio c, tbl_pasarela p, tbl_colComerPasar o ";
$where = stripslashes("where c.idcomercio = o.idcomercio
			and c.activo = 'S' ");
if (strlen($fecha1 > 0)) $where .= stripslashes("and (o.fechaIni <= $fecha and o.fechaFin >= $fecha) ");
$where .= stripslashes("and c.idcomercio in ($comercId)
            and (o.idpasarelaT in ($pasarelaid) or o.idpasarelaW in ($pasarelaid))");
$orden = 'c.nombre asc';

$busqueda = array();
$columnas = array(
		array('Comercio', "nombre", "150", "center", "left" ),
		array('Pasarela Web', "pasarelaW", "70", "center", "left" ),
		array('Pasarela TPV', "pasarelaT", "70", "center", "left" ),
		array('Fecha Inicio', "fechaIn", "130", "center", "center" ),
		array('Fecha Fin', "fechaFi", "130", "center", "center" )
);



//echo $vista.$where.$orden;
$querys = tabla( 600, 'E', $vista, $orden, $where, $colEsp, $busqueda, $columnas );

?>
<script type="text/javascript">
    var fec =  $("#fecha1").val();
    $("#fecha1").val('')
    $("#fechAct").click(function(){
        if($(this).attr('checked')) {
            $("#fecha1").val(fec).attr('readonly','');
        } else {
            $("#fecha1").val('').attr('readonly','readonly');
        }
    });
</script>
