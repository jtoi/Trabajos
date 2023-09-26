<?php
require_once('nusoap.php');
$wsdl="http://www.rfea.es/rfeaCarnet/services/WebSrvValidarInformacion?wsdl";
$client=new soapiclient($wsdl, 'wsdl');
$param=array('in0'=>$_POST['lic'], 'in1'=>$_POST['carnet'], 'in2'=>$_POST['fecha'], 'in3'=>'', 'in4'=>'', 'in5'=>'2012BI002', 'in6'=>'kirolak', 'in7'=>'prT5lIvD');
//sleep(10);
//echo "<response><Estado>KO</Estado><CarnetPlus></CarnetPlus><Licencia>34563456</Licencia><Anio>1960</Anio><TipoLicencia></TipoLicencia><Identificador></Identificador><Nombre></Nombre><Apellidos></Apellidos><FechaNacimiento></FechaNacimiento><Sexo></Sexo><CP></CP></response>";
//echo "<response><Estado>OK</Estado><CarnetPlus></CarnetPlus><Licencia>M2552</Licencia><Anio>1972</Anio><TipoLicencia>N</TipoLicencia><Identificador>11822094W</Identificador><Nombre>LUIS MIGUEL</Nombre><Apellidos>MARTIN BERLANAS</Apellidos><FechaNacimiento>11/01/1972</FechaNacimiento><Sexo>0</Sexo><CP>28231</CP></response>";
echo $client->call('validadInformacion', $param);
?>