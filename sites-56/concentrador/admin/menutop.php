<?php //defined( '_VALID_ENTRADA' ) or die( 'Restricted access' ); ?>
<script language="JavaScript" type="text/JavaScript">
//  QuickMenu Pro, Copyright (c) 1998 - 2003, OpenCube Inc. - http://www.opencube.com


/**********************************************************************************************
**********************************************************************************************

                              Bullet and Icon Image Library

**********************************************************************************************
**********************************************************************************************/



/*-------------------------------------------
Bullet and Icon image library - Unlimited bullet
or icon images may be defined below and then associated
with any sub menu items within the 'Sub Menu Structure
and Text' section of this data file.  Relative
positioned icon images may also be associated
with any main menu item in the 'main menu items' section.
--------------------------------------------*/


    //Relative positioned icon images (flow with main menu or sub item text)

	dqm__icon_image0 = "../images/bullet.gif"
	dqm__icon_rollover0 = "../images/bullet_hl.gif"
	dqm__icon_image_wh0 = "13,8"



    //Absolute positioned icon images (coordinate positioned, sub menus only)

	dqm__2nd_icon_image0 = "../images/arrow.gif"
	dqm__2nd_icon_rollover0 = "../images/arrow.gif"
	dqm__2nd_icon_image_wh0 = "13,10"
	dqm__2nd_icon_image_xy0 = "0,4"





/**********************************************************************************************
**********************************************************************************************

                              Main Menu Settings

**********************************************************************************************
**********************************************************************************************/



/*---------------------------------------------
Main Item Widths and Heights
-----------------------------------------------*/


	dqm__main_width = 150			//default main item widths
	dqm__main_height = 20			//default main item heights

	//dqm__main_widthX			//specific main item widths
	//dqm__main_heightX			//specific main item heights



/*---------------------------------------------
Main Menu Borders Dividers and Layout
-----------------------------------------------*/


	dqm__main_horizontal =true		//align menu bar horizontally or vertically

	dqm__main_border_width = 1;		//the thickness of the border in pixels
	dqm__main_border_color = 'transparent'	//HEX color or set width to transparent for no borders


	dqm__main_use_dividers = true		//When true the item gap setting is ignored
						//and the border width and color are used to
						//separate each main menu item.


	dqm__main_item_gap = 5			//the gap between main menu items in pixels


	dqm__align_items_bottom_and_right = true	//align items of different size to the bottom
							//or right edge of the largest main menu item
							//depending on the horizontal or vertical layout
							//of the main menu bar - false aligns items to
							//the top and left

/*---------------------------------------------
Menu Item Background and Text Colors
-----------------------------------------------*/


	dqm__main_bgcolor = "#3E8ED2"		//default color for all items, HEX or 'transparent'
	dqm__main_bgcolorX = "#3E8ED2"		//specific menu item color, HEX or 'transparent'

	dqm__main_hl_bgcolor = "#82ddfb"	//HEX color value or set to 'transparent'
	dqm__main_hl_bgcolorX = "#eeeeee"

	dqm__main_textcolor = "#FFFFFF"
	dqm__main_textcolorX = "#111111"

	dqm__main_hl_textcolor = "#111111"
	dqm__main_hl_textcolorX = "#ff0000"



/*---------------------------------------------
Menu Item Font Settings
-----------------------------------------------*/


	dqm__main_fontfamily = "sans-serif"	//Any available system font
	dqm__main_fontsize = 11			//Defined with pixel sizing
	dqm__main_textdecoration = "none"	//set to: 'none', or 'underline'
	dqm__main_fontweight = "normal"		//set to: 'normal', or 'bold'
	dqm__main_fontstyle = "normal"		//set to: 'normal', or 'italic'


	//rollover effect

	dqm__main_hl_textdecoration = "none"



/*---------------------------------------------
Main Menu Margins and Text Alignment
-----------------------------------------------*/


	dqm__main_text_alignment = "center"		//set to: 'left', 'center' or 'right'
	dqm__main_margin_top = 2
	dqm__main_margin_bottom = 2
	dqm__main_margin_left = 5
	dqm__main_margin_right = 4



	//specific settings

	dqm__main_text_alignmentX = "center"		//set to: 'left', 'center' or 'right'
	dqm__main_margin_topX = 4
	dqm__main_margin_bottomX = 4



/*---------------------------------------------
Optional Status Bar Text
-----------------------------------------------*/


	//dqm__status_text0 = "Sample text - Main Menu Item 0"
	//dqm__status_text1 = "Sample text - Main Menu Item 1"



/*---------------------------------------------
Main Menu Items (Text, URL's, and Icons)
-----------------------------------------------*/



/**********************************************************************************************
**********************************************************************************************

                              Sub Menu Settings

**********************************************************************************************
**********************************************************************************************/


/*-------------------------------------------
Colors, Borders, Dividers, and more...
--------------------------------------------*/


	dqm__sub_menu_width = 150      		//default sub menu widths
	dqm__sub_xy = "0,0"            		//default sub x,y coordinates - defined relative
						//to the top-left corner of parent image or sub menu


	dqm__urltarget = "_self"		//default URL target: _self, _parent, _new, or "my frame name"

	dqm__border_width = 1
	dqm__divider_height = 0

	dqm__border_color = "#82ddfb"		//Hex color or 'transparent'
	dqm__menu_bgcolor = "#3E8ED2"		//Hex color or 'transparent'
	dqm__hl_bgcolor = "#e6e6e6"

	dqm__mouse_off_delay = 100		//defined in milliseconds (activated after mouse stops)
	dqm__nn4_mouse_off_delay = 100		//defined in milliseconds (activated after leaving sub)



/*-------------------------------------------
Font settings and margins
--------------------------------------------*/


    //Font settings

	dqm__textcolor = "#FFFFFF"
	dqm__fontfamily = "sans-serif"		//Any available system font
	dqm__fontsize = 11			//Defined with pixel sizing
	dqm__fontsize_ie4 = 9			//Defined with point sizing
	dqm__textdecoration = "none"		//set to: 'normal', or 'underline'
	dqm__fontweight = "normal"		//set to: 'normal', or 'bold'
	dqm__fontstyle = "normal"		//set to: 'normal', or 'italic'


    //Rollover font settings

	dqm__hl_textcolor = "#000000"
	dqm__hl_textdecoration = "none"	//set to: 'none', or 'underline'



    //Margins and text alignment

	dqm__text_alignment = "left"		//set to: 'left', 'center' or 'right'
	dqm__margin_top = 2
	dqm__margin_bottom = 3
	dqm__margin_left = 5
	dqm__margin_right = 4



/*---------------------------------------------
Optional Status Bar Text
-----------------------------------------------*/


	dqm__show_urls_statusbar = false

	//dqm__status_text1_0 = "Sample text - Main Menu Item 1, Sub Item 0"
	//dqm__status_text1_0 = "Sample text - Main Menu Item 1, Sub Item 1"



/*-------------------------------------------
Internet Explorer Transition Effects
--------------------------------------------*/


    //Options include - none | fade | pixelate |iris | slide | gradientwipe | checkerboard | radialwipe | randombars | randomdissolve |stretch

	dqm__sub_menu_effect = "none"
	dqm__sub_item_effect = "none"


    //Define the effect duration in seconds below.

	dqm__sub_menu_effect_duration = .4
	dqm__sub_item_effect_duration = .4


    //Specific settings for various transitions.

	dqm__effect_pixelate_maxsqare = 25
	dqm__effect_iris_irisstyle = "CIRCLE"		//CROSS, CIRCLE, PLUS, SQUARE, or STAR
	dqm__effect_checkerboard_squaresx = 14
	dqm__effect_checkerboard_squaresY = 14
	dqm__effect_checkerboard_direction = "RIGHT"	//UP, DOWN, LEFT, RIGHT


    //Opacity and drop shadows.

	dqm__sub_menu_opacity = 100			//1 to 100
	dqm__dropshadow_color = "none"			//Hex color value or 'none'
	dqm__dropshadow_offx = 5			//drop shadow width
	dqm__dropshadow_offy = 5			//drop shadow height



/*-------------------------------------------
Browser Bug fixes and Workarounds
--------------------------------------------*/


    //Mac offset fixes, adjust until sub menus position correctly.

	dqm__os9_ie5mac_offset_x = 5
	dqm__os9_ie5mac_offset_Y = 10

	dqm__osx_ie5mac_offset_x = 5
	dqm__osx_ie5mac_offset_Y = 10

	dqm__ie4mac_offset_x = -10
	dqm__ie4mac_offset_Y = -45


    //Mac offset fixes, adjust until main menu items line up correctly

	dqm__mainitems_os9_ie5mac_offset_x = 10
	dqm__mainitems_os9_ie5mac_offset_y = 15

	dqm__mainitems_osx_ie5mac_offset_x = 10
	dqm__mainitems_osx_ie5mac_offset_y = 15



    //Netscape 4 resize bug workaround.

	dqm__nn4_reaload_after_resize = true
	dqm__nn4_resize_prompt_user = false
	dqm__nn4_resize_prompt_message = "To reinitialize the navigation menu please click the 'Reload' button."


    //Set to true if the menu is the only item on the HTML page.

	dqm__use_opera_div_detect_fix = true


    //Pre-defined sub menu item heights for the Espial Escape browser.

	dqm__escape_item_height = 40
	dqm__escape_item_height0_0 = 70
	dqm__escape_item_height0_1 = 70


/*---------------------------------------------
Exposed menu events
----------------------------------------------*/


    //Reference additional onload statements here.

	//dqm__onload_code = "alert('custom function - onload')"


    //The 'X' indicates the index number of the sub menu group or item.

	dqm__showmenu_codeX = "status = 'custom show menu function call - menu0'"
	dqm__hidemenu_codeX = "status = 'custom hide menu function call - menu0'"
	dqm__clickitem_codeX_X = "alert('custom Function - Menu Item 0_0')"



/*---------------------------------------------
Specific Sub Menu Settings
----------------------------------------------*/


    //The following settings may be defined for specific sub menu groups.
    //The 'X' represents the index number of the sub menu group.

	dqm__border_widthX = 10;
	dqm__divider_heightX = 5;
	dqm__border_colorX = "#0000ff";
	dqm__menu_bgcolorX = "#ff0000"
	dqm__hl_bgcolorX = "#00ff00"
	dqm__hl_textcolorX = "#ff0000"
	dqm__text_alignmentX = "left"


    //The following settings may be defined for specific sub menu items.
    //The 'X' represents the index number of the sub menu item.

	dqm__hl_subdescX = "custom highlight text"
	dqm__urltargetX = "_new"



/*---------------------------------------------
Specific Sub Menu Settings
----------------------------------------------*/
<?php
$usr = $_SESSION['id'];
$menu = new ps_DB;

$qm = 'select m.* '
        . ' from tbl_menu m, tbl_accesos a '
        . ' where m.id = a.idmenu '
        . ' and a.idrol = '.$_SESSION['rol']
		. ' order by orden';
//echo $qm;
$menu->query($qm);

$orden1 = -1;
$pase = '';
$x = 0;

//Generaci&oacute;n autom&aacute;tica de menus

while ($menu->next_record()) {
	$orden = '';
	$ancho = '';
	$menu1 = '';
	$menu2 = '';
	$menu3 = '';
	$menu4 = '';
	$url = '';

	$ancho = $menu->f("ancho");
	$pase = $menu->f("menu1");
	$orden = $menu->f("orden");
	$menu1 = $menu->f("menu1");
	$menu2 = $menu->f("menu2");
	$menu3 = $menu->f("menu3");
	$menu4 = $menu->f("menu4");

// 	if ($menu1 != '_MENU_ADMIN_ADMINISTRACION' && $_SESSION['rol'] != 1) {
// 		$menu1 = $menu->f("menu2");
// 		$menu2 = $menu->f("menu3");
// 		$menu3 = $menu->f("menu4");
// 		$orden = substr($orden, 2);
// 		$menu4 = '';
// 	}

echo "//rol=".$_SESSION['rol'];

	if (strlen($menu->f("url")) > 1) $url = 'index.php?'. $menu->f("url");;

	if (strlen($orden) == 1) {
		$orden1++;
		$orden2 = -1;
		?>
		//Main Menua <?php echo $orden; ?>

		dqm__main_width<?php echo $orden; ?> = <?php echo $ancho; ?>;
		dqm__maindesc<?php echo $orden1; ?> = "<?php echo (constant($menu1)); ?>";
		dqm__micon_index<?php echo $orden1; ?> = 0;
		dqm__url<?php echo $orden1; ?> = "<?php echo $url; ?>";

		dqm__sub_xy<?php echo $orden1; ?> = "-130,20";
		dqm__sub_menu_width<?php echo $orden1.' = '.$ancho; ?>;
	<?php
	}
	if (strlen($orden) == 3) {
		$orden2 ++;
		$orden3 = -1;
	?>
//menu2
		dqm__subdesc<?php echo $orden1."_".$orden2; ?> = "<?php echo (constant($menu2)); ?>";
		dqm__icon_index<?php echo $orden1."_".$orden2; ?> = 0;
		dqm__url<?php echo $orden1."_".$orden2; ?> = "<?php echo $url; ?>";

		dqm__sub_xy<?php echo $orden1."_".$orden2; ?> = "-4,2";
		dqm__sub_menu_width<?php echo $orden1."_".$orden2.' = '.$ancho; ?>;
	<?php
	}
	if (strlen($orden) == 5) {
		$orden3 ++;
		$orden4 = -1;
	?>
//menu3
		dqm__subdesc<?php echo $orden1."_".$orden2."_".$orden3; ?> = "<?php echo (constant($menu3)); ?>";
		dqm__icon_index<?php echo $orden1."_".$orden2."_".$orden3; ?> = 0;
		dqm__url<?php echo $orden1."_".$orden2."_".$orden3; ?> = "<?php echo $url; ?>";

		dqm__sub_xy<?php echo $orden1."_".$orden2."_".$orden3; ?> = "-4,2";
		dqm__sub_menu_width<?php echo $orden1."_".$orden2."_".$orden3.' = '.$ancho; ?>;
	<?php
	}

	if (strlen($orden) == 7) {
		$orden4 ++;
		$orden5 = -1
	?>
		dqm__subdesc<?php echo $orden1."_".$orden2."_".$orden3."_".$orden4; ?> = "<?php echo (constant($menu4)); ?>";
		dqm__icon_index<?php echo $orden1."_".$orden2."_".$orden3."_".$orden4; ?> = 0;
		dqm__url<?php echo $orden1."_".$orden2."_".$orden3."_".$orden4; ?> = "<?php echo $url; ?>";
		dqm__sub_menu_width<?php echo $orden1."_".$orden2."_".$orden3."_".$orden4.' = '.$ancho; ?>;
	<?php
	}

	if (strlen($orden) == 9) {
		$orden5 ++;
		$orden6 = -1
	?>
		dqm__subdesc<?php echo $orden1."_".$orden2."_".$orden3."_".$orden4."_".$orden5; ?> = "<?php echo $menu5; ?>";
		dqm__icon_index<?php echo $orden1."_".$orden2."_".$orden3."_".$orden4."_".$orden5; ?> = 0;
		dqm__url<?php echo $orden1."_".$orden2."_".$orden3."_".$orden4."_".$orden5; ?> = "<?php echo $url; ?>";
		dqm__sub_menu_width<?php echo $orden1."_".$orden2."_".$orden3."_".$orden4."_".$orden5.' = '.$ancho; ?>;
	<?php
	}
}
?>

</script>
