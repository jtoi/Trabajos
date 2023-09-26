<?php
/**
 * @package Helix Ultimate Framework
 * @author enginetemplates https://www.enginetemplates.com
 * @copyright Copyright (c) 2010 - 2018 enginetemplates
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined ('_JEXEC') or die();

extract($displayData);

/**
 * Layout variables
 * -----------------
 * @var   JForm        $form    Form with extra options for the set
 * @var   JLayoutFile  $this    Context
 */

?>
<div class="setoptions-form-wrapper">
<?php foreach ($form->getGroup(null) as $field) : ?>
	<?php echo $field->renderField(); ?>
<?php endforeach; ?>
</div>
