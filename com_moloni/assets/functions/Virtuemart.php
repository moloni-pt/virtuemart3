<?php

namespace Moloni\Functions;

class Virtuemart
{
    public static function customFields()
    {
        $results = Sql::select('*', 'virtuemart_userfields');
        return ($results);
    }

    public static function getAllOrders()
    {
        $results = Sql::select('*', 'virtuemart_orders', 'moloni_sent < 1', 'virtuemart_order_id ASC');
        return ($results);
    }

    public static function getOneOrder($id)
    {
        $results = Sql::select('*', 'virtuemart_orders', "moloni_sent < 1 AND virtuemart_order_id = '$id'", 'virtuemart_order_id ASC');
        return ($results);
    }

    public static function getAllItemsByOrder($id)
    {
        $id = (int)$id;
        $results = Sql::select('*', 'virtuemart_order_items', "virtuemart_order_id = '$id'");
        return ($results);
    }

    public static function getOneClientByOrder($orderID)
    {
        $orderID = (int)$orderID;
        $results = Sql::select('*', 'virtuemart_order_userinfos', "virtuemart_order_id = '$orderID'");
        return ($results);
    }

    public static function getOneCountryByID($id)
    {
        $id = (int)$id;
        $results = Sql::select('*', 'virtuemart_countries', "virtuemart_country_id = '$id'");
        return ($results);
    }

}