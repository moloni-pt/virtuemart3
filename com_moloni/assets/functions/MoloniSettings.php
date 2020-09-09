<?php

namespace Moloni\Functions;

class MoloniSettings
{
    public static function documentSets()
    {
        $values = array('company_id' => COMPANY_ID);
        $results = Base::cURL('documentSets/getAll', $values);
        return ($results);
    }

    public static function paymentMethods()
    {
        $values = array('company_id' => COMPANY_ID);
        $results = Base::cURL('paymentMethods/getAll', $values);
        return ($results);
    }

    public static function measurementUnits()
    {
        $values = array('company_id' => COMPANY_ID);
        $results = Base::cURL('measurementUnits/getAll', $values);
        return ($results);
    }

    public static function exemptionReasons()
    {
        $results = Base::cURL('taxExemptions/getAll');
        return ($results);
    }

    public static function maturityDates()
    {
        $values['company_id'] = COMPANY_ID;
        $results = Base::cURL('maturityDates/getAll', $values);
        return ($results);
    }

    public static function countries()
    {
        $results = Base::cURL('countries/getAll');
        return ($results);
    }

    public static function languages()
    {
        $results = Base::cURL('languages/getAll');
        return ($results);
    }

}