<?php

namespace Moloni\Functions;

class MoloniDb
{
    public static $settings = [];

    public static function setTokens($access_token, $refresh_token)
    {
        Sql::insert('moloni_api', array('main_token' => $access_token, 'refresh_token' => $refresh_token));
    }

    public static function refreshTokens()
    {
        $dbInfo = self::getInfo();
        $results = Base::refreshCURL($dbInfo->refresh_token);

        if (!$results) {
            Sql::delete('moloni_api', 'id', $dbInfo->id);
            return false;
        }

        Sql::update('moloni_api', array(
            'id' => $dbInfo->id,
            'main_token' => $results['access_token'],
            'refresh_token' => $results['refresh_token']),
            'id'
        );

        return true;
    }

    public static function getInfo()
    {
        $results = Sql::select('*', 'moloni_api');
        return ((isset($results[0]) ? $results[0] : false));
    }

    public static function defineValues()
    {
        $results = self::getInfo();
        if (isset($results->id)) {
            define('SESSION_ID', $results->id);
        }

        if (isset($results->main_token)) {
            define('ACCESS_TOKEN', trim($results->main_token));
        }

        if (isset($results->refresh_token)) {
            define('REFRESH_TOKEN', $results->refresh_token);
        }

        if (isset($results->company_id)) {
            define('COMPANY_ID', $results->company_id);
        }
    }

    public static function defineConfigs()
    {
        $results = Sql::select('*', 'moloni_api_config');
        foreach ($results as $config) {
            if (defined(strtoupper($config->config))) {
                continue;
            }

            define(strtoupper($config->config), $config->selected);

            self::$settings[$config->config] = $config->selected;
        }
    }
}