<?php

namespace Moloni\Functions;

class General
{
    public static function verifyZip($zip)
    {
        //$regexp = "\d{4}-\d{3}";
        //if (preg_match($regexp, $zip)) {
        //NH - Validar Codigo postal
        if (preg_match('/^[0-9]{4,4}([- ]?[0-9]{3,3})?$/', $zip)) {
            $zip = $zip;
        } else {
            $zip = '1000-100'; // usar Cod nao valido
        }

        return ($zip);
    }

    public static function getCountryID($id)
    {
        $resultsMoloni = MoloniSettings::countries();
        $resultsVM = Virtuemart::getOneCountryByID($id);

        if(isset($resultsMoloni) && is_array($resultsMoloni)) {
            foreach ($resultsMoloni as $result) {
                if (strtoupper($result['iso_3166_1']) === $resultsVM[0]->country_2_code) {
                    return ($result['country_id']);
                }
            }
        }

        return ('1');
    }

    public static function getLanguageID($id)
    {
        $resultsMoloni = MoloniSettings::languages();
        $resultsVM = Virtuemart::getOneCountryByID($id);

        if(isset($resultsMoloni) && is_array($resultsMoloni)) {
            foreach ($resultsMoloni as $result) {
                if (strtoupper($result['code']) === $resultsVM[0]->country_2_code) {
                    return ($result['language_id']);
                }
            }
        }

        return ('1');
    }

    public static function markOrder($id, $value = 1)
    {
        Sql::update('virtuemart_orders', array('virtuemart_order_id' => $id, 'moloni_sent' => $value), 'virtuemart_order_id');
    }

}
