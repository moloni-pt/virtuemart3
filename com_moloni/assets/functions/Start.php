<?php

namespace Moloni\Functions;

class Start
{
    const version = 1.00;

    public static function login()
    {
        if (isset($_POST['user'], $_POST['pass']) && trim($_POST['user']) !== '' && trim($_POST['pass']) !== '') {
            $login = Base::loginCURL(trim($_POST['user']), trim($_POST['pass']));
            if ($login) {
                MoloniDb::setTokens($login['access_token'], $login['refresh_token']);
            } else {
                Messages::$messages['login'] = "Utilizador/Password Errados";

                return false;
            }
        }

        $dbInfo = MoloniDb::getInfo();

        if (isset($dbInfo->main_token)) {
            define('LOGGED', true);
        } else {
            define('LOGGED', false);
            return false;
        }

        MoloniDb::refreshTokens();
        MoloniDb::defineValues();

        if (defined('COMPANY_ID')) {
            return true;
        }

        if (isset($_GET['company_id'])) {
            Sql::update('moloni_api', array('id' => SESSION_ID, 'company_id' => $_GET['company_id']), 'id');
            MoloniDb::defineValues();
        }

        return true;
    }

    public static function update()
    {

        $con = curl_init();
        $url = 'http://plugins.moloni.com/virtuemart2/vm2/update.xml';
        curl_setopt($con, CURLOPT_URL, $url);
        curl_setopt($con, CURLOPT_HEADER, false);
        curl_setopt($con, CURLOPT_RETURNTRANSFER, true);

        $xmlStr = curl_exec($con);
        $xmlObj = simplexml_load_string($xmlStr);
        $arrXml = self::objectsIntoArray($xmlObj);

        if (self::version == $arrXml['currentVersion']) {
            echo "<a href='index.php?option=com_moloni&action=update'>
					<div style='margin-top: 10px; font-size: 14px; color: black; float: right; margin-right: 14px; font-family: Arial, Helvetica, sans-serif;'> Re-instalar actualização</div></a>";
        } else {
            echo "<a href='index.php?option=com_moloni&action=update'>
					<div style='margin-top: 10px; font-size: 18px; color: red; float: right; margin-right: 14px; font-family: Arial, Helvetica, sans-serif;'> Actualização disponível</div></a>";
        }
    }

    public static function objectsIntoArray($arrObjData, $arrSkipIndices = array())
    {
        $arrData = array();

        // if input is object, convert into array
        if (is_object($arrObjData)) {
            $arrObjData = get_object_vars($arrObjData);
        }

        if (is_array($arrObjData)) {
            foreach ($arrObjData as $index => $value) {
                if (is_object($value) || is_array($value)) {
                    $value = self::objectsIntoArray($value, $arrSkipIndices); // recursive call
                }
                if (in_array($index, $arrSkipIndices)) {
                    continue;
                }
                $arrData[$index] = $value;
            }
        }
        return $arrData;
    }

    public static function forceUpdate()
    {
        $con = curl_init();
        $url = 'http://plugins.moloni.com/virtuemart2/vm2/update.xml';
        curl_setopt($con, CURLOPT_URL, $url);
        curl_setopt($con, CURLOPT_HEADER, false);
        curl_setopt($con, CURLOPT_RETURNTRANSFER, true);

        $xmlStr = curl_exec($con);
        $xmlObj = simplexml_load_string($xmlStr);
        $arrXml = self::objectsIntoArray($xmlObj);

        chmod(COMPONENT_PATH . '', 0777);

        echo '<pre>';
        foreach ($arrXml['files']['file'] as $key => $file) {
            self::getText($file);
        }
        echo '<br>Actualize a página!';
        echo '<div>';
        echo $arrXml['changes'];
        echo '</div>';

        chmod(COMPONENT_PATH . '', 0755);
    }

    public static function getText($file)
    {

        $con = curl_init();
        $url = 'plugins.moloni.com/virtuemart2/vm2/' . $file;
        curl_setopt($con, CURLOPT_URL, $url);
        curl_setopt($con, CURLOPT_HEADER, false);
        curl_setopt($con, CURLOPT_RETURNTRANSFER, true);

        $text = curl_exec($con);

        $fileN = str_replace('.txt', '', $file);
        if (trim($text) == '') {
            echo 'Erro ao actualizar no ficheiro ' . ($fileN) . '!<br>';
        } else {
            $handle = fopen(COMPONENT_PATH . '/' . ($fileN), 'w');
            fwrite($handle, ($text));
            echo 'Ficheiro bem actualizado ' . ($fileN) . '!<br>';
        }

        curl_close($con);
    }
}
