<?php
defined('_VALID_ENTRADA') or die('Restricted access');

//var_dump($_REQUEST);

if ($_REQUEST['p'] == '' || $_REQUEST['p'] == 'nc') {
	$pag = "pagina/nc.php";
} else {
	if (!$pag = $ent->isAlfabeto($_REQUEST['p'], 4))
		exit;
	else {
		$pag = "pagina/$pag.php";}
}

if (is_file($pag)) {
	include $pag;}
//echo json_encode($_SESSION);
global $temp;
global $html;
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<meta name="description" content="">
		<meta name="author" content="Julio Toirac<jtoirac@arteorganizer.com>">
		<link rel="icon" href="favicon.ico">

		<title><?php echo $titag ?></title>

		<!-- Bootstrap core CSS -->
		<link href="template/css/bootstrap.min.css" rel="stylesheet">

		<!-- Custom styles for this template -->
		<link href="template/css/dashboard.css" rel="stylesheet">
		<link rel="stylesheet" type="text/css" href="template/js/markitup/skins/markitup/style.css" />
		<link rel="stylesheet" type="text/css" href="template/js/markitup/sets/default/style.css" />
		<script src="template/js/jquery-3.1.1.slim.min.js" integrity="sha384-A7FZj7v+d/sdmMqp/nOQwliLvUsJfDHW+k9Omg/a/EheAdgtzNs3hpfag6Ed950n" crossorigin="anonymous"></script>
		<script src="template/js/jquery.js"></script>
		<script type="text/javascript">var grupoRol = <?php echo $_SESSION['grupo_rol']; ?></script>
		<script src="template/js/text-<?php echo $_SESSION['idioma']; ?>.js"></script>
		<script src="template/js/HtmlCookie.js"></script>
		<script src="template/js/valglob.js"></script>
		<script src="template/js/markitup/jquery.markitup.js"></script>
		<script type="text/javascript" src="template/js/markitup/sets/default/set.js"></script>
	</head>

	<body>

		<nav class="navbar navbar-default navbar-fixed-top">
			<div class="container-fluid">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>                        
					</button>
					<a class="navbar-brand" href="index.php?p=nc">Logo</a>
					
				</div>
				<div class="collapse navbar-collapse" id="myNavbar">
					<ul class="nav navbar-nav navbar-right">
						<li class="cammay"><a href="index.php?p=obr"><?php echo ($fun->idioma('obra')) ?></a></li>
						<li class="cammay"><a href="index.php?p=crr"><?php echo ($fun->idioma('currículo')) ?></a></li>
						<li class="cammay"><a href="index.php?p=clt"><?php echo ($fun->idioma('cliente')) ?></a></li>
						<li class="cammay"><a href="index.php?p=ctc"><?php echo ($fun->idioma('contacto')) ?></a></li>
					<?php if ($_SESSION['grupo_rol'] < 6) { ?>
						<li class="dropdown">
							<a class="dropdown-toggle cammay" data-toggle="dropdown" href="#"><?php echo ($fun->idioma('admin')) ?>
								<span class="caret"></span></a>
									<ul class="dropdown-menu">
									<li><a href="index.php?p=art"><?php echo ($fun->idioma('Artistas')) ?></a></li>
									<li><a href="index.php?p=usr"><?php echo ($fun->idioma('Usuarios')) ?></a></li>
									<li><a href="index.php?p=eobr"><?php echo ($fun->idioma('Estado Obra')) ?></a></li>
									<li><a href="index.php?p=epg"><?php echo ($fun->idioma('Estado Pago')) ?></a></li> 
									<li><a href="index.php?p=mdo"><?php echo ($fun->idioma('Medio')) ?></a></li> 
									<li><a href="index.php?p=mnd"><?php echo ($fun->idioma('Moneda')) ?></a></li> 
									<li><a href="index.php?p=srs"><?php echo ($fun->idioma('Series')) ?></a></li>
									<li><a href="index.php?p=edc"><?php echo ($fun->idioma('Ediciones')) ?></a></li>
									<!--<li><a href="index.php?p=rls"><?php echo ($fun->idioma('Roles')) ?></a></li> 
									<li><a href="index.php?p=cng"><?php echo ($fun->idioma('Configuracion')) ?></a></li>-->
								</ul>
							</li>
					<?php } elseif ($_SESSION['grupo_rol'] < 11) { ?>
									<li><a href="index.php?p=usr"><?php echo ($fun->idioma('Usuarios')) ?></a></li>
									<li><a href="index.php?p=cta"><?php echo ($fun->idioma('Cuenta Bancaria')) ?></a></li>
									<li><a href="index.php?p=srs"><?php echo ($fun->idioma('Series')) ?></a></li>
									<li><a href="index.php?p=edc"><?php echo ($fun->idioma('Ediciones')) ?></a></li>
									<li><a href="index.php?p=fac"><?php echo ($fun->idioma('Facturacion')) ?></a></li>
									<li><a href="index.php?p=frt"><?php echo ($fun->idioma('Oferta')) ?></a></li>
									<li><a href="index.php?p=cng"><?php echo ($fun->idioma('Configuracion')) ?></a></li>
								</ul>
							</li>
					<?php } ?>
						<li class="dropdown">
							<a class="dropdown-toggle cammay" data-toggle="dropdown" href="#"><?php echo $_SESSION['admin_nom'] ?>
								<span class="caret"></span></a>
								<ul class="dropdown-menu">
									<li><a href="index.php?p=dpr"><?php echo ($fun->idioma('Datos personales')) ?></a></li>
									<li><a href="index.php?r=1"><?php echo ($fun->idioma('Salir')) ?> <!--<span id="logoff" class="glyphicon glyphicon-off"></span>--></a></li>
								</ul>
						</li>
					</ul>
				</div>
			</div>
		</nav>
		
		<div class="container-fluid" id="alerti" style="display:none"></div>

		<div class="container-fluid">
			<h1 class="center-block" style="text-align:center;max-width: <?php echo $anchTit; ?>"><?php echo $titag; ?></h1>
		</div>

		<div class="container hide" id="botInsMod">
			<div class="btn-group btn-group-justified botonesup">
				<a href="javascript:void(0)" id="btnBusc" class="btn btn-lg btn-primary"><?php echo ($fun->idioma('Buscar')) ?></a>
				<a href="javascript:void(0)" id="btnInsrt" class="btn btn-lg btn-primary"><?php echo ($fun->idioma('Insertar')) ?></a>
			</div>
		</div>

		<?php echo $formulario; ?>

		<div class="container-fluid" id="tablaGen"><!-- No borrar este div -->
			
		</div>

		<!-- Bootstrap core JavaScript
		================================================== -->
		<!-- Placed at the end of the document so the pages load faster -->
		<script src="template/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
		<script src="template/js/bootstrap.min.js"></script>
		<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
		<script src="template/js/ie10-viewport-bug-workaround.js"></script>
		<script src="template/js/HtmlCookie.js"></script>
		<?php echo $scriptInf; ?>
	</body>
</html>
