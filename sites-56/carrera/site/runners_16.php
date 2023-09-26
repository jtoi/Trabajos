<?php session_start();
include_once '../configuracion.php';
require "class_mysql.php";
global $conn;

//print_r($_REQUEST);
if ($_POST['export'] == 'csv') {
	$file = (date("mdHis")).(rand (0, 99)).".csv";
	$urlPassCSV = "desc/".$urlPass.$file;

	$contenido = "Id,Nombre Participante,Fecha Nac,F. Solicitud,DNI,TelÈfono,Pago,Correo,Localidad,CP,Sexo,Altura,Peso,Carnet Club Kirolak,".
					"DÌas Entrenamiento,DÌas - Horario,øCorres habitualmente?,øCu·ntos minutos?,".
					"øQuÈ; ritmo de media carrera sueles llevar?,øHas entrenado previamente con un Plan organizado?,".
					"øHas realizado trabajo de fuerza especÌ≠fica para la carrera?,øHas realizado test de aptitud mÈdico?,".
					"øHas realizado prueba de esfuerzo mÈdico?,øParticipas habitualmente en carreras?,øSobre quÈ distancia?,".
					"øEn quÈ marca?,øTienes o has tenido lesiones?,øCu·les?,øTienes pulsÛmetro?,".
					"øTe gustarÌ≠a tener un plan m·s personalizado?,øTe gustarÌ≠a entrenar m·s dÌ≠as?,".
					"øTe gustarÌ≠a participar en carreras?,øTe gustarÌ≠a ir a competiciones fuera de nuestra ciudad?\n";
	
	$sql = 'SELECT a.id, upper(concat(a.nombre, " ", apellidos)) alumno, from_unixtime(fechaInsc, "%d/%m/%Y")finsc, case pin when "Si" then "30 Euros" '.
				'else "36 Euros" end pag, carnet, date_format(fnac, "%e/%c/%Y")fn, licencia_num, telf, a.correo, localidad, cp, doc, pais, '.
				'direccion, sexo, observaciones
		FROM   participantes a
		where  a.idevento = 17 ';
//echo $sql;
	$result=$conn->execute($sql) or die(mysql_error());
	$i = 0;
	$lin = array();
	
	while($item =  mysql_fetch_object($result)){
		$horar = $dias = $modal = '';
		if (stripos($item->direccion,'21|')>-1){
			$dias = 'Lun - Mie - Vie';
			$modal = $dias;
			if (stripos($item->direccion,'|1')>0) $horar = 'De 18:30 a 19:30h';
			else $horar = 'De 19:30 a 20:30h';
		} else {
			$dias = 'Otros dÌas';
			if (stripos($item->direccion,'A')>0) $horar = 'MaÒanas';
			if (stripos($item->direccion,'D')>0) $horar .= ' / MediodÌas';
			if (stripos($item->direccion,'T')>0) $horar .= ' / Tardes';
			if (stripos($horar,' / ') == 1) $horar = substr($horar,2);
			if (stripos($item->direccion,'MJS')>0) $modal = "Mar - Jue - Sab";
			if (stripos($item->direccion,'LMV')>0) $modal .= " / Lun - Mie - Vie";
			if (stripos(horar,' / ') == 1) $modal = substr($modal,2);
		}
		$arrExp = explode("|", $item->observaciones);
		
		$contenido .= $item->id.",".utf8_decode(strtoupper($item->alumno)).",".$item->fn.",".$item->finsc.",".$item->doc.",".$item->telf.
					",".$item->pag.",".strtolower($item->correo).",".$item->localidad.",".$item->cp.
					",".$item->sexo.",".$item->licencia_num." cms,".$item->pais." Kg,".$item->carnet.",".$dias.",".$modal." | ".$horar.
					",";
		foreach ($arrExp as $value) {
			$contenido .= $value.",";
		}
		$contenido .= "\n";
// 		$i++;
	}
// 	for ($x=0;$x<$i;$x++) {
// 		$contenido .= '"'.$lin[$x][0].'","'.$lin[$x][1].'","'.$lin[$x][2].'","'.$lin[$x][3].'","'.$lin[$x][4].'","'.$lin[$x][5].'","'.$lin[$x][6].'","'.$lin[$x][7].'","'.$lin[$x][8];
// 		$q = "select nombre, reg1, reg2, reg3 from prueba p, registros r where p.id = r.idprueba and r.idparticipante = ".$lin[$x][0];
// 		$result=$conn->execute($q) or die(mysql_error());
// 		for ($j=0;$j<2;$j++){
// 			$it = mysql_fetch_object($result);
// 			$contenido .= '","'.utf8_decode(strtoupper($it->nombre)).'","'.utf8_decode(strtoupper($it->reg1)).'","'.utf8_decode(strtoupper($it->reg2)).'","'
// 					.utf8_decode(strtoupper($it->reg3));
// 		}
// 		$contenido .= '","'.$lin[$x][9].'","'.$lin[$x][10].'","'.$lin[$x][11].'"'."\n";
		
// 	}

	if ($ficherw = fopen( $urlPassCSV, 'w' )) {
		fwrite ( $ficherw, $contenido );
		fclose ( $ficherw );
		header ("Content-Type: application/octet-stream");
		header ("Content-Disposition: attachment; filename=".$file." ");
		header ("Content-Length: ".filesize($urlPassCSV));
		header ("Pragma: no-cache");
		header ("Expires: 0");
		readfile($urlPassCSV);
		exit;
	} else exit( "Error el fichero csv no pudo ser escrito, compruebe los permisos del directorio." );
	
}

if ($_POST['pwd'] && $_POST['user']) {
	$usr = $_POST['user'];
	$pwd = $_POST['pwd'];
	$arrNomb = array('maria','admin');
	$arrCont = array('Santut12','carrera');
	$id = array_search($usr,$arrNomb);
	
	if ($arrCont[$id] == $pwd) {
		$_SESSION['user'] = "$usr";
		$_SESSION['iduser'] = "$id";
	}
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="es" lang="es">
<head>
    <meta name="author" content="Julio Toirac (jtoirac@gmail.com)" />
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1" />
	<link rel="stylesheet" href="images/style.css" type="text/css" />
        <title>InscripciÛn Escuela de Atletismo de Bilbao</title>
        <script type="text/javascript" src="js/jquery.js"></script>
	<!--<script type="text/javascript" src="js/jlogin.js"></script>-->
	<script type="text/javascript" src="js/jquery.dataTables.min.js"></script>
	<script type="text/javascript">
		function fnFormatDetails ( oTable, nTr ){
			var aData = oTable.fnGetData( nTr );
			var discip = aData[16].split("|");
			var dias, modal, horar = '';
			var even = Array();
			var prueba = '';
			for (i=0;i<discip.length;i++) {
				even[i] = discip[i].split('|');
				prueba += even[i][0]+" - ";
			}

			prueba = prueba.substr(0, prueba.length - 3);

			if (aData[14].indexOf('21|')>-1){
				dias = 'Lun - Mie - Vie';
				modal = dias;
				if (aData[14].indexOf('|1')>0) horar = 'De 18:30 a 19:30h';
				else horar = 'De 19:30 a 20:30h';
			} else {
				 dias = 'Otros d&iacute;as';
				 if (aData[14].indexOf('A')>0) horar = 'Ma&ntilde;anas';
				 if (aData[14].indexOf('D')>0) horar += '/Mediod&iacute;as';
		 		 if (aData[14].indexOf('T')>0) horar += '/Tardes';
				 if (horar.indexOf('/') == 0) horar = horar.substr(1);
				 if (aData[14].indexOf('MJS')>0) modal = "Mar - Jue - Sab";
				 if (aData[14].indexOf('LMV')>0) modal += "/Lun - Mie - Vie";
				 if (horar.indexOf('/') == 0) modal = modal.substr(1);
			}
			
//			if (discip.length == 2) var even = Array(discip[0].split('|'),discip[1].split('|')); else var even = Array(discip[0].split('|'));
			var sOut = '<table class="datosOc" cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">';
			sOut += '<tr><td class="neg">Nombre Participante:</td><td>'+aData[1]+'</td><td class="neg">Fecha Nac.:</td><td>'+aData[6]+'</td></tr>';
			sOut += '<tr><td class="neg">F. Solicitud:</td><td>'+aData[2]+'</td><td class="neg">DNI: </td><td>'+aData[12]+'</td></tr>';
			sOut += '<tr><td class="neg">Tel&eacute;fono:</td><td>'+aData[8]+'</td><td class="neg">Pago:</td><td>'+aData[4]+'</td></tr>';
			sOut += '<tr><td class="neg">Correo: </td><td><a href="mailto:'+aData[9]+'">'+aData[9]+'</a></td><td class="neg">Localidad:</td><td>'+aData[10]+'</td></tr>';
			sOut += '<tr><td class="neg">CP: </td><td>'+aData[11]+'</td><td class="neg">Sexo:</td><td>'+aData[15]+'</td></tr>';
			sOut += '<tr><td class="neg">Altura: </td><td>'+aData[13]+' cms</td><td class="neg">Peso: </td><td>'+aData[7]+' Kg</td></tr>';
			sOut += '<tr><td class="neg">Carnet Club Kirolak: </td><td colspan="3">'+aData[12]+'</td></tr>';
			sOut += '<tr><td class="neg">D&iacute;as Entrenamiento: </td><td>'+dias+'</td><td class="neg">D&iacute;as - Horario: </td><td>'+modal+' | '+horar+'</td></tr>';
			
			sOut += '<tr><td class="neg">&iquest;Corres habitualmente?:</td><td>'+discip[0]+'</td><td class="neg">&iquest;Cu&aacute;ntos minutos?:</td><td>'+discip[1]+'</td></tr>';
			sOut += '<tr><td class="neg">&iquest;Qu&eacute; ritmo de media carrera sueles llevar?:</td><td>'+discip[2]+'</td><td class="neg">&iquest;Has entrenado previamente con un Plan organizado?:</td><td>'+discip[3]+'</td></tr>';
			sOut += '<tr><td class="neg">&iquest;Has realizado trabajo de fuerza espec&iacute;fica para la carrera?:</td><td>'+discip[4]+'</td><td class="neg">&iquest;Has realizado test de aptitud m&eacute;dico?:</td><td>'+discip[5]+'</td></tr>';
			sOut += '<tr><td class="neg">&iquest;Has realizado prueba de esfuerzo m&eacute;dico?:</td><td>'+discip[6]+'</td><td class="neg">&iquest;Participas habitualmente en carreras?:</td><td>'+discip[7]+'</td></tr>';
			sOut += '<tr><td class="neg">&iquest;Sobre qu&eacute; distancia?:</td><td>'+discip[8]+'</td><td class="neg">&iquest;En qu&eacute; marca?:</td><td>'+discip[9]+'</td></tr>';
			sOut += '<tr><td class="neg">&iquest;Tienes o has tenido lesiones?:</td><td>'+discip[10]+'</td><td class="neg">&iquest;Cu&aacute;les?:</td><td>'+discip[11]+'</td></tr>';
			sOut += '<tr><td class="neg">&iquest;Tienes puls&iacute;metro?:</td><td>'+discip[12]+'</td><td class="neg">&iquest;Te gustar&iacute;a tener un plan m&aacute;s personalizado?:</td><td>'+discip[13]+'</td></tr>';
			sOut += '<tr><td class="neg">&iquest;Te gustar&iacute;a entrenar m&aacute;s d&iacute;as?:</td><td>'+discip[14]+'</td><td class="neg">&iquest;Te gustar&iacute;a participar en carreras?:</td><td>'+discip[15]+'</td></tr>';
			sOut += '<tr><td class="neg">&iquest;Te gustar&iacute;a ir a competiciones fuera de nuestra ciudad?:</td><td colspan="3">'+discip[16]+'</td></tr>';
			sOut += '</table>';
			return sOut;
		}

		$(document).ready(function() {
			var where = ' a.idevento = 17 ';
			var oTable = $('#example').dataTable( {
					"bProcessing": true,
					"bServerSide": true,
					"sPaginationType": "full_numbers",
					"sAjaxSource": "cargar2runners.php?tabla=true&where="+where,
					"oLanguage": {
						"sLengthMenu": "Mostrar _MENU_ participantes por p√°gina",
						"sZeroRecords": "No se encontraron coincidencias - lo siento",
						"sInfo": "Mostrando _START_ a _END_ de _TOTAL_ participantes",
						"sInfoEmpty": "Mostrando 0 a 0 de 0 participantes",
						"sProcessing": "Procesando la informaci√≥n espere...",
						"oPaginate": {
							"sFirst":    "Primero",
							"sPrevious": "Anterior",
							"sNext":     "Pr√≥ximo",
							"sLast":     "√öltimo"
						},
						"sEmptyTable": "No hay datos disponibles en la tabla",
						"sInfoFiltered": "",
						"sSearch": "Buscar:"
					},
//					"bSortable": false, "aTargets": [ 0 ],
					"aaSorting": [[ 0, "desc" ]],
					"aoColumnDefs": [ 
										{ "bVisible": false, "aTargets": [ 6 ] },
										{ "bVisible": false, "aTargets": [ 7 ] },
										{ "bVisible": false, "aTargets": [ 8 ] },
										{ "bVisible": false, "aTargets": [ 9 ] },
										{ "bVisible": false, "aTargets": [ 10 ] },
										{ "bVisible": false, "aTargets": [ 11 ] },
										{ "bVisible": false, "aTargets": [ 12 ] },
										{ "bVisible": false, "aTargets": [ 13 ] },
										{ "bVisible": false, "aTargets": [ 14 ] },
										{ "bVisible": false, "aTargets": [ 15 ] },
										{ "bVisible": false, "aTargets": [ 16 ] }
					]
			} );		
		

		$('#example tbody tr').live('click', function () {
			var nTr = this;
			var rowSal = '';
			
			( this.className.match('odd') ) ? rowSal = "odd" : rowSal = "even"; 
			if ( this.className.match('details_close') ) {
				/* This row is already open - close it */
				this.className = rowSal+" details_open";
				oTable.fnClose( nTr );
			} else {
				/* Open this row */
				this.className = rowSal+" details_close";
				oTable.fnOpen( nTr, fnFormatDetails(oTable, nTr), 'details' );
			}
		} );
		
		$("#example_first").html('<img src="images/ff.png" width="29" height="19" alt="Primera" title="Primera" />');
		$("#example_previous").html('<img src="images/f.png" width="29" height="19" alt="Anterior" title="Anterior" />');
		$("#example_next").html('<img src="images/b.png" width="29" height="19" alt="Siguiente" title="Siguiente" />');
		$("#example_last").html('<img src="images/fb.png" width="29" height="19" alt="√öltima" title="√öltima" />');
		
		$("#even").change(function(){
			if ($(":selected").val() == 1) window.open("index2.php","_self");
			if ($(":selected").val() == 4) window.open("index3.php","_self");
			if ($(":selected").val() == 5) window.open("index.php","_self");
		})
	} );


	</script>
</head>
<body>
<?php
if(isset($_SESSION['user']) && $_SESSION['user'] != ""){
?>
	<div class="content">
		<div class="logo">
			<h1>INSCRIPCIONES</h1>
		</div>
		<div id="logueduser">Conectado como: <span style="color:brown;"><?php echo $_SESSION["user"]; ?></span></div>
		<div class="newsletter">
			<?php if($_SESSION["iduser"]==1){?>
			<ul>
				<li><a href="newClient.php">Nuevo Cliente</a></li>
				<li><a href="edClient.php">Editar Clientes</a></li>
				<li><a href="logout.php">Logout</a></li>
			</ul>
			<?php }else{?>
			<ul>
			<li><a href="edClient.php?cli=<?php echo $_SESSION["iduser"];?>">Modificar Datos</a></li>
			<li><a href="logout.php">Logout</a></li>
			</ul>
			<?php }?>
		</div>
		<div class="search_field">
			<form method="post" action="">
				<span class="clEven" style="margin-right: 112px;" >Evento: Runners Bilbao 2016<!-- <select id="even" name="even">
					<option value="5" selected="true">Milla Marina Femenina</option>
					<option value="1">Milla Internacional</option>
					<option value="4">Villa de Bilbao / 2013</option>
					<option value="3">8va. Milla Internacional / 2013</option>
				</select>--></span>
				<input type="hidden" value="csv" name="export" />
				<input type="image" value="csv" style="cursor: pointer;float: right;margin-right: 20px;"
					src="images/excel.jpg" title="Exportar a CSV" alt="Exportar a CSV" />
			</form>
		</div>
        <div class="content2">
            <div class="listop">
                <table width="100%" border="0" cellpadding="0" cellspacing="0">
                    <tr >
                        <td valign="top"><img src="images/lc.gif" width="10" height="10" align="left"/></td>
                        <td><span class="white">Listado de Participantes</span></td>
                        <td valign="top"><img src="images/rc.gif" width="10" height="10" align="right"/></td>
                    </tr>
                </table>
            </div>
			<table cellpadding="0" cellspacing="0" border="0" width="100%" class="display" id="example">
				<thead>
					<tr>
						<th width="5%">Id</th>
						<th width="30%">Nombre y Apellidos</th>
						<th width="15%">F. Solicitud</th>
						<th width="15%">D&iacute;as de entrenamiento</th>
						<th width="15%">Pago</th>
						<th width="15%">Carnet Kirolak</th>
						<th width="10%">Fecha de Nac.</th>
						<th width="10%">Pago</th>
						<th width="10%">Tel&eacute;fono</th>
						<th width="25%">Correo</th>
						<th width="15%">Localidad</th>
						<th width="5%">CP</th>
						<th width="25%">DNI</th>
						<th width="45%">Colegio</th>
						<th width="15%">Carnet Kirolak</th>
						<th width="15%">Carnet Kirolak</th>
						<th width="15%">Carnet Kirolak</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td colspan="12" class="dataTables_empty">Cargando datos del servidor...</td>
					</tr>
				</tbody>
				<tfoot>
					<tr class="lisbot">
						<th>Id</th>
						<th>Nombre y Apellidos</th>
						<th>F. Solicitud</th>
						<th>D&iacute;as de entrenamiento</th>
						<th>Pago</th>
						<th>Carnet Kirolak</th>
						<th>Fecha de Nac.</th>
						<th>Pago</th>
						<th>Tel&eacute;fono</th>
						<th>Correo</th>
						<th>Localidad</th>
						<th>CP</th>
						<th>DNI</th>
						<th>Colegio</th>
						<th>Carnet Kirolak</th>
						<th>Carnet Kirolak</th>
						<th>Carnet Kirolak</th>
					</tr>
				</tfoot>
			</table>
			<div class="footer">
				&copy; Copyright 2011 Inscripci&oacute;n Carrera.
			</div>
		</div>
	</div>
<?php } else{ ?>
	<div class="logo" style="text-align:center;padding:10px;">
                    <h1><a href="#" title="Inscripci&oacute;n de atletas">INSCRIPCI&Oacute;N DE <span class="red">ATLETAS</span></a></h1>
	</div>
	<div id="loginbox">
		
		<form name="login" action="" method="post">
		
			<p>Usuario:&nbsp;&nbsp;&nbsp;&nbsp; <input type="text" name="user" id="user" /></p>
			<p>Contrse&ntilde;a: <input type="password" name="pwd" id="pwd" /></p>
			<br /><input type="submit" name="go" id="go" value="Entrar" style="margin-left:28px;" />
			<input type="reset" name="rst" id="rst" value="Cancelar" />
			</form>
		
	</div>
	<div id="output"></div>
<?php } ?>
</body>
</html>
