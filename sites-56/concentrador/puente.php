<?php
$d = $_REQUEST;
// $d['url'] = "https://administracomercios.com/ejec.php";
// $d['url'] = "http://localhost/concentrador/ejec.php";
// $d['time'] = "1442423531";
// $d['sql'] = "select t.idtransaccion, t.codigo, t.identificador, a.nombre 'solicitada por', a.email, from_unixtime(d.fecha, '%d/%m/%Y %H:%i') 'fecha sol.', c.nombre 'comercio', t.valor_inicial/100 'valor Ini', d.valorDev 'a devolver', from_unixtime(t.fecha, '%d/%m/%Y %H:%i') 'fecha oper.', p.nombre 'pasarela', b.nombre 'devuelta por', from_unixtime(d.fechaDev, '%d/%m/%Y %H:%i') 'el día' from tbl_transacciones t, tbl_devoluciones d, tbl_admin a, tbl_admin b, tbl_pasarela p, tbl_comercio c where c.idcomercio = t.idcomercio and p.idPasarela = t.pasarela and t.idtransaccion = d.idtransaccion and d.idadmin = a.idadmin and d.devpor = b.idadmin and d.fechaDev != 0 order by d.fecha desc";
// $d['firm'] = "56026e5b2daaf64c584c36713c5e1ab01748e6db";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,$d['url']);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, "var={$d['time']}&sql={$d['sql']}&cod={$d['firm']}");

curl_exec ($ch);
if (curl_errno($ch)) echo "Error en la resp :".curl_strerror(curl_errno($ch))."<br>\n";
$crlerror = curl_error($ch);

if ($crlerror) echo "Error en la resp :".$crlerror."<br>\n";
$curl_info = curl_getinfo($ch);
curl_close($ch);

// 	sleep(60*1);
// 	time_sleep_until(time()+(60*1));
// 	corre($url,$time,$sql,$firm);
?>