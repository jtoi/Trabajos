<?php defined( '_VALID_ENTRADA' ) or die( 'Restricted access' );

function genTabs($camino, $entrada) {
	$cod_tab = str_replace("<?php defined( '_VALID_ENTRADA' ) or die( 'Restricted access' ); ?>", '', read_file($camino.'tab.html.php'));
	$salto = explode('{corte}', $cod_tab);
	
	$tripa = '';
	for ($x = 0; $x < count($entrada) ; $x++) {
		$tripa .= str_replace('{titulo}', $entrada[$x], $salto[1]);
		if ($x == 0) $tripa = str_replace('tabs', 'tabs_active', $tripa);
	}

	$cod_tabGen = $salto[0].$tripa.$salto[2];
	$cod_tabGen = str_replace('{camino}', $camino, $cod_tabGen);
	return $cod_tabGen;
}

?>
