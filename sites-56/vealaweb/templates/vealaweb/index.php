<?php
/**
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

$inicio = false;
if ((JRequest::getVar('view') == 'article' && JRequest::getVar('id') == '1') || JRequest::getVar('option') == '') $inicio = true;

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" >
<head>
<jdoc:include type="head" />
<meta name="author" content="Julio Toirac (jtoirac@vealawebcuba.com)" />
<?php if ($inicio) { ?>
<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/template.css" type="text/css" />
<?php } else { ?>
<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/template1.css" type="text/css" />
<?php } ?>
<!--[if lte IE 6]>
<?php if ($inicio) { ?>
<link href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/ieonly.css" rel="stylesheet" type="text/css" />
<?php } else { ?>
<link href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/ieonly (copy).css" rel="stylesheet" type="text/css" />
<?php } ?>
<![endif]-->
<!--[if lte IE 7]>
<?php if ($inicio) { ?>
<link href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/ieonly7.css" rel="stylesheet" type="text/css" />
<?php } else { ?>
<link href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/ieonly7 (copy).css" rel="stylesheet" type="text/css" />
<?php } ?>
<![endif]-->
<script type="text/javascript" src="<?php echo $this->baseurl ?>/templates/vealaweb/js/jquery.min.js"></script>
<?php if ($inicio) { ?>
<script type="text/javascript" src="<?php echo $this->baseurl ?>/templates/vealaweb/js/jquery.cycle.min.js"></script>
<?php } ?>
<script type="text/javascript" src="<?php echo $this->baseurl ?>/templates/vealaweb/js/jquery.pngFix.pack.js"></script>
<script type="text/javascript">
$(document).ready(function() {
<?php if ($inicio) { ?>
	$('#imgRota')
		.before('<div id="nav">')
		.cycle({
			fx:			'fade', // choose your transition type, ex: fade, scrollUp, shuffle, etc...
			speed:		100, 
			timeout:	16000,
			pause:		1,
			before:		onBefore, 
			after:		onAfter,
			pager:		'#nav'
	});
	$('.anU').mouseover(function(){
		$('#imgRota').cycle('pause');
	}).mouseout(function(){
		$('#imgRota').cycle('resume');
	})
	
	function onBefore() {
		$('#anunnc1').fadeOut(1);$('#anunnc2').fadeOut(1);$('#anunnc3').fadeOut(1);$('#anunnc4').fadeOut(1);
		
//		$('#annu').html("Scrolling image:<br>" + this.src.indexOf('1.jpg') + ' pase ' + this.src); 
	}
	
	function onAfter()  {
		if (this.src.indexOf('1.jpg') > 0) $('#anunnc1').fadeIn(1000);
		if (this.src.indexOf('2.jpg') > 0) $('#anunnc2').fadeIn(1000);
		if (this.src.indexOf('3.jpg') > 0) $('#anunnc3').fadeIn(1000);
		if (this.src.indexOf('4.jpg') > 0) $('#anunnc4').fadeIn(1000);
	}
	
<?php } ?>

	$(document).pngFix();
	$('a:contains("Ver m")').addClass('vermas');
	$(".imgFlotante").addClass('fotoVirada');
	
//	$(".respT").hide();
//	$(".pregT").mouseover(function(){
//		$(this).css({
//			'cursor': 'pointer',
//			'text-decoration': 'underline'
//		});
//	})


    //Tooltips
    $(".pregT").click(function(){
        tip = $(this).find('.respT');
        tip.toggle(); //Show tooltip
    }).mousemove(function(e) {
        var mousex = e.pageX + 20; //Get X coodrinates
        var mousey = e.pageY + 20; //Get Y coordinates
        var tipWidth = tip.width(); //Find width of tooltip
        var tipHeight = tip.height(); //Find height of tooltip

        //Distance of element from the right edge of viewport
        var tipVisX = $(window).width() - (mousex + tipWidth);
        //Distance of element from the bottom of viewport
        var tipVisY = $(window).height() - (mousey + tipHeight);

        if ( tipVisX < 20 ) { //If tooltip exceeds the X coordinate of viewport
            mousex = e.pageX - tipWidth/2;
        } if ( tipVisY < 20 ) { //If tooltip exceeds the Y coordinate of viewport
            mousey = e.pageY - tipHeight/2;
        }
        //Absolute position the tooltip according to mouse position
        tip.css({  top: mousey, left: mousex });
    });

});


</script>
<meta name="google-site-verification" content="BROpx7gMB5J__gdmJZaJFaoNrmtnsEyfI7amOXT6swg" />
<script type="text/javascript"> 
 
  var _gaq = _gaq || []; 
  _gaq.push(['_setAccount', 'UA-21299478-1']); 
  _gaq.push(['_trackPageview']); 
 
  (function() { 
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true; 
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js'; 
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s); 
  })(); 
 
</script>
<meta name="google-site-verification" content="BROpx7gMB5J__gdmJZaJFaoNrmtnsEyfI7amOXT6swg" />
</head>
<body text="">
<div id="todo">
	<div id="top">
		<div id="menutop"><jdoc:include type="modules" name="top" style="xhtml" /></div>
	</div>
	<div id="medio">
		<div id="izq"><jdoc:include type="modules" name="left" style="xhtml" /></div>
		<div id="centro">
			<div id="centro0">
				<div id="centro1">
					<jdoc:include type="message" />
					<jdoc:include type="component" />
				</div>
			</div>
		</div>
		<?php if (!$inicio) { ?><jdoc:include type="modules" name="pos2" style="xhtml" /><?php } ?>
<?php if ($inicio) { ?>
		<div id="der">
			<div id="imgRota">
				<img id="imagCamb" src="templates/vealaweb/images/imagen1.jpg" height="256" width="322" />
				<img id="imagCamb" src="templates/vealaweb/images/imagen2.jpg" height="256" width="322" />
				<img id="imagCamb" src="templates/vealaweb/images/imagen3.jpg" height="256" width="322" />
				<img id="imagCamb" src="templates/vealaweb/images/imagen4.jpg" height="256" width="322" />
			</div>
		</div>
<?php } else { ?>
<?php } ?>
	</div>
	<div id="abajo">
		<jdoc:include type="modules" name="pos1" style="xhtml" />
	</div>
	<div id="footer">
		<div id="footer1">
			<jdoc:include type="modules" name="pos3" style="xhtml" /><br>
			&copy; VealawebCuba.com Todos los derechos reservados
		</div>
	</div>
</div>

<jdoc:include type="modules" name="debug" />

</body>
</html>
