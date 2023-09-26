<?php
include_once('conf.php');
include_once('mysqli.php');
include_once('correo.php');

spamChange();
ponUsuario();
/** realiza el envio de los newsletter cada 15 min */
if (date('i') == '00' || date('i') == '30' || date('i') == '45' || date('i') == '15') enviaNews();

function spamChange()
{
	$temp = new ps_DB;

	$temp->query("select params from mp_modules where id = 479");
	$str = '';

	if ($temp->num_rows() > 0) {
		$arrSpam = json_decode($temp->f('params'));

		mt_srand(time());
		$numA = mt_rand(0, 9);
		$numB = mt_rand(0, 9);

		$arrSpam->anti_spam_q = "Solve: $numA + $numB =&nbsp;";
		$arrSpam->anti_spam_a = $numA + $numB;

		$temp->query("update mp_modules set params = '" . json_encode($arrSpam) . "' where id = 479");
	}
}
function ponUsuario()
{
	$temp = new ps_DB;
	$f = "../mailing_list.txt";
	$file = new SplFileObject($f);

	while (!$file->eof()) {

		$elem = $file->fgets();
		if (strlen($elem) > 1) {
			echo  $elem . "<br>";
			$arrUsr = explode('&', $elem);
			$temp->query("select id from mp_newsletter_user where email = '" . trim($arrUsr[1]) . "'");
			if ($temp->num_rows() == 0)
				$temp->query("insert into mp_newsletter_user (nombre, email) values ('" . ucwords(trim($arrUsr[0])) . "', '" . strtolower(trim($arrUsr[1])) . "')");
		}
	}

	$file = null;
	file_put_contents($f, "");
}

function enviaNews()
{
	$temp = new ps_DB;
	$can = _CANT_A_ENVIAR; //cantidad de envíos
	$correoFinal = _CORREO_FROM;

	$temp->query("select asunto, contenido from mp_newsletter where aprobado = 1 and enviado = 0 order by fecha desc limit 1");

	if ($temp->num_rows() == 1) {

		(strlen($temp->f('asunto')) > 4) ? $asunto = $temp->f('asunto') : $asunto = _ASUNTO_CORREO;
		(strlen($temp->f('contenido')) > 4) ? $mensaje = $temp->f('contenido') : $mensaje = _CUERPO_CORREO;

		$fichero = 'newsletter.pdf';
		if (!file_exists($fichero)) {
			$fichero = '';
		}

		$q = "select id, nombre, email from mp_newsletter_user where enviado = 0 and borrado = 0";
		if (_MODO_PRUEBA) $q = "select id, nombre, email from mp_newsletter_user where enviado = 0 and borrado = 0 and email like '%mailinator%'";
		$temp->query($q);

		if ($temp->num_rows() != 0) {
			$arrUsr = $temp->loadAssocList();

			$i = 0;
			foreach ($arrUsr as $usuario) {
				$i += 1;
				$mensaje = str_replace("{{cliente}}", ucwords($usuario['nombre']), $mensaje);
				envia($usuario['email'], $asunto, $mensaje . "<br><br><br><span style='font-size:10px;'>Si no desea recibir este Newsletter puede cancelar la subscripci&oacute;n siguiendo este <a href='http://mabelpobletstudios.com/mail/unsubscribe.php?valor=" . $usuario['email'] . "'>enlace</a></span>", $fichero);

				$temp->query("update mp_newsletter_user set enviado = 1 where id = " . $usuario['id']);
				if ($i == $can) break;
			}
		} else {
			envia($correoFinal, $asunto, $mensaje . "<br><br><br><span style='font-size:10px;'>Si no desea recibir este Newsletter puede cancelar la subscripci&oacute;n siguiendo este <a href='http://mabelpobletstudios.com/mail/unsubscribe.php?valor=" . $correoFinal . "'>enlace</a></span>", $fichero);

			/** si no quedan mas usuarios por enviarles el newsletter marco todos los newsletter como enviados (por si había alguno que no estaba)*/
			$temp->query("update mp_newsletter set enviado = 1");

			/** como ya todos los newsletter están como enviados, se pasan todos los usuarios a por enviar para cuando entre el próximo newsletter*/
			$temp->query("update mp_newsletter_user set enviado = 0");
		}
	}
}
