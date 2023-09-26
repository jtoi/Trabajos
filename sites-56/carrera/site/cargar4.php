<?php
include '../configuracion.php';

if ($_REQUEST['tabla']) {
	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 * Easy set variables
	 */
	
	/* Array of database columns which should be read and sent back to DataTables. Use a space where6
	 * you want to insert a non-database field (for example a counter or static image)
	 * 
	 * 
	 */
	$aColumns = array( 'a.id', 
						'upper(concat(a.nombre, " ", apellidos))', 
						'date_format(fnac, "%e/%c/%Y")', 
						'case idprueba when 99 then "Prebenjamin" when 100 then "Benjamin" when 101 then "Alevin" when 102 then "Infantil" when 103 then "Cadete" when 104 then "Juvenil" else "Junior" end prueba', 
						'case licencia_num when 2 then "Prueba" else "Mensualidad" end', //4
						'telf', 
						'a.correo', 
						'upper(direccion)', 
						'upper(localidad)', 
						'cp', 
						'doc', 
						'upper(pais)', 
						'observaciones', 
						'carnet', //13
						'from_unixtime(fechaInsc, "%d/%m/%Y")', 
						'r.nombre', 
						'r.tel', 
						"case tipoDoc when '' then '0,00' else tipoDoc end", 
						'case idprueba when 103 then "Lunes, Mi&eacute;rcoles y Viernes" when 104 then "Lunes, Mi&eacute;rcoles y Viernes" when 105 then "Lunes, Mi&eacute;rcoles y Viernes" when 102 then "Martes, Jueves y Viernes" else "Martes y Jueves" end',
						'sexo',
						'provincia',
						'club',
						'pin'
					); //19
	
	/* Indexed column (used for fast and accurate table cardinality) */
	$sIndexColumn = "a.id";
	
	/* DB table to use */
	$sTable = "participantes a, representantes r";

	include_once('cargar.php');

	}
?>
