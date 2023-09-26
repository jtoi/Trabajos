<?php
require("class.filetotext.php");

//$docObj = new Filetotext("test.docx");
$docObj = new Filetotext("../valores.pdf");
$return = $docObj->convertToText();

echo( json_encode($return ) );
