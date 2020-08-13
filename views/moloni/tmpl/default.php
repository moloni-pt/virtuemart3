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
if (isset($_GET['action']) && $_GET['action'] == "update") {
    start::forceUpdate();
} else {
    if (start::login()) {
        if (isset($_GET['action']) && $_GET['action'] == 'makeInvoice') {
            $orderID = (int)$_GET['id'];
            $orderInfo = vmBasics::getOneOrder($orderID);
            $orderItems = vmBasics::getAllItemsByOrder($orderID);
            $client = vmBasics::getOneClientByOrder($orderID);

            if (count($orderInfo) > 0) {
                moloniDB::defineConfigs();
                $costumerID = entities::getCostumerID($client);
                $invoiceResult = documents::createInvoice($orderID, $orderInfo, $orderItems, $costumerID);
                if (!$invoiceResult) {

                } else {
                    echo "<h1><b>Factura n.º $_GET[id] gerada com sucesso!</b></h1><br>";
                    general::listAllOrder();
                }
            } else {
                base::triggerError("A encomenda não existe ou já foi gerada factura!");
            }
        } elseif (isset($_GET['action']) && $_GET['action'] == 'removeOrder') {
            echo "<h1 style='color: red'><b>Factura n.º $_GET[id] removida com sucesso!</b></h1><br>";
            general::markOrder($_GET['id']);
            general::listAllOrder();
        } else {
            general::listAllOrder();
        }

        echo "</table>";
        // start::update();
    }
}