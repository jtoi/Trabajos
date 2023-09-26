<?php defined( '_VALID_ENTRADA' ) or die( 'Restricted access' );
$html = new tablaHTML;
global $temp;
$corCreo = new correo();

//global $temp;
$d = $_POST;

//	Comercio
$comer = $_SESSION['idcomStr'];
$query = "select id from tbl_comercio where id in (".$comer.") and activo = 'S' order by id limit 0,1";
$temp->query($query);
$comercios = $temp->f('id');

(isset ($d['comercio'])) ? $comercId = $d['comercio'] : $comercId = $comercios;
(isset ($d['idiom'])) ? $idiom = $d['idiom'] : $idiom = 1;
(isset ($d['tipo'])) ? $tipo = $d['tipo'] : $tipo = 1;

$q = "select texto, from_unixtime(fecha,'%d/%m/%Y %H:%i:%s')fec from tbl_traducciones where idcomercio = $comercId ".
		" and idIdioma = $idiom and tipo = $tipo";
$temp->query($q);
$texto = $temp->f('texto');
$fec = $temp->f('fec');

$html->idio = $_SESSION['idioma'];
$html->tituloPag = _MENU_ADMIN_IDIOMA;
$html->tituloTarea = ' ';
$html->anchoTabla = 750;
$html->tabed = true;
$html->anchoCeldaI = 80;
$html->anchoCeldaD = 640;

$html->inHide(false, 'inserta');

if ($comer == 'todos') {
    $query = "select idcomercio id, nombre from tbl_comercio where activo = 'S' order by nombre";
    $html->inSelect(_COMERCIO_TITULO, 'comercio', 2, $query,  str_replace(",", "', '", $comercId));
} elseif (strpos ($comer, ",")) {
    $query = "select id, nombre from tbl_comercio where id in (".$comer.") and activo = 'S' order by nombre";
//		echo $query;
    $html->inSelect(_COMERCIO_TITULO, 'comercio', 2, $query,  str_replace(",", "', '", $comercId));
} else $html->inHide ($comercId, 'comercio');
$arrVal = array(array('1',_MENU_ADMIN_INVITACION),array('0',_MENU_ADMIN_CONDICIONES),array('2',_MENU_ADMIN_VOUCHER));
$html->inSelect('Tipo', 'tipo', 3, $arrVal, $tipo);
$q = "select id, nombre from tbl_idioma order by nombre";
$html->inSelect('Idioma', 'idiom', 2, $q, $idiom);
$html->inTextoL('Última fecha de modificación: <span id="fecT">'.$fec.'</span>');
$html->inTexarea(_BITACORA_TEXT,  utf8_decode($texto), 'texto', 6, null, null, null, 40);
echo $html->salida('<input class="formul" id="enviaForm" name="enviar" type="button" value="' . _FORM_SEND . '" />');

//}
?>
<script type="text/javascript" src="../js/jquery.myfun_c00cc3a629e84ef270c28b0d705a1ce1.js"></script>
<script type="text/javascript" src="../js/tinymce/tinymce.min.js"></script>
<script type="text/javascript" charset="iso-8859-1" >

tinymce.init({
    selector: "textarea#texto",
    theme: "modern",
    width: 600,
    height: 200,
    resize: "both",
    plugins: [
         "advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker",
         "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime nonbreaking",
         "save table contextmenu directionality emoticons template paste textcolor"
   ],
   content_css: "../js/tinymce/skins/lightgray/content.min.css",
   toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview fullpage | forecolor backcolor emoticons",
   entity_encoding : "raw",
   style_formats: [
        {title: 'Bold text', inline: 'b'},
        {title: 'Red text', inline: 'span', styles: {color: '#ff0000'}},
        {title: 'Red header', block: 'h1', styles: {color: '#ff0000'}},
        {title: 'Example 1', inline: 'span', classes: 'example1'},
        {title: 'Example 2', inline: 'span', classes: 'example2'},
        {title: 'Table styles'},
        {title: 'Table row 1', selector: 'tr', classes: 'tablerow1'}
    ]
 });

function envia() {
//    if ($("#texto").val()) {
        $(".alerti").esperaDiv('muestra');
        $("#enviaForm").hide();
		$(".title_pag1").css("margin-top",1);
		$(".avisoDivi").detach();
//        alert(tinyMCE.activeEditor.getContent());
		$.ajax({
			type: 'POST',
			url: 'componente/comercio/ejec.php',
			dataType: 'text',
			contentType: 'application/x-www-form-urlencoded; charset=iso-8859-1',
			data: ({
				fun:'insidiom',
                ins:$("#inserta").val(),
                idi:$("#idiom").val(),
                tipo:$("#tipo").val(),
                com:$("#comercio").val(),
                tex:tinyMCE.activeEditor.getContent()
			}),
			success: function (data) {
				var datos = eval('(' + data + ')');
				$(".alerti").esperaDiv('cierra');
				$("#enviaForm").show();
				if (datos.error.length > 0) {
					$(".tabTodo").append("<div class='avisoDivi' style='text-align:center;color:red;position:absolute;top:110px;width:750px;'>"+datos.error+"</div>");
					$(".title_pag1").css("margin-top",20);
				}
				if (datos.tex.length > 0) {
					$(".tabTodo").append("<div class='avisoDivi' style='text-align:center;color:green;position:absolute;top:110px;width:750px;'>"+datos.tex+"</div>");
					$(".title_pag1").css("margin-top",20);
				}
				if (datos.cont) {
//                    alert(datos.cont.fec);
					tinyMCE.activeEditor.setContent(decodeURIComponent(escape(datos.cont.tex)));
					$("#fecT").html(datos.cont.fec);
				}
				$("#inserta").val('');
			}
		});
//    }
}

$(document).ready(function(){
	envia();
// 	$("#idiom").change(function (){
// 		alert('hola');
// 		envia()
// 	});
	$("#comercio").change(function (){envia()});
	$("#tipo").change(function (){envia()});
	$("#enviaForm").click(function(){ $("#inserta").val(true);envia()});
	$("#idiom").change(function(){alert("hola")});
});

</script>