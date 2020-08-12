<?php
/**
 * @package Moloni
 * @author Nuno Almeida
 * @website https://www.moloni.com
 * @email nuno@datasource.pt
 * @copyright Moloni
 * @license 
 * */
// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

//class MoloniController extends JController
class MoloniController extends JControllerLegacy
{
    //function display()
    public function display($cachable = false, $urlparams = array())
    {

        $this->initiate();
        parent::display();
    }

    function initiate()
    {
        $document = JFactory::getDocument();

        //$document->addScript('//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js');
        require_once(JPATH_COMPONENT_ADMINISTRATOR . "/assets/functions/base.functions.php");
        require_once(JPATH_COMPONENT_ADMINISTRATOR . "/assets/functions/general.functions.php");
        require_once(JPATH_COMPONENT_ADMINISTRATOR . "/assets/functions/start.functions.php");
        require_once(JPATH_COMPONENT_ADMINISTRATOR . "/assets/functions/entities.class.php");
        require_once(JPATH_COMPONENT_ADMINISTRATOR . "/assets/functions/documents.class.php");
        require_once(JPATH_COMPONENT_ADMINISTRATOR . "/assets/functions/items.class.php");
    }

}
