<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

class MoloniController extends JControllerLegacy
{
    //function display()
    public function display($cachable = false, $urlparams = array())
    {
        $this->initiate();
        parent::display();
    }

    public function initiate()
    {
        $document = JFactory::getDocument();
        require_once(JPATH_COMPONENT_ADMINISTRATOR . "/vendor/autoload.php");
    }
}
