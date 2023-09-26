<?php

/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
*/
class tablaHTML  {
	
	var $columnas			= '';
	var $tablas				= '';
	var $buscar				= '';
	var $orden				= '';
	var $numpag				= 0;
	var $largpagina			= 1;
	var $trad				= false; //si true, trata de realizar la traducción del campo
	private $numrows		= 0;
	private $numcols		= 0;
	private $sale			= '';
	
	function __construct() {}

	function __destruct() {}
	
	function tabla() {
		$temp = new ps_DB();
		$this->preparDatos();
		
		$q = "select {sel} from ". $this->tablas;
		if (strlen($this->buscar) > 0){
			$q .= " where ". $this->buscar;
		}
		
		error_log (str_replace("{sel}", "count(*) total", $q));
		//calculando la cantidad de records de la query
		$temp->query(str_replace("{sel}", "count(*) total", $q));
		//envía para preparar la paginación
		$this->paginacion($temp->f('total'));
		
		//ejecuta la query principal
		$liminf = ($this->numpag - 1) * $this->largpagina;
		
		if (strlen($this->orden)) {
			$this->cambOrden();
			$q .= " order by " . $this->orden;
		}
		$q .= " limit " . $liminf . "," . $this->largpagina;
		error_log (str_replace("{sel}", $this->limpcolum($this->columnas), $q));
		$temp->query(str_replace("{sel}", $this->limpcolum($this->columnas), $q));
//		$this->cuerpo($temp->loadAssocList());
		$this->cuerpo($temp->loadRowList());
		return $this->sale;
	}
	
	/**
	 * Pone las comillas en el orden para permitir ordenar por
	 * frases pj. Fecha Modificada
	 * @return boolean
	 */
	private function cambOrden() {
		$cade = $ord = '';
		$arrOr = explode(',', $this->orden);
		$this->orden = '';
		for ($i=0; $i<count($arrOr); $i++){
			if (stristr($arrOr[$i], 'asc')) $ord = " asc";
			else $ord = " desc";
			$this->orden .= "`".str_replace("'", "", str_replace($ord, "", $arrOr[$i]))."` $ord,";
		}
		$this->orden = rtrim($this->orden, ',');
		return true;
	}

	/**
	 * Quita de los nombres de las columnas los tipos para que puedan ser ejecutados
	 * correctamente por mysql
	 * @param strin $param columna a limpiar
	 * @return string
	 */
	private function limpcolum($param) {
		$arrLimp = array('{borr}', '{edit}', '{fec}', '{mail}', '{col}', '{diner}');
		foreach ($arrLimp as $limp) {
			$param =  str_replace($limp, '', $param);
		}
		$param = str_replace('¬', ',', $param);
		return $param;
	}
	
	/**
	 * Construye cada línea de datos de la tabla
	 * @param integer $filaId Id de la Fila
	 * @param array $arrParam 
	 * @trad boolean trata de traducir el campo si tiene la traducción
	 * @return boolean
	 */
	private function consRopw($filaId=0, $arrParam=array(), $tipoCol=array()) {
		global $fun;
		
//		var_dump($tipoCol);
		$col = $arrParam[array_keys($tipoCol, "col")[0]];
		if (strlen($col) > 1) {$this->sale .= '<tr style="color:'.$col.'">';}
		else {$this->sale .= '<tr>';}
//		$idRow = $arrParam[array_keys($tipoCol, "id")[0]];
		$ttol = count($arrParam);
		$calTtol = 1;
		for ($j = 0; $j < count($tipoCol); $j++) {
			if ($ttol == $calTtol)	break;
			switch ($tipoCol[$j]) {
				case 'fec':
					if ($arrParam[$j] == 0) { $res = '-';}
					else {$res = date($_SESSION['fechaf'].' '.$_SESSION['horaf'], $arrParam[$j]);}
					$this->sale .= '<td>' . $res . '</td>';
					$calTtol++;
					break;
				case 'edit':
					$this->sale .= '<td><i class="glyphicon glyphicon-pencil" onclick="javascript:tipoEdit('.$arrParam[$j].')"></i></td>';
					break;
				case 'mail':
					$this->sale .= '<td><a href="mailto:'.$arrParam[$j].'">'.$arrParam[$j].'</a></td>';
					$calTtol++;
					break;
				case 'borr':
					$this->sale .= '<td><i class="glyphicon glyphicon-trash" onclick="javascript:tipoBorra('.$arrParam[$j].')"></i></td>';
					break;
				case 'diner':
					$this->sale .= '<td>'.$fun->frdinero($arrParam[$j]).'</td>';
					break;
				default:
					if ($this->trad) {
//						error_log("campo=".$campo);
//						error_log("arrParam=".$arrParam[$j]);
						if (!$campo = $fun->idioma($arrParam[$j])) {$campo = $arrParam[$j];}
					} else {$campo = $arrParam[$j];}
					
					if ($tipoCol[$j] != 'id' && $tipoCol[$j] != 'col') {
						$this->sale .= '<td>' . $campo . '</td>';
						$calTtol++;
					}
					break;
			}
		}
		$this->sale .= '</tr>';
		return true;
	}
	
	/**
	 * Construye la tabla propiamente dicha
	 * @param array $arrParam Array de la query que origina la tabla
	 * @return boolean
	 */
	private function cuerpo($arrParam) {
//		var_dump($arrParam[0]);
		if (strpos($this->columnas, "¬")) {
			$arrCol = explode('¬', $this->columnas);
		} else {
			$arrCol = explode(',', $this->columnas);
		}
		
		$this->sale .= '<main class="col-md-6 offset-sm-3 col-lg-12 offset-md-6 pt-3"><div class="table-responsive"><table class="table table-striped table-hover"><thead>';
		$tipoCol = array();
		for ($i = 0; $i < count($arrParam); $i++) {
			$colOrd = $ordOrd = $tod = '';
			$salt = 0;
			$arrSal = array();
			if ($i == 0) { //Linea del encabezado de la tabla
				$this->sale .= '<tr>';
				for ($j = 0; $j<count($arrCol); $j++) {
					$orden = 1;
					$adic = '';
					if (stripos($arrCol[$j], ' ') > -1) {
						if (stripos($arrCol[$j], '{fec}') > -1) { //determina las columnas que llevan fecha
							$tipoCol[] = 'fec';
						} elseif (stripos($arrCol[$j], '{mail}') > -1) { //determmina las columnas con tratamiento de correos
							$tipoCol[] = 'mail';
						} elseif (stripos($arrCol[$j], '{edit}') > -1) { //determmina las columnas para editar artículo
							$tipoCol[] = 'edit';
							$orden = 0;
						} elseif (stripos($arrCol[$j], '{borr}') > -1) { //determmina las columnas para borrar
							$tipoCol[] = 'borr';
							$orden = 0;
						} elseif (stripos($arrCol[$j], '{ip}') > -1) { //determmina las columnas con tratamiento de ips
							$tipoCol[] = 'ip';
						} elseif (stripos($arrCol[$j], '{diner}') > -1) { //determmina las columnas con tratamiento de valores
							$tipoCol[] = 'diner';
						} elseif (stripos($arrCol[$j], '{col}') > -1) { //determmina las columnas con tratamiento de valores
							$tipoCol[] = 'col';
						}
						else {
							$tipoCol[] = '';
						}
						
						if (stripos($arrCol[$j], 'end ') > -1) {
							$arrscol[0] = substr($arrCol[$j], 0, stripos($arrCol[$j], 'end ')+3);
							$arrscol[1] = substr($arrCol[$j], stripos($arrCol[$j], 'end ')+4);
						} else {
							$arrscol = explode(" '", $arrCol[$j]);
						}
						
						$tod = str_replace("'", '', $this->limpcolum($arrscol[1]));
						if (stripos($this->orden, $tod) > -1 ) {
							$adic = ' <i class="glyphicon glyphicon-menu-down"></i>';
							if (stripos($this->orden, ' desc') == 0) {
								$ordOrd = ' desc';
								$adic = ' <i class="glyphicon glyphicon-menu-up"></i>';
							} else {
								$ordOrd = ' asc';
								$adic = ' <i class="glyphicon glyphicon-menu-down"></i>';
							}
						} else  $ordOrd = ' desc';
						
						if ($orden) {
							$this->sale .= "<th><a class=\"encTabl\" href=\"javascript:cambOrden('$tod$ordOrd')\">" .$tod.$adic."</a></th>";
						} else {
							$this->sale .= '<th>' .$tod.'</th>';
						}
						
					} else {
						if (stripos($arrCol[$j], 'id') > -1) {//la columna con el id de la tabla
							$arrSal[] = $arrParam[$i][$salt];
							$tipoCol[] = 'id';
							$salt++;
						} 
//						elseif (stripos($arrCol[$j], 'editar') > -1) {//la columna para Editar el record
//							$this->sale .= '<th>Editar</th>';
//							$arrSal[] = '';
//							$tipoCol[] = 'mod';
//						} elseif (stripos($arrCol[$j], 'borr') > -1) {// La columna para borrar el record
//							$this->sale .= '<th>Borrar</th>';
//							$arrSal[] = '';
//							$tipoCol[] = 'borr';
//						}
					}
					
				}
				$this->sale .= '</tr></thead><tbody>';
			} 
				$this->consRopw($salt, $arrParam[$i], $tipoCol);
			
		}
		$this->sale .= '</tbody></table></div></main>';
		return true;
		
	}
	
	/**
	 * Prepara los datos que se pasan para ser ejecutados en la query ppal
	 * @return boolean
	 */
	private function preparDatos () {
		if (stripos($this->columnas, '¬')) {
			$this->columnas = str_replace("¬ ", "¬", $this->columnas);
		} else {
			$this->columnas = str_replace(", ", "¬", $this->columnas);
		}
		$this->orden = str_replace(", ", ",", $this->orden);
		$arrcol = explode("¬", $this->columnas);
		$this->numcols = count($arrcol);
		return true;
	}
	
	/**
	 * Prepara el encabezado de paginación de la tabla
	 * @param int $numrows cantidad de líneas por página
	 */
	private function paginacion($numrows) {
		$fun = new funcion;
		
		$this->sale .= '<div class="ver"><ul class="pagination">';
		$cantpags = $numrows/$this->largpagina;
		for ($i = 0; $i < $cantpags; $i++) {
			$pag = $i+1;
			$this->sale .= '<li';
			if ($this->numpag == $pag) $this->sale .= ' class="active"';
			$this->sale .= '><a href="javascript:cambPag('. $pag.');">'.$pag.'</a></li>';
		}
		
		//si el número de la pagina actual es 1 o es el número máximo de páginas de 
		//la query se pone a sí mismo
		$pagAtr = $this->numpag - 1;
		$pagAde = $this->numpag + 1;
		if ($this->numpag == 1) $pagAtr = $this->numpag;
		if ($this->numpag >= $cantpags) $pagAde = $this->numpag;
		
		$this->sale .= '</ul><div class="btn-group btn-group botonesup" style="float: right; margin: 5px !important"><a href="javascript:cambPag('. $pagAtr .');" id="btnPagAtr" class="btn btn-m btn-primary pg-bton">'.$fun->idioma('Previo').'</a><a href="javascript:cambPag('. $pagAde .');" id="btnPagAde" class="btn btn-m btn-primary pg-bton">'.$fun->idioma('Proximo').'</a></div></div>';
	}
	
}

?>

