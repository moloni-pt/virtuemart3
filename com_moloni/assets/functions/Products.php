<?php

namespace Moloni\Functions;

class Products
{
    public static $exists;

    public static function getItemByRef($sku, $item)
    {

        $reference = PRODUCT_PREFIX . $sku;
        if ($reference == '') {
            $reference = PRODUCT_PREFIX . $item->virtuemart_order_item_id;
        }

        $values['company_id'] = COMPANY_ID;
        $values['reference'] = $reference;
        $values['qty'] = '1';
        $values['offset'] = '0';

        $results = Base::cURL('products/getByReference', $values);

        if (count($results) > 0) {
            $itemID = $results[0]['product_id'];
        } else {
            $itemID = self::insertItem($reference, $item);
        }
        return ($itemID);
    }

    public static function insertItem($reference, $item)
    {

        $values['company_id'] = COMPANY_ID;
        $values['category_id'] = self::getItemCategory($item->virtuemart_product_id);
        $values['type'] = '1';
        $values['name'] = $item->order_item_name;
        $values['summary'] = '';
        $values['reference'] = $reference;
        $values['price'] = $item->product_item_price;
        $values['unit_id'] = MEASURE_UNIT;
        $values['has_stock'] = '0';
        $values['stock'] = '0';

        if ((float)$item->product_tax > 0) {
            $values['taxes'] = array();
            $values['taxes'][0]['tax_id'] = self::getTaxByVal(round(($item->product_tax * 100) / $item->product_item_price));
            $values['taxes'][0]['value'] = round(($item->product_tax * 100) / $item->product_item_price);
            $values['taxes'][0]['order'] = '1';
            $values['taxes'][0]['cumulative'] = '0';
        } else {
            $values['exemption_reason'] = EXEMPTION_REASON;
        }

        $results = Base::cURL('products/insert', ($values));

        if (!isset($results['product_id'])) {
            Base::genError('products/insert', $values, $results);
            exit;
        } else {
            return ($results['product_id']);
        }
    }

    public static function getItemCategory($id)
    {
        $results = Sql::select('*', 'virtuemart_product_categories', "virtuemart_product_id = $id");
        $name = '';

        try {
            $results = Sql::select('*', 'virtuemart_categories_en_gb', 'virtuemart_category_id = ' . $results[0]->virtuemart_category_id . '');
            if (!empty($results)) {
                $name = $results[0]->category_name;
            }
        } catch (\Exception $ex) {
            try {
                $results = Sql::select('*', 'virtuemart_categories_pt_pt', 'virtuemart_category_id = ' . $results[0]->virtuemart_category_id . '');

                if (!empty($results)) {
                    $name = $results[0]->category_name;
                }
            } catch (\Exception $ex) {
                $name = '';
            }
        }


        if (empty($name)) {
            $name = 'Sem categoria';
        }

        $resultsCategory = self::getCategoryByName($name);
        if ($resultsCategory) {
            return ($resultsCategory);
        }

        return (self::insertCategory($name));
    }

    public static function getCategoryByName($name, $parentID = 0)
    {
        if ($name == '') {
            $name = 'Sem categoria';
        }

        $categorias = self::getCategoriasAll($parentID);

        if ($categorias) {
            foreach ($categorias as $categoria) {

                if (preg_replace('/[^\w\d ]/', '', (strtolower(htmlspecialchars_decode($categoria['name'])))) == preg_replace('/[^\w\d ]/', '', (htmlspecialchars_decode(strtolower($name))))) {
                    self::$exists = $categoria['category_id'];
                    break;
                }

                if ($categoria['num_categories'] > 0)
                    self::getCategoryByName($name, $categoria['category_id']);
            }
        } else {
            return (false);
        }
        if (empty(self::$exists) or self::$exists == '' or !self::$exists) {
            return (false);
        } else
            return (self::$exists);
    }

    public static function getCategoriasAll($parent = 0)
    {
        $values['company_id'] = COMPANY_ID;
        $values['parent_id'] = $parent;
        $results = Base::cURL('productCategories/getAll', $values);
        return ($results);
    }

    public static function insertCategory($name)
    {
        $values['company_id'] = COMPANY_ID;
        $values['parent_id'] = '0';
        $values['name'] = $name;
        $values['description'] = '';
        $results = Base::cURL('productCategories/insert', $values);
        return ($results['category_id']);
    }

    public static function getTaxByVal($val)
    {
        $taxID = 0;
        $values['company_id'] = COMPANY_ID;
        $results = Base::cURL('taxes/getAll', $values);

        if(!empty($results)) {
            foreach ($results as $tax) {

                if (round($tax['value'], 2) === (round($val, 2))) {
                    $taxID = $tax['tax_id'];
                    break;
                }

                if (round($tax['value']) === (round($val))) {
                    $taxID = $tax['tax_id'];
                }
            }
        }


        if ($taxID === 0) {
            $taxID = self::getTaxByVal(23);
        }

        return $taxID;
    }

    public static function getShipByRef($sku, $fullPrice, $taxPrice)
    {

        $reference = PRODUCT_PREFIX . $sku;

        $values['company_id'] = COMPANY_ID;
        $values['reference'] = $reference;
        $values['qty'] = '1';
        $values['offset'] = '0';

        $results = Base::cURL('products/getByReference', $values);

        if (count($results) > 0) {
            $itemID = $results[0]['product_id'];
        } else {
            $itemID = self::insertShiping($reference, $fullPrice, $taxPrice);
        }
        return ($itemID);
    }

    public static function insertShiping($reference, $fullPrice, $taxPrice)
    {

        $values['company_id'] = COMPANY_ID;

        $resultsCategory = self::getCategoryByName('Portes');
        if ($resultsCategory) {
            $id = $resultsCategory;
        } else {
            $id = self::insertCategory('Portes');
        }

        $values['category_id'] = self::getCategoryByName('Portes');
        $values['type'] = '1';
        $values['name'] = 'Portes';
        $values['summary'] = 'Custo de transporte';
        $values['reference'] = $reference;
        $values['price'] = $fullPrice;
        $values['unit_id'] = MEASURE_UNIT;
        $values['has_stock'] = 1;
        $values['stock'] = '1';
        if ($taxPrice > 0) {
            $values['taxes'] = array();
            //245583
            $values['taxes'][0]['tax_id'] = self::getTaxByVal(($taxPrice * 100) / $fullPrice);
            $values['taxes'][0]['value'] = $taxPrice;
            $values['taxes'][0]['order'] = '1';
            $values['taxes'][0]['cumulative'] = '0';
        } else {
            $values['exemption_reason'] = EXEMPTION_REASON;
        }
        $results = Base::cURL('products/insert', ($values));

        if (!isset($results['product_id'])) {
            Base::genError('products/insert', $values, $results);
            exit;
        } else {
            return ($results['product_id']);
        }
    }

}
