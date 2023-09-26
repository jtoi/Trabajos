<?php
/**
 * pspfacade.php 
 * 
 * This file describes way the PSP
 * @author Olivier Cheneson <olivier.cheneson@amadeus.com>
 * @version 1.0
 * @package pspfacade.php
 */

/**
 *include the configuration files for mysql
 */

/**
 * Objects that represents the initial variables and input values
 */
$ENC 					= "";
$ENC_TIME 				= "";
$CONFIRMATION_URL 		= "";
$CANCELLATION_URL 		= "";
$CHECKSUM				= "";
$SITE					= "";
$ENC_TYPE				= "";
$EMAIL					= "";
$SESSION_ID				= "";
$LANGUAGE				= "";
$AMOUNT					= "";
$mode					= "";
if(isset($_REQUEST['ENC']))					{	$ENC = $_REQUEST['ENC'];								} else{$ENC = 0;}
if(isset($_REQUEST['ENC_TYPE']))			{	$ENC_TYPE = $_REQUEST['ENC_TYPE'];						} else{$ENC_TYPE = 0;}
if(isset($_REQUEST['ENC_TIME']))			{	$ENC_TIME = $_REQUEST['ENC_TIME'];						}
if(isset($_REQUEST['CONFIRMATION_URL']))	{	$CONFIRMATION_URL = $_REQUEST['CONFIRMATION_URL'];		}
if(isset($_REQUEST['CANCELLATION_URL']))	{	$CANCELLATION_URL = $_REQUEST['CANCELLATION_URL'];		}
if(isset($_REQUEST['SITE']))				{	$SITE = $_REQUEST['SITE'];								}
if(isset($_REQUEST['EMAIL']))				{	$EMAIL = $_REQUEST['EMAIL'];							}
if(isset($_REQUEST['SESSION_ID']))			{	$SESSION_ID = $_REQUEST['SESSION_ID'];					}
if(isset($_REQUEST['LANGUAGE']))			{	$LANGUAGE = $_REQUEST['LANGUAGE'];						}
if(isset($_GET['mode']))					{	$mode = $_GET['mode'];								}
if(isset($_REQUEST['AMOUNT']))					{	$AMOUNT = $_REQUEST['AMOUNT'];								}

$xmlparamfile = "../DBManager/XML/".$SITE."_TP_SiteParameter.xml";

//if (file_exists($xmlparamfile)) {
//	$xml = simplexml_load_file($xmlparamfile);
//	$rows = $xml->xpath('//TABLE/ROW');
//	//print_r ($rows);
//	foreach ($rows as $row){
//		if($row->ParameterName == "EXT_EncKey"){
//			$ENCRYPTION_KEY = $row->ParameterValue;
//		}
//		if($row->ParameterName == "EXT_MerchantID"){
//			$MERCHANT_ID = $row->ParameterValue;
//		}
//	}
//
//} else {
//exit('Erreur !.');
//} 


/**
 * CHECKSUM computation
 */
//$CONFIRMATION_URL = "https://siteacceptance.wftc1.e-travel.com/plnext/pspcuba/BookTripPlan.action;jsessionid=vCpCN1lW691gHmrs3gWvnN6nlhp9VWSJdhCT2TT61yJpS9nGtpvG!1375028344!502853258?PAGE_TICKET=&STATUS=OK&ACTION=BOOK&SITE=ADMPADMP&LANGUAGE=ES&OFFICE_ID=HAVCU08ZZ";
//$CANCELLATION_URL = "https://siteacceptance.wftc1.e-travel.com/plnext/pspcuba/BookTripPlan.action;jsessionid=vCpCN1lW691gHmrs3gWvnN6nlhp9VWSJdhCT2TT61yJpS9nGtpvG!1375028344!502853258?PAGE_TICKET=&STATUS=KO&ACTION=BOOK&SITE=ADMPADMP&LANGUAGE=ES&OFFICE_ID=HAVCU08ZZ";
$ACKNOWLEDGEMENT_URL = "http://www.amadeus.com";
$PAYMENT_REFERENCE = "111111111111";
$ENCRYPTION_KEY = "fgrt34sdsw2";
$MERCHANT_ID = "Cubana";
$StringtoCompute = "";
$StringtoCompute .= $SESSION_ID;
$StringtoCompute .= $PAYMENT_REFERENCE;
$StringtoCompute .= urlencode($ACKNOWLEDGEMENT_URL);
$StringtoCompute .= $ENCRYPTION_KEY;
echo "<br>cadena=$StringtoCompute";
$CHECKSUM = strtoupper(md5($StringtoCompute));



//Update the confirmation url


if($mode == "override"){
	
	$arrayURL = split('/', $CONFIRMATION_URL);	
	$URLArray = split('\.', $arrayURL[sizeof($arrayURL)-1]);	
	$ActionName = $URLArray[0];
	
	if($ActionName == "RebookingBookTripPlan"){
		$ActionName = "BookTripPlan";
		$CONFIRMATION_URL = str_replace("RebookingBookTripPlan", "Override", $CONFIRMATION_URL);
	}else{
		
		$CONFIRMATION_URL = str_replace($ActionName, "Override", $CONFIRMATION_URL);
	}
	$CONFIRMATION_URL .= "&EXTERNAL_ID=PAYMENT&EMBEDDED_TRANSACTION=".$ActionName;	
}






?>

<script type="text/javascript">
function submitRefuse(){

	document.Confirmationfrm.action ="<?php print $CANCELLATION_URL?>";
	document.Confirmationfrm.submit();
	
}






</script>



<form name="Confirmationfrm" id="Confirmationfrm" action="<?php  print $CONFIRMATION_URL;  ?>" method="post">
<table style="display:block;width:800px;"  border="0">
    <tr>
        <td colspan=2 align="center"><font size=5><strong>Implementation Bank</strong></font><br>Yes, we can not</h1>
        </td>

    </tr>	
    <tr>
        <td>SITE
        </td>
        <td style="display:block;width:500px;"><?php  print $SITE;  ?>
        </td>
    </tr>
    <tr>
        <td>ENC
        </td>
        <td><input style="display:block;width:500px;" type="text" name="ENC" value="<?php  print $ENC;  ?>">
        </td>
    </tr>
    <tr>
        <td>ENC_TIME
        </td>
        <td><input style="display:block;width:500px;" type="text" name="ENC_TIME" value="<?php  print $ENC_TIME;  ?>">
        </td>
    </tr>
    <tr>
        <td>SESSION_ID
        </td>
        <td><input style="display:block;width:500px;" type="text" name="SESSION_ID" value="<?php  print $SESSION_ID;  ?>">
        </td>
    </tr>
    <tr>
        <td>ENC_TYPE
        </td>
        <td><input style="display:block;width:500px;" type="text" name="ENC_TYPE" value="<?php  print $ENC_TYPE;  ?>">
        </td>
    </tr>
    <tr>
        <td>ACKNOWLEDGEMENT_URL
        </td>
        <td><input style="display:block;width:500px;" type="text" name="ACKNOWLEDGEMENT_URL" value="<?php  print $ACKNOWLEDGEMENT_URL;  ?>">
        </td>
    </tr>
    <tr>
        <td>FP Format
        </td>
        <td><input style="display:block;width:500px;" type="text" name="SO_SITE_EXT_FP_FORMAT" value="EXT%X"></td>
    </tr>
    <tr>
        <td>PAYMENT_REFERENCE
        </td>
        <td><input style="display:block;width:500px;" type="text" name="PAYMENT_REFERENCE" value="<?php  print $PAYMENT_REFERENCE;  ?>">
        Hey!! i saw you want to change the payment Reference, are you sure ?  ... please check the FOP table first!!</td>
    </tr>
    <tr>
        <td>EMAIL
        </td>
        <td><?php  print $EMAIL;  ?>
        </td>
    </tr>
    <tr>
        <td>AMOUNT
        </td>
        <td><?php  print $AMOUNT;  ?>
        </td>
    </tr>
    <tr bgcolor="pink">
        <td>ENCRYPTION_KEY
        </td>
        <td><?php  print $ENCRYPTION_KEY;  ?>
        </td>
    </tr>
    <tr bgcolor="pink">
        <td>MERCHANT_ID
        </td>
        <td><?php  print $MERCHANT_ID;  ?>
        </td>
    </tr>
    <tr bgcolor="pink">
        <td>SO_GL
        </td>
        <td><textarea id=SO_GL name=SO_GL rows="12" cols="15">
<?php print '<?xml version="1.0" encoding="iso-8859-1"?><SO_GL>
<GLOBAL_LIST mode="complete">
<NAME>SO_SINGLE_MULTIPLE_COMMAND_BUILDER</NAME>
<LIST_ELEMENT>
<CODE>1</CODE>
<LIST_VALUE><![CDATA[RM CONFIRMATION PAYMENT BY OVERRIDE]]></LIST_VALUE>
<LIST_VALUE>S</LIST_VALUE>
</LIST_ELEMENT>
</GLOBAL_LIST>
</SO_GL>';
?>	
</textarea>
        </td>
    </tr>	
	
	
	
	
    <tr bgcolor="green">
        <td>CHECKSUM
        </td>
        <td><input style="display:block;width:500px;" readonly type="text" name="CHECKSUM" value="<?php  print $CHECKSUM;  ?>">
        </td>
    </tr>
    <tr>
        <td>
        </td>
        <td><input type="submit" value="Pay">
        <input type="button" onclick="submitRefuse();" value="Refuse">
        </td>
    </tr>
</table>

<DIV>Parameters posted</DIV><BR>
<?php
	print "<DIV>";
foreach ($_POST as $key => $val) {
    print "<SPAN>" . $key."</SPAN>"." = <SPAN>" . $val."</SPAN><BR>";
}  
	print "</DIV>";
?>
</form>



