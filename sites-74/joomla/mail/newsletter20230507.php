<?php
// echo json_encode($_REQUEST) . "<br><br>";
// echo json_encode($_FILES) . "<br><br>";
// echo "file->" . isset($_FILES['archivo']);

include_once('conf.php');
include_once('mysqli.php');
$temp = new ps_DB;
$f = "plantilla.php";
$file = new SplFileObject($f, 'r');
$contents = $file->fread($file->getSize());
$file = null;

if (isset($_REQUEST['envia'])) {
	include_once('correo.php');

	$pase = 1;
	$sale = $fichero = '';
	$nombre = 'Newsletter_' . date('Y-M-d');
	(strlen($_REQUEST['asunto']) > 3) ? $asunto = $_REQUEST['asunto'] : $asunto = _ASUNTO_CORREO;
	(strlen($_REQUEST['mensaje']) > 3) ? $mensaje = $_REQUEST['mensaje'] : $mensaje = _CUERPO_CORREO;
	$mensaje = str_replace("{{cliente}}", _CORREO_FROM_NOMBRE, $mensaje);
	$asunto = utf8_decode($asunto);

	$fichero = 'newsletter.pdf';

	if (file_exists($fichero)) {
		if (!unlink($fichero)) {
			$sale = "Error: $fichero cannot be deleted due to an error";
		}
	}

	if (isset($_FILES['archivo'])) {
		//chequeo de tamaño
		if ($_FILES['archivo']['size'] > 10000000) {
			$sale = '<div class="koDiv"> Error: sobrepasado el tamaño del fichero</div>';
			$pase = 0;
		}

		//chequeo del mime
		if ($_FILES['archivo']['type'] != 'application/pdf') {
			$sale = '<div class="koDiv"> Error: No es un fichero .PDF</div>';
			$pase = 0;
		}

		//salva el fichero en disco
		if ($pase > 0) {
			$path = "documentos/";

			// if (!file_exists($path)) {
			// 	mkdir($path, 0777, true);
			// }

			$target = $path . $fichero;
			move_uploaded_file($_FILES['archivo']['tmp_name'], $fichero);

			$nombre = $_FILES['archivo']['name'];
		}
	} else $fichero = '';

	$temp->query("insert into mp_newsletter (nombre, asunto, contenido) values ('$nombre', '$asunto', '$mensaje')");
	$temp->query("update mp_newsletter_user set enviado = 0");


	envia(_CORREO_FROM, $asunto, $mensaje . "<br><br><br><span style='font-size:10px;'>Si no desea recibir este Newsletter puede eliminar la subscripci&oacute;n siguiendo este <a href='http://mabelpobletstudios.com/mail/unsubscribe.php?valor=" . _CORREO_FROM . "'>enlace</a></span>", $fichero);


	$sale = '<div class="okDiv">El Newsletter se subió al servidor y se enviará para revisión</div>';
} elseif (isset($_REQUEST['aceptar'])) { //Acepta los newsletter que se suben

	$sale = '';

	if (isset($_REQUEST['cambia']) && $_REQUEST['cambia'] > 0) {

		$temp->query("update mp_newsletter set aprobado = 1 where id = " . $_REQUEST['cambia']);
		$sale .= '<div class="okDiv"> El Newsletter fué modificado y comnzar&aacute; a enviarse en breve.</div>';
	} else {

		$temp->query("select m.id, m.nombre, date_format(m.fecha, '%d/%m/%Y') fec from mp_newsletter m, mp_newsletter n where n.id = m.id and n.fecha = (select max(fecha) from mp_newsletter) and m.aprobado = 0 and m.enviado = 0 order by id desc limit 0,1");

		if ($temp->num_rows() > 0) {

			$nombre = $temp->f('nombre');
			$fecha = $temp->f('fec');
			$id = $temp->f('id');

			$sale .= '
				<h4>Newsletter para Aprobar</h4>
				<div class="newsApro">
					<ul>
						<li>' . $nombre . '</li>
						<li>' . $fecha . '</li>
						<li><a href="http://mabelpobletstudios.com/mail/newsletter20230507.php?aceptar=1&cambia=' . $id . '">Aprobar para enviar</a></li>
					</ul>
				</div>
			';
		} else $sale .= '<div class="okDiv"> No hay Newsletter pendientes de aprobaci&oacute;n.</div>';
	}
} elseif (isset($_REQUEST['cliente'])) {

	$sale = '';

    $temp->query("select id from mp_newsletter_user");
    $cant = $temp->num_rows();

	$temp->query("select id, case nombre when '' then '&nbsp;' when null then '&nbsp;' else nombre end nombre, email, date_format(fecha,'%d/%m/%Y') fec from mp_newsletter_user order by fecha desc limit 0,50");

	if ($cant > 0) {
		$arrUsr = $temp->loadAssocList();

		$temp->query("select id from mp_newsletter_user where enviado = 1");
		$enviado = $temp->num_rows();

		$sale .= '
		<div class="totUsua">
			<ul>
				<li>Total de Clientes inscritos: ' . $cant . '</li>
				<li>Se ha enviado a: ' . $enviado . '</li>
			</ul>
		</div>';

		$sale .= '<div>';

		foreach ($arrUsr as $usuario) {
			$sale .= '
				<div class="listUsua">
					<ul>
						<li>' . ucwords($usuario['nombre']) . '</li>
						<li>' . $usuario['email'] . '</li>
						<li>' . $usuario['fec'] . '</li>
					</ul>
				</div>
				';
		}

		$sale .= '</div>';
	} else $sale .= '<div class="okDiv"> No hay usuarios inscritos a&uacute;n.</div>';
} else {

	$sale = '
	<div class="contact-form">
		<form id="contact-form" action="" enctype="multipart/form-data" method="post" class="form-validate">
			<fieldset>
				<!--<div class="form-group field-spacer">
					<span class="spacer"><span class="before"></span><span class="text"><label id="jform_spacer-lbl" class=""><strong class="red">*</strong> Campo requerido</label></span><span class="after"></span></span>
				</div>-->
				<div class="form-group">
					<label id="jform_contact_name-lbl" for="newsAsunto" class="hasPopover required" title="Asunto" data-content="Introduzca aquí el asunto del correo">Asunto<!--<span class="star">&#160;*</span>--></label>
					<input type="text" name="asunto" id="newsAsunto" value="" class="form-control required" size="30" title="Asunto del correo" aria-required="true" />
				</div>
				<div class="form-group">
					<label id="jform_contact_email-lbl" for="jform_contact_email" class="hasPopover required" title="Mensaje" data-content="Mensaje de contacto">Mensaje<!--<span class="star">&#160;*</span>--></label>
					<textarea name="mensaje" class="form-control validate-email required" id="newsMensaje" cols="30" rows="10" aria-required="true" ></textarea>			
				</div>
				<div class="form-group">
					<label id="jform_contact_name-lbl" for="jform_contact_name" class="hasPopover required invalid" title="" data-content="Newsletter" data-original-title="Newsletter">
						Fichero PDF<!--<span class="star">&nbsp;*</span>--></label>
					<input type="file" name="archivo" id="archivo">

				</div>
			</fieldset>
			<div class="control-group">
				<div class="controls">
					<input type="hidden" value="1" name="envia" />
					<button class="btn btn-primary validate" type="submit">Enviar</button>
				</div>
			</div>
		</form>
	</div>
	';
}




echo str_replace('{{cont}}', $sale, $contents);
