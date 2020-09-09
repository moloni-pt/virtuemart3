<?php

use Moloni\Functions\MoloniDb;
use Moloni\Functions\Sql;
use Moloni\Functions\MoloniSettings;
use Moloni\Functions\Virtuemart;

defined('_JEXEC') or die('Restricted access');

MoloniDb::defineValues();
$message = '';
if (isset($_POST['action']) && $_POST['action'] == 'registarAlteracoes') {
    $message = "<div class='msgAlertaForms' style='width: 200px; height: 50px; float:right;'>Dados guardados</div>";
    foreach ($_POST['opt'] as $key => $val) {
        $update = Sql::update('moloni_api_config', array('config' => $key, 'selected' => $val), 'config');
    }
}
$moloniConfigs = Sql::select('*', 'moloni_api_config');

echo "<form method='POST' action='' id='formOpcoes'>";
echo "<div class='tituloTab'>Configurações $message</div><ul class='listform'>";

if (defined('COMPANY_ID')) {

    //Série de Documento
    echo '<li><label>Série de documento:</label>';
    $docSets = MoloniSettings::documentSets();
    echo "<select name='opt[document_set_id]' id='docSet' class='inputOut'>";
    foreach ($docSets as $docSet) {
        if ($docSet['document_set_id']) {
            $selected = 'selected';
        } else {
            $selected = '';
        }

        echo "<option value='$docSet[document_set_id]' $selected>$docSet[name]</option>";
    }

    echo '</select></li>';

    //Razão de Isenção
    echo '<li><label>Razão de Isenção:</label>';
    echo "<select name='opt[exemption_reason]' id='razaoIsencao' class='inputOut'>";
    echo "<option value=''>Nenhuma</option>";
    $exemptionReasons = MoloniSettings::exemptionReasons();
    foreach ($exemptionReasons as $exemReas) {
        if ($exemReas['code']) {
            $selected = 'selected';
        } else {
            $selected = '';
        }

        echo "<option value='$exemReas[code]' $selected>$exemReas[name]</option>";
    }

    echo '</select></li>';


    //Prazo de Pagamento
    echo '<li><label>Prazo de Pagamento:</label>';
    echo "<select name='opt[maturity_date]' id='prazoVencimento' class='inputOut'>";
    $maturityDates = MoloniSettings::maturityDates();
    foreach ($maturityDates as $maturity) {
        if ($maturity['maturity_date_id']) {
            $selected = 'selected';
        } else {
            $selected = '';
        }

        echo "<option value='$maturity[maturity_date_id]' $selected>$maturity[name]</option>";
    }

    echo '</select></li>';


    //Método de Pagamento
    echo '<li><label>Método de pagamento:</label>';
    $payMethds = MoloniSettings::paymentMethods();
    echo "<select name='opt[payment_method]' id='paymentMethod' class='inputOut'>";
    foreach ($payMethds as $payMethd) {
        if ($payMethd['payment_method_id']) {
            $selected = 'selected';
        } else {
            $selected = '';
        }

        echo "<option value='$payMethd[payment_method_id]' $selected>$payMethd[name]</option>";
    }

    echo '</select></li>';

    //Unidade de Medida
    echo '<li><label>Unidade de Medida:</label>';
    $measureUnits = MoloniSettings::measurementUnits();
    echo "<select name='opt[measure_unit]' id='measureUnit' class='inputOut'>";
    foreach ($measureUnits as $measureUnit) {
        if ($measureUnit['unit_id']) {
            $selected = 'selected';
        } else {
            $selected = '';
        }

        echo "<option value='$measureUnit[unit_id]' $selected>$measureUnit[name]</option>";
    }

    echo '</select></li>';

    //Estado do Documento
    echo '<li><label>Estado do documento:</label>';
    echo "<select name='opt[document_status]' class='inputOut'>";
    if ('document_status' == '0') {
        $selected = 'selected';
    } else {
        $selected = '';
    }

    echo "<option value='0' $selected>Rascunho</option>";

    if ('document_status' == '1') {
        $selected = 'selected';
    } else {
        $selected = '';
    }

    echo "<option value='1' $selected>Fechado</option>";
    echo '</select></li>';

    //Tipo de Documento
    echo '<li><label>Tipo de documento:</label>';
    echo "<select name='opt[document_type]' class='inputOut'>";
    if ('invoices') {
        $selected = 'selected';
    } else {
        $selected = '';
    }

    echo "<option value='invoices' $selected>Fatura</option>";

    if ('invoiceReceipts') {
        $selected = 'selected';
    } else {
        $selected = '';
    }

    echo "<option value='invoiceReceipts' $selected>Fatura/Recibo</option>";
    echo '</select></li>';

    //Ref. Cliente
    echo '<li><label>Ref. Clientes:</label>';
    echo "<input type='text' name='opt[client_prefix]' value='" . $selected . "' class='inputOut'>";

    //Ref. Artigos
    echo '<li><label>Ref. Artigos:</label>';
    echo "<input type='text' name='opt[product_prefix]' value='" . $selected . "' class='inputOut'>";

    //Contribuinte
    echo '<li><label>Contribuinte:</label>';
    $customFields = Virtuemart::customFields();
    echo "<select name='opt[vat_field]' id='field' class='inputOut'>";
    foreach ($customFields as $field) {
        if ($field->name) {
            $selected = 'selected';
        } else {
            $selected = '';
        }

        echo "<option value='" . $field->name . "' $selected>";
        echo JText::_($field->title);
        echo '</option>';
    }

    echo '</select></li>';
    echo '</ul><br><hr><br>';

    echo "<a href='#' class='actionButton' onclick='document.getElementById(\"formOpcoes\").submit();'>Guardar Alterações</a>";
    echo "<input type='hidden' value='registarAlteracoes' name='action'>";
    echo '</form>';
}

?>

<script>
    jQuery('.msgAlertaForms').delay(3000).fadeOut('slow');
</script>