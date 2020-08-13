<?php
defined('_JEXEC') or die('Restricted access');

class start
{

    const version = 1.00;

    public static function login()
    {
        $error = '';

        if (isset($_POST['user']) && isset($_POST['pass']) && trim($_POST['user']) <> '' and trim($_POST['pass']) <> '') {
            $login = base::loginCURL(trim($_POST['user']), trim($_POST['pass']));
            if ($login) {
                moloniDB::setTokens($login['access_token'], $login['refresh_token']);
            } else {
                $error = TRUE;
            }
        }

        $dbInfo = moloniDB::getInfo();
        if (isset($dbInfo->main_token)) {
            define('LOGGED', TRUE);
        } else {
            define('LOGGED', FALSE);
        }

        if (!LOGGED) {
            self::loginForm($error);
            return (FALSE);
        }

        moloniDB::refreshTokens();
        moloniDB::defineValues();
        if (defined('COMPANY_ID')) {
            return (TRUE);
        }

        if (isset($_GET['company_id'])) {
            $update = sql::update('moloni_api', array('id' => SESSION_ID, 'company_id' => $_GET['company_id']), 'id');
            moloniDB::defineValues();
            return (FALSE);
        }

        self::companiesForm();
        return (FALSE);

    }

    public static function loginForm($error = FALSE)
    {
        echo "<div id='formLogin'>";
        echo "<a href='https://moloni.com/dev/' target='_BLANK'> <img src='https://www.moloni.com/_imagens/_tmpl/bo_logo_topo_01.png' width='300px'> </a>
			<hr> <form id='formPerm' method='POST' action=''><table>";
        echo "<tr> <td><label for='username'>Utilizador/Email</label> </td><td><input type='text' name='user'></td></tr>";


        echo "<tr> <td><label for='password'>Password</label></td><td><input type='password' name='pass'></td></tr>";
        if ($error) {
            echo "<tr> <td></td><td style='text-align: center;'> Utilizador/Password Errados</td></tr>";
        }
        echo "<tr> <td></td><td><input type='submit' name='submit' value='login'><input type='reset' name='limpar' value='limpar'> <span class='goRight power'>Powered by: Moloni API</span></td></tr>";
        echo '</table></form></div>';
    }

    public static function companiesForm()
    {
        $companies = base::selectCompanies();
        echo "<div class='outBoxEmpresa'>";
        foreach ($companies as $key => $company) {
            echo '<div class="caixaLoginEmpresa" onclick=" window.location.href=\'index.php?option=com_moloni&company_id=' . $company['company_id'] . '\' " title="Login/Entrar ' . $company['name'] . '">';
            echo '<div class="caixaLoginEmpresa_logo">';
            echo '		<span>';
            if (trim($company['image']) <> '') echo '<img src="https://www.moloni.com/_imagens/?macro=imgAC_iconeEmpresa_s2&amp;img=' . $company['image'] . '" alt="' . $company['name'] . '" style="margin:0 10px 0 0; vertical-align:middle;">';
            echo '		</span>';
            echo '	</div>';
            echo '	<span class="t14_b">' . $company['name'] . '</span>';
            echo '	<br>' . $company['address'] . '';
            echo '	<br>' . $company['zip_code'] . '';
            echo '	<p><b>Contribuinte</b>: ' . $company['vat'] . '</p></div>';
        }
        echo '</div>';
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