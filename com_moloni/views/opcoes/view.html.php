<?php

use Moloni\Functions\Messages;
use Moloni\Functions\MoloniDb;
use Moloni\Functions\Sql;

jimport('joomla.application.component.view');

if (!class_exists( 'VmConfig' )) {
    require(JPATH_ROOT .'/administrator/components/com_virtuemart/helpers/config.php');
}
VmConfig::loadConfig();
if (!class_exists( 'VmModel' )) {
    require(JPATH_ROOT .'/administrator/components/com_virtuemart/helpers/vmmodel.php');
}

/**
 * Classe responsável pelo menu de configurações de utilizador do componente 'Moloni'.
 * É apresentada uma lista de opões, as quais serão usadas para gerir documentos e vão de acordo com as
 * preferências do utilizador.
 *
 * Class MoloniViewOpcoes
 */
class MoloniViewOpcoes extends JViewLegacy
{
    /**
     * Display do menu de configurações (index_settings). Import do CSS necessário.
     *
     * @param string $tmpl Template a ser mostrado (index_$tmpl). Corresponde ao nome da view que vai ser mostrada.
     *
     * @return Exception|mixed|void
     */
    public function display($tmpl = '')
    {
        JHtml::stylesheet(Juri::base() . 'components/com_moloni/assets/css/style.css');
        JHtml::_('jquery.framework');

        $this->setLayout('index');
        $tmpl = empty($tmpl) ? 'settings' : $tmpl;

        try {
            $this->init();
        } catch (Exception $e) {
        }

        parent::display($tmpl);
    }

    /**
     * Função principal das configurações. São obtidos os valores das configurações definidas referentes ao
     * utilizador logado e é chamada a função responsável por guardar os novos valores guardados.
     *
     * @return null
     * @throws Exception
     */
    public function init()
    {
        MoloniDb::defineValues();

        $this->saveSettings();

        return null;
    }

    /**
     * Guarda as novas alterações feitas nas Configurações
     *
     * @return null
     *
     * @throws \Exception
     */
    public function saveSettings()
    {
        if (isset($_POST['action']) && $_POST['action'] === 'registarAlteracoes') {
            Messages::$messages[] = "<div 
class='msgAlertaForms'>Dados guardados 
<a class='moloniClose' onclick='this.parentNode.style.display = \"none\"'>&#10005;</a>
</div>";

            foreach ($_POST['opt'] as $key => $val) {
                try {
                    $results = Sql::select('*', 'moloni_api_config', "config = '$key'");

                    if(isset($results) && count($results)) {
                        Sql::update('moloni_api_config', ['config' => $key, 'selected' => $val], 'config');
                    } else {
                        Sql::insert('moloni_api_config', ['config' => $key, 'selected' => $val]);
                    }
                } catch (Exception $e) {
                    throw new \Exception('Ups, algo correu mal :(', 0, $e);
                }
            }
        }

        return null;
    }
}
