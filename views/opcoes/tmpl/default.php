<?php
/**
 * @package Moloni
 * @author Nuno Almeida
 * @website https://www.moloni.com
 * @email nuno@datasource.pt
 * @copyright Moloni
 * @license
 * */
defined('_JEXEC') or die('Restricted access');

moloniDB::defineValues();
$message = '';
if (isset($_POST['action']) && $_POST['action'] == 'registarAlteracoes') {
    $message = "<div class='msgAlertaForms' style='width: 200px; height: 50px; float:right;'>Dados guardados</div>";
    foreach ($_POST['opt'] as $key => $val) {
        $update = sql::update('moloni_api_config', array('config' => $key, 'selected' => $val), 'config');
    }
}
$moloniConfigs = sql::select('*', 'moloni_api_config');

echo "<form method='POST' action='' id='formOpcoes'>";
echo "<div class='tituloTab'>Configurações $message</div><ul class='listform'>";
foreach ($moloniConfigs as $config) {

    switch ($config->config) {
        case 'document_set_id':
            if (defined('COMPANY_ID')) {
                echo '<li><label>Série de documento:</label>';
                $docSets = moloniBasics::documentSets();
                echo "<select name='opt[document_set_id]' id='docSet' class='inputOut'>";
                foreach ($docSets as $docSet) {
                    if ($config->selected == $docSet['document_set_id']) {
                        $selected = 'selected';
                    } else {
                        $selected = '';
                    }
                    echo "<option value='$docSet[document_set_id]' $selected>$docSet[name]</option>";
                }
                echo '</select></li>';
            };
            break;


        case 'exemption_reason':
            echo '<li><label>Razão de Isenção:</label>';
            echo "<select name='opt[exemption_reason]' id='razaoIsencao' class='inputOut'>";
            echo "<option value=''>Nenhuma</option>";
            $exemptionReasons = moloniBasics::exemptionReasons();
            foreach ($exemptionReasons as $exemReas) {
                if ($config->selected == $exemReas['code']) {
                    $selected = 'selected';
                } else {
                    $selected = '';
                }
                echo "<option value='$exemReas[code]' $selected>$exemReas[name]</option>";
            }
            echo '</select></li>';
            break;

        case 'maturity_date':
            echo '<li><label>Prazo de Pagamento:</label>';
            echo "<select name='opt[maturity_date]' id='prazoVencimento' class='inputOut'>";
            $maturityDates = moloniBasics::maturityDates();
            foreach ($maturityDates as $maturity) {
                if ($config->selected == $maturity['maturity_date_id']) {
                    $selected = 'selected';
                } else {
                    $selected = '';
                }
                echo "<option value='$maturity[maturity_date_id]' $selected>$maturity[name]</option>";
            }
            echo '</select></li>';
            break;

        case 'payment_method':
            if (defined('COMPANY_ID')) {
                echo '<li><label>Método de pagamento:</label>';
                $payMethds = moloniBasics::paymentMethods();
                echo "<select name='opt[payment_method]' id='paymentMethod' class='inputOut'>";
                foreach ($payMethds as $payMethd) {
                    if ($config->selected == $payMethd['payment_method_id']) {
                        $selected = 'selected';
                    } else {
                        $selected = '';
                    }
                    echo "<option value='$payMethd[payment_method_id]' $selected>$payMethd[name]</option>";
                }
                echo '</select></li>';
            }
            break;

        case 'measure_unit':
            if (defined('COMPANY_ID')) {
                echo '<li><label>Unidade de Medida:</label>';
                $measureUnits = moloniBasics::measurementUnits();
                echo "<select name='opt[measure_unit]' id='measureUnit' class='inputOut'>";
                foreach ($measureUnits as $measureUnit) {
                    if ($config->selected == $measureUnit['unit_id']) {
                        $selected = 'selected';
                    } else {
                        $selected = '';
                    }
                    echo "<option value='$measureUnit[unit_id]' $selected>$measureUnit[name]</option>";
                }
                echo '</select></li>';
            };
            break;

        case 'document_status':
            echo '<li><label>Estado do documento:</label>';
            echo "<select name='opt[document_status]' class='inputOut'>";
            if ($config->selected == '0')
                $selected = 'selected';
            else
                $selected = '';
            echo "<option value='0' $selected>Rascunho</option>";
            if ($config->selected == '1')
                $selected = 'selected';
            else
                $selected = '';
            echo "<option value='1' $selected>Fechado</option>";
            echo '</select></li>';
            break;

        case 'document_type':
            echo '<li><label>Tipo de documento:</label>';
            echo "<select name='opt[document_type]' class='inputOut'>";
            if ($config->selected == 'invoices')
                $selected = 'selected';
            else
                $selected = '';
            echo "<option value='invoices' $selected>Factura</option>";

            if ($config->selected == 'invoiceReceipts')
                $selected = 'selected';
            else
                $selected = '';
            echo "<option value='invoiceReceipts' $selected>Factura/Recibo</option>";
            echo '</select></li>';
            break;

        case 'client_prefix':
            echo '<li><label>Ref. Clientes:</label>';
            echo "<input type='text' name='opt[client_prefix]' value='" . $config->selected . "' class='inputOut'>";
            break;

        case 'product_prefix':
            echo '<li><label>Ref. Artigos:</label>';
            echo "<input type='text' name='opt[product_prefix]' value='" . $config->selected . "' class='inputOut'>";
            break;

        case 'vat_field':
            echo '<li><label>Contribuinte</label>';
            $customFields = vmBasics::customFields();
            echo "<select name='opt[vat_field]' id='field' class='inputOut'>";
            foreach ($customFields as $field) {
                if ($config->selected == $field->name) {
                    $selected = 'selected';
                } else {
                    $selected = '';
                }
                echo "<option value='" . $field->name . "' $selected>";
                echo JText::_($field->title);
                echo '</option>';
            }
            echo '</select></li>';
            break;
    }
}
echo '</ul><br><hr><br>';

echo "<a href='#' class='actionButton' onclick='document.getElementById(\"formOpcoes\").submit();'>Guardar Alterações</a>";
echo "<input type='hidden' value='registarAlteracoes' name='action'>";
echo '</form>';

?>
<script>
    jQuery('.msgAlertaForms').delay(3000).fadeOut('slow');
</script>