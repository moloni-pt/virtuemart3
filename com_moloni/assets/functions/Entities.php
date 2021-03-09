<?php

namespace Moloni\Functions;

class Entities
{
    public static function getCostumerID($clientInfo)
    {
        $vat = '';
        $vatField = defined('VAT_FIELD') ? VAT_FIELD : 'undefined';

        if (isset($clientInfo[0]->$vatField)) {
            $vat = $clientInfo[0]->$vatField;
        }

        if (trim($vat) === '') {
            $vat = '999999990';
        }

        if ($vat !== '999999990') {
            $clientID = self::getByVat(['vat' => $vat]);
        } else {
            $clientID = self::getByEmail(['email' => $clientInfo[0]->email]);
        }

        if (!$clientID) {
            $nextNumber = self::getNextNumber();

            if (!$nextNumber) {
                $nextNumber = defined('CLIENT_PREFIX') ? CLIENT_PREFIX : '';
                $nextNumber .= (($clientInfo[0]->virtuemart_user_id > 0) ? $clientInfo[0]->virtuemart_user_id : $vat);
            }

            $values = [];
            $values['company_id'] = COMPANY_ID;
            $values['vat'] = $vat;
            $values['number'] = $nextNumber;
            $values['name'] = str_replace('  ', ' ', $clientInfo[0]->first_name . ' ' . $clientInfo[0]->middle_name . ' ' . $clientInfo[0]->last_name);
            $values['language_id'] = General::getLanguageID($clientInfo[0]->virtuemart_country_id);
            $values['address'] = $clientInfo[0]->address_1;
            $values['zip_code'] = General::verifyZip($clientInfo[0]->zip);
            $values['city'] = $clientInfo[0]->city;
            $values['country_id'] = General::getCountryID($clientInfo[0]->virtuemart_country_id);
            $values['email'] = $clientInfo[0]->email;
            $values['website'] = '';
            $values['phone'] = $clientInfo[0]->phone_1;
            $values['fax'] = $clientInfo[0]->fax;
            $values['contact_name'] = str_replace('  ', ' ', $clientInfo[0]->first_name . ' ' . $clientInfo[0]->middle_name . ' ' . $clientInfo[0]->last_name);
            $values['contact_email'] = $clientInfo[0]->email;
            $values['contact_phone'] = $clientInfo[0]->phone_2;
            $values['notes'] = '';
            $values['salesman_id'] = '';
            $values['maturity_date_id'] = MATURITY_DATE;
            $values['payment_day'] = '';
            $values['discount'] = '';
            $values['credit_limit'] = '';
            $values['payment_method_id'] = PAYMENT_METHOD;
            $values['delivery_method_id'] = '';
            $values['field_notes'] = '';

            return self::costumerInsert($values);
        }

        return ($clientID);
    }

    public static function getByVat($values)
    {
        $values['company_id'] = COMPANY_ID;
        $values['qty'] = '1';
        $values['offset'] = '0';
        $values['exact'] = '1';

        $results = Base::cURL('customers/getByVat', $values);
        if (isset($results[0]) && is_array($results[0]) && isset($results[0]['customer_id']) > 0) {
            return ($results[0]['customer_id']);
        }

        return false;
    }

    public static function getByEmail($values)
    {
        $values['company_id'] = COMPANY_ID;
        $values['qty'] = '1';
        $values['offset'] = '0';
        $values['exact'] = '1';

        $results = Base::cURL('customers/getByEmail', $values);
        if (count($results[0]) > 0) {
            return ($results[0]['customer_id']);
        }

        return false;
    }

    public static function getNextNumber()
    {
        $results = Base::cURL('customers/getNextNumber', ['company_id' => COMPANY_ID]);
        return isset($results['number']) ? $results['number'] : false;
    }

    public static function costumerInsert($values)
    {
        $results = Base::cURL('customers/insert', $values);
        if (!isset($results['customer_id'])) {
            Base::genError('customers/insert', $values, $results);
            return false;
        }

        return ($results['customer_id']);
    }
}