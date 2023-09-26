<?php defined( '_VALID_ENTRADA' ) or die( 'Restricted access' );
//include_once 'mobile_device_detect.php';
//	$mobileUrl = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."mobile/index.php";
//	mobile_device_detect($mobileUrl,null,$mobileUrl,$mobileUrl,$mobileUrl,null,$mobileUrl,true);
//echo date('h:i:s a', time());
$temp = new ps_DB();
global $temp;
$pase = 0;

// foreach ($_SESSION as $key => $value) {
// 	echo "$key => $value<br>";
// }

$q = "select distinct cambOperEuro, operEur from tbl_comercio where id in (".$_SESSION['idcomStr'].")";
$temp->query($q);
$arrVales = $temp->loadAssocList();
//var_dump($arrVales);
$max1 = $max2 = 0;
for ($i=0;$i<count($arrVales);$i++){
	if ($max1 < $arrVales[$i]['cambOperEuro']) $max1 = $arrVales[$i]['cambOperEuro'];
	if ($max2 < $arrVales[$i]['operEur']) $max2 = $arrVales[$i]['operEur'];
}
//echo "$max1 - $max2";

if ($max2 == 0 || $max2 == 2 || $_SESSION['rol'] < 11) {
	$query = "select format(valor,4) valor, nombre from tbl_setup where nombre in (select moneda from tbl_moneda where activo = 1 and moneda != 'EUR') order by nombre";
	$temp->query($query);
	$arrVal = $temp->loadAssocList();
}

if ($max2 > 0) {
	if ($max1 == 1) {
		$q = "select distinct format(tasa,4) valor, m.moneda nombre from tbl_tasaComercio o, tbl_moneda m where monedaCamb = 978 and o.monedaBas = m.idmoneda and m.activo = 1 and o.idcomercio in (".$_SESSION['idcomStr'].") and o.fecha = (select max(fecha) from tbl_tasaComercio where idcomercio in (".$_SESSION['idcomStr'].")) and m.idmoneda != 978 order by m.moneda";
	} else {
		$q = "select distinct format(tasa,4) valor, m.moneda nombre from tbl_colCambBanco o, tbl_moneda m where o.idmoneda = m.idmoneda and o.fecha = (select max(fecha) from tbl_colCambBanco) and o.idbanco = 26 and o.idmoneda in (124,840,826) order by m.moneda";
	}
	$temp->query($q);
	$arrValer = $temp->loadAssocList();
}


// print_r($_SERVER);

//header("Expires: Thu, 10 Jul 2022 08:52:00 GMT"); //Date in the past
//header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
//header("Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0, false"); //HTTP/1.1
//header("Pragma: no-cache");

?>

<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Panel de Administraci&oacute;n</title>
<link rel="stylesheet" href="template/css/estilo.min_201208110709.css" type="text/css" />


<script type="text/javascript" src="../js/jquery.js"></script>
<script type="text/javascript" src="../js/globalfunc-<?php echo $_SESSION['idioma'] ?>.mod.min_201208101748.js"></script>
<script type="text/javascript" src="../js/jquery_002_003_menu.min_201208111328.js"></script>
<script type="text/javascript" src="../js/datepicker_<?php echo $_SESSION['idioma'] ?>.js"></script>

<script language="javascript">
	function redirect(url){
		window.open(url, '_self');
		return false;
	}
	function showId(id)
	{
		var obj = document.getElementById(id);
		obj.style.display = 'block';
		return false;
	}
	function hideId(id)
	{
		var obj = document.getElementById(id);
		obj.style.display = 'none';
		return false;
	}

$(document).ready(function(){
	$("#noscript").hide();
	$('#fecha1').DatePicker({
		format:'d/m/Y',
		date: $('#fecha1').val(),
		current: $('#fecha1').val(),
		starts: 1,
		position: 'right',
		onBeforeShow: function(){
			$('#fecha1').DatePickerSetDate($('#fecha1').val(), true);
		},
		onChange: function(formated, dates){
			$('#fecha1').val(formated);
			//$('#fecha1').DatePickerHide();
		}
	});

	$('#fecha2').DatePicker({
		format:'d/m/Y',
		date: $('#fecha2').val(),
		current: $('#fecha2').val(),
		starts: 7,
		position: 'right',
		onBeforeShow: function(){
			$('#fecha2').DatePickerSetDate($('#fecha2').val(), true);
		},
		onChange: function(formated, dates){
			$('#fecha2').val(formated);
			//$('#fecha2').DatePickerHide();
		}
	});

	$('#fecha3').DatePicker({
		format:'d/m/Y',
		date: $('#fecha3').val(),
		current: $('#fecha3').val(),
		starts: 7,
		position: 'right',
		onBeforeShow: function(){
			$('#fecha3').DatePickerSetDate($('#fecha3').val(), true);
		},
		onChange: function(formated, dates){
			$('#fecha3').val(formated);
			//$('#fecha3').DatePickerHide();
		}
	});

	$('#fecha4').DatePicker({
		format:'d/m/Y',
		date: $('#fecha4').val(),
		current: $('#fecha4').val(),
		starts: 7,
		position: 'right',
		onBeforeShow: function(){
			$('#fecha4').DatePickerSetDate($('#fecha4').val(), true);
		},
		onChange: function(formated, dates){
			$('#fecha4').val(formated);
			//$('#fecha4').DatePickerHide();
		}
	});

	$('#fecha5').DatePicker({
		format:'d/m/Y',
		date: $('#fecha5').val(),
		current: $('#fecha5').val(),
		starts: 7,
		position: 'right',
		onBeforeShow: function(){
			$('#fecha5').DatePickerSetDate($('#fecha5').val(), true);
		},
		onChange: function(formated, dates){
			$('#fecha5').val(formated);
			//$('#fecha5').DatePickerHide();
		}
	});
	
	$("textarea").each(function(){
		if ($(this).val().length == 6) 
		$(this).val('');
	});
	
	if(!$(".datepicker")) {
		$('#fecha1').attr("readonly", false);
		$('#fecha2').attr("readonly", false);
		$('#fecha3').attr("readonly", false);
		$('#fecha4').attr("readonly", false);
		$('#fecha5').attr("readonly", false);
	}
    
    $("#monLi").click(function(e){
        $("#divTasa").toggle();
        e.stopPropagation();
    });
    $("body").click(function(){$("#divTasa").hide();});

});

</script>
</head>
<body>
	<div id="noscript" style="font-size: 14px;text-align: center;color:black;">Si al terminar de cargar la página no desaparece este cartel<br>Su navegador no tiene habilitado Javascript le sugiero que no siga adelante hasta que lo habilite.</div>
<table id="princ" width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td colspan="4" onclick="window.open('index.php?componente=comercio&pag=inicio','_self')" class="banner">&nbsp;</td>

  </tr>
  <tr>
    <td class="separamen"></td>
    <td class="menu">
		<div id="cambioEur">
			<table cellpadding="0" cellspacing="0" width="700">
				<tr>
					<td class="cambEur"><?php echo _EUROA ?>:</td>
					<td>
                        <span id="monLi"><?php echo _VERTASA ?></span>
						<?php
						if (count($arrValer) > 0) {
							
						?>
                        <div id="divTasa" class="divTasaG">
							<div class='monhab'><?php if (count($arrVal) > 0){ ?>
								<span class="mond">moneda habilitada</span>
								<?php
								for ($i=0; $i<count($arrVal); $i++) {
									echo $arrVal[$i]['nombre']." - ".$arrVal[$i]['valor']."<br>";
								}
							}
								?>
							</div>
							<div class='monhab' id='ceuros'>
								<span class="mond">Cobros en Euros</span>
								<?php
								for ($i=0; $i<count($arrValer); $i++) {
									echo $arrValer[$i]['nombre']." - ".$arrValer[$i]['valor']."<br>";
								}
								?>
							</div>
                        </div>
						<?php } else { ?>
                        <div id="divTasa" class="divTasa">
							<div class='monhab'>
								<span class="mond">moneda habilitada</span>
								<?php
								for ($i=0; $i<count($arrVal);$i++) {
									echo $arrVal[$i]['nombre']." - ".$arrVal[$i]['valor']."<br>";
								}
								?>
							</div>
                        </div>
						<?php } ?>
					</td>
					<td>
						<span id="liveclock1"><strong><?php echo _HORA_ESP ?>:</strong> <?php
							$timeZone=date_default_timezone_get();
							date_default_timezone_set('Europe/Berlin');
				//			ini_set('date.timezone', 'Europe/Madrid');
							if (function_exists("date_default_timezone_set") && function_exists("date_default_timezone_get"))
							echo date('H:i:s', time());
				//			ini_set('date.timezone', $timeZone);
//							date_default_timezone_set('America/Santo_Domingo');
							date_default_timezone_set('America/New_York');
							?></span><br />
						<span id="liveclock2"><strong><?php echo _HORA_CUB ?>:</strong> <?php echo date('H:i:s', time());date_default_timezone_set($timeZone);?></span>
					</td>
					<td>
						 <strong><?php echo _AUTENT_LOGIN; ?>:</strong> <?php echo $_SESSION['admin_nom']; ?>
					</td>
				</tr>
			</table>
		</div>
	</td>
	<td class="menu_cente">
		<div id="menu">
		<ul class="menu">
		<?php
			$query = "select m.id,title,parentid,link from tbl_menu m, tbl_accesos a where m.id = a.idmenu and a.idrol = {$_SESSION['rol']} and parentid = '0'
					order by id";
//			echo $query."<br>";
			$temp->query($query);
			$menu1 = $temp->loadObjectList();

			foreach ($menu1 as $item) {
				if (strlen($item->link) == 0) $url = '#'; else $url = $item->link;
				echo "<li><a href='$url' class='parent'><span>".constant($item->title)."</span></a>";

				echo "<div><ul>";

				$query = "select m.id,title,parentid,link from tbl_menu m, tbl_accesos a where m.id = a.idmenu and a.idrol = {$_SESSION['rol']} and parentid = '".$item->id."' order by orden";
				$temp->query($query);
				$menu2 = $temp->loadObjectList();

				foreach ($menu2 as $item2) {
						if (strlen($item2->link) == 0) $url = '#'; else $url = $item2->link;
					if ($item2->id == 75) {
						if ($_SESSION['reclamaciones'] == 1) 
							echo "<li><a href='$url' class=''><span>".constant($item2->title)."</span></a>";
					} elseif ($item2->id == 67 ) {
						$temp->query("select count(*) total from tbl_comercio where lotes = 1 and id in ({$_SESSION['idcomStr']})");
						if ($temp->f('total') > 0) {
							echo "<li><a href='$url' class=''><span>".constant($item2->title)."</span></a>";
						}
					} elseif ($item2->id == 39 ) {
						if ($_SESSION['comercio'] == 'todos' || $_SESSION['comercio'] == '527341458854') {
							echo "<li><a href='$url' class=''><span>".constant($item2->title)."</span></a>";
						}
					} elseif($item2->id == 63) {
						$q = "select count(id) total from tbl_comercio where id in ({$_SESSION['idcomStr']}) and operEur != 0 and activo = 'S' and cambOperEuro != 0";
						$temp->query($q);
						if ($temp->f('total') != 0) {
								echo "<li><a href='$url' class=''><span>".constant($item2->title)."</span></a>";
							}
					} else if ($item2->id == 46) {
						$q = "select count(id) total from tbl_comercio where id in ({$_SESSION['idcomStr']}) and usarTasaCuc != 0 and activo = 'S'";
						$temp->query($q);
						if ($temp->f('total') != 0) {
								echo "<li><a href='$url' class=''><span>".constant($item2->title)."</span></a>";
							}
                    } else {
						if ($item2->id == 35) {
							$q = "select count(id) total from tbl_colPasarComTran where idcomercio in ({$_SESSION['idcomStr']})";
							$temp->query($q);
							if ($temp->f('total') != 0) {
								echo "<li><a href='$url' class=''><span>".constant($item2->title)."</span></a>";
							}
						} else {
							echo "<li><a href='$url' class=''><span>".constant($item2->title)."</span></a>";
						}
					}
				}
				echo "</ul></div>";
			}		
		?>		
		</ul></div>
	</td>
	<td height="24" width="50">
        <span onclick="window.open('index.php?componente=core&pag=logout', '_self')" class="css_log-out"></span></td>
  </tr>
  <tr>
    <td colspan="4" class="inf"></td>
  </tr>
</table>
    <div id="alerti" style="display: none"></div>
<div id="todoDiv">
<?php hotel_put() ?>
</div>
</body>
</html>