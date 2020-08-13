<?php
defined('_JEXEC') or die('Restricted access');

class base
{
    public static function testCURL()
    {
        $con = curl_init();
        $url = 'https://api.moloni.pt/v1/products/getOne/?access_token=FAKETOKEN';   /* Substituir pelo token atual */

        curl_setopt($con, CURLOPT_URL, $url);
        curl_setopt($con, CURLOPT_POST, true);
        curl_setopt($con, CURLOPT_POSTFIELDS, http_build_query([]));
        curl_setopt($con, CURLOPT_HEADER, false);
        curl_setopt($con, CURLOPT_RETURNTRANSFER, true);

        $res_curl = curl_exec($con);
        curl_close($con);

        $res_txt = json_decode($res_curl, true);
        if (isset($res_txt['error'])) {
            return true;
        }

        return true;
    }

    public static function loginCURL($user, $pass)
    {
        $values = '';
        $con = curl_init();
        $url = "https://api.moloni.pt/v1/grant/?grant_type=password&client_id=nunong21&client_secret=68bb9e790079a624200cf342d53e2f575f597c2b&username=$user&password=$pass";
        curl_setopt($con, CURLOPT_URL, $url);
        curl_setopt($con, CURLOPT_POST, FALSE);
        curl_setopt($con, CURLOPT_POSTFIELDS, FALSE);
        curl_setopt($con, CURLOPT_HEADER, false);
        curl_setopt($con, CURLOPT_RETURNTRANSFER, true);

        $res_curl = curl_exec($con);
        curl_close($con);

        // an�lise do resultado
        $res_txt = json_decode($res_curl, true);
        if (!isset($res_txt['error'])) {
            return ($res_txt);
        }

        self::genError($url, $values, $res_txt);
        return false;
    }

    public static function genError($url, $values, $array)
    {
        echo "<br><b>Foi encontrado um erro!</b> <br> <b>Url: </b>$url <br>";
        if ($values > 0) {
            echo 'Valores a serem enviados:<br> <pre>';
            print_r($values);
            echo '</pre><br> ';
        }
        echo '<b>Resposta recebida</b>: <br> <pre>';
        print_r($array);
        echo '</pre>';
    }

    public static function refreshCURL($refresh)
    {

        $con = curl_init();
        $url = "https://api.moloni.pt/v1/grant/?grant_type=refresh_token&client_id=nunong21&client_secret=68bb9e790079a624200cf342d53e2f575f597c2b&refresh_token=$refresh";
        curl_setopt($con, CURLOPT_URL, $url);
        curl_setopt($con, CURLOPT_POST, FALSE);
        curl_setopt($con, CURLOPT_POSTFIELDS, FALSE);
        curl_setopt($con, CURLOPT_HEADER, false);
        curl_setopt($con, CURLOPT_RETURNTRANSFER, true);

        $res_curl = curl_exec($con);
        curl_close($con);

        // an�lise do resultado
        $res_txt = json_decode($res_curl, true);
        if (!isset($res_txt['error'])) {
            return ($res_txt);
        }

        self::genError($url, [], $res_txt);
        return (false);
    }

    public static function triggerFatalError($message)
    {
        trigger_error($message, E_USER_ERROR);
    }

    public static function triggerError($message)
    {
        echo $message;
        return (TRUE);
    }

    public static function selectCompanies()
    {
        $companies = [];
        $results = self::cURL('companies/getAll');

        foreach ($results as $company) {
            if ($company['company_id'] !== 5) {
                $companies[] = $company;
            }
        }

        return ($companies);
    }

    public static function cURL($action, $values = false)
    {
        $con = curl_init();
        $url = 'https://api.moloni.pt/v1/' . $action . '/?access_token=' . ACCESS_TOKEN;
        curl_setopt($con, CURLOPT_URL, $url);
        curl_setopt($con, CURLOPT_POST, true);
        curl_setopt($con, CURLOPT_POSTFIELDS, http_build_query($values));
        curl_setopt($con, CURLOPT_HEADER, false);
        curl_setopt($con, CURLOPT_RETURNTRANSFER, true);

        $res_curl = curl_exec($con);
        curl_close($con);

        // análise do resultado
        $res_txt = json_decode($res_curl, true);
        if (!isset($res_txt['error'])) {
            return ($res_txt);
        }

        self::genError($url, $values, $res_txt);
        return (FALSE);
    }

}

class moloniDB
{
    public static function setTokens($access_token, $refresh_token)
    {
        sql::insert('moloni_api', array('main_token' => $access_token, 'refresh_token' => $refresh_token));
    }

    public static function refreshTokens()
    {
        $dbInfo = self::getInfo();
        $results = base::refreshCURL($dbInfo->refresh_token);

        if (!$results) {
            sql::delete('moloni_api', 'id', $dbInfo->id);
            return false;
        }

        sql::update('moloni_api', array('id' => $dbInfo->id, 'main_token' => $results['access_token'], 'refresh_token' => $results['refresh_token']), 'id');
        return true;
    }

    public static function getInfo()
    {
        $results = sql::select('*', 'moloni_api');
        //return($results[0]);
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
        $results = sql::select('*', 'moloni_api_config');
        foreach ($results as $config) {
            define(strtoupper($config->config), $config->selected);
        }
    }

}
