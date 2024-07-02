<?php

use Tygh\Http;
use Tygh\Registry;
use Tygh\Enum\OrderStatuses;
use B2P\Client;
use B2P\Responses\Error;
use B2P\Models\Enums\CurrencyCode;
use B2P\Models\Interfaces\CreditOrder;
use function Clue\StreamFilter\prepend;

/**
 * @var array                 $order_info
 * @var array                 $processor_data
 * @var string                $mode
 */

defined('BOOTSTRAP') or die('Access denied');

require_once Registry::get('config.dir.addons') . "/best2pay/sdk/sdk_autoload.php";

$client = fn_best2pay_get_client(fn_best2pay_get_processor_data($mode));

if(defined('PAYMENT_NOTIFICATION')){
    /**
     * Получение и обработка ответа от сторонних
     * платежных сервисов и систем оплаты.
     *
     * Доступные переменные:
     * @var string $mode цель запроса
     */

    if(($mode === 'success') || ($mode === 'fail')){
        try {
            if(isset($_REQUEST['error'])) {
                fn_set_notification('E', 'Error', __('best2pay.redirect_error') . ":\n" . $_REQUEST['error']);
                fn_redirect('checkout.checkout');
            }

            if(!empty($_REQUEST['modal'])) {
                $_REQUEST['action'] = "payment_notification.{$mode}";
                fn_create_payment_form(fn_url('best2pay.redirect'), $_REQUEST);
            }

            $ct_order_id = (int)$_REQUEST['id'];
            if(!$ct_order_id)
                throw new Exception(__('best2pay.no_order_id'));

            $pc_ref_id = (int)$_REQUEST['reference'];
            if(!$pc_ref_id)
                throw new Exception(__('best2pay.no_reference_id'));

            $operation_id = (int)$_REQUEST['operation'];
            if(!$operation_id)
                throw new Exception(__('best2pay.unknown_operation'));

            $order_info = fn_get_order_info($pc_ref_id);
            $processor_data = fn_get_payment_method_data((int)$order_info['payment_id']);

            $ct_order = $client->order(['id' => $ct_order_id]);
            if($ct_order instanceof Error)
                throw new Exception($ct_order->description->getValue());

            $operationType = $ct_order->getOperation($operation_id)->type->getValue()->name;

            if (!fn_check_payment_script(BEST2PAY_PROCESSOR, (int)$ct_order->reference))
                die(__('best2pay.access_denied'));

            $pp_response['order_id'] = $ct_order_id;
            $pp_response['status'] = $ct_order->getState();
            $pp_response['payment_type'] = $processor_data['processor_params']['payment_type'];
            $pp_response['currency'] = CART_PRIMARY_CURRENCY;
            $pp_response['amount'] = $ct_order->amount / 100;

            $paid = false;

            if($ct_order instanceof CreditOrder) {
                $pp_response['order_status'] = fn_best2pay_get_custom_order_status($ct_order->isPaid() ? 'COMPLETE' : 'LOAN', $processor_data['processor_params']);

                $paid = true;
            } else {
                $pp_response['order_status'] = fn_best2pay_get_custom_order_status($operationType, $processor_data['processor_params']);

                $paid = $ct_order->isPaid();
            }

            /*
            'order_completed' => 'C',
            'order_authorized' => 'A',
            'order_loan' => 'Y',
            'order_canceled' => 'E',
            */




            fn_finish_payment((int)$ct_order->reference, $pp_response);
            fn_order_placement_routines('route', (int)$ct_order->reference);








        } catch (\throwable $e) {
            echo '<pre>'; print_r($e->getMessage()); echo '</pre>';

            $pp_response['order_status'] = OrderStatuses::FAILED;
        }

    } elseif($mode == 'notify') {
        try {
            $response = file_get_contents("php://input");

            $ct_order_id = $client->handleResponse($response)->order_id->getValue();
            if(!$ct_order_id)
                throw new Exception(__('best2pay.no_order_id'));

            $operation_id = $client->handleResponse($response)->id->getValue();
            if(!$operation_id)
                throw new Exception(__('best2pay.unknown_operation'));

            $ct_order = $client->order(['id' => $ct_order_id]);
            if($ct_order instanceof Error)
                throw new Exception($ct_order->description->getValue());

            $ct_ref_id = $ct_order->reference;
            if(!$ct_ref_id)
                throw new Exception(__('best2pay.no_reference_id'));

            $operationType = $ct_order->getOperation($operation_id)->type->getValue()->name;

            $order_info = fn_get_order_info($ct_ref_id);
            $processor_data = fn_get_payment_method_data((int)$order_info['payment_id']);

            $pp_response['order_id'] = $ct_order->id;
            $pp_response['status'] = $ct_order->getState();

            if($ct_order instanceof CreditOrder) {
                $pp_response['order_status'] = fn_best2pay_get_custom_order_status($ct_order->isPaid() ? 'COMPLETE' : 'LOAN', $processor_data['processor_params']);

                $paid = true;
            } else {
                $pp_response['order_status'] = fn_best2pay_get_custom_order_status($operationType, $processor_data['processor_params']);

                $paid = $ct_order->isPaid();
            }

            fn_update_order_payment_info($ct_ref_id, $pp_response);
            fn_change_order_status($ct_ref_id, $pp_response['order_status']);
            echo "ok";


        } catch(Exception $e) {
            die($e->getMessage());
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

    $confirm_uri = 'payment_notification.success?payment=best2pay&order_id=' . $order_info['order_id'];
    $cancel_uri = 'payment_notification.fail?payment=best2pay&order_id=' . $order_info['order_id'];

    if(!empty($processor_data['processor_params']['modal_payform'])) {
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
        'url' => fn_url($confirm_uri),
        'failurl' => fn_url($cancel_uri),
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

        $operation_params = ['id' => $b2p_order_id];

        if (str_contains($processor_data['processor_params']['payment_type'], 'WithInstallment') && $shop_cart)
            $operation_params['shop_cart'] = base64_encode(json_encode($shop_cart));
        if (str_contains($processor_data['processor_params']['payment_type'], 'loan'))
            $operation_params['reference'] = $order_info['order_id'];

        $url = call_user_func([$client, $processor_data['processor_params']['payment_type']], $operation_params);

        if (!empty($processor_data['processor_params']['modal_payform']))
            fn_create_payment_form(fn_url('best2pay.modal'), ['modal_url' => $url]);

        fn_redirect($url, true, true);
    } catch (Throwable $e) {
        fn_set_notification('E', 'Error', $e->getMessage());
        fn_redirect('checkout.checkout');
    }
}

exit;