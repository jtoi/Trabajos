<?php session_start();
include_once '../configuracion.php';
require "class_mysql.php";
global $conn;

//print_r($_REQUEST);
if (isset($_POST['export']) && $_POST['export'] == 'csv') {
	$file = (date("mdHis")).(rand (0, 99)).".csv";
	$urlPassCSV = "desc/".$urlPass.$file;

	$contenido = "Id,Alumno Nombre y Apellidos,Fecha Nac.,Categoría,Pago,Tel.,Correo,Direccion,Localidad,CP,DNI,Colegio,Observaciones,Carnet Kirolak,".
						"Fecha Inscripción,Tutor,DNI Tutor,A Pagar,Días,Sexo,DNI Titular Cta.,Titular Cta.,IBAN\n";

	$sql = 'SELECT SQL_CALC_FOUND_ROWS a.id, upper(concat(a.nombre, " ", apellidos)) alumno, date_format(fnac, "%e/%c/%Y") fn, case idprueba when 99 then "Prebenjamin" when 100 then "Benjamin" when 101 then "Alevin" when 102 then "Infantil" when 103 then "Cadete" when 104 then "Juvenil" else "Junior" end prueba, case licencia_num when 2 then "Prueba" else "Mensualidad" end cat, telf, a.correo, upper(direccion) dir, upper(localidad) loc, cp, doc, upper(pais) pa, observaciones, carnet, from_unixtime(fechaInsc, "%d/%m/%Y") fi, r.nombre tu, r.tel, case tipoDoc when "" then "0" else tipoDoc end td, case idprueba when 103 then "Lunes Mi�rcoles y Viernes" when 104 then "Lunes Mi�rcoles y Viernes" when 105 then "Lunes Mi�rcoles y Viernes" when 102 then "Martes Jueves y Viernes" else "Martes y Jueves" end dia, sexo, provincia, club, pin FROM participantes a, representantes r where  a.idevento = 37 and a.id = r.idparticipante  ORDER BY  a.id  desc';
	
	// $sql = 'SELECT a.id, upper(concat(a.nombre, " ", apellidos)) alumno, date_format(fnac, "%e/%c/%Y") fn, 
	// 			case idprueba when 99 then "Prebenjamin" when 100 then "Benjamin" when 101 then "Alevin" when 102 then "Infantil" when 103 then "Cadete" 
	// 			when 104 then "Juvenil" else "Junior" end prueba, case licencia_num when 2 then "Semana prueba" else "Mensualidad" end cat, telf, a.correo,
	// 			direccion dir, localidad loc, cp, doc, upper(pais) pa, observaciones, carnet, from_unixtime(fechaInsc, "%d/%m/%Y") fi, r.nombre tu, r.tel, 
	// 			tipoDoc, 
	// 		case licencia_num when "5" then "Lunes - Mi&eacute;rcoles y Viernes" else "Martes y Jueves" end dia
	// 	FROM   participantes a, representantes r
	// 	where  a.idevento = 28  and a.id = r.idparticipante';
// echo $sql;
	$result=$conn->execute($sql) or die(mysqli_error($result));
	$i = 0;
	$lin = array();
	
	while($item =  mysqli_fetch_object($result)){
		$contenido .= $item->id.",".utf8_decode(strtoupper($item->alumno)).",".$item->fn.",".utf8_decode(strtoupper($item->prueba)).
					",".$item->cat.",".$item->telf.",".strtolower($item->correo).",".utf8_decode(strtoupper(str_replace(",", "-", $item->dir))).",".
					utf8_decode(strtoupper(str_replace(",", "-", $item->loc))).",".$item->cp.",".$item->doc.",".utf8_decode(strtoupper($item->pa)).",".
					utf8_decode(strtoupper($item->observaciones)).",".$item->carnet.",".$item->fi.",".utf8_decode(strtoupper($item->tu)).",".
					$item->tel.",".$item->td.",".$item->dia.",".$item->sexo.",".$item->provincia.",".$item->club.",".$item->pin.","."\n";
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
        <title>Inscripci&oacute;n Escuela de Atletismo de Bilbao</title>
        <script type="text/javascript" src="js/jquery.js"></script>
	<!--<script type="text/javascript" src="js/jlogin.js"></script>-->
	<script type="text/javascript" src="js/jquery.dataTables.min.js"></script>
	<script type="text/javascript">
		function fnFormatDetails ( oTable, nTr ){
			var aData = oTable.fnGetData( nTr );
			var discip = aData[6].split(" * ");
			var even = Array();
			var prueba = '';
			for (i=0;i<discip.length;i++) {
				even[i] = discip[i].split('|');
				prueba += even[i][0]+" - ";
			}
//			alert(aData);
			prueba = prueba.substr(0, prueba.length - 3);
//			if (discip.length == 2) var even = Array(discip[0].split('|'),discip[1].split('|')); else var even = Array(discip[0].split('|'));  
			var sOut = '<table class="datosOc" cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">';
			sOut += '<tr><td class="neg">Nombre Participante:</td><td>'+aData[1]+'</td><td class="neg">Sexo:</td><td>'+aData[19]+'</td></tr>';
			sOut += '<tr><td class="neg">A&ntilde;o Nac.:</td><td>'+aData[2]+'</td><td class="neg">Categor&iacute;a:</td><td>'+aData[3]+'</td></tr>';
			sOut += '<tr><td class="neg">DNI: </td><td>'+aData[10]+'</td><td class="neg">Tel&eacute;fono:</td><td>'+aData[5]+'</td></tr>';
			sOut += '<tr><td class="neg">Pago:</td><td>'+aData[4]+'</td><td class="neg">Correo: </td><td><a href="mailto:'+aData[6]+'">'+aData[6]+'</a></td></tr>';
			sOut += '<tr><td class="neg">Direcci&oacute;n:</td><td>'+aData[7]+'</td><td class="neg">CP: </td><td>'+aData[9]+'</td></tr>';
			sOut += '<tr><td class="neg">Lugar:</td><td>'+aData[8]+'</td><td class="neg">Colegio: </td><td>'+aData[11]+'</td></tr>';
			sOut += '<tr><td class="neg">Carnet Club Kirolak: </td><td>'+aData[13]+'</td><td class="neg">Padre o Tutor:</td><td>'+aData[15]+'</td></tr>';
			sOut += '<tr><td class="neg">DNI Tutor: </td><td>'+aData[16]+'</td><td class="neg">A Pagar:</td><td>'+aData[17]+' &euro;</td></tr>';
			sOut += '<tr><td class="neg">D&iacute;as: </td><td>'+aData[18]+'</td><td class="neg">Titular de la Cta.: </td><td>'+aData[21]+'</td></tr>';
			
			sOut += '<tr><td class="neg">DNI Titular:</td><td>'+aData[20]+'</td><td class="neg">IBAN:</td><td>'+aData[22]+'</td></tr>';

			sOut += '<tr><td class="neg">Observaciones:</td><td colspan="3">'+aData[12]+'</td></tr>';
			sOut += '</table>';
			return sOut;
		}

		$(document).ready(function() {
			var where = ' a.idevento = 37 and a.id = r.idparticipante ';
			var oTable = $('#example').dataTable( {
					"bProcessing": true,
					"bServerSide": true,
					"sPaginationType": "full_numbers",
					"sAjaxSource": "cargar4.php?tabla=true&where="+where,
					"oLanguage": {
						"sLengthMenu": "Mostrar _MENU_ participantes por p&aacute;gina",
						"sZeroRecords": "No se encontraron coincidencias - lo siento",
						"sInfo": "Mostrando _START_ a _END_ de _TOTAL_ participantes",
						"sInfoEmpty": "Mostrando 0 a 0 de 0 participantes",
						"sProcessing": "Procesando la informaci&oacute;n espere...",
						"oPaginate": {
							"sFirst":    "Primero",
							"sPrevious": "Anterior",
							"sNext":     "Pr&oacute;ximo",
							"sLast":     "Último"
						},
						"sEmptyTable": "No hay datos disponibles en la tabla",
						"sInfoFiltered": "",
						"sSearch": "Buscar:"
					},
//					"bSortable": false, "aTargets": [ 0 ],
					"aaSorting": [[ 0, "desc" ]],
					"aoColumnDefs": [ 
						{ "bVisible": false, "aTargets": [ 4,7,8,9,10,11,12,13,14,16,17,18,19,20,21,22 ] }
					]
			} );		
		

		$('#example tbody tr').live('click', function () {
			var nTr = this;
			var rowSal = '';
			
			( this.className.match('odd') ) ? rowSal = "odd" : rowSal = "even"; 
			if ( this.className.match('details_close') )
			{
				/* This row is already open - close it */
				this.className = rowSal+" details_open";
				oTable.fnClose( nTr );
			}
			else
			{
				/* Open this row */
				this.className = rowSal+" details_close";
				oTable.fnOpen( nTr, fnFormatDetails(oTable, nTr), 'details' );
			}
		} );
		
		$("#example_first").html('<img src="images/ff.png" width="29" height="19" alt="Primera" title="Primera" />');
		$("#example_previous").html('<img src="images/f.png" width="29" height="19" alt="Anterior" title="Anterior" />');
		$("#example_next").html('<img src="images/b.png" width="29" height="19" alt="Siguiente" title="Siguiente" />');
		$("#example_last").html('<img src="images/fb.png" width="29" height="19" alt="Última" title="Última" />');
		
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
				<span class="clEven" style="margin-right: 112px;" >Evento: Escuela de Atletismo de Bilbao 2021 - 2022<!-- <select id="even" name="even">
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
						<th width="20%">Nombre y Apellidos</th>
						<th width="10%">Fecha de Nac.</th>
						<th width="15%">Categor&iacute;a</th>
						<th width="10%">Pago</th>
						<th width="10%">Tel&eacute;fono</th>
						<th width="25%">Correo</th>
						<th width="15%">Direcci&oacute;n</th>
						<th width="15%">Localidad</th>
						<th width="5%">CP</th>
						<th width="25%">DNI</th>
						<th width="45%">Colegio</th>
						<th width="7%">Observaciones</th>
						<th width="15%">Carnet Kirolak</th>
						<th width="15%">F. Solicitud</th>
						<th width="20%">Tutor</th>
						<th width="15%">Tutor</th>
						<th width="15%">Tutor</th>
						<th width="15%">Tutor</th>
						<th width="15%">Tutor</th>
						<th width="15%">Tutor</th>
						<th width="15%">Tutor</th>
						<th width="15%">Tutor</th>
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
						<th>Fecha de Nac.</th>
						<th>Categor&iacute;a</th>
						<th>Pago</th>
						<th>Tel&eacute;fono</th>
						<th>Correo</th>
						<th>Direcci&oacute;n</th>
						<th>Localidad</th>
						<th>CP</th>
						<th>DNI</th>
						<th>Colegio</th>
						<th>Observaciones</th>
						<th>Carnet Kirolak</th>
						<th>F. Solicitud</th>
						<th>Tutor</th>
						<th>Tutor</th>
						<th>Tutor</th>
						<th>Tutor</th>
						<th>Tutor</th>
						<th>Tutor</th>
						<th>Tutor</th>
						<th>Tutor</th>
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
