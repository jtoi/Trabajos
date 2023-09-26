<?php
	include '../configuracion.php';
	include "class_mysql.php";
	$conn=new conbd();
	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 * Easy set variables
	 */
	
	/* Array of database columns which should be read and sent back to DataTables. Use a space where
	 * you want to insert a non-database field (for example a counter or static image)
	 */
	$arrCateg = array("Txupete", "Pre-benjam&iacute;n","Alevin","Infantil y Cadete","Abierta Fem","Abierta Masc", "Veteranos/as Federados","Federados/as Elite Fem",
						"Federados/as Elite Masc","Benjam&iacute;n");
	$aColumns = array( 'a.id', "concat(a.nombre,' ',a.apellidos )", 'p.nombre categoria', 'date_format(fnac, "%d/%m/%Y") fn', 'tipoDoc', 'cp', 
						'from_unixtime(fechaInsc, "%d/%m/%Y") fi', 'correo', 'licencia_num', 'a.sexo', 'club', 'telfm', 'licencia');
	$where = " p.id = a.idprueba ";

	
	/* Indexed column (used for fast and accurate table cardinality) */
	$sIndexColumn = "a.id";
	
	/* DB table to use */
	$sTable = "participantes a, prueba p";
	
	/* Database connection information */
	$gaSql['user']       = $usr;
	$gaSql['password']   = $cntr;
	$gaSql['db']         = $bd;
	$gaSql['server']     = $srv; 
	
	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 * If you just want to use the basic configuration for DataTables with PHP server-side, there is
	 * no need to edit below this line
	 */
	
	/* 
	 * MySQL connection
	 */
	$gaSql['link'] =  mysql_pconnect( $gaSql['server'], $gaSql['user'], $gaSql['password']  ) or
		die( 'Could not open connection to server' );
	
	mysql_select_db( $gaSql['db'], $gaSql['link'] ) or 
		die( 'Could not select database '. $gaSql['db'] );
	
	
	/* 
	 * Paging
	 */
	$sLimit = "";
	if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
	{
		$sLimit = "LIMIT ".mysql_real_escape_string( $_GET['iDisplayStart'] ).", ".
			mysql_real_escape_string( $_GET['iDisplayLength'] );
	}
	
	
	/*
	 * Ordering
	 */
	if ( isset( $_GET['iSortCol_0'] ) )
	{
		$sOrder = "ORDER BY  ";
		for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
		{
			if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
			{
				$sOrder .= $aColumns[ intval( $_GET['iSortCol_'.$i] ) ]."
				 	".mysql_real_escape_string( $_GET['sSortDir_'.$i] ) .", ";
			}
		}
		
		$sOrder = substr_replace( $sOrder, "", -2 );
		if ( $sOrder == "ORDER BY" )
		{
			$sOrder = "";
		}
	}
	
	
	/* 
	 * Filtering
	 * NOTE this does not match the built-in DataTables filtering which does it
	 * word by word on any field. It's possible to do here, but concerned about efficiency
	 * on very large tables, and MySQL's regex functionality is very limited
	 */
	$sWhere = "";
	if ( $_GET['sSearch'] != "" )
	{
		$sWhere = "WHERE (";
		for ( $i=0 ; $i<count($aColumns) ; $i++ )
		{
			$sWhere .= $aColumns[$i]." LIKE '%".mysql_real_escape_string( $_GET['sSearch'] )."%' OR ";
		}
		$sWhere = substr_replace( $sWhere, "", -3 );
		$sWhere .= ')';
	}
	
	/* Individual column filtering */
	for ( $i=0 ; $i<count($aColumns) ; $i++ )
	{
		if ( $_GET['bSearchable_'.$i] == "true" && $_GET['sSearch_'.$i] != '' )
		{
			if ( $sWhere == "" )
			{
				$sWhere = "WHERE ";
			}
			else
			{
				$sWhere .= " AND ";
			}
			$sWhere .= $aColumns[$i]." LIKE '%".mysql_real_escape_string($_GET['sSearch_'.$i])."%' ";
		}
	}
	
	if (isset ($_REQUEST['where'])) {
		if (strstr($sWhere, 'WHERE')) $sWhere .= " and ".$_REQUEST['where']; else $sWhere .= "where ".$_REQUEST['where'];
	
	}
	
	if (strlen($where) > 3) {
		if ($sWhere == "") $sWhere .= "where ".$where; else $sWhere .= " and ".$where;  
	
	}
	/*
	 * SQL queries
	 * Get data to display
	 */
	$sQuery = "SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns))." FROM   $sTable $sWhere $sOrder $sLimit";
	$helpq = $sQuery;
	// echo($sQuery);
	$rResult = mysql_query( $sQuery, $gaSql['link'] ) or die(mysql_error());
	
	/* Data set length after filtering */
	$sQuery = "
		SELECT FOUND_ROWS()
	";
	$rResultFilterTotal = mysql_query( $sQuery, $gaSql['link'] ) or die(mysql_error());
	$aResultFilterTotal = mysql_fetch_array($rResultFilterTotal);
	$iFilteredTotal = $aResultFilterTotal[0];
	
	/* Total data set length */
	$sQuery = "
		SELECT COUNT(".$sIndexColumn.")
		FROM   $sTable
	";
	$rResultTotal = mysql_query( $sQuery, $gaSql['link'] ) or die(mysql_error());
	$aResultTotal = mysql_fetch_array($rResultTotal);
	$iTotal = $aResultTotal[0];
	
	
	/*
	 * Output
	 */
	$output = array(
		"sEcho" => intval($_GET['sEcho']),
		"iTotalRecords" => $iTotal,
		"iTotalDisplayRecords" => $iFilteredTotal,
		"aaData" => array()
	);
	error_log(intval($_GET['sEcho']));
	error_log($iTotal);
	error_log($iFilteredTotal);
	
	error_log(json_encode($output));
	
	while ( $aRow = mysql_fetch_array( $rResult ) ) {
		$row = array();
		$prueb = '';
		$salida = '';
		for ( $i=0 ; $i<count($aColumns) ; $i++ ) {
			if ( $aColumns[$i] != ' ' ) {
				/* General output */
				$row[] = utf8_decode($aRow[ $i ]);
				if ($i == 1) {
					
				} 
				if ($i == 3) {
				}
			}
		}
		$output['aaData'][] = $row;
	}
	error_log("sale=".json_encode($output));

	echo json_encode( $output );
?>