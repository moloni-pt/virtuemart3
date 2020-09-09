<?php

namespace Moloni\Functions;

class Sql
{
    public static function select($fields, $table, $where = false, $order = false, $limit = false)
    {
        try {
            $db = \JFactory::getDBO();
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
        } catch (\Exception $e) {
            throw new \Exception('Ups, algo correu mal :(', 0, $e);
        }
    }

    public static function insert($table, $values)
    {
        try {
            $insertValues = new \stdClass();

            foreach ($values as $key => $val) {
                $insertValues->$key = $val;
            }

            $result = \JFactory::getDbo()->insertObject('#__' . $table, $insertValues);
            return ($result);
        } catch (\Exception $e) {
            throw new \Exception('Ups, algo correu mal :(', 0, $e);
        }
    }

    public static function update($table, $values, $id)
    {
        try {
            $insertValues = new \stdClass();
            foreach ($values as $key => $val) {
                $insertValues->$key = $val;
            }

            $result = \JFactory::getDbo()->updateObject('#__' . $table, $insertValues, $id);

            return ($result);
        } catch (\Exception $e) {
            throw new \Exception('Ups, algo correu mal :(', 0, $e);
        }
    }

    public static function delete($table, $field, $id)
    {
        try {
            $db = \JFactory::getDbo();
            $query = $db->getQuery(true);
            $conditions = array(
                $db->quoteName($field) . '=' . $id
            );
            $query->delete($db->quoteName('#__' . $table));
            $query->where($conditions);
            $db->setQuery($query);
            $result = $db->query();
        } catch (\Exception $e) {
            throw new \Exception('Ups, algo correu mal :(', 0, $e);
        }
    }

    /**
     * Remove o ID da db 'moloni_api'
     *
     * @throws \Exception
     */
    public static function cleanMoloniDatabases()
    {
        foreach (self::select('*', 'moloni_api') as $item) {
            self::delete('moloni_api', 'id', $item->id);
        }

    }
}