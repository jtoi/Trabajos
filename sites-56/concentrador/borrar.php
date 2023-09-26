<style>
th.rotate {
  /* Something you can count on */
  height: 140px;
  white-space: nowrap;
}

th.rotate > div {
  transform: 
    /* Magic Numbers */
    translate(-15px, 45px)
    /* 45 is really 360 - 45 */
    rotate(270deg);
  width: 40px;
}
th.rotate > div > span {
  padding: 5px 10px;
}
</style>

<table cellpadding="0" cellspacing="0" align="center">
	<tr>
		<th class="rotate"><div><span>10 kilograms</span></div></th>
		<th class="rotate"><div><span>20 kilograms</span></div></th>
		<th class="rotate"><div><span>30 kilograms</span></div></th>
		<th class="rotate"><div><span>40 kilograms</span></div></th>
		<th class="rotate"><div><span>50 kilograms</span></div></th>
	</tr>
    <tr>
        <td class="uno">A</td>
        <td class="uno">B</td>
        <td class="uno">C</td>
        <td class="uno">D</td>
        <td class="uno">E</td>
    </tr>
    <tr>
        <td>X</td>
        <td>G</td>
        <td>H</td>
        <td>I</td>
        <td>J</td>
    </tr>
    <tr>
        <td>L</td>
        <td>L</td>
        <td>M</td>
        <td>N</td>
        <td>O</td>
    </tr>
    <tr>
        <td>Q</td>
        <td>Q</td>
        <td>R</td>
        <td>S</td>
        <td>T</td>
    </tr>
    
    <tr>
        <td>A</td>
        <td>B</td>
        <td>C</td>
        <td>D</td>
        <td>E</td>
    </tr>
    <tr>
        <td>G</td>
        <td>G</td>
        <td>H</td>
        <td>I</td>
        <td>J</td>
    </tr>
    <tr>
        <td>L</td>
        <td>L</td>
        <td>M</td>
        <td>N</td>
        <td>O</td>
    </tr>
    <tr>
        <td>Q</td>
        <td>Q</td>
        <td>R</td>
        <td>S</td>
        <td>T</td>
    </tr>
    
    <tr>
        <td>B</td>
        <td>B</td>
        <td>C</td>
        <td>D</td>
        <td>E</td>
    </tr>
    
</table>
	
<?php

/* 
 * Un fichero de prueba para verificar la captura de las tasas de cambio
 * en Yahoo Finances

//paso uno el normal
define( '_VALID_ENTRADA', 1 );

include_once( 'configuration.php' );
include_once( 'admin/classes/entrada.php' );
include 'include/mysqli.php';
include_once( 'admin/adminis.func.php' );

$temp = new ps_DB;
$horaTasa = mktime(14, 0, 0, date("m"), date("d"), date("Y"));

$q = "select moneda from tbl_moneda where idmoneda != 978";
$texto .= $q."<br>";
$temp->query($q);
$den = $temp->loadResultArray();
$tasaInc = leeSetup('incBCE'); //incremento sobre la tasa de cambio
		
$c = curl_init();
curl_setopt( $c, CURLOPT_URL,  'http://www.floatrates.com/daily/eur.xml');
curl_setopt( $c, CURLOPT_SSL_VERIFYPEER, false );
curl_setopt( $c, CURLOPT_HTTPHEADER, array('Content-type: text/xml; charset=utf-8',));
curl_setopt( $c, CURLOPT_RETURNTRANSFER, true );
$xml_response = curl_exec($c);
curl_close($c);                

$sxml= new SimpleXMLElement($xml_response);

$rates= array();
foreach($sxml->item as $item) {
	$rates[(string)$item->targetCurrency] = (str_replace(',','',$item->exchangeRate));
}

if (count($rates) > 10) {
	foreach($den as $item) {
		$q = "insert into tbl_cambio (visa, moneda, fecha) values (".($rates[$item]+$tasaInc).", '$item', $horaTasa)";
		echo $q."<br>";
	}
}
 */
?>