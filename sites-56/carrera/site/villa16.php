<?php session_start();
include_once '../configuracion.php';
require "class_mysql.php";
global $conn;
//print_r($_POST);

if ($_POST['export'] == 'csv') {
	$file = (date("mdHis")).(rand (0, 99)).".csv";
	$urlPassCSV = "desc/".$urlPass.$file;

	$contenido = "Id,Atleta Nombre y Apellidos,Tel. Móvil,Correo,Sexo,Año Nac.,Club,Licencia no.,Prueba\n";
	
	$sql="select a.id, concat(a.nombre,' ',apellidos) atleta, telfm, correo, 
				a.sexo, date_format(fnac, '%Y') fnac, club, licencia_num, p.nombre prueba
			from participantes a, prueba p where a.idevento = 19 and a.idprueba = p.id";
    
	$result=$conn->execute($sql) or die(mysql_error());
	
	while($item =  mysql_fetch_object($result)){
		$contenido .= $item->id.",".utf8_decode(strtoupper($item->atleta)).",".$item->telfm.",".strtolower($item->correo).",".$item->sexo.",".$item->fnac.
					",".$item->club.",".$item->licencia_num.",".utf8_decode(strtoupper($item->prueba))."\n";
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
    <meta name="author" content="Alejandro D&iacute;z Cadavid modificado por Julio Toirac (jtoirac@gmail.com)" />
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1" />
	<link rel="stylesheet" href="images/style.css" type="text/css" />
        <title>Inscripci&oacute;n de Atletas</title>
        <script type="text/javascript" src="js/jquery.js"></script>
	<!--<script type="text/javascript" src="js/jlogin.js"></script>-->
	<script type="text/javascript" src="js/jquery.dataTables.min.js"></script>
	<script type="text/javascript">
		function fnFormatDetails ( oTable, nTr ){
			var aData = oTable.fnGetData( nTr );
//			if (discip.length == 2) var even = Array(discip[0].split('|'),discip[1].split('|')); else var even = Array(discip[0].split('|'));
			var sOut = '<table class="datosOc" cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">';
			sOut += '<tr><td class="neg">Nombre Participante:</td><td>'+aData[1]+'</td><td class="neg">Prueba:</td><td>'+aData[8]+'</td></tr>';
			sOut += '<tr><td class="neg">A&ntilde;o Nac.:</td><td>'+aData[3]+'</td><td class="neg">Sexo:</td><td>'+aData[2]+'</td></tr>';
			sOut += '<tr><td class="neg">Correo:</td><td><a href="mailto:'+aData[5]+'">'+aData[5]+'</a></td><td class="neg">Tel. M&oacute;vil:</td><td>'+aData[4]+'</td></tr>';
			sOut += '<tr><td class="neg">Club:</td><td>'+aData[7]+'</td><td class="neg">Licencia:</td><td>'+aData[6]+'</td></tr>';
			sOut += '</table>';
			return sOut;
		}

		$(document).ready(function() {
			var where = ' a.idevento = 19 and a.idprueba = p.id ';
			var oTable = $('#example').dataTable( {
					"bProcessing": true,
					"bServerSide": true,
					"sPaginationType": "full_numbers",
					"sAjaxSource": "cargarvilla16.php?where="+where,
					"oLanguage": {
						"sLengthMenu": "Mostrar _MENU_ participantes por p&aacute;gina",
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
					"aaSorting": [[ 0, "desc" ]],
					"bSortable": false, "aTargets": [ 0 ]//,
// 					"aoColumnDefs": [ 
// 						{ "bVisible": false, "aTargets": [ 6 ] }
// 					]
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
				<!--<span class="clEven">Evento: <select id="even" name="even">
					<option value="5">Milla Marina Femenina</option>
					<option value="1">Milla Internacional</option>
					<option value="4" selected="true">Villa de Bilbao / 2013</option>
					<option value="3">8va. Milla Internacional / 2013</option>
				</select></span>-->
				<input type="hidden" value="csv" name="export" />
				<input type="image" style="cursor: pointer;float: right;margin-right: 20px;" src="images/excel.jpg" title="Exportar a CSV" alt="Exportar a CSV" />
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
						<th width="7%">Sexo</th>
						<th width="10%">Fecha de Nac.</th>
						<th width="15%">Telf M&oacute;vil</th>
						<th width="15%">Correo</th>
						<th width="15%">Licencia</th>
						<th width="45%">Localidad</th>
						<th width="45%">Prueba</th>
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
						<th>Sexo</th>
						<th>Fecha de Nac.</th>
						<th>Telf M&oacute;vil</th>
						<th>Correo</th>
						<th>Licencia</th>
						<th>Localidad</th>
						<th>Prueba</th>
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
