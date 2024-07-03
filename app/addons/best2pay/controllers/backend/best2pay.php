<?php

defined('BOOTSTRAP') or die('Access denied');

use B2P\Responses\Error;
use B2P\Models\Interfaces\CreditOrder;

/**
 * @var string $mode
 */

$client = fn_best2pay_get_client(fn_best2pay_get_processor_data($mode));

if($mode === 'complete') {
    try {
        $order_id = $_REQUEST['order_id'];
        if(!$order_id)
            throw new Exception(__('Undefined order ID'));

        $order_info = fn_get_order_info($order_id);
        $processor_data = fn_get_payment_method_data((int)$order_info['payment_id']);

        $pc_order_id = $order_info['payment_info']['order_id'];
        if(!$pc_order_id)
            throw new Exception(__('Failed to get Best2pay order ID'));

        $ct_order = $client->order(['id' => $pc_order_id]);
        if($ct_order instanceof Error)
            throw new Exception($ct_order->description->getValue());

        $complete_result = $ct_order->complete();
        if(!$complete_result)
            throw new Exception(__('Unable to debit funds'));

        if(!empty($processor_data['processor_params']['order_completed']))
            fn_change_order_status($order_id, $processor_data['processor_params']['order_completed']);

        if($ct_order instanceof CreditOrder) {
            // TODO проверить соответствие
            fn_update_order_payment_info($order_info['order_id'], ['status' => 'COMPLETED', 'order_status' => fn_best2pay_get_custom_order_status('COMPLETE', $processor_data['processor_params'])]);
            fn_set_notification('N', 'OK', __('The loan agreement was successfully completed and signed'));
        } else {
            fn_update_order_payment_info($order_info['order_id'], ['status' => 'COMPLETED', 'order_status' => fn_best2pay_get_custom_order_status('COMPLETE', $processor_data['processor_params'])]);
            fn_set_notification('N', 'OK', __('best2pay.payment_successful'));
        }

        return [CONTROLLER_STATUS_OK, 'orders.details?order_id=' . $order_id];
    } catch (Throwable $e) {
        fn_set_notification('E', 'ERROR', $e->getMessage());

        return [CONTROLLER_STATUS_OK, 'orders.details?order_id=' . $order_id];
    }
} elseif($mode === 'refund') {
    try {
        $order_id = $_REQUEST['order_id'];
        if(!$order_id)
            throw new Exception(__('Undefined order ID'));

        $order_info = fn_get_order_info($order_id);
        $processor_data = fn_get_payment_method_data((int)$order_info['payment_id']);

        $pc_order_id = $order_info['payment_info']['order_id'];
        if(!$pc_order_id)
            throw new Exception(__('Failed to get Best2pay order ID'));

        $ct_order = $client->order(['id' => $pc_order_id]);
        if($ct_order instanceof Error)
            throw new Exception($ct_order->description->getValue());

        $reverse_result = $ct_order->reverse();
        if(!$reverse_result)
            throw new Exception(__('Unable to issue a refund on a credit order'));

        if(!empty($processor_data['processor_params']['order_canceled']))
            fn_change_order_status($order_id, $processor_data['processor_params']['order_canceled']);

        fn_update_order_payment_info($order_info['order_id'], ['status' => 'CANCELED', 'order_status' => fn_best2pay_get_custom_order_status('REVERSE', $processor_data['processor_params'])]);
        fn_set_notification('N', 'OK', __('best2pay.refund_completed'));

        return [CONTROLLER_STATUS_OK, 'orders.details?order_id=' . $order_id];
    } catch (Throwable $e) {
        fn_set_notification('E', 'ERROR', $e->getMessage());

        return [CONTROLLER_STATUS_OK, 'orders.details?order_id=' . $order_id];
    }
}

exit;