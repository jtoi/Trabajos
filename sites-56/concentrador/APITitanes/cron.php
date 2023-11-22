<?php define('_VALID_ENTRADA', 1);
require_once('configuration.php');
require_once 'mysqli.php';
require_once 'funciones.php';
$temp = new ps_DB;

$q = "select IdTitanes from tit_Personas Where Active = 0 or Admited = 0";

	$temp->query($q);
	$arrElem = $temp->loadAssocList();

	foreach ($arrElem as $key => $value) {
		personSummary(json_encode(array("PersonId"=>$value['IdTitanes'])));
	}

?>