<?php
defined('_JEXEC') or die('Restricted access');

class documents
{
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

            $values['products'][$x]['product_id'] = products::getItemByRef($item->order_item_sku, $item);
            $values['products'][$x]['name'] = $item->order_item_name;
            $values['products'][$x]['summary'] = '';
            $values['products'][$x]['qtd'] = $item->product_quantity;
            $values['products'][$x]['price'] = $item->product_item_price;
            $values['products'][$x]['discount'] = $discount;
            $values['products'][$x]['order'] = $x + 1;
            if ((float)$item->product_tax > 0) {
                $values['products'][$x]['taxes'] = [];
                $taxRate = products::getTaxByVal(($item->product_tax / $item->product_item_price) * 100);

                $values['products'][$x]['taxes'][0]['tax_id'] = $taxRate;
                $values['products'][$x]['taxes'][0]['value'] = $item->product_tax;
            } else {
                $values['products'][$x]['exemption_reason'] = EXEMPTION_REASON;
            }

            $x = $x + 1;
        }

        if ($orderInfo[0]->order_shipment > 0) {
            $values['products'][$x]['product_id'] = products::getShipByRef('portes', $orderInfo[0]->order_shipment, $orderInfo[0]->order_shipment_tax);
            $values['products'][$x]['name'] = 'Portes';
            $values['products'][$x]['summary'] = '';
            $values['products'][$x]['qtd'] = '1';
            $values['products'][$x]['price'] = $orderInfo[0]->order_shipment;
            $values['products'][$x]['discount'] = '';
            $values['products'][$x]['order'] = $x + 1;

            if ((float)$orderInfo[0]->order_shipment_tax > 0) {
                $values['products'][$x]['taxes'] = array();
                $values['products'][$x]['taxes'][0]['tax_id'] = products::getTaxByVal(($orderInfo[0]->order_shipment_tax / $orderInfo[0]->order_shipment) * 100);
                $values['products'][$x]['taxes'][0]['value'] = $orderInfo[0]->order_shipment_tax;
            } else {
                $values['products'][$x]['exemption_reason'] = EXEMPTION_REASON;
            }
        }

        $values['notes'] = '';
        $values['status'] = defined('DOCUMENT_STATUS') ? DOCUMENT_STATUS : 0;

        $results = base::cURL(DOCUMENT_TYPE . '/insert', ($values));
        if (!isset($results['document_id'])) {
            base::genError(DOCUMENT_TYPE . '/insert', $values, $results);
        } else {
            sql::update('virtuemart_orders', array('virtuemart_order_id' => $orderID, 'moloni_sent' => $results['document_id']), 'virtuemart_order_id');
            return ($results['document_id']);
        }

        return false;
    }

}
