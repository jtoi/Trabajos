<?php session_start();
require "class_mysql.php";
global $conn;

if ($_POST['export'] == 'csv') {
	$file = (date("mdHis")).(rand (0, 99)).".csv";
	$urlPassCSV = "desc/".$urlPass.$file;

	$contenido = "Id,Nombre y Apellidos,Identif,No. Identif,Sexo,Fecha Nac.,Categoría,Dirección,Localidad,Provincia,País,Nacionalidad,CP,Tel. fijo,Tel. Móvil,Correo,Club,Licencia no.\n";
	$sql="select a.id, concat(a.nombre,' ',apellidos) nombre, sexo, tipoDoc, doc, fnac, u.corto prueba, direccion, localidad, provincia, pais, nacionalidad,
				cp, telf, telfm, correo, club, licencia_num
			from participantes a, prueba u where u.id = idprueba";
	$result=$conn->execute($sql) or die(mysql_error());
	while($item =  mysql_fetch_object($result)){
		$contenido .= '"'.$item->id.'","'.utf8_decode(strtoupper($item->nombre)).'","'.utf8_decode(strtoupper($item->tipoDoc)).'","'.utf8_decode(strtoupper($item->doc)).'","'.
						$item->sexo.'","'.$item->fnac.'","'.$item->prueba.'","'.utf8_decode(strtoupper($item->direccion)).'","'.
						utf8_decode(strtoupper($item->localidad)).'","'.utf8_decode(strtoupper($item->provincia)).'","'.utf8_decode(strtoupper($item->pais)).'","'.
						utf8_decode(strtoupper($item->nacionalidad)).'","'.strtoupper($item->cp).'","'.$item->telf.'","'.$item->telfm.'","'.strtolower($item->correo).'","'.
						$item->club.'","'.$item->licencia_num.'"'."\n";
	}
	
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


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="es" lang="es">
<head>
    <meta name="author" content="Alejandro D&iacute;z Cadavid modificado por Julio Toirac (jtoirac@gmail.com)" />
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1" />
	<link rel="stylesheet" href="images/style.css" type="text/css" />
        <title>Inscripción de Atletas</title>
        <script type="text/javascript" src="js/jquery.js"></script>
	<script type="text/javascript" src="js/jlogin.js"></script>
	<script type="text/javascript" src="js/jquery.dataTables.min.js"></script>
	<script type="text/javascript">
		function fnFormatDetails ( oTable, nTr ){
			var aData = oTable.fnGetData( nTr );
			var sOut = '<table class="datosOc" cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">';
			sOut += '<tr><td>Nombre Participante:</td><td>'+aData[1]+'</td><td>Dirección:</td><td>'+aData[5]+'</td></tr>';
			sOut += '<tr><td>Tipo Identificación:</td><td>'+aData[12]+'</td><td>Identificación:</td><td>'+aData[13]+'</td></tr>';
			sOut += '<tr><td>Categoría:</td><td>'+aData[2]+'</td><td>Localidad:</td><td>'+aData[6]+'</td></tr>';
			sOut += '<tr><td>Sexo:</td><td>'+aData[3]+'</td><td>CP:</td><td>'+aData[7]+'</td></tr>';
			sOut += '<tr><td>Año Nac.:</td><td>'+aData[4]+'</td><td>Correo:</td><td><a href="mailto:'+aData[10]+'">'+aData[10]+'</a></td></tr>';
			sOut += '<tr><td>Tel. Fijo:</td><td>'+aData[8]+'</td><td>Telf. Móvil:</td><td>'+aData[9]+'</td></tr>';
			sOut += '<tr><td>Licencia:</td><td>'+aData[11]+'</td><td>Provincia:</td><td>'+aData[14]+'</td></tr>';
			sOut += '<tr><td>País:</td><td>'+aData[15]+'</td><td>Nacionalidad:</td><td>'+aData[16]+'</td></tr>';
			sOut += '<tr><td>Club:</td><td>'+aData[17]+'</td><td>Carnet:</td><td>'+aData[18]+'</td></tr>';
			sOut += '<tr><td>Pin:</td><td>'+aData[19]+'</td><td></td><td></td></tr>';
			sOut += '</table>';
			return sOut;
		}

		$(document).ready(function() {
			var where = ' a.idprueba = u.id and a.idevento = 1 ';
			var oTable = $('#example').dataTable( {
					"bProcessing": true,
					"bServerSide": true,
					"sPaginationType": "full_numbers",
					"sAjaxSource": "cargar2.php?where="+where,
					"oLanguage": {
						"sLengthMenu": "Mostrar _MENU_ participantes por página",
						"sZeroRecords": "No se encontraron coincidencias - lo siento",
						"sInfo": "Mostrando _START_ a _END_ de _TOTAL_ participantes",
						"sInfoEmpty": "Mostrando 0 a 0 de 0 participantes",
						"sProcessing": "Procesando la información espere...",
						"oPaginate": {
							"sFirst":    "Primero",
							"sPrevious": "Anterior",
							"sNext":     "Próximo",
							"sLast":     "Último"
						},
						"sEmptyTable": "No hay datos disponibles en la tabla",
						"sInfoFiltered": "",
						"sSearch": "Buscar:"
					},
					"aaSorting": [[ 1, "asc" ]],
					"bSortable": false, "aTargets": [ 0 ],
					"aoColumnDefs": [ 
						{ "bVisible": false, "aTargets": [ 5 ] },
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
						{ "bVisible": false, "aTargets": [ 16 ] },
						{ "bVisible": false, "aTargets": [ 17 ] },
						{ "bVisible": false, "aTargets": [ 18 ] },
						{ "bVisible": false, "aTargets": [ 19 ] }
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
			<h1><a href="#" title="Inscripci&oacute;n de atletas">INSCRIPCI&Oacute;N DE <span class="red">ATLETAS</span></a></h1>
		</div>
		<div id="logueduser">Conectado como: <span style="color:brown;"><?php echo $_SESSION["user"]?></span></div>
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
				<span class="clEven">Evento: <select id="even">
					<option value="5">Milla Marina Femenina</option>
					<option value="1" selected="true">Milla Internacional</option>
					<option value="4">Villa de Bilbao / 2013</option>
					<option value="3">8va. Milla Internacional / 2013</option>
				</select></span>
				<input type="image" value="csv" name="export" style="cursor: pointer;float: right;margin-right: 20px;" src="images/excel.jpg" title="Exportar a CSV" alt="Exportar a CSV" />
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
						<th width="25%">Nombre y Apellidos</th>
						<th width="45%">Categoría</th>
						<th width="7%">Sexo</th>
						<th width="10%">Fecha de Nac.</th>
						<th width="15%">Direccion</th>
						<th width="15%">Localidad</th>
						<th width="15%">CP</th>
						<th width="15%">Telf Fijo</th>
						<th width="15%">Telf Móvil</th>
						<th width="15%">Correo</th>
						<th width="15%">Licencia No.</th>
						<th width="15%">Tipo ident.</th>
						<th width="15%">Identificacion</th>
						<th width="15%">Provincia</th>
						<th width="15%">País</th>
						<th width="15%">Nacionalidad</th>
						<th width="15%">Club</th>
						<th width="15%">Carnet</th>
						<th width="15%">Pin</th>
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
						<th>Categoría</th>
						<th>Sexo</th>
						<th>Fecha de Nac.</th>
						<th>Direccion</th>
						<th>Localidad</th>
						<th>CP</th>
						<th>Telf Fijo</th>
						<th>Telf Móvil</th>
						<th>Correo</th>
						<th>Licencia No.</th>
						<th>Tipo ident.</th>
						<th>Identificacion</th>
						<th>Provincia</th>
						<th>País</th>
						<th>Nacionalidad</th>
						<th>Club</th>
						<th>Carnet</th>
						<th>Pin</th>
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
		
            <form name="login">
		
			<p>Usuario:&nbsp;&nbsp;&nbsp;&nbsp; <input type="text" name="user" id="user" /></p>
			<p>Contrse&ntilde;a: <input type="password" name="pwd" id="pwd" /></p>
			<br /><input type="button" name="go" id="go" value="Entrar" style="margin-left:28px;" />
			<input type="reset" name="rst" id="rst" value="Cancelar" />
			</form>
		
	</div>
	<div id="output"></div>
<?php } ?>
</body>
</html>
