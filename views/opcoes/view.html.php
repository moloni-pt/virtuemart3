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
jimport( 'joomla.application.component.view');
class MoloniViewOpcoes extends JViewLegacy
{
	function display ($tpl = null)
	{
		JHtml::stylesheet(Juri::base() . 'components/com_moloni/assets/css/style.css');
		
		parent::display($tpl);
	}
	
}