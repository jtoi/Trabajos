<?php

/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
*/
class tablaHTML {
	
	var $idio			= 'es';
	var $tituloPag		= 'Inicio';
	var $java			= '';
	var $anchoTabla		= 400;
	var $anchoCeldaI	= 200;
	var $anchoCeldaD	= 200;
	var $tituloTarea	= 'Tarea';
	var $classCss		= 'formul';
	var $maxLenght		= 150;
	var $contenido		= '';
	var $tripa			= '';
	var $tabed			= '';
	var $hide			= false;							// hace desaparecer / aparecer el formulario y lo reemplaza por un texto
	var $gifAlm			= '../images/almanaque2.gif';
	var $hidden			= '';								// variable que almacena los campos hidden del formulario
	var $finDiv			= '</div></div>';
	var $pathJS			= "../";
	
	function __construct() {}

	//genera pares de options en base a una query los elementos seleccionados son multiples pasados en el array $id
	//las columnas en la query pasada deben tener como alias
	//id y nombre
	function opciones_sel_Arr($select, $id = null, $todos = null, $primer = null) {
		$cadena_orden = '';

		$grup = new ps_DB();
		$grup->query($select);
		$arrResult = $grup->loadAssocList();

//		si debe mostrar el primer option del select como la sumatoria de todos los elementos
		if ($todos) {
			$ids = explode("', '", $id);
			$i=0;
			foreach ($ids as $item) {
				$salArr[$i++]=$item;
			}

			if (count(array_intersect($salArr, $arrResult)) == count($arrResult	)) 
				$cadena_orden .= '<option selected value="' . implode("', '", $grup->loadResultArray()) . '">Todos</option>' . "\n";
			else $cadena_orden .= '<option value="' . implode("', '", $grup->loadResultArray()) . '">Todos</option>' . "\n";
		}

		if ($primer) {
			$cadena_orden .= '<option selected value="0">Todos</option>' . "\n";
		}
		
		//se construyen las option normales de acuerdo al select enviado
		foreach ($arrResult as $item) {
			if (is_array($id) && in_array($item['id'], $id))
				$cadena_orden .= '<option selected value="' . $item['id'] . '">' . utf8_decode($item['nombre']) . '</option>' . "\n";
			elseif ($item['id'] == $id)
				$cadena_orden .= '<option selected value="' . $item['id'] . '">' . utf8_decode($item['nombre']) . '</option>' . "\n";
			else
				$cadena_orden .= '<option value="' . $item['id'] . '">' . utf8_decode($item['nombre']) . '</option>' . "\n";
		}
		return $cadena_orden;
	}

	/**
	 * Para la subida de Ficheros
	 *
	 * @param [type] $texto
	 * @param [type] $nombre
	 * @param [type] $id
	 * @param [type] $texFin
	 * @return void
	 */
	function inFile($texto, $nombre, $id = null, $texFin = null){
		$css = $this->classCss;
		if ($id == null) $id = $nombre;
		// if ($aclar != null) $texto = '<spam class="'.$this->class.'"><label for="'.$id.'" title="'.$aclar.'">'.$texto.'</label></span>';
		// if (strpos($adicional, 'disabled')>-1) $css .= " elmDisab";
		
		$this->tripa .= $this->iniDiv($texto, $id).
					'<input type="file" name="' . $nombre . '" class="' .$css. '" id="' . $id . '" />'.$texFin.$this->finDiv;
	}

	/**
	 * genera pares de options en base a una query los elementos seleccionados son multiples pasados en el array $id
	 * las columnas en la query pasada deben tener como alias id y nombre, en síntesis, 
	 * como la anterior pero poniedo las comillas en la opción todo para que no haya que estarlas poniendo en la pág donde se use
	 * 
	 * @param type $select
	 * @param type $id
	 * @param type $todos
	 * @return string 
	 */
	function opciones_sel_ArrS($select, $id = null, $todos = null) {
		$cadena_orden = '';

		$grup = new ps_DB();
		$grup->query($select);
		$arrResult = $grup->loadResultArray();

//		si debe mostrar el primer option del select como la sumatoria de todos los elementos
		if ($todos) {
			$ids = explode("','", $id);
			$i=0;
			foreach ($ids as $item) {
				$salArr[$i++]=$item;
			}

			if (count(array_intersect($salArr, $arrResult)) == count($arrResult	)) 
				$cadena_orden .= '<option selected value="\'' . implode("','", $grup->loadResultArray()) . '\'">Todos</option>' . "\n";
			else $cadena_orden .= '<option value="\'' . implode("','", $grup->loadResultArray()) . '\'">Todos</option>' . "\n";
		}
		
		//se construyen las option normales de acuerdo al select enviado
		while ($grup->next_record()) {
			if (is_array($id) && in_array($grup->f('id'), $id))
				$cadena_orden .= '<option selected value="' . $grup->f('id') . '">' . utf8_decode($grup->f('nombre')) . '</option>' . "\n";
			elseif ($grup->f('id') == $id)
				$cadena_orden .= '<option selected value="' . $grup->f('id') . '">' . utf8_decode($grup->f('nombre')) . '</option>' . "\n";
			else
				$cadena_orden .= '<option value="' . $grup->f('id') . '">' . utf8_decode($grup->f('nombre')) . '</option>' . "\n";
		}
		return $cadena_orden;
	}

	/**
	 * genera pares de options en base a una query las columnas en la query pasada deben tener como alias id y nombre
	 * 
	 * @param string $select
	 * @param string $id
	 * @return string 
	 */
	function opciones_sel($select, $id = null, $tipo = 1, $nombre = null) {
		$cadena_orden = '';
		$i=0;
		
		$grup = new ps_DB();
		$grup->query($select);
		while ($grup->next_record()) {
			if ($tipo == 1) {
				if ($grup->f('id') == $id)
					$cadena_orden .= '<option selected value="' . $grup->f('id') . '">' .utf8_decode( $grup->f('nombre')) . '</option>' . "\n";
				else
					$cadena_orden .= '<option value="' . $grup->f('id') . '">' . utf8_decode($grup->f('nombre')) . '</option>' . "\n";
			} elseif ($tipo == 2) {
				if (in_array($grup->f('id'), $id))
					$cadena_orden .= '<div class="radElm" ><input type="checkbox" checked="checked" id="'.$nombre.'_'.$i.'" class="' . $this->classCss . '" name="' . $nombre . 
										'" value="'. $grup->f('id') .'" /><label for="'.$nombre.'_'.$i.'" >'. utf8_decode($grup->f('nombre')) .'</label></div>';
				else
					$cadena_orden .= '<div class="radElm" ><input type="checkbox" id="'.$nombre.'_'.$i.'" class="' . $this->classCss . '" name="' . $nombre . 
										'" value="'. $grup->f('id') .'" /><label for="'.$nombre.'_'.$i.'" >'. utf8_decode($grup->f('nombre')) .'</label></div>';
				$i++;
			}
		}
		return $cadena_orden;
	}

	/**
	 * genera pares de options en base a arrays pasados
	 * 
	 * @param array $valores_arr
	 * @param string $val_sel
	 * @return string 
	 */
	function opciones_arr($valores_arr, $val_sel) {
// 		if (_MOS_PHP_DEBUG) trigger_error("Valores array= $valores_arr");
		$cadena_orden = '';
		for ($x = 0; $x < count($valores_arr); $x++) {
			error_log ("VALORES- ".$valores_arr[$x][0]. "== $val_sel");
			if (!is_array($val_sel)) {
				if ($valores_arr[$x][0] == $val_sel)
					$cadena_orden .= '<option selected value="' . $valores_arr[$x][0] . '">' . $valores_arr[$x][1] . '</option>' . "\n";
				else
					$cadena_orden .= '<option value="' . $valores_arr[$x][0] . '">' . $valores_arr[$x][1] . '</option>' . "\n";
			} else {
				if (in_array($valores_arr[$x][0], $val_sel)) 
					$cadena_orden .= '<option selected value="' . $valores_arr[$x][0] . '">' . $valores_arr[$x][1] . '</option>' . "\n";
				else
					$cadena_orden .= '<option value="' . $valores_arr[$x][0] . '">' . $valores_arr[$x][1] . '</option>' . "\n";
			}
			
		}
		return $cadena_orden;
	}

	/**
	 * genera pares de options en base a dos números
	 * @param integer $inicio
	 * @param integer $final
	 * @param integer $id
	 * @return string 
	 */
	function opciones($inicio, $final, $id) {
		$cadena_orden = '';
		for ($x = $inicio; $x <= $final; $x++) {
			if ($x == $id) $cadena_orden .= '<option selected value="' . $x . '">' . $x . '</option>' . "\n";
			else $cadena_orden .= '<option value="' . $x . '">' . $x . '</option>' . "\n";
		}
		return $cadena_orden;
	}

	/**
	 * Construye los encabezados de los formularios
	 */
	function inicio($sigue=false) {
		if ($sigue) 
			$cont = '<div style="margin-bottom:30px; width:100%; float:left;"><div style="width:' . 
				$this->anchoTabla . 'px" class="tabTodo"><div class="title_pag1">' . $this->tituloPag . 
				'</div><form action="" method="post" enctype="multipart/form-data" name="admin_form_'.  rand(0000, 9999).
				'" onsubmit="return(true);">'.$this->hidden.'<div class="title_tarea1"><div class="title_tarea22"><span '
				.'class="title_tarea2"></span><span style="width:' . ($this->anchoTabla - 18) . 'px" class="title_tarea3">'. 
				$this->tituloTarea.'</span><span class="title_tarea4"></span><div style="width:' . ($this->anchoTabla + 2) . 
				'px" class="title_tarea33">';
		else
			$cont = '<div style="margin-bottom:30px; width:100%; float:left;"><div style="width:' . $this->anchoTabla . 'px" class="tabTodo"><div class="title_pag1">' . $this->tituloPag . '</div><form action="" method="post" enctype="multipart/form-data" name="admin_form_'.  rand(0000, 9999).'" onsubmit="return(verifica());">'.$this->hidden.'<div class="title_tarea1"><div class="title_tarea22"><span class="title_tarea2"></span><span style="width:' . ($this->anchoTabla - 18) . 'px" class="title_tarea3">'. $this->tituloTarea.'</span><span class="title_tarea4"></span><div style="width:' . ($this->anchoTabla + 2) . 'px" class="title_tarea33">';
		if ($this->hide) {
//			Encabezado para formularios con hide
			$cont .= '<div id="divFormHid">';
		}
		
		if ($this->tabed) {
//			Encabezado para formularios con hide
			$cont .= '<div id="divFormHid1">';
		}
				
		$this->contenido = $this->java . $cont;
	}
	
	
	function medio($num) {
		$this->tripa .= '<div class="botForm" style="width:' . $this->anchoTabla . 'px"><input class="formul" id="enviaForm'.$num.'" onclick="$(\'#divFormHid'.($num-1).'\').css(\'display\', \'none\');$(\'#divFormHid'.$num.'\').css(\'display\', \'block\');" name="enviar" type="button" value="' . _FORM_SIGUIENTE . '" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name="reset" type="reset" class="formul cancel" value="' . _FORM_CANCEL . '" /></div></div><div id="divFormHid'.$num.'">';
	}

	/**
	 * Escribe el cierre del formulario con la botonera
	 * 
	 * @param string $botones
	 * @param string $texto
	 * @param string $onhide Texto del label para mostrar y esconder el formulario
	 * @return string 
	 */
	function salida($botones = null, $texto = null, $sigue = false) {
		$this->inicio($sigue);

		if ($texto) $this->contenido .= '<div style="width:' . $this->anchoTabla . 'px" class="textForm">'.$texto.'</div>';
		$this->contenido .= $this->tripa;
		$this->contenido .= '<div class="botForm" style="width:' . $this->anchoTabla . 'px">';
		if (!$botones) $this->contenido .= '<input class="formul" id="enviaForm" name="enviar" type="submit" value="' . _FORM_SEND . '" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name="reset" type="reset" id="reset" class="formul cancel" value="' . _FORM_CANCEL . '" />';
		else $this->contenido .= $botones;
		$this->contenido .= '</div>';
		
		if ($this->hide) {
			$this->contenido .= '</div><div id="onhideText" style="width:' . ($this->anchoTabla) . 'px"><span>'. _FORM_MOSTRAR .'</span></div>';
			$this->contenido .= '<script language="javascript">$("#onhideText span").toggle(function(){$("#divFormHid").css("display", "block");$("#onhideText").removeClass("onshowTextShow");$("#onhideText").addClass("onhideTextShow");$("#onhideText span").get(0).innerHTML = "'._FORM_OCULTAR.'&nbsp;&nbsp;&nbsp;";},function(){$("#divFormHid").css("display", "none");$("#onhideText span").get(0).innerHTML = "'._FORM_MOSTRAR.'";$("#onhideText").removeClass("onhideTextShow");$("#onhideText").addClass("onshowTextShow");});</script>';
		}
		
		if ($this->tabed) {
			$this->contenido .= '</div><script language="javascript">$(document).ready(function(){$("#divFormHid2").css("display", "none");$("#divFormHid3").css("display", "none");$("#divFormHid4").css("display", "none");$("#divFormHid5").css("display", "none");$("#divFormHid6").css("display", "none");});$(".cancel").click(function(){$("#divFormHid1").css("display", "block");$("#divFormHid2").css("display", "none");$("#divFormHid3").css("display", "none");$("#divFormHid4").css("display", "none");$("#divFormHid5").css("display", "none");$("#divFormHid6").css("display", "none");});</script>';
		}
		
		return $this->contenido .= '</div></div></div></form></div></div>';
	}

	/**
	 * Crea un text box hidden con los datos de entrada
	 *
	 * @param string $valor
	 * @param string $nombre
	 * @param string $id id del text box, si no se suministra usa el mismo nombre
	 * @param string $adicional permite adicionar una serie de parámetros al textbox directamente pj. readonly, javascript, etc
	 */
	function inHide($valor, $nombre, $id = null, $adicional = null) {
		if ($id == null) $id = $nombre;
		$this->hidden .= '<input type="hidden" value="' . $valor .'" name="' . $nombre .'"	id="'. $id .'" '.$adicional.' />';
	}

	/**
	 * Construye las divs
	 *
	 * @param string $texto
	 * @param string $id
	 * @return string
	 */
	function iniDiv($texto, $id) {
		return '<div id="div_'.$id.'" style="width:' . $this->anchoTabla . 'px" class="lineaT"><div style="width:' . $this->anchoCeldaI . 'px" class="derecha1">' . $texto . ':</div>
				<div style="width:' . $this->anchoCeldaD . 'px" class="izquierda1">';
	}
	
	/**
	 * Construye una caja con Div para poner elementos del formulario dentro y luego 
	 * poder ocultarlos y mostrarlos con javascript
	 * @param string $id id del div por el que debe ser llamado
	 * @param string $clase clase con la que se aplicar css según fichero de css
	 * @param string $estilo estilo que le será aplicado
	 */
	function inCajaini($id, $clase, $estilo) {
		$this->tripa .= '<div id="caj_'.$id.'" class="'.$clase.'" style="'.$estilo.'">';
	}
	
	/**
	 * Cierra la caja Div construida con la función anterior
	 */
	function inCajaout(){
		$this->tripa .= '</div>';
	}

	/**
	 *Crea un text box con los datos de entrada
	 *
	 * @param string $texto
	 * @param string $valor
	 * @param string $nombre
	 * @param string $id id del text box, si no se suministra usa el mismo nombre
	 * @param string $texFin texto que se adiciona al final del textbox como aclaratoria, una marca de obligatorio al llenar, etc
	 * @param string $adicional permite adicionar una serie de parámetros al textbox directamente pj. readonly, javascript, etc
	 * @param string $aclar label aclaratorio que saldrá en mouseover sobre texto
	 */
	function inTextb($texto, $valor, $nombre, $id = null, $texFin = null, $adicional = null, $aclar = null, $clase = null) {
		if ($id == null) $id = $nombre;
		if ($aclar != null) $texto = '<spam class="haslabel"><label for="'.$id.'" title="'.$aclar.'">'.$texto.'</label></span>';

		$this->tripa .= $this->iniDiv($texto, $id).
						'<input maxlength="' . $this->maxLenght . '" class="' . $this->classCss . ' '.$clase.'" type="text" value="' . $valor . '" name="' . $nombre . '" id="' . $id . '" '.$adicional.' />'.$texFin.$this->finDiv;
	}
	
	/**
	 *Crea un password box con los datos de entrada
	 *
	 * @param string $texto
	 * @param string $valor
	 * @param string $nombre
	 * @param string $id id del text box, si no se suministra usa el mismo nombre
	 * @param string $texFin texto que se adiciona al final del textbox como aclaratoria, una marca de obligatorio al llenar, etc
	 * @param string $adicional permite adicionar una serie de parámetros al textbox directamente pj. readonly, javascript, etc
	 * @param string $aclar label aclaratorio que saldrá en mouseover sobre texto
	 */
	function inPass($texto, $valor, $nombre, $id = null, $texFin = null, $adicional = null, $aclar = null) {
		if ($id == null) $id = $nombre;
		if ($aclar != null) $texto = '<spam class="haslabel"><label for="'.$id.'" title="'.$aclar.'">'.$texto.'</label></span>';

		$this->tripa .= $this->iniDiv($texto, $id).
						'<input maxlength="' . $this->maxLenght . '" class="' . $this->classCss . '" type="password" value="' . $valor . '" name="' . $nombre . '" id="' . $id . '" '.$adicional.' />'.$texFin.$this->finDiv;
	}
	
	/**
	 * Crea un select
	 * tipo 1: Corresponde a un select con query pero para múltiples elementos seleccionados
	 * tipo 2: Una query con un solo elemento seleccionado
	 * tipo 3: Pares de opciones generados en base a un array
	 * tipo 4: Opciones generadas en base a dos números (la entrada es un array con ambos números)
	 * tipo 5: Corresponde a un select con query pero para múltiples elementos seleccionados y el primer elemento de la lista es el conjunto de todos los otros
	 *
	 * @param string $texto
	 * @param string $nombre nombre del valor
	 * @param integer $tipo
	 * @param array $valInicio
	 * @param array $valSelec
	 * @param string $id Identificador Id
	 * @param string $aclar label aclaratorio que saldrá en mouseover sobre texto
	 * @param string $adicional permite adicionar una serie de parámetros al textbox directamente pj. readonly, javascript, etc
	 */
	function inSelect($texto, $nombre, $tipo, $valInicio, $valSelec = null, $id = null, $aclar = null, $adicional = null) {
		if ($id == null) $id = $nombre;
		if ($aclar != null) $texto = '<spam class="haslabel"><label for="'.$id.'" title="'.$aclar.'">'.$texto.'</label></span>';

		if (preg_match('/multiple/', $adicional)) $this->tripa .= $this->iniDiv($texto, $id).'<select class="' . $this->classCss . '" name="' . $nombre . '[]" id="' . $id . '" '.$adicional.'>';
		else $this->tripa .= $this->iniDiv($texto, $id).'<select class="' . $this->classCss . '" name="' . $nombre . '" id="' . $id . '" '.$adicional.'>';

		switch ($tipo) {
			case 1:
				$this->tripa .= $this->opciones_sel_Arr($valInicio, $valSelec);
				break;
			case 2:
				$this->tripa .= $this->opciones_sel($valInicio, $valSelec);
				break;
			case 3:
				$this->tripa .= $this->opciones_arr($valInicio, $valSelec);
				break;
			case 4:
				$this->tripa .= $this->opciones($valInicio[0], $valInicio[1], $valSelec);
				break;
			case 5:
				$this->tripa .= $this->opciones_sel_Arr($valInicio, $valSelec, true); 
				break;
			case 6:
				$this->tripa .= $this->opciones_sel_ArrS($valInicio, $valSelec, true);
				break;
			case 7:
				$this->tripa .= $this->opciones_sel_Arr($valInicio, $valSelec, null, true); //query con varias opciones y la primera tiene id 0
				break;

		}

		$this->tripa .= '</select>'.$this->finDiv;
	}

	/**
	 * Crea un select
	 * tipo 1: Corresponde a un select con query pero para múltiples elementos seleccionados
	 * tipo 2: Una query con un solo elemento seleccionado
	 * tipo 3: Pares de opciones generados en base a un array
	 * tipo 4: Opciones generadas en base a dos números (la entrada es un array con ambos números)
	 *
	 * @param string $texto
	 * @param string $nombre
	 * @param integer $tipo
	 * @param array $valInicio
	 * @param array $valSelec
	 * @param string $id
	 * @param string $aclar label aclaratorio que saldrá en mouseover sobre texto
	 * @param string $adicional permite adicionar una serie de parámetros al textbox directamente pj. readonly, javascript, etc
	 */
	function inCheckBox($texto, $nombre, $tipo, $valInicio, $valSelec = null, $id = null, $aclar = null, $adicional = null) {
		if ($id == null) $id = $nombre;
		if ($aclar != null) $texto = '<spam class="haslabel"><label for="'.$id.'" title="'.$aclar.'">'.$texto.'</label></span>';

		$this->tripa .= $this->iniDiv($texto, $id);

		switch ($tipo) {
			case 1:
				$this->tripa .= $this->check_elem_Arr($valInicio, $nombre, $valSelec);
				break;
			case 2:
				$this->tripa .= $this->opciones_sel($valInicio, $valSelec, 2, $nombre);
				break;
			case 3:
				$this->tripa .= $this->check_arr($valInicio, $valSelec, $nombre);
				break;
			case 4:
				$this->tripa .= $this->opciones($valInicio[0], $nombre, $valInicio[1], $valSelec);
				break;
			case 5:
				$this->tripa .= '<input type="checkbox" id="'.$nombre.'" value="'. $valInicio .'"  class="' . $this->classCss . '" name="' . $nombre . '" '.$adicional.' />';

		}

		$this->tripa .= $this->finDiv;
	}

	/**
	 * genera pares de options en base a arrays pasados
	 * 
	 * @param array $valores_arr
	 * @param string $val_sel
	 * @return string 
	 */
	function check_arr($valores_arr, $val_sel, $nombre) {
		$cadena_orden = '';
		for ($x = 0; $x < count($valores_arr); $x++) {
//			echo $valores_arr[$x][0]." == $val_sel<br>";
			if (in_array($valores_arr[$x][0], $val_sel)) 
					$cadena_orden .= '<label for="'.$nombre.'"><input id="'.$nombre.
						'" type ="checkbox" checked="checked" value="' . $valores_arr[$x][0] . '">' . $valores_arr[$x][1] . '</label>' . "\n";
				else
					$cadena_orden .= '<label for="'.$nombre.'"><input id="'.$nombre.
						'" type ="checkbox" value="' . $valores_arr[$x][0] . '">' . $valores_arr[$x][1] . '</label>' . "\n";
		}
		return $cadena_orden;
	}

//genera pares de options en base a una query los elementos seleccionados son multiples pasados en el array $id
	//las columnas en la query pasada deben tener como alias
	//id y nombre
	function check_elem_Arr($select, $nombre, $id = null) {
		$cadena_orden = '';

		$grup = new ps_DB();
		$grup->query($select);

		while ($grup->next_record()) {
			if (is_array($id) && in_array($grup->f('id'), $id))
				$cadena_orden .= '<input type="checkbox" checked=true onclick="cargaCaract(this)" id="' . $grup->f('id') . '" value="' . $grup->f('id') . '"  class="' . $this->classCss . '" name="' . $nombre . '">';
			elseif ($grup->f('id') == $id)
				$cadena_orden .= '<input type="checkbox" checked=true onclick="cargaCaract(this)" id="' . $grup->f('id') . '" value="' . $grup->f('id') . '"  class="' . $this->classCss . '" name="' . $nombre . '">';
			else
				$cadena_orden .= '<input type="checkbox" onclick="cargaCaract(this)" id="' . $grup->f('id') . '" value="' . $grup->f('id') . '"  class="' . $this->classCss . '" name="' . $nombre . '">';
			$cadena_orden .= ' <label for="' . $grup->f('id') . '">gggg' . $grup->f('nombre') . '</label>' . "<br>";
		}
		return $cadena_orden;
	}

	/**
	 * Crea un textbox para entrar fechas
	 * 
	 * @param string $texto texto a mostrar al cliente
	 * @param string $valor valor de entrada si lo tuviera
	 * @param string $nombre nombre del campo
	 * @param string $id id del textbox que mostrará la fecha es obligatoria
	 * @param string $altImag texto al mostrar al pasar el mouse por encima de la imágen que muestra el calendario
	 * @param string $aclar label aclaratorio que saldrá en mouseover sobre texto
	 * @param string $adicional permite adicionar una serie de parámetros al textbox directamente pj. readonly, javascript, etc si $es null por defecto se escribe readonly
	 * @param string $textFin adiciona un texto al final del textbox
	 */
	function inFecha($texto, $valor, $nombre, $id=null, $altImag=null, $aclar = null, $adicional = null, $texFin = '&nbsp;(dd/mm/yyyy)') {
		$maxLenght = $this->maxLenght;
		$this->maxLenght = 10;
		if (!$adicional) $adicional = 'readonly ';
		//$texFin = '<input type="image" src="'.$this->gifAlm.'" alt="'.$altImag.'"  title="'.$altImag.'" name="entr" id="entra" onClick="return showCalendar(\''.$id.'\', \'dd/mm/y\');" />&nbsp;(dd/mm/yyyy)';
		$this->inTextb($texto, $valor, $nombre, $id, $texFin, $adicional, $aclar);
		$this->maxLenght = $maxLenght;
	}
	
	/**
	 *Crea un text area con los datos de entrada
	 *
	 * @param string $texto texto a mostrar al cliente
	 * @param string $valor valor por defecto del textarea
	 * @param string $nombre nombre del campo
	 * @param integer $rows cantidad de líneas en el textarea
	 * @param string $id id del text box, si no se suministra usa el mismo nombre
	 * @param string $texFin texto que se adiciona al final del textbox como aclaratoria, una marca de obligatorio al llenar, etc
	 * @param string $adicional permite adicionar una serie de parámetros al textbox directamente pj. readonly, javascript, etc
	 * @param integer $cols cantidad de columnas en el textarea
	 * @param string $aclar label aclaratorio que saldrá en mouseover sobre texto
	 */
	function inTexarea($texto, $valor, $nombre, $rows, $id = null, $texFin = null, $adicional = null, $cols = null, $aclar = null) {
		if ($id == null) $id = $nombre;
		if ($aclar != null) $texto = '<spam class="haslabel"><label for="'.$id.'" title="'.$aclar.'">'.$texto.'</label></span>';

		$this->tripa .= $this->iniDiv($texto, $id).'
						<textarea cols="'.$cols.'" rows="'.$rows.'" class="' . $this->classCss . '" type="text" name="' . $nombre . '" id="' . $id . '" '.$adicional.' />' . $valor . '</textarea>'.$texFin.$this->finDiv;
	}

	/**
	 * Crea un grupo de radio buttons
	 *
	 * @param string $texto texto a mostrar al cliente
	 * @param array $valorIni array con los valores que tomarán los radio buttons
	 * @param string $nombre nombre del campo
	 * @param array $etiq array con los nombres de las etiquetas de los radios
	 * @param string $valor valor por defecto
	 * @param string $aclar label aclaratorio que saldrá en mouseover sobre texto
	 * @param boolean $separador si es true los radios se separan por cambio de línea sino por espacios
	 */
	function inRadio($texto, $valorIni, $nombre, $etiq, $valor, $aclar = null, $separador = false ) {
		if (!$separador) $separador = '&nbsp;&nbsp;&nbsp;&nbsp;';
		else $separador = '<br />';
		if ($aclar != null) $texto = '<spam class="haslabel"><label for="'.$nombre.'" title="'.$aclar.'">'.$texto.'</label></span>';
		$this->tripa .= $this->iniDiv($texto, $nombre);
		for ($i=0; $i< count($valorIni); $i++) {
			if ($valor == $valorIni[$i]) $adic = ' checked '; else $adic = '';
			$this->tripa .= '<input type="radio" id="'.$i.'_'.$nombre.'" '.$adic.' name="'.$nombre.'" class="'.$nombre.'" value="'.$valorIni[$i].'" />
					<label for="'.$i.'_'.$nombre.'" >'.$etiq[$i].'</label>'.$separador;
		}
		$this->tripa .= $this->finDiv;
	}
	
	/**
	 * Permite en el formulario poner texto solamente 
	 * @param string $texto
	 * @param string $valor 
	 */
	function inTexto($texto, $valor) {
		$this->tripa .= $this->iniDiv($texto, "textoSimple").
						'<span class="' . $this->classCss . '" >'.$valor.'</span>'.$this->finDiv;
	}

	/**
	 * Permite en el formulario poner texto solamente en una línea completa
	 * @param string $texto
	 * @param string $idDiv id del layer donde se mostrara texto
	 */
	function inTextoL($texto, $idDiv = null) {
		$this->tripa .= '<div id="div_'.$idDiv.'" style="width:' . $this->anchoTabla . 'px" class="lineaT"><div id="'.$idDiv.'" style="width:' . ($this->anchoTabla - 10) . 'px" class="centro1"><span class="' . $this->classCss . '" >'.$texto.'</span></div></div>';
	}
	
}

?>

