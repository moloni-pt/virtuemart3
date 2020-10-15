<?php

use Moloni\Functions\Messages;
use Moloni\Functions\Virtuemart;

defined('_JEXEC') or die('Restricted access');
$orders = Virtuemart::getAllOrders();

$orderStatusModel = VmModel::getModel('orderstatus');
$orderStatuses = $orderStatusModel->getOrderStatusNames(true);
?>

<?php Messages::printMessages(); ?>

<table class="tblMoloni">
    <thead>
    <tr>
        <th>ID Encomenda</th>
        <th>Referência Encomenda</th>
        <th>Cliente</th>
        <th>Estado</th>
        <th>Total</th>
        <th>Taxa</th>
        <th>Data Encomenda</th>
        <th>Ações</th>
    </tr>
    </thead>
    <tbody>
    <?php if (!empty($orders)): ?>
        <?php foreach ($orders as $order): ?>
            <?php $client = Virtuemart::getOneClientByOrder($order->virtuemart_order_id); ?>
            <tr>
                <td><?php echo $order->virtuemart_order_id; ?></td>
                <td>
                    <a href='index.php?option=com_virtuemart&view=orders&task=edit&virtuemart_order_id=<?php echo $order->virtuemart_order_id; ?>' target='_blank'
                    >
                        <?php echo $order->order_number; ?>
                    </a>
                </td>
                <td>
                    <a href='index.php?option=com_virtuemart&view=user&task=edit&virtuemart_user_id[]=<?php echo $order->virtuemart_user_id; ?>' target='_blank'
                    >
                        <?php echo $client[0]->first_name; ?>
                        <?php echo $client[0]->last_name; ?>
                    </a>
                </td>
                <td>
                    <?php
                    $order_status = $order->order_status;
                    foreach ($orderStatuses as $orderStatus) {
                        if ($order->order_status === $orderStatus['order_status_code']) {
                            echo $orderStatus['order_status_name'];
                            break;
                        }
                    }
                    ?>
                </td>
                <td><?php echo round($order->order_total, 2); ?> €</td>
                <td><?php echo round($order->order_tax, 2); ?> €</td>
                <td><?php echo $order->created_on; ?></td>
                <td>
                    <a class='btn'
                       href='index.php?option=com_moloni&action=makeInvoice&id=<?php echo $order->virtuemart_order_id; ?>'
                    >
                        <span class='icon-save'></span>Gerar
                    </a>
                    <a class='btn'
                       href='index.php?option=com_moloni&action=removeOrder&id=<?php echo $order->virtuemart_order_id; ?>'
                    >
                        <span class='icon-unpublish'></span>Descartar
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>

<?php if (empty($order)): ?>
    <div class="msgAlertaForms2">Todas as encomendas já foram geradas!</div>
<?php endif; ?>
