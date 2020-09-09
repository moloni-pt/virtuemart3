<?php

namespace Moloni\Functions;

class Base
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
        curl_setopt($con, CURLOPT_POST, false);
        curl_setopt($con, CURLOPT_POSTFIELDS, false);
        curl_setopt($con, CURLOPT_HEADER, false);
        curl_setopt($con, CURLOPT_RETURNTRANSFER, true);

        $res_curl = curl_exec($con);
        curl_close($con);

        $res_txt = json_decode($res_curl, true);
        if (!isset($res_txt['error'])) {
            return ($res_txt);
        }

        self::genError($url, $values, $res_txt);
        return false;
    }

    /**
     * Gera uma mensagem de erro usando o método de adicionar uma mensagem de sessão
     *
     * @param $url string Url do pedido feito a API
     * @param $values array Valores que foram enviados via API para inserção de um documento no Moloni
     * @param $array array Resposta vinda da API
     *
     * @return null
     */
    public static function genError($url, $values, $array)
    {
        Messages::addSessionMessage(
            "<div> 
<div class='msgAlertaForms3'>
<a class='moloniClose' onclick='this.parentNode.style.display = \"none\"'>&#10005;</a>
<p align='left'><b>Foi encontrado um erro!</b></p> <p align='left'>Url: <a style='color: black'>$url</a></p>
<a onclick='showMoloniErrors();' style='cursor: pointer'><p align='left'>Clique para obter mais informações!</p></a>
<div class='msgAlertaForms3' style='display: none' id='showMoloniConsoleLogError'>
<p align='left'><b>Valores a serem enviados:</b></p> 
<pre class='preForm' >" . print_r($values, true) . "</pre>
<p align='left'><b>Resposta recebida:</b></p> 
<pre class='preForm' >" . print_r($array, true) . "</pre>
</div>
</div>
</div>"
        );

        return null;
    }

    public static function refreshCURL($refresh)
    {

        $con = curl_init();
        $url = "https://api.moloni.pt/v1/grant/?grant_type=refresh_token&client_id=nunong21&client_secret=68bb9e790079a624200cf342d53e2f575f597c2b&refresh_token=$refresh";
        curl_setopt($con, CURLOPT_URL, $url);
        curl_setopt($con, CURLOPT_POST, false);
        curl_setopt($con, CURLOPT_POSTFIELDS, false);
        curl_setopt($con, CURLOPT_HEADER, false);
        curl_setopt($con, CURLOPT_RETURNTRANSFER, true);

        $res_curl = curl_exec($con);
        curl_close($con);

        // analise do resultado
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
        return (true);
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

    public static function cURL($action, $values = [])
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
        return false;
    }
}
