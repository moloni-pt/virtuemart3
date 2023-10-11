<?php

use Moloni\Functions\Messages;
use Moloni\Functions\MoloniDb;
use Moloni\Functions\MoloniSettings;
use Moloni\Functions\Virtuemart;

defined('_JEXEC') or die('Restricted access');

MoloniDb::defineConfigs();

Messages::printMessages();
?>

<form method='POST' action='' id='formOpcoes'>
    <?php if (defined('COMPANY_ID')) : ?>
        <ul class="listform">
            <li>
                <label for="docSet">Série de documento:</label>
                <select name='opt[document_set_id]' id='docSet' class='inputOut'>
                    <?php $docSets = MoloniSettings::documentSets(); ?>
                    <?php foreach ($docSets as $docSet) : ?>
                        <?php $selected = ((int)$docSet['document_set_id'] === (int)MoloniDb::$settings['document_set_id']); ?>
                        <option value='<?php echo $docSet['document_set_id'] ?>' <?php echo $selected ? 'selected' : '' ?>>
                            <?php echo $docSet['name'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </li>


            <li>
                <label for="razaoIsencao">Razão de Isenção:</label>
                <select name='opt[exemption_reason]' id='razaoIsencao' class='inputOut'>
                    <?php $exemptionReasons = MoloniSettings::exemptionReasons(); ?>
                    <?php foreach ($exemptionReasons as $exemReas) : ?>
                        <?php $selected = ($exemReas['code'] === MoloniDb::$settings['exemption_reason']); ?>
                        <option value='<?php echo $exemReas['code'] ?>' <?php echo $selected ? 'selected' : '' ?>>
                            <?php echo $exemReas['name'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </li>


            <li>
                <label for="prazoVencimento">Prazo de Pagamento:</label>
                <select name='opt[maturity_date]' id='prazoVencimento' class='inputOut'>
                    <?php $maturityDates = MoloniSettings::maturityDates(); ?>
                    <?php foreach ($maturityDates as $maturity) : ?>
                        <?php $selected = ((int)$maturity['maturity_date_id'] === (int)MoloniDb::$settings['maturity_date']); ?>
                        <option value='<?php echo $maturity['maturity_date_id'] ?>' <?php echo $selected ? 'selected' : '' ?>>
                            <?php echo $maturity['name'] ?>
                        </option>
                    <?php endforeach; ?>

                </select>
            </li>

            <li>
                <label for="paymentMethod">Método de pagamento:</label>
                <select name='opt[payment_method]' id='paymentMethod' class='inputOut'>
                    <?php $payMethds = MoloniSettings::paymentMethods(); ?>
                    <?php foreach ($payMethds as $payMethd): ?>
                        <?php $selected = ($payMethd['payment_method_id'] === (int)MoloniDb::$settings['payment_method']); ?>
                        <option value='<?php echo $payMethd['payment_method_id'] ?>' <?php echo $selected ? 'selected' : '' ?>>
                            <?php echo $payMethd['name'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </li>

            <li>
                <label for="measureUnit">Unidade de Medida:</label>
                <select name='opt[measure_unit]' id='measureUnit' class='inputOut'>
                    <?php $measureUnits = MoloniSettings::measurementUnits(); ?>
                    <?php foreach ($measureUnits as $measureUnit): ?>
                        <?php $selected = ($measureUnit['unit_id'] === (int)MoloniDb::$settings['measure_unit']); ?>
                        <option value='<?php echo $measureUnit['unit_id'] ?>' <?php echo $selected ? 'selected' : '' ?>>
                            <?php echo $measureUnit['name'] ?>
                        </option>
                    <?php endforeach; ?>

                </select>
            </li>

            <li>
                <label>Estado do documento:</label>
                <select name='opt[document_status]' class='inputOut'>
                    <?php $selected = (int)MoloniDb::$settings['document_status']; ?>
                    <option value="0" <?php echo $selected === 0 ? 'selected' : '' ?>>Rascunho</option>
                    <option value="1" <?php echo $selected === 1 ? 'selected' : '' ?>>Fechado</option>
                </select>
            </li>

            <li>
                <label>Tipo de documento:</label>
                <select name='opt[document_type]' class='inputOut'>";
                    <?php $selected = MoloniDb::$settings['document_type']; ?>
                    <option value="invoices"
                        <?php echo $selected === 'invoices' ? 'selected' : '' ?>>Fatura
                    </option>
                    <option value="invoiceReceipts"
                        <?php echo $selected === 'invoiceReceipts' ? 'selected' : '' ?>>Fatura/Recibo
                    </option>
                    <option value="simplifiedInvoices"
                        <?php echo $selected === 'simplifiedInvoices' ? 'selected' : '' ?>>Fatura Simplificada
                    </option>
                </select>
            </li>

            <li>
                <label for="field">Contribuinte:</label>
                <select name='opt[vat_field]' id='field' class='inputOut'>
                    <?php $customFields = Virtuemart::customFields(); ?>
                    <?php foreach ($customFields as $field) : ?>
                        <?php $selected = ($field->name === (string)MoloniDb::$settings['vat_field']); ?>
                        <option value='<?php echo $field->name ?>' <?php echo $selected ? 'selected' : '' ?>>
                            <?php echo $field->name ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </li>

            <li>
                <label>Enviar email:</label>
                <select name='opt[email_send]' class='inputOut'>
                    <?php $selected = (int)MoloniDb::$settings['email_send']?>
                    <option value="0" <?php echo $selected === 0 ? 'selected' : '' ?>>Não</option>
                    <option value="1" <?php echo $selected === 1 ? 'selected' : '' ?>>Sim</option>
                </select>
            </li>

            <li>
                <label>Gerar automaticamente documentos:</label>
                <select id="invoice_auto" name='opt[invoice_auto]' class='inputOut' onchange="onInvoiceAutoChange(this.value)">
                    <?php $selected = (int)MoloniDb::$settings['invoice_auto']?>
                    <option value="0" <?php echo $selected === 0 ? 'selected' : '' ?>>Não</option>
                    <option value="1" <?php echo $selected === 1 ? 'selected' : '' ?>>Sim</option>
                </select>

                <script>
                    function onInvoiceAutoChange(gerarDocumentos) {
                        if (gerarDocumentos && gerarDocumentos === '1') {
                            document.getElementById('invoice_auto_status_line').style['display'] = 'list-item';
                        } else {
                            document.getElementById('invoice_auto_status_line').style['display'] = 'none';
                        }
                    }
                </script>
            </li>

            <li id="invoice_auto_status_line"
                <?php echo (defined('INVOICE_AUTO') && INVOICE_AUTO === '0' ? 'style="display: none;"' : '') ?>>
                <?php
                $orderStatusModel = VmModel::getModel('orderstatus');
                $orderStatuses = $orderStatusModel->getOrderStatusNames(true);
                ?>
                <label>Estado do documento a ser gerado automaticamente:</label>
                <select name='opt[invoice_auto_status]' class='inputOut'>
                    <?php $selected = MoloniDb::$settings['invoice_auto_status']; ?>
                    <?php foreach ($orderStatuses as $orderStatus) : ?>
                        <option value='<?php echo $orderStatus['order_status_code'] ?>' <?php echo $selected === $orderStatus['order_status_code'] ? 'selected' : '' ?>>
                            <?php echo $orderStatus['order_status_name'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </li>

            <input type='hidden' value='registarAlteracoes' name='action'>
        </ul>
    <?php endif; ?>
</form>

