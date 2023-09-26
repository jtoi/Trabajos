<?php

include_once('conf.php');

function envia($to, $asunto, $body = '', $fichero = '')
{
	ini_set("include_path", '/home/mabelpob/php:' . ini_get("include_path"));
	require_once("Mail.php");
	require_once("Mail/mime.php");

	$from = _CORREO_FROM;
	$host = 'mabelpobletstudios.com';
	$username = $from;
	$password = 'Nn&(Oah+Ww9a';

	$headers = array();
	$headers['From'] = $from;
	$headers['To'] = $to;
	$headers['Subject'] = $asunto;
	if (_MODO_PRUEBA) $headers['Bcc'] = 'jtoirac@gmail.com';
	$headers['Return-Path'] = $from;
	$headers["Message-ID"] = "<" . md5(uniqid(time())) . "@mabelpobletstudios.com>";

	// $headers["Content-Type: image/png"];
	// $headers["Content-ID: <img>"];
	// $headers["Content-Disposition: inline"];
	// $headers["Content-Transfer-Encoding: base64 \r\n\r\n{$base64imgdata} "];

	$message = new Mail_mime();

	if (strlen($fichero) > 4) {
		$mimeFile = mime_content_type($fichero);

		if ($mimeFile == 'application/pdf') {
			$message->addAttachment($fichero, mime_content_type($fichero));
		}
	}
	$mimeparams = array();
	$mimeparams['text_encoding'] = "7bit";
	$mimeparams['text_charset'] = "UTF-8";

	$message->setHTMLBody($body);

	$body = $message->get($mimeparams);
	$headers = $message->headers($headers);

	$smtp = Mail::factory(
		'smtp',
		array(
			'host' => $host,
			'auth' => true,
			'username' => $username,
			'password' => $password
		)
	);

	$mail = $smtp->send($to, $headers, $body);

	if (PEAR::isError($mail)) {
		return "Error: $mail";
	}
	return true;
}
