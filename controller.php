<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

class MoloniController extends JControllerLegacy
{
    public function display($cachable = false, $urlparams = array())
    {
        $this->initiate();
        parent::display($cachable, $urlparams);
    }

    public function initiate()
    {
        require_once(JPATH_COMPONENT_ADMINISTRATOR . "/vendor/autoload.php");
    }
}
