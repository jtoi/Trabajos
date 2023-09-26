<?php
/**
 * @package Helix Ultimate Framework
 * @author enginetemplates http://enginetemplates.com
 * @copyright Copyright (c) 2010 - 2018 enginetemplates
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined ('_JEXEC') or die();

if (!$this->params->get('comingsoon'))
{
  header("Location: " . \JURI::root());
}

echo JLayoutHelper::render('comingsoon', array('language' => $this->language, 'direction' => $this->direction, 'params' => $this->params));