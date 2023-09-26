<?php 
$ftp_serverAis = '82.223.109.187';
$ftp_user_nameAis = 'www';
$ftp_user_passAis = 'A1sr3m3s4s*';

$d = $_REQUEST;

$dir = "/var/www/vhosts/administracomercios.com/httpdocs/ficTitan/".$d['usr'];

$conn_id = ftp_connect($ftp_serverAis);
echo "se conecta ?-".ftp_login($conn_id, $ftp_user_nameAis, $ftp_user_passAis);

ftp_chdir($conn_id, 'daliergl');
ftp_pasv($conn_id, true);

$contents = ftp_nlist($conn_id, ".");


foreach($contents as $key => $value){
	//echo ftp_get($conn_id, $dir, $value, FTP_BINARY);
	echo $value."\n";
}

ftp_close($conn_id);
?>
