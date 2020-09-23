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

    /**
     * Se o plugin for instalado através do importador de extensões do Virtuemart
     * A pasta /vendor não é importada e não temos acesso ao vendor/autoload.php
     */
    public function initiate()
    {
        if (file_exists(JPATH_COMPONENT_ADMINISTRATOR . "/vendor/autoload.php")) {
            require_once(JPATH_COMPONENT_ADMINISTRATOR . "/vendor/autoload.php");
        } else {
            foreach (scandir(JPATH_COMPONENT_ADMINISTRATOR . "/assets/functions") as $filename) {
                $path = JPATH_COMPONENT_ADMINISTRATOR . "/assets/functions/" . $filename;
                if (file_exists($path) && is_file($path)) {
                    require_once $path;
                }
            }
        }
    }
}
