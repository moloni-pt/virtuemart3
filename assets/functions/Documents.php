<?php

namespace Moloni\Functions;

use ReCaptcha\RequestMethod\Curl;

/**
 * Classe que cria os documentos para serem inseridos no Moloni
 *
 * @package Moloni\Functions
 *
 */
class Documents
{
    /**
     * Criação de uma documento para ser inserido via API
     *
     * @param $orderID int Id da encomenda
     * @param $orderInfo array Informações da encomenda
     * @param $orderItems array Artigos da encomenda
     * @param $clientID int Id do cliente
     *
     * @return bool
     *
     * @throws \Exception
     */
    public static function createInvoice($orderID, $orderInfo, $orderItems, $clientID)
    {
        $values['company_id'] = COMPANY_ID;
        $values['date'] = date('d-m-Y');
        $values['expiration_date'] = date('d-m-Y');
        $values['document_set_id'] = defined('DOCUMENT_SET_ID') ? DOCUMENT_SET_ID : 0;
        $values['customer_id'] = $clientID;
        $values['our_reference'] = '';
        $values['your_reference'] = '';
        $values['financial_discount'] = '';
        $values['salesman_id'] = '';
        $values['salesman_commission'] = '';
        $values['deduction_id'] = '';
        $values['special_discount'] = '';
        $values['related_documents_notes'] = '';
        $values['products'] = array();
        $x = 0;

        foreach ($orderItems as $item) {
            $discount = abs(($item->product_subtotal_discount / $item->product_quantity) * 100 / $item->product_item_price);
            $discount = ($discount < 0) ? 0 : ($discount > 100) ? 100 : $discount;

            $values['products'][$x]['product_id'] = Products::getItemByRef($item->order_item_sku, $item);
            $values['products'][$x]['name'] = $item->order_item_name;
            $values['products'][$x]['summary'] = '';
            $values['products'][$x]['qtd'] = $item->product_quantity;
            $values['products'][$x]['price'] = $item->product_item_price;
            $values['products'][$x]['discount'] = $discount;
            $values['products'][$x]['order'] = $x + 1;
            if ((float)$item->product_tax > 0) {
                $values['products'][$x]['taxes'] = [];
                $taxRate = Products::getTaxByVal(($item->product_tax / $item->product_item_price) * 100);

                $values['products'][$x]['taxes'][0]['tax_id'] = $taxRate;
                $values['products'][$x]['taxes'][0]['value'] = $item->product_tax;
            } else {
                $values['products'][$x]['exemption_reason'] = EXEMPTION_REASON;
            }

            $x++;
        }

        if ($orderInfo[0]->order_shipment > 0) {
            $values['products'][$x]['product_id'] = Products::getShipByRef(
                'portes',
                $orderInfo[0]->order_shipment,
                $orderInfo[0]->order_shipment_tax
            );
            $values['products'][$x]['name'] = 'Portes';
            $values['products'][$x]['summary'] = '';
            $values['products'][$x]['qtd'] = '1';
            $values['products'][$x]['price'] = $orderInfo[0]->order_shipment;
            $values['products'][$x]['discount'] = '';
            $values['products'][$x]['order'] = $x + 1;

            if ((float)$orderInfo[0]->order_shipment_tax > 0) {
                $values['products'][$x]['taxes'] = array();
                $values['products'][$x]['taxes'][0]['tax_id'] = Products::getTaxByVal(($orderInfo[0]->order_shipment_tax / $orderInfo[0]->order_shipment) * 100);
                $values['products'][$x]['taxes'][0]['value'] = $orderInfo[0]->order_shipment_tax;
            } else {
                $values['products'][$x]['exemption_reason'] = EXEMPTION_REASON;
            }
        }

        $values['notes'] = '';
        $values['status'] = 0;

        $results = Base::cURL(DOCUMENT_TYPE . '/insert', ($values));

        if (!isset($results['document_id'])) {
            return false;
        } else {
            Sql::update('virtuemart_orders', array('virtuemart_order_id' => $orderID, 'moloni_sent' => $results['document_id']), 'virtuemart_order_id');
        }

        $addedDocument = Base::cURL(DOCUMENT_TYPE . '/getOne', ['company_id' => COMPANY_ID, 'document_id' => $results['document_id']]);

        if (defined('DOCUMENT_STATUS') && (int)DOCUMENT_STATUS === 1) {
            $orderTotal = (float)$orderInfo[0]->order_total;
            $documentTotal = (float)$addedDocument['exchange_total_value'] > 0 ? (float)$addedDocument['exchange_total_value'] : (float)$addedDocument['net_value'];
            if ($orderTotal !== $documentTotal) {
                Messages::addSessionMessage(
                    "<div class='msgAlertaForms2'>Fatura n.º $_GET[id] inserida mas totais não correspondem!
<a class='moloniClose' onclick='this.parentNode.style.display = \"none\"'>&#10005;</a>
</div>"
                );
            }

            $closeDocument = [];
            $closeDocument['document_id'] = $results['document_id'];
            $closeDocument['status'] = 1;
            $closeDocument['company_id'] = COMPANY_ID;

            if ((int)MoloniDb::$settings['email_send'] === 1 ) {
                Messages::addSessionMessage(
                    "<div class='msgSucesso'>Documento enviado por email para o cliente!
<a class='moloniClose' onclick='this.parentNode.style.display = \"none\"'>&#10005;</a>
</div>"
                );
                $orderID = (int)$_GET['id'];
                $client = Virtuemart::getOneClientByOrder($orderID);

                $closeDocument['send_email'] = [];
                $closeDocument['send_email'][] = [
                    'email' => $client[0]->email,
                    'name' => $client[0]->first_name. ''. $client[0]->last_name,
                    'msg' => ''
                ];
            }

            $result = Base::cURL(DOCUMENT_TYPE . '/update', $closeDocument);

            if (!isset($result['valid']) || (int)$result['valid'] === 0) {
                Messages::addSessionMessage("
<div class='msgAlertaForms2'>Não foi possível fechar o documento!
<a class='moloniClose' onclick='this.parentNode.style.display = \"none\"'>&#10005;</a>
</div>"
                );
            }

            Messages::addSessionMessage(
                "<div class='msgSucesso'>Documento inserido no Moloni!
<a class='moloniClose' onclick='this.parentNode.style.display = \"none\"'>&#10005;</a>
</div>"
            );
            return true;
        } else {
            Messages::addSessionMessage(
                "<div class='msgSucesso'>Documento inserido como rascunho no Moloni!
<a class='moloniClose' onclick='this.parentNode.style.display = \"none\"'>&#10005;</a>
</div>"
            );
        }

        return true;
    }
}
