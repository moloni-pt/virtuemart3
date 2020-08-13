<?php
defined('_JEXEC') or die('Restricted access');

class sql
{
    public static function select($fields, $table, $where = false, $order = false, $limit = false)
    {
        try {
            $db = JFactory::getDBO();
            $query = $db->getQuery(true);
            $query->select($fields);
            $query->from('#__' . $table);

            if ($where) {
                $query->where($where);
            }

            if ($order) {
                $query->order($order);
            }

            if ($limit) {
                $query->limit($limit);
            }

            $db->setQuery($query);
            $results = $db->loadObjectList();
            return ($results);
        } catch (Exception $e) {
            throw new Exception('Ups, algo correu mal :(', 0, $e);
        }
    }

    public static function insert($table, $values)
    {
        try {
            $insertValues = new stdClass();
            foreach ($values as $key => $val) {
                $insertValues->$key = $val;
            }
            $result = JFactory::getDbo()->insertObject('#__' . $table, $insertValues);
            return ($result);
        } catch (Exception $e) {
            throw new Exception('Ups, algo correu mal :(', 0, $e);
        }
    }

    public static function update($table, $values, $id)
    {
        try {
            $insertValues = new stdClass();
            foreach ($values as $key => $val) {
                $insertValues->$key = $val;
            }
            $result = JFactory::getDbo()->updateObject('#__' . $table, $insertValues, $id);
            return ($result);
        } catch (Exception $e) {
            throw new Exception('Ups, algo correu mal :(', 0, $e);
        }
    }

    public static function delete($table, $field, $id)
    {
        try {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $conditions = array(
                $db->quoteName($field) . '=' . $id
            );
            $query->delete($db->quoteName('#__' . $table));
            $query->where($conditions);
            $db->setQuery($query);
            $result = $db->query();
        } catch (Exception $e) {
            throw new Exception('Ups, algo correu mal :(', 0, $e);
        }
    }

}

class moloniBasics
{
    public static function documentSets()
    {
        $values = array('company_id' => COMPANY_ID);
        $results = base::cURL('documentSets/getAll', $values);
        return ($results);
    }

    public static function paymentMethods()
    {
        $values = array('company_id' => COMPANY_ID);
        $results = base::cURL('paymentMethods/getAll', $values);
        return ($results);
    }

    public static function measurementUnits()
    {
        $values = array('company_id' => COMPANY_ID);
        $results = base::cURL('measurementUnits/getAll', $values);
        return ($results);
    }

    public static function exemptionReasons()
    {
        $results = base::cURL('taxExemptions/getAll');
        return ($results);
    }

    public static function maturityDates()
    {
        $values['company_id'] = COMPANY_ID;
        $results = base::cURL('maturityDates/getAll', $values);
        return ($results);
    }

    public static function countries()
    {
        $results = base::cURL('countries/getAll');
        return ($results);
    }

    public static function languages()
    {
        $results = base::cURL('languages/getAll');
        return ($results);
    }

}

class vmBasics
{
    public static function customFields()
    {
        $results = sql::select('*', 'virtuemart_userfields');
        return ($results);
    }

    public static function getAllOrders()
    {
        $results = sql::select('*', 'virtuemart_orders', 'moloni_sent < 1', 'virtuemart_order_id ASC');
        return ($results);
    }

    public static function getOneOrder($id)
    {
        $results = sql::select('*', 'virtuemart_orders', "moloni_sent < 1 AND virtuemart_order_id = '$id'", 'virtuemart_order_id ASC');
        return ($results);
    }

    public static function getAllItemsByOrder($id)
    {
        $id = (int)$id;
        $results = sql::select('*', 'virtuemart_order_items', "virtuemart_order_id = '$id'");
        return ($results);
    }

    public static function getOneClientByOrder($orderID)
    {
        $orderID = (int)$orderID;
        $results = sql::select('*', 'virtuemart_order_userinfos', "virtuemart_order_id = '$orderID'");
        return ($results);
    }

    public static function getOneCountryByID($id)
    {
        $id = (int)$id;
        $results = sql::select('*', 'virtuemart_countries', "virtuemart_country_id = '$id'");
        return ($results);
    }

}

class general
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
        $resultsMoloni = moloniBasics::countries();
        $resultsVM = vmBasics::getOneCountryByID($id);
        foreach ($resultsMoloni as $result) {
            if (strtoupper($result['iso_3166_1']) == $resultsVM[0]->country_2_code) {
                return ($result['country_id']);
            }
        }
        return ('1');
    }

    public static function getLanguageID($id)
    {
        $resultsMoloni = moloniBasics::languages();
        $resultsVM = vmBasics::getOneCountryByID($id);
        foreach ($resultsMoloni as $result) {
            if (strtoupper($result['code']) == $resultsVM[0]->country_2_code) {
                return ($result['language_id']);
            }
        }
        return ('1');
    }

    public static function listAllOrder()
    {
        $orders = vmBasics::getAllOrders();
        echo "<table class='listOrders tblMoloni'>";
        echo '<thead>';
        echo '<th>ID</th>';
        echo '<th>REF</th>';
        echo '<th>Cliente</th>';
        echo '<th>Estado</th>';
        echo '<th>Total</th>';
        echo '<th>Taxa</th>';
        echo '<th>Data de Entrega</th>';
        echo '<th>Acção</th>';
        echo '</thead>';

        foreach ($orders as $order) {
            $client = vmBasics::getOneClientByOrder($order->virtuemart_order_id);
            //NH - Manter links na mm pagina - target _self
            echo '<tr>';
            echo '<td>' . $order->virtuemart_order_id . '</td>';
            echo "<td><a href='index.php?option=com_virtuemart&view=orders&task=edit&virtuemart_order_id=$order->virtuemart_order_id' target='_self'>$order->order_number</a></td>";
            echo "<td><a href='index.php?option=com_virtuemart&view=user&task=edit&virtuemart_user_id[]=$order->virtuemart_user_id' target='_self'>" . $client[0]->first_name . ' ' . $client[0]->last_name . '</a></td>';
            echo '<td>' . ($order->order_status) . '</td>';
            echo '<td>' . round($order->order_total, 2) . ' €</td>';
            echo '<td>' . round($order->order_tax, 2) . ' €</td>';
            echo '<td>' . $order->delivery_date . ' </td>';
            echo "<td><a class='btn' href='index.php?option=com_moloni&action=makeInvoice&id=" . $order->virtuemart_order_id . "'><span class='icon-save'></span>Gerar</a>";
            echo " | <a class='btn' href='index.php?option=com_moloni&action=removeOrder&id=" . $order->virtuemart_order_id . "'><span class='icon-cancel'></span>Descartar</a></td>";
            echo '</tr>';
        }
    }

    public static function markOrder($id, $value = 1)
    {
        sql::update('virtuemart_orders', array('virtuemart_order_id' => $id, 'moloni_sent' => $value), 'virtuemart_order_id');
    }

}
