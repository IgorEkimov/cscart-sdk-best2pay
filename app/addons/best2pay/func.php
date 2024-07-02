<?php

use B2P\Client;
use B2P\Models\Enums\CurrencyCode;
use B2P\Responses\Error;
use B2P\Models\Interfaces\CreditOrder;

use Tygh\Registry;
use Tygh\Enum\ObjectStatuses;
use Tygh\Enum\YesNo;
use Tygh\Http;
use Tygh\Enum\OrderDataTypes;
use Tygh\Enum\OrderStatuses;

defined('BOOTSTRAP') or die('Access denied');

require_once Registry::get('config.dir.addons') . "/best2pay/sdk/sdk_autoload.php";

function fn_best2pay_get_processor_data($mode) {
    if($_REQUEST['payment_id']) {
        $processor_data = fn_get_payment_method_data($_REQUEST['payment_id']);
    } else {
        try {
            if($mode === 'notify') {
                $response = file_get_contents("php://input");
                $response_xml = fn_best2pay_parse_xml($response);
                $order_id = (int)$response_xml['reference'];
            } else {
                $order_id = (int)$_REQUEST['reference'];
            }

            if(!$order_id)
                throw new Exception(__('best2pay.no_order_id'));

            $order_info = fn_get_order_info($order_id);
            $processor_data = fn_get_payment_method_data($order_info['payment_id']);
        } catch(Exception $e) {
            die($e->getMessage());
        }
    }

    return $processor_data['processor_params'];
}

function fn_best2pay_get_client($params) : Client | string {
    try {
        if (isset($params['sector_id']) && $params['password']) {
            $client = new Client((int)$params['sector_id'], $params['password'], (bool)$params['test_mode'], (bool)$params['hash_algo']);
        } else {
            throw new Exception(__('best2pay.no_client'));
        }
    } catch (Exception $e) {
        return $e->getMessage();
    }

    return $client;
}

/**
 * @throws \Exception
 */
function fn_best2pay_parse_xml($string) {
    if (!$string)
        throw new Exception(__('best2pay.empty_response'));

    $xml = simplexml_load_string($string);
    if (!$xml)
        throw new Exception(__('best2pay.invalid_xml'));

    $valid_xml = json_decode(json_encode($xml), true);
    if (!$valid_xml)
        throw new Exception(__('best2pay.invalid_xml'));

    return $valid_xml;
}

/**
 * @throws \Exception
 */
function fn_best2pay_get_currency($currency): int {
    if (isset(CurrencyCode::cases()[$currency])) {
        return CurrencyCode::cases()[$currency];
    } else throw new Exception(__('best2pay.no_currency'));
}

function fn_best2pay_calc_fiscal_positions_shop_cart($client, $order_info) : array {
    $fiscal_positions = [];
    $fiscal_amount = 0;
    $shop_cart = [];
    $tax = $order_info['payment_method']['processor_params']['tax'];

    foreach ($order_info['products'] as $b_key => $basket_item) {
        $fiscal_positions[$b_key]['quantity'] = (int)$basket_item['amount'];
        $fiscal_amount += $basket_item['amount'] * ($fiscal_positions[$b_key]['amount'] = $client->centifyAmount($basket_item['price']));
        $fiscal_positions[$b_key]['tax'] = (int)$tax;
        $fiscal_positions[$b_key]['name'] = str_ireplace([';', '|'], '', $basket_item['product']);

        $shop_cart[] = [
            'name' => $basket_item['product'],
            'goodCost' => $basket_item['price'],
            'quantityGoods' => (int)$basket_item['amount']
        ];
    }

    if($order_info['shipping_cost'] > 0){
        $fiscal_positions[] = [
            'quantity' => 1,
            'amount' => $client->centifyAmount($order_info['shipping_cost']),
            'tax' => (int)$tax,
            'name' => 'Доставка'
        ];
        $fiscal_amount += $client->centifyAmount($order_info['shipping_cost']);
        $shop_cart[] = [
            'name' => 'Доставка',
            'goodCost' => $order_info['shipping_cost'],
            'quantityGoods' => 1
        ];
    }

    if ($fiscal_diff = abs($fiscal_amount - $client->centifyAmount($order_info['total']))) {
        $fiscal_positions[] = [1, $fiscal_diff, (int)$tax, 'Скидка', 14];
        $shop_cart = [];
    }

    return [$fiscal_positions, $shop_cart];
}

function fn_best2pay_get_custom_order_status($operation_type, $params) {
    return match ($operation_type) {
        'PURCHASE', 'PURCHASE_BY_QR', 'COMPLETE' => !empty($params['order_completed']) ? $params['order_completed'] : OrderStatuses::PAID,
        'AUTHORIZE' => !empty($params['order_authorized']) ? $params['order_authorized'] : OrderStatuses::PAID,
        'LOAN' => !empty($params['order_loan']) ? $params['order_loan'] : OrderStatuses::PAID,
        'REVERSE' => !empty($params['order_canceled']) ? $params['order_canceled'] : OrderStatuses::CANCELED,
        default => '',
    };
}


/* TODO Order state (статусы заказов)
 *
 * REGISTERED
 *
 * AUTHORIZED
 *
 * P2PAUTHORIZED
 *
 * COMPLETED
 *
 * CANCELED
 *
 * BLOCKED
 *
 * EXPIRED
 *
 * */













































































/**
 * Creates Best2Pay payment processor on add-on installation.
 *
 * @return void
 */
function fn_best2pay_add_payment_processor()
{
    db_query(
        'INSERT INTO ?:payment_processors ?e', [
            'processor'          => 'Best2Pay',
            'processor_script'   => BEST2PAY_PROCESSOR,
            'processor_template' => 'views/orders/components/payments/cc_outside.tpl',
            'admin_template'     => 'best2pay.tpl',
            'callback'           => YesNo::NO,
            'type'               => 'P',
            'addon'              => 'best2pay',
        ]
    );
}

/**
 * Removes Best2Pay payment processor and disables payment methods on add-on uninstallation.
 *
 * @return void
 */
function fn_best2pay_delete_payment_processor()
{
    $addon_processor_id = db_get_field(
        'SELECT processor_id FROM ?:payment_processors WHERE processor_script = ?s',
        BEST2PAY_PROCESSOR
    );

    db_query(
        'UPDATE ?:payments SET status = ?s, processor_params = ?s, processor_id = ?i WHERE processor_id = ?s',
        ObjectStatuses::DISABLED,
        '',
        0,
        $addon_processor_id
    );

    db_query(
        'DELETE FROM ?:payment_processors WHERE processor_id = ?i',
        $addon_processor_id
    );
}

function fn_best2pay_get_url($params) {
    return empty($params['test_mode']) ? 'https://pay.best2pay.net' : 'https://test.best2pay.net';
}

function fn_best2pay_operation_is_valid($response, $params) {
    if(empty($response['reason_code']) && !empty($response['code']) && !empty($response['description']))
        throw new Exception($response['code'] . " : " . $response['description']);
    if(empty($response['signature']))
        throw new Exception(__('best2pay.empty_signature'));
    $tmp_response = (array)$response;
    unset($tmp_response['signature'], $tmp_response['ofd_state']);
    $signature = base64_encode(md5(implode('', $tmp_response) . $params['password']));
    if ($signature !== $response['signature'])
        throw new Exception(__('best2pay.invalid_signature'));
    if(!in_array($response['type'], BEST2PAY_SUPPORTED_TYPES))
        throw new Exception(__('best2pay.unknown_operation') . ' : ' . $response['type']);
    return true;
}

function fn_best2pay_prepare_order_info(&$order_info) {
    if($order_info['payment_method']['processor'] == 'Best2Pay') {
        $payment_type = !empty($order_info['payment_info']['payment_type']) ? $order_info['payment_info']['payment_type'] : '';
        $prefix = 'best2pay.';
        $type_name = __($prefix . $payment_type);
        if(strpos($type_name, $prefix) === false)
            $order_info['payment_info']['payment_type'] = $type_name;
    }
}

function fn_best2pay_order_can_be_refund($order_info) {
    if($order_info['payment_method']['processor'] == 'Best2Pay') {
        $status = !empty($order_info['payment_info']['status']) ? $order_info['payment_info']['status'] : '';
        $order_id = !empty($order_info['payment_info']['order_id']) ? $order_info['payment_info']['order_id'] : '';
        if ($order_id && ($status == 'COMPLETED' || $status == 'AUTHORIZED'))
            return true;
    }
    return false;
}

function fn_best2pay_order_can_be_complete($order_info) {
    if($order_info['payment_method']['processor'] == 'Best2Pay') {
        $status = !empty($order_info['payment_info']['status']) ? $order_info['payment_info']['status'] : '';
        $order_id = !empty($order_info['payment_info']['order_id']) ? $order_info['payment_info']['order_id'] : '';
        if ($order_id && $status == 'AUTHORIZED')
            return true;
    }
    return false;
}

function fn_best2pay_order_refund($order_info, $params) {
    $data = fn_best2pay_prepare_order_data($order_info);
    fn_best2pay_sign_data($data, $params);
    $payment_type = !empty($order_info['payment_info']['payment_type']) ? $order_info['payment_info']['payment_type'] : '';
    $path = ($payment_type == 'halva' || $payment_type == 'plait_two_steps') ? '/webapi/custom/svkb/Reverse' : '/webapi/Reverse';
    $url = fn_best2pay_get_url($params) . $path;
    $response = Http::post($url, $data);
    $response_xml = fn_best2pay_parse_xml($response);
    if (!fn_best2pay_operation_is_valid($response_xml, $params))
        throw new Exception(__('best2pay.operation_not_valid'));
    if($response_xml['state'] !== BEST2PAY_OPERATION_APPROVED)
        throw new Exception(__('best2pay.operation_not_approved'));
    return ['status' => $response_xml['order_state']];
}

function fn_best2pay_order_complete($order_info, $params) {
    $data = fn_best2pay_prepare_order_data($order_info);
    fn_best2pay_sign_data($data, $params);
    $payment_type = !empty($order_info['payment_info']['payment_type']) ? $order_info['payment_info']['payment_type'] : '';
    $path = ($payment_type == 'halva' || $payment_type == 'plait_two_steps') ? '/webapi/custom/svkb/Complete' : '/webapi/Complete';
    $url = fn_best2pay_get_url($params) . $path;
    $response = Http::post($url, $data);
    $response_xml = fn_best2pay_parse_xml($response);
    if (!fn_best2pay_operation_is_valid($response_xml, $params))
        throw new Exception(__('best2pay.operation_not_valid'));
    if($response_xml['state'] !== BEST2PAY_OPERATION_APPROVED)
        throw new Exception(__('best2pay.operation_not_approved'));
    return ['status' => $response_xml['order_state']];
}

function fn_best2pay_prepare_order_data($order_info) {
    $best2pay_id = !empty($order_info['payment_info']['order_id']) ? $order_info['payment_info']['order_id'] : '';
    if(!$best2pay_id)
        throw new Exception(__('best2pay.no_order_id'));
    $amount = intval($order_info['total'] * 100);
    $currency = '643';
    return [
        'id' => $best2pay_id,
        'amount' => $amount,
        'currency' => $currency
    ];
}

function fn_best2pay_sign_data(&$data, $params, $password = true) {
    $sign = $params['sector_id'] . implode('', $data);
    if($password)
        $sign .= $params['password'];
    $data['sector'] = $params['sector_id'];
    $data['signature'] = base64_encode(md5($sign));
}