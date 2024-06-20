<?php

use Tygh\Http;
use Tygh\Registry;
use Tygh\Enum\OrderStatuses;
use B2P\Client;
use B2P\Responses\Error;
use B2P\Models\Enums\CurrencyCode;
use B2P\Models\Interfaces\CreditOrder;

defined('BOOTSTRAP') or die('Access denied');

require_once Registry::get('config.dir.addons') . "/best2pay/sdk/sdk_autoload.php";

if(defined('PAYMENT_NOTIFICATION')){
    /**
     * Получение и обработка ответа от сторонних
     * платежных сервисов и систем оплаты.
     *
     * Доступные переменные:
     * @var string $mode цель запроса
     */


    //echo '<pre>'; print_r($_REQUEST); echo '</pre>';

    file_put_contents($_SERVER['DOCUMENT_ROOT']."/empty_log.txt", var_export($_REQUEST, true),FILE_APPEND);

    if(($mode == 'success') || ($mode == 'fail')){
        try {
            if(isset($_REQUEST['error'])) {
                fn_set_notification('E', 'Error', 'Failed to pay for the order' . ":\n" . $_REQUEST['error']);
                fn_redirect('checkout.checkout');
            }

            if(!empty($_REQUEST['modal'])) {
                $args = $_REQUEST;
                $args['action'] = "payment_notification.{$mode}";
                fn_create_payment_form(fn_url('best2pay.redirect'), $args);
            }

            $ct_order_id = (int)$_REQUEST['id'];
            if(!$ct_order_id)
                throw new Exception('Failed to get Best2pay order ID');

            $pc_ref_id = (int)$_REQUEST['reference'];
            if(!$pc_ref_id)
                throw new Exception('Undefined order ID');

            //            $pc_order_id = (int)get_post_meta($pc_ref_id, 'best2pay_order_id', true);
            //            if($ct_order_id !== $pc_order_id)
            //                throw new Exception('Request data is not valid');

            $order_info = fn_get_order_info($pc_ref_id);
            $processor_data = fn_get_payment_method_data((int)$order_info['payment_id']);
            $params = !empty($processor_data['processor_params']) ? $processor_data['processor_params'] : [];
            $client = fn_best2pay_get_client($params);

            $ct_order = $client->order(['id' => $ct_order_id]);
            if($ct_order instanceof Error)
                throw new Exception($ct_order->description->getValue());

            $order_id = !empty($_REQUEST['order_id']) ? (int)$_REQUEST['order_id'] : 0;
            if (!fn_check_payment_script(BEST2PAY_PROCESSOR, $order_id))
                die(__('best2pay.access_denied'));



        } catch (\throwable $e) {
            echo '<pre>'; print_r($e->getMessage()); echo '</pre>';
        }



        $pp_response['order_id'] = $ct_order_id;
        $pp_response['status'] = $ct_order->getState();
        $pp_response['payment_type'] = !empty($params['payment_type']) ? $params['payment_type'] : '';
        $pp_response['currency'] = CART_PRIMARY_CURRENCY;


        //$pp_response['amount'] = (!empty($operation['buyIdSumAmount']) ? $operation['buyIdSumAmount'] : $operation['amount']) / 100;


        $pp_response['amount'] = $ct_order->amount / 100;



        // TODO get operation ?
        $operation_id = !empty($_REQUEST['operation']) ? (int)$_REQUEST['operation'] : 0;
        $operationType = $ct_order->getOperation($operation_id)->type->getValue()->name;
        $operationState = $ct_order->getOperation($operation_id)->state->getValue()->name;


        // TODO END
        if($operationState === BEST2PAY_OPERATION_APPROVED)
            $pp_response['order_status'] = fn_best2pay_get_custom_order_status($operationType, $params);
        else
            $pp_response['order_status'] = OrderStatuses::FAILED;

        fn_finish_payment($order_id, $pp_response);
        fn_order_placement_routines('route', $order_id);







        //        if (!fn_check_payment_script(BEST2PAY_PROCESSOR, $order_id)) {
        //            die(__('best2pay.access_denied'));
        //        }
        //        $operation_id = !empty($_REQUEST['operation']) ? (int)$_REQUEST['operation'] : 0;
        //        $native_id = !empty($_REQUEST['id']) ? (int)$_REQUEST['id'] : 0;
        //        $order_info = fn_get_order_info($order_id);
        //        $processor_data = fn_get_payment_method_data((int) $order_info['payment_id']);
        //        $params = !empty($processor_data['processor_params']) ? $processor_data['processor_params'] : [];
        //
        //        $data = [
        //            'id' => $native_id,
        //            'operation' => $operation_id,
        //        ];
        //        fn_best2pay_sign_data($data, $params);
        //        $url = fn_best2pay_get_url($params) . '/webapi/Operation';
        //        $operation = [];
        //        $operation_is_valid = false;
        //        try {
        //            $response = Http::post($url, $data);
        //            $operation = fn_best2pay_parse_xml($response);
        //            $operation_is_valid = fn_best2pay_operation_is_valid($operation, $params);
        //        } catch(Exception $e) {
        //            fn_set_notification('E', 'Error', $e->getMessage());
        //            fn_order_placement_routines('route', $order_id);
        //        }
        //
        //
        //        $pp_response['order_id'] = $native_id;
        //        $pp_response['status'] = $operation['order_state'];
        //        $pp_response['payment_type'] = !empty($params['payment_type']) ? $params['payment_type'] : '';
        //        $pp_response['currency'] = 'RUB';
        //        $pp_response['amount'] = (!empty($operation['buyIdSumAmount']) ? $operation['buyIdSumAmount'] : $operation['amount']) / 100;
        //
        //        if($operation_is_valid && $operation['state'] === BEST2PAY_OPERATION_APPROVED && in_array($operation['type'], BEST2PAY_PAYMENT_TYPES))
        //            $pp_response['order_status'] = fn_best2pay_get_custom_order_status($operation['type'], $params);
        //        else
        //            $pp_response['order_status'] = OrderStatuses::FAILED;
        //
        //        fn_finish_payment($order_id, $pp_response);
        //        fn_order_placement_routines('route', $order_id);






    } elseif($mode == 'notify') {
        try {
            $response = file_get_contents("php://input");
            $response_xml = fn_best2pay_parse_xml($response);
        } catch(Exception $e) {
            die($e->getMessage());
        }

        $order_info = fn_get_order_info($response_xml['reference']);
        $processor_data = fn_get_payment_method_data((int) $order_info['payment_id']);
        $params = !empty($processor_data['processor_params']) ? $processor_data['processor_params'] : [];
        $client = fn_best2pay_get_client($params);

        if ($isNotifyRequest = fn_best2pay_isNotifyRequest($client)) {
            $input = file_get_contents("php://input");
            $ct_order_id = $client->handleResponse($input)->order_id->getValue();

            $ct_order = $client->order(['id' => $ct_order_id]);
            if($ct_order instanceof Error)
                throw new Exception($ct_order->description->getValue());





            //$response = $client->getState($order_info['payment_info']['payment_id']);
            //if ($ct_order->isPaid()) {
            //fn_update_order_payment_info($order_id, ['addons.tinkoff.payment_status' => $response['Status']]);


            if (in_array($ct_order->getState(), ['AUTHORIZED', 'COMPLETED'])) {
                fn_change_order_status($ct_order->reference, OrderStatuses::COMPLETE);
            }
            if (in_array($ct_order->getState(), ['REJECTED', 'CANCELED', 'REVERSED', 'REFUNDED'])) {
                fn_change_order_status($ct_order->reference, OrderStatuses::CANCELED);
            }


            //}
            //            fn_order_placement_routines('route', $order_info['order_id'], false);





        }

    }


    /* TODO соответствие статусов
    const PAID = 'P';
    const COMPLETE = 'C';
    const OPEN = 'O';
    const FAILED = 'F';
    const DECLINED = 'D';
    const BACKORDERED = 'B';
    const CANCELED = 'I';
    const INCOMPLETED = 'N';
    const PARENT = 'T';
    */









    //	if(($mode == 'success') || ($mode == 'fail')){
    //		if(!empty($_REQUEST['modal'])) {
    //			$args = $_REQUEST;
    //			$args['action'] = "payment_notification.{$mode}";
    //			fn_create_payment_form(fn_url('best2pay.redirect'), $args);
    //		}
    //
    //		$order_id = !empty($_REQUEST['order_id']) ? (int)$_REQUEST['order_id'] : 0;
    //		if (!fn_check_payment_script(BEST2PAY_PROCESSOR, $order_id)) {
    //			die(__('best2pay.access_denied'));
    //		}
    //		$operation_id = !empty($_REQUEST['operation']) ? (int)$_REQUEST['operation'] : 0;
    //		$native_id = !empty($_REQUEST['id']) ? (int)$_REQUEST['id'] : 0;
    //		$order_info = fn_get_order_info($order_id);
    //		$processor_data = fn_get_payment_method_data((int) $order_info['payment_id']);
    //		$params = !empty($processor_data['processor_params']) ? $processor_data['processor_params'] : [];
    //
    //		$data = [
    //			'id' => $native_id,
    //			'operation' => $operation_id,
    //		];
    //		fn_best2pay_sign_data($data, $params);
    //		$url = fn_best2pay_get_url($params) . '/webapi/Operation';
    //		$operation = [];
    //		$operation_is_valid = false;
    //		try {
    //			$response = Http::post($url, $data);
    //			$operation = fn_best2pay_parse_xml($response);
    //			$operation_is_valid = fn_best2pay_operation_is_valid($operation, $params);
    //		} catch(Exception $e) {
    //			fn_set_notification('E', 'Error', $e->getMessage());
    //			fn_order_placement_routines('route', $order_id);
    //		}
    //
    //		$pp_response['order_id'] = $native_id;
    //		$pp_response['status'] = $operation['order_state'];
    //		$pp_response['payment_type'] = !empty($params['payment_type']) ? $params['payment_type'] : '';
    //		$pp_response['currency'] = 'RUB';
    //		$pp_response['amount'] = (!empty($operation['buyIdSumAmount']) ? $operation['buyIdSumAmount'] : $operation['amount']) / 100;
    //
    //		if($operation_is_valid && $operation['state'] === BEST2PAY_OPERATION_APPROVED && in_array($operation['type'], BEST2PAY_PAYMENT_TYPES))
    //			$pp_response['order_status'] = fn_best2pay_get_custom_order_status($operation['type'], $params);
    //		else
    //			$pp_response['order_status'] = OrderStatuses::FAILED;
    //
    //		fn_finish_payment($order_id, $pp_response);
    //		fn_order_placement_routines('route', $order_id);

    //	} elseif($mode == 'notify') {
    //
    //		try {
    //			$response = file_get_contents("php://input");
    //			$response_xml = fn_best2pay_parse_xml($response);
    //		} catch(Exception $e) {
    //			die($e->getMessage());
    //		}
    //
    //		if(!empty($response_xml['reason_code'])) {
    //			$order_id = $response_xml['reference'];
    //			$order_info = fn_get_order_info($order_id);
    //			$processor_data = fn_get_payment_method_data((int) $order_info['payment_id']);
    //			$params = !empty($processor_data['processor_params']) ? $processor_data['processor_params'] : [];
    //			try {
    //				$operation_is_valid = fn_best2pay_operation_is_valid($response_xml, $params);
    //				if(!$operation_is_valid)
    //					throw new Exception(__('best2pay.operation_not_valid'));
    //			} catch(Exception $e) {
    //				die($e->getMessage());
    //			}
    //			$pp_response['order_id'] = $response_xml['order_id'];
    //			$pp_response['status'] = $response_xml['order_state'];
    //
    //			if ($response_xml['reason_code'] == 1) {
    //				$pp_response['order_status'] = fn_best2pay_get_custom_order_status($response_xml['type'], $params);
    //			} else {
    //				$pp_response['order_status'] = OrderStatuses::FAILED;
    //			}
    //			fn_update_order_payment_info($order_id, $pp_response);
    //			fn_change_order_status($order_id, $pp_response['order_status']);
    //			echo "ok";
    //		}
    //	}
} else {
    /**
     * Запуск необходимой для принятия платежей логики,
     * после того как клиент нажмет кнопку "Создать заказ".
     *
     * Доступные переменные:
     *
     * @var array $order_info Полная информация о заказе
     * @var array $processor_data Информация о обработчике платежа
     */

    $params = !empty($processor_data['processor_params']) ? $processor_data['processor_params'] : [];

    $client = fn_best2pay_get_client($params);

    $confirm_uri = 'payment_notification.success?payment=best2pay&order_id=' . $order_info['order_id'];
    $cancel_uri = 'payment_notification.fail?payment=best2pay&order_id=' . $order_info['order_id'];

    if(!empty($params['modal_payform'])) {
        $confirm_uri .= "&modal=1";
        $cancel_uri .= "&modal=1";
    }

    [$fiscal_positions, $shop_cart] = fn_best2pay_calc_fiscal_positions_shop_cart($client, $order_info);

    $register_data = [
        'reference' => $order_info['order_id'],
        'amount' => $client->centifyAmount($order_info['total']),
        'currency' => fn_best2pay_get_currency(CART_PRIMARY_CURRENCY),
        'email' => $order_info['email'],
        'phone' => $order_info['phone'],
        'description' => 'Оплата заказа ' . $order_info['order_id'],
        'url' => fn_url($confirm_uri, AREA, 'current'),
        'failurl' => fn_url($cancel_uri, AREA, 'current'),
        'mode' => 0,
        'fiscal_positions' => $fiscal_positions
    ];

    try {
        $response = $client->register($register_data);
        if($response instanceof Error)
            throw new Exception($response->description->getValue());

        $b2p_order_id = (int)$response->id;
        if (!$b2p_order_id)
            throw new Exception(__('best2pay.payment_process_error'));
    } catch (Exception $e) {
        fn_set_notification('E', 'Error', $e->getMessage());
        fn_redirect('checkout.checkout');
    }

    $operation_params = ['id' => $b2p_order_id];
    if (str_contains($params['payment_type'], 'WithInstallment') && $shop_cart)
        $operation_params['shop_cart'] = base64_encode(json_encode($shop_cart));
    if (str_contains($params['payment_type'], 'loan'))
        $operation_params['reference'] = $order_info['order_id'];

    $url = call_user_func([$client, $params['payment_type']], $operation_params);

    if (!empty($params['modal_payform']))
        fn_create_payment_form(fn_url('best2pay.modal'), ['modal_url' => $url]);

    fn_redirect($url, true, true);
}

exit;