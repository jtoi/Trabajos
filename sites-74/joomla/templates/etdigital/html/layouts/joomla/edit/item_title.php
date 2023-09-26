<?php
/**
 * @package Helix Ultimate Framework
 * @author enginetemplates https://www.enginetemplates.com
 * @copyright Copyright (c) 2010 - 2018 enginetemplates
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined ('JPATH_BASE') or die();

$title = $displayData->getForm()->getValue('title');
$name = $displayData->getForm()->getValue('name');

?>

<?php if ($title) : ?>
	<h4><?php echo $title; ?></h4>
<?php endif; ?>

<?php if ($name) : ?>
	<h4><?php echo $name; ?></h4>
<?php endif;
