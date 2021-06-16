<?php

use Moloni\Functions\Messages;
use Moloni\Functions\Sql;
use Moloni\Functions\Start;
use Moloni\Functions\Virtuemart;
use Moloni\Functions\MoloniDb;
use Moloni\Functions\Entities;
use Moloni\Functions\Documents;
use Moloni\Functions\General;

defined('_JEXEC') or die('Restricted access');

if (!class_exists( 'VmConfig' )) {
    require(JPATH_ROOT .'/administrator/components/com_virtuemart/helpers/config.php');
}
VmConfig::loadConfig();
if (!class_exists( 'VmModel' )) {
    require(JPATH_ROOT .'/administrator/components/com_virtuemart/helpers/vmmodel.php');
}

/**
 * Classe responsável pela home do componente 'Moloni'. É apresentada a tabela das encomendas do utilizador com os
 * respetivos detalhes e ações para cada documento (gerar ou descartar o documento).
 *
 * Class MoloniViewMoloni
 */
class MoloniViewMoloni extends JViewLegacy
{
    private $tmpl = 'login';

    /**
     * Display da view "home" (index_orders). Display da toolbar que permite aceder as configurações e fazer logout.
     * Import tanto do JavaScript e CSS necessário. O direcionamento é feito por GET ($ _GET ['action']).
     *
     * @param string $tmpl Template a ser mostrado (index_$tmpl). Corresponde ao nome da view que vai ser mostrada.
     *
     * @return Exception|mixed|void
     */
    public function display($tmpl = '')
    {
        JHtml::stylesheet(Juri::base() . 'components/com_moloni/assets/css/style.css');
        JHtml::script(Juri::base() . 'components/com_moloni/assets/js/moloni.js');

        JToolBarHelper::title('Moloni: Cloud Business Tools', 'moloni-titulo');

        $this->setLayout('index');

        if (isset($_GET['action']) && $_GET['action'] === "update") {
            Start::forceUpdate();
        }

        if ($this->isLoggedIn()) {
            $this->tmpl = empty($tmpl) ? 'orders' : $tmpl;
            $this->addToolbar();
        }

        if (isset($_GET['action']) && $_GET['action'] === 'makeInvoice') {
            $this->makeInvoice();
        }

        if (isset($_GET['action']) && $_GET['action'] === 'removeOrder') {
            $this->removeOrder();
        }

        if (isset($_GET['action']) && $_GET['action'] === 'logout') {
            $this->logout();
        }

        parent::display($this->tmpl);
    }

    /**
     * Verifica se o utilizador esta logado e caso tenha feito login com sucesso é redirecionado para a
     * a view "index_companies", a qual permite selecionar a empresa que o utilizador deseja. Caso não tenha
     * nenhuma empresa disponível é apresentada mensagem de erro e com opção de voltar a fazer login.
     *
     * @return bool
     */
    private function isLoggedIn()
    {
        if (Start::login()) {
            if (!defined('COMPANY_ID') || (int)COMPANY_ID <= 0) {
                $this->tmpl = 'companies';

                return false;
            }

            return true;
        }

        return false;
    }

    /**
     * Limpar o Url
     */
    private function limparUrl()
    {
        $urlFinal = strtok($_SERVER['REQUEST_URI'], "&");

        $url = sprintf(
            "%s://%s%s",
            isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http',
            $_SERVER['SERVER_NAME'],
            $urlFinal
        );
        header('location: ' . $url);
    }

    /**
     * Após ser chamado este método é redirecionado para a "Home" do componente 'Moloni'.
     * (http://lojas.spydesk.com/virtuemart32/administrator/index.php?option=com_moloni)
     */
    public function homeMoloni()
    {
        session_write_close();
        header('Location: http://lojas.spydesk.com/virtuemart32/administrator/index.php?option=com_moloni');
    }

    /**
     * Logout da conta
     *
     */
    private function logout()
    {
        Sql::cleanMoloniDatabases();

        $this->limparUrl();
    }

    /**
     * Adiciona a toolbar com as opções de "Configurações" e de "Logout" a view principal do componente 'Moloni'
     *
     * @return null
     */
    protected function addToolbar()
    {
        $toolbar = JToolBar::getInstance('toolbar');
        $toolbar->appendButton(
            'Popup',
            'options',
            'Configurações',
            'index.php?option=com_moloni&view=opcoes&tmpl=component',
            1000,
            500,
            0,
            0,
            '',
            '',
            '<button type="button" class="btn" data-dismiss="modal">'
            . JText::_('Cancelar')
            . '</button>'
            . '<button type="button" class="btn btn-success"'
            . ' onclick="jQuery(\'#modal-options iframe\').contents().find(\'#formOpcoes\').submit();">'
            . JText::_('Guardar')
            . '</button>'
        );

        JToolbarHelper::link('index.php?option=com_moloni&action=logout', 'Logout', 'logout');

        return null;
    }

    /**
     * Ao clicar no botão da tabela "Ações -> Gerar" é emitido um documento que irá ser inserido no Moloni.
     * Caso a criação do documento seja feita com sucesso, serão retornadas as mensagens de sucesso caso contrário é
     * apresentado uma mensagem de erro com os respetivos detalhes do erro.
     */
    public function makeInvoice()
    {
        $orderID = (int)$_GET['id'];
        $orderInfo = Virtuemart::getOneOrder($orderID);
        $orderItems = Virtuemart::getAllItemsByOrder($orderID);
        $client = Virtuemart::getOneClientByOrder($orderID);

        if (count($orderInfo) > 0) {
            MoloniDb::defineConfigs();

            $costumerID = Entities::getCostumerID($client);
            $invoiceResult = Documents::createInvoice($orderID, $orderInfo, $orderItems, $costumerID);

            if ($invoiceResult) {
                Messages::addSessionMessage(
"<div class='msgSucesso'>Fatura n.º $_GET[id] gerada com sucesso!
<a class='moloniClose' onclick='this.parentNode.style.display = \"none\"'>&#10005;</a>
</div>"
                );
            }
        } else {
            Messages::addSessionMessage(
                "<div class='msgAlertaForms3'>A encomenda não existe ou já foi gerado o documento!
<a class='moloniClose' onclick='this.parentNode.style.display = \"none\"'>&#10005;</a>
</div>"
            );
        }

        $this->homeMoloni();
    }

    /**
     * Ao clicar no botão da tabela "Ações -> Descartar" é removida uma encomenda da tabela e apresenta mensagem.
     */
    public function removeOrder()
    {
        General::markOrder($_GET['id']);
        Messages::addSessionMessage(
            "<div class='msgAlertaForms2'>Fatura n.º $_GET[id] removida com sucesso!
<a class='moloniClose' onclick='this.parentNode.style.display = \"none\"'>&#10005;</a>
</div>"
        );
        $this->homeMoloni();
    }
}
