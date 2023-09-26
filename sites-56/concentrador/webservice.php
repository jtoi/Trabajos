<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

//include 'lib/BBva_class.php';
//$bbva = new Model_BbvaClient();

$palabra = 'qwertyasdf0123456789';
$terminal = '1';
$valor = '145';
$moneda = '978';
$idtr = 'AA43623462';
$codcom = '332263839';
$pan = '4548812049400004';
$cvv = '123';
$tipo = 'A';
$term = '1';
$fec = '1214';

//$arr = $bbva->sendPeticion($idtr, $pan, $fec, $cvv, $valor);
//
//foreach ($arr as $key => $value) {
//    echo "$key => $value<br>";
//}

$firma = sha1($valor . $idtr . $codcom . $moneda . $pan . $cvv . $tipo . $palabra);

//$cad = array("datoEntrada"=>"<DATOSENTRADA>"
//        . "<DS_MERCHANT_AMOUNT>$valor</DS_MERCHANT_AMOUNT>"
//        . "<DS_MERCHANT_ORDER>$idtr</DS_MERCHANT_ORDER>"
//        . "<DS_MERCHANT_MERCHANTCODE>$codcom</DS_MERCHANT_MERCHANTCODE>"
//        . "<DS_MERCHANT_CURRENCY>$moneda</DS_MERCHANT_CURRENCY>"
//        . "<DS_MERCHANT_PAN>$pan</DS_MERCHANT_PAN>"
//        . "<DS_MERCHANT_CVV2>$cvv</DS_MERCHANT_CVV2>"
//        . "<DS_MERCHANT_TRANSACTIONTYPE>$tipo</DS_MERCHANT_TRANSACTIONTYPE>"
//        . "<DS_MERCHANT_TERMINAL>$term</DS_MERCHANT_TERMINAL>"
//        . "<DS_MERCHANT_EXPIRYDATE>$fec</DS_MERCHANT_EXPIRYDATE>"
//        . "<DS_MERCHANT_MERCHANTSIGNATURE>$firma</DS_MERCHANT_MERCHANTSIGNATURE>"
//    . "</DATOSENTRADA>");

$params = array(
    "DS_MERCHANT_AMOUNT"=>$valor,
    "DS_MERCHANT_ORDER"=>$idtr,
    "DS_MERCHANT_MERCHANTCODE"=>$codcom,
    "DS_MERCHANT_CURRENCY"=>$moneda,
    "DS_MERCHANT_PAN"=>$pan,
    "DS_MERCHANT_CVV2"=>$cvv,
    "DS_MERCHANT_TRANSACTIONTYPE"=>$tipo,
    "DS_MERCHANT_TERMINAL"=>$term,
    "DS_MERCHANT_EXPIRYDATE"=>$fec,
    "DS_MERCHANT_MERCHANTSIGNATURE"=>$firma
);

/*$sale = '<wsdl:definitions targetNamespace="http://webservice.sis.sermepa.es" xmlns:apachesoap="http://xml.apache.org/xml-soap" xmlns:impl="http://webservice.sis.sermepa.es" xmlns:intf="http://webservice.sis.sermepa.es" xmlns:wsdl="http://schemas.xmlsoap.org/wsdl/" xmlns:wsdlsoap="http://schemas.xmlsoap.org/wsdl/soap/" xmlns:xsd="http://www.w3.org/2001/XMLSchema">
  <wsdl:types>
    <schema elementFormDefault="qualified" targetNamespace="http://webservice.sis.sermepa.es" xmlns="http://www.w3.org/2001/XMLSchema" xmlns:apachesoap="http://xml.apache.org/xml-soap" xmlns:impl="http://webservice.sis.sermepa.es" xmlns:intf="http://webservice.sis.sermepa.es" xmlns:wsdl="http://schemas.xmlsoap.org/wsdl/">
   <element name="trataPeticion">
    <complexType>
     <sequence>
      <element name="datoEntrada" nillable="true" type="xsd:string"/>
      '.$cad.'
     </sequence>
    </complexType>
   </element>
   <element name="trataPeticionResponse">
    <complexType>
     <sequence>
      <element name="trataPeticionReturn" nillable="true" type="xsd:string"/>
     </sequence>
    </complexType>
   </element>
   <element name="consultaDCC">
    <complexType>
     <sequence>
      <element name="datoEntrada" nillable="true" type="xsd:string"/>
     </sequence>
    </complexType>
   </element>
   <element name="consultaDCCResponse">
    <complexType>
     <sequence>
      <element name="consultaDCCReturn" nillable="true" type="xsd:string"/>
     </sequence>
    </complexType>
   </element>
  </schema>
  </wsdl:types>
  <wsdl:message name="consultaDCCResponse">
    <wsdl:part element="intf:consultaDCCResponse" name="parameters"/>
  </wsdl:message>
  <wsdl:message name="trataPeticionResponse">
    <wsdl:part element="intf:trataPeticionResponse" name="parameters"/>
  </wsdl:message>
  <wsdl:message name="consultaDCCRequest">
    <wsdl:part element="intf:consultaDCC" name="parameters"/>
  </wsdl:message>
  <wsdl:message name="trataPeticionRequest">
    <wsdl:part element="intf:trataPeticion" name="parameters"/>
  </wsdl:message>
  <wsdl:portType name="SerClsWSEntrada">
    <wsdl:operation name="trataPeticion">
      <wsdl:input message="intf:trataPeticionRequest" name="trataPeticionRequest"/>
      <wsdl:output message="intf:trataPeticionResponse" name="trataPeticionResponse"/>
    </wsdl:operation>
    <wsdl:operation name="consultaDCC">
      <wsdl:input message="intf:consultaDCCRequest" name="consultaDCCRequest"/>
      <wsdl:output message="intf:consultaDCCResponse" name="consultaDCCResponse"/>
    </wsdl:operation>
  </wsdl:portType>
  <wsdl:binding name="SerClsWSEntradaSoapBinding" type="intf:SerClsWSEntrada">
    <wsdlsoap:binding style="document" transport="http://schemas.xmlsoap.org/soap/http"/>
    <wsdl:operation name="trataPeticion">
      <wsdlsoap:operation soapAction=""/>
      <wsdl:input name="trataPeticionRequest">
        <wsdlsoap:body use="literal"/>
      </wsdl:input>
      <wsdl:output name="trataPeticionResponse">
        <wsdlsoap:body use="literal"/>
      </wsdl:output>
    </wsdl:operation>
    <wsdl:operation name="consultaDCC">
      <wsdlsoap:operation soapAction=""/>
      <wsdl:input name="consultaDCCRequest">
        <wsdlsoap:body use="literal"/>
      </wsdl:input>
      <wsdl:output name="consultaDCCResponse">
        <wsdlsoap:body use="literal"/>
      </wsdl:output>
    </wsdl:operation>
  </wsdl:binding>
  <wsdl:service name="SerClsWSEntradaService">
    <wsdl:port binding="intf:SerClsWSEntradaSoapBinding" name="SerClsWSEntrada">
      <wsdlsoap:address location="https://sis-t.redsys.es:25443/sis/services/SerClsWSEntrada"/>
    </wsdl:port>
  </wsdl:service>
</wsdl:definitions>';*/

//$headers = array(             
//    "Content-type: text/xml;charset=\"utf-8\"", 
//    "Accept: text/xml", 
//    "Cache-Control: no-cache", 
//    "Pragma: no-cache", 
//    "SOAPAction: \"run\"", 
//    "Content-length: ".strlen($cad),
//);

$url = "https://sis-t.redsys.es:25443/sis/services/SerClsWSEntrada";
//$url = "http://localhost/concentrador/dalesoap.php";

//$chx = curl_init($url);
//curl_setopt($chx, CURLOPT_POST, false);
//curl_setopt($chx, CURLOPT_HEADER, false);
//curl_setopt($chx, CURLOPT_HTTPHEADER, $headers);
//curl_setopt($chx, CURLOPT_RETURNTRANSFER, true);
//curl_setopt($chx, CURLOPT_POSTFIELDS, $sale);
//$sale = curl_exec($chx);
//curl_close($chx);
//echo $sale;

$options = array(
                'soap_version'=>SOAP_1_2,
                'exceptions'=>true,
                'trace'=>1,
                'cache_wsdl'=>WSDL_CACHE_NONE
            );
try {
$client = new SoapClient($url, $options);
//$response = $client->__soapCall("datoEntrada",$params);
//print_r($response);
} catch (SoapFault $fault){
    trigger_error("SOAP Fault: (faultcode: {$fault->faultcode}, faultstring: {$fault->faultstring})", E_USER_ERROR);
}

//$requestParams = array(
//    'CityName' => 'Berlin',
//    'CountryName' => 'Germany'
//);
//
//$client = new SoapClient('http://www.webservicex.net/geoipservice.asmx?WSDL');
//$result = $client->GetGeoIP(array('IPAddress' => '8.8.8.8'));
//
//print_r($result);

//$client = new SoapClient('http://www.webservicex.net/geoipservice.asmx?WSDL');
//var_dump($client->__getFunctions()); 
//var_dump($client->__getTypes());

//$wsdl = file_get_contents('https://www.google.com/apis/ads/publisher/v201204/ForecastService?wsdl');
//echo $wsdl;
?>