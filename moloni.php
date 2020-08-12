<?php
/**
* @package Moloni
* @author Nuno Almeida
* @website https://www.moloni.com
* @email nuno@datasource.pt
* @copyright Moloni
* @license 
**/

// no direct access
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controller');
// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_moloni')) {
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

$controller = JControllerLegacy::getInstance('Moloni');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
