<?php

namespace Moloni\Functions;

use Moloni\Functions\Log;

class Hooks
{
    private $orderId;
    private $orderStatus;
    private $order_number;
    private $orderObj;

    function __construct($order)
    {
        $this->orderId = $order->virtuemart_order_id;
        $this->orderStatus = $order->order_status;
        $this->order_number = $order->order_number;
    }

    public function init()
    {
        MoloniDb::defineValues();
        MoloniDb::defineConfigs();

        if (defined('INVOICE_AUTO') && defined('INVOICE_AUTO_STATUS') &&
            (int)INVOICE_AUTO === 1 && INVOICE_AUTO_STATUS === $this->orderStatus) {

            $orderInfo = Virtuemart::getOneOrder($this->orderId);

            if (count($orderInfo) > 0) {
                if (Start::login()) {
                    //sets the order with the data that we got from the database (for safety)
                    $this->orderObj = $orderInfo;

                    return true;
                }

                $msg = sprintf('Login inválido, encomenda %s não foi criada.', $this->order_number);
            } else {
                $msg = sprintf('Encomenda %s não encontrada ou já foi gerada.', $this->order_number);
            }
        } else {
            $msg = sprintf('Documentos automáticos desativados ou estado diferente do escolhido. (%s)', $this->order_number);
        }

        Log::write($msg);

        return false;
    }

    public function createDocument()
    {
        $orderItems = Virtuemart::getAllItemsByOrder($this->orderId);
        $client = Virtuemart::getOneClientByOrder($this->orderId);
        $costumerID = Entities::getCostumerID($client);

        $invoiceResult = Documents::createInvoice($this->orderId, $this->orderObj, $orderItems, $costumerID);

        if ($invoiceResult) {
            $msg = sprintf('Documento da encomenda %s gerada automaticamente com sucesso!', $this->order_number);
        } else {
            $msg = sprintf('Algo pode ter corrido errado na encomenda %s.', $this->order_number);
        }

        //cleans any messages that may have been set during document creation
        $_SESSION['messages'] = [];

        Log::write($msg);
    }
}