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

class MoloniViewMoloni extends JViewLegacy
{
    function display($tpl = null)
    {
        JHtml::stylesheet(Juri::base() . 'components/com_moloni/assets/css/style.css');

        $this->addToolbar();

        parent::display($tpl);
    }

    protected function addToolbar()
    {
        // Options button.
        if (JFactory::getUser()->authorise('core.admin', 'com_moloni')) {
            JToolBarHelper::preferences('com_moloni');
        }
        
        JToolBarHelper::title('Moloni: Cloud Business Tools', 'moloni-titulo');
        $toolbar = JToolBar::getInstance('toolbar');
        //$toolbar->appendButton('Link', 'save', 'Início', 'index.php?option=com_moloni', 500, 210);
        $toolbar->appendButton('Popup', 'options', 'Configurações', 'index.php?option=com_moloni&view=opcoes&tmpl=component', 600, 400);
    }

}
