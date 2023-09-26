<?php
foreach ($_REQUEST as $key => $value) {
	error_log("$key => $value");
}

?>