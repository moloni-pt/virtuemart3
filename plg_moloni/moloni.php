<?php

use Moloni\Functions\Hooks;

// no direct access
defined('_JEXEC') or die('Restricted access');

class plgSystemMoloni extends JPlugin
{
    /**
     * Method called by triggers when an order status is changed
     * @param $data object order object
     * @param $old_order_status string old order status code
     * @throws Exception
     */
    public function plgVmOnUpdateOrderPayment($data, $old_order_status)
    {

        require_once __DIR__ . '/../../../administrator/components/com_moloni/vendor/autoload.php';

        if (isset($data, $old_order_status)) {
            $hook = new Hooks($data);

            if ($hook->init()) {
                $hook->createDocument();
            }
        }
    }
}
