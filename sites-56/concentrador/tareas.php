<?php

/* 
 * Fichero para manejar los Clientes de Fincimex y revisar la documentación
 */

//ini_set('display_errors', 0);
//error_reporting(0);
header("Cache-Control: no-cache");
header("Pragma: no-cache");
header('Content-Type: text/html; charset=utf-8');;


define('_VALID_ENTRADA', 1);
require_once('configuration.php');
date_default_timezone_set('America/Havana');
// require_once("admin/classes/SecureSession.class.php");
// $Session = new SecureSession(_TIEMPOSES); //la sessión cambiada a una duración de 5 horas a partir del 17/01/18	

// require_once('admin/classes/entrada.php');
include 'include/mysqli.php';
require_once('include/hoteles.func.php');
// require_once( 'include/correo.php' );
// require_once('admin/adminis.func.php');

$temp = new ps_DB;
// $correo = new correo;
// $ent = new entrada;

$d = $_REQUEST;
// var_dump($d);
$buscar = "";

if ($d['busca'] == 1) {

	if ($d['ret']) {
		$buscar .= " and i.id like '".$d['ret']."'";
	}
	if ($d['ado']) {
		$buscar .= " and t.estado like '".$d['ado']."'";
	}
}

if ($d['idtarea'] > 0) {
	// echo "entra en 1";
	//busca la tarea para mostrar sus valores para modificarlos
	$title = 'Modificar Tarea';
	$add = '<span class="espec" onclick="window.open(\'tareas.php\',\'_self\')">Insertar tarea</span>';

	if (isset($d['fecha'])) {
		$fec = to_unix($d['fecha'].' '.$d['hora'].':'.$d['min'].':00');

		$q = "update trabajos set fecha = '$fec', estado = '{$d['estado']}', texto = '{$d['observ']}' where id = ".$d['idtarea'];
		$temp->query($q);
	}

	$q = "select i.id, t.id idtr, i.trabajo, t.fecha, t.estado, t.texto from itrabajos i, trabajos t where t.idtrab = i.id and t.id = ".$d['idtarea'];
	$temp->query($q);
	// echo $q;
	$arrRow = $temp->loadRowList();
	$arrRow = $arrRow[0];
	// var_dump($arrRow);

	$fecs = date('d/m/Y', $arrRow[3]);
	$hor = date('H', $arrRow[3]);
	$min = date('i', $arrRow[3]);
	$est = $arrRow[4];
	$tar = $arrRow[5];
	$trab = $arrRow[0];
	$taream = $arrRow[1];

} elseif (strlen($d['tarea']) > 2 || $d['tar'] > 0) {
	// Inserta una tarea nueva o adiciona datos a una existente
	// echo "entra en 2";
	$fec = to_unix($d['fecha'].' '.$d['hora'].':'.$d['min'].':00');
	if (strlen($d['tarea']) > 2) {
		$q = "insert into itrabajos (trabajo) values ('{$d['tarea']}')";
		$temp->query($q);
		$idt = $temp->last_insert_id();
	} else if ($d['tar'] > 0) $idt = $d['tar'];

	$q = "insert into trabajos (idtrab, fecha, estado, texto) values ('$idt', '$fec', '{$d['estado']}', '{$d['observ']}')";
	$temp->query($q);

	$title = 'Insertar Tarea';
	$fecs = date('d/m/Y');
	$hor = date('H');
	$min = date('i');
	$est = 'I';
	$tar = '';
	$trab = 0;
} else {
	//Entrada inicial
	// echo "entra en 3";
	$title = 'Insertar Tarea';
	$fecs = date('d/m/Y');
	$hor = date('H');
	$min = date('i');
	$est = 'I';
	$tar = '';
	$trab = 0;
}

?>
<style>
	body {
		font-family: Arial, sans-serif;
		font-size: 11px;
	}

	#tablam{
		font-family: Arial, sans-serif;
		font-size: 11px;
	}

	thead th {
		margin: 0 15px;
	}

	td {
		padding: 6px 4px;
		display: table-cell;
	}

	.css_x-office-document {
		background: url(images/iconosA201208112100.jpg) no-repeat -1px -1px transparent;
		height: 22px;
		width: 22px;
		display: block;
		float: left;
		margin: 0 2px;
	}

	.espec {
		cursor: pointer;
	}

	.nov {
		width: 95px;
	}

	.tarea {
		font-weight: bold;
	}

	.titula {
		font-size:1.5em;
		font-weight:bold;
	}
</style>

<form action="" method="post" enctype="multipart/form-data">
<?php echo "<spam class='titula'>Buscar</spam><br><br>";
$q = "select id, trabajo from itrabajos order by trabajo";
$temp->query($q);
$arrTareas = $temp->loadAssocList();
array_unshift($arrTareas, array("id"=>'%','trabajo'=>''));
?>
<input type="hidden" name="busca" value="1" />
<label class="tarea">Tarea: </label><select id="ret" name="ret">
<?php 
for ($i=0; $i<count($arrTareas); $i++){
	echo  '<option value="'.$arrTareas[$i]['id'].'">'.$arrTareas[$i]['trabajo'].'</option>';
}
?>
</select>
<br><br>
<label class="tarea">Estado: </label><select id="ado" name="ado">
	<?php 
	$arrEst[] = array('%','');
	$arrEst[] = array('I', 'Indicada');
	$arrEst[] = array('P', 'En Proceso');
	$arrEst[] = array('D', 'Pend. Definición');
	$arrEst[] = array('T', 'Terminada');
	
	for ($i=0; $i<count($arrEst); $i++){
		if ($arrEst[$i][0] == '%')
			echo '<option selected="selected" value="'.$arrEst[$i][0].'">'.$arrEst[$i][1].'</option>';
		else
			echo '<option value="'.$arrEst[$i][0].'">'.$arrEst[$i][1].'</option>';
	}
	?>
</select>
<br><br>

<input type="submit" value="Buscar"/>
</form><br><br>
<hr>
<p></p><br>

<form action="" method="post" enctype="multipart/form-data">
<?php echo "<spam class='titula'>".$title."&nbsp;&nbsp;&nbsp;&nbsp;$add</spam>";
$q = "select id, trabajo from itrabajos order by trabajo";
$temp->query($q);
$arrTarea = $temp->loadAssocList();
array_unshift($arrTarea, array("id"=>0,'trabajo'=>''));
?>

<br><br>
<input type="hidden" name="idtaream" id="idtaream" value="<?php echo $taream; ?>" />
<label class="tarea">Tarea: </label><input type="text" name="tarea" id="tarea" />&nbsp;&nbsp;&nbsp;<label class="tarea">O Seleccione: </label>
<select id="tar" name="tar">
<?php 
for ($i=0; $i<count($arrTarea); $i++){
	if ($trab == $arrTarea[$i]['id'])
		echo  '<option selected="selected" value="'.$arrTarea[$i]['id'].'">'.$arrTarea[$i]['trabajo'].'</option>';
	else {
		echo  '<option value="'.$arrTarea[$i]['id'].'">'.$arrTarea[$i]['trabajo'].'</option>';
	}
}
?>
</select>
<br><br>
<label class="tarea">Fecha: </label> <input type="text" name="fecha" id="fecha" value="<?php echo $fecs; ?>" />dd/mm/yy&nbsp;&nbsp;&nbsp;
<label class="tarea">Hora: </label> <select id="hora" name="hora">
	<?php for ($h=0; $h<24; $h++) {
		if ($hor == $h) echo '<option selected="selected" value="'.$h.'">'.$h.'</option>';
		else echo '<option value="'.$h.'">'.$h.'</option>';
		}?>
</select>&nbsp;&nbsp;&nbsp;
<label class="tarea">Min: </label><select id="min" name="min">
	<?php for ($m=0; $m<60; $m++) {
		if ($min == $m) 
			echo '<option selected="selected" value="'.$m.'">'.$m.'</option>';
		else
			echo '<option value="'.$m.'">'.$m.'</option>';
	}?>
</select>
	<br /><br>
<label class="tarea">Estado: </label><select id="estado" name="estado">
	<?php $arrEst = array(
		array('I', 'Indicada'),
		array('P', 'En Proceso'),
		array('D', 'Pend. Definición'),
		array('T', 'Terminada')
	); 
	for ($i=0; $i<count($arrEst); $i++){
		if ($arrEst[$i][0] == $est)
			echo '<option selected="selected" value="'.$arrEst[$i][0].'">'.$arrEst[$i][1].'</option>';
		else
			echo '<option value="'.$arrEst[$i][0].'">'.$arrEst[$i][1].'</option>';
	}
	?>
</select>
	</br><br>
<label class="tarea">Observaciones: </label><textarea cols='120' name='observ'><?php echo $tar; ?></textarea >
<br><br>
<input type="submit" value="Enviar">
</form>
<br><br><hr><br><br>
<?php 

$q = "select t.id, i.trabajo, from_unixtime(t.fecha, '%d/%m/%Y %H:%i') fecha, case t.estado when 'I' then 'Indicada' when 'P' then 'En Proceso' when 'D' then 'Pendiente' when 'T' then 'Terminada' end estado, t.texto from itrabajos i, trabajos t where t.idtrab = i.id $buscar order by t.fecha desc limit 0,100";
$temp->query($q);
$arrTrab = $temp->loadAssocList();
// var_dump($arrTrab);
echo "<table id='tablam'><thead class='header'><th>Trabajo</th><th class='nov'>Fecha</th><th>Estado</th><th>Observaciones</th></thead><tbody>";
for ($i = 0; $i < count($arrTrab); $i++) {
 echo "<tr class='espec' onclick='window.open(\"tareas.php?idtarea={$arrTrab[$i]['id']}\",\"_self\")'><td>{$arrTrab[$i]['trabajo']}</td><td>{$arrTrab[$i]['fecha']}</td><td>{$arrTrab[$i]['estado']}</td><td>{$arrTrab[$i]['texto']}</td></tr>";
}
echo "</tbody></table>";
?>

<div id="clock"></div>

<script type="text/javascript">
function startTime() {
    var today = new Date();
    var hr = today.getHours();
    var min = today.getMinutes();
	var day = today.getDate();
	var mes = today.getMonth();
    var ano = today.getFullYear();

    //Add a zero in front of numbers<10
    min = checkTime(min);
    day = checkTime(day);
	document.getElementById("hora").selectedIndex = hr;
	document.getElementById("min").selectedIndex = min;
	document.getElementById('fecha').value = day + "/" + (mes+1) + "/" + ano;
    var time = setTimeout(function(){ startTime() }, 500);
}
function checkTime(i) {
    if (i < 10) {
        i = "0" + i;
    }
    return i;
}
startTime();
</script>
