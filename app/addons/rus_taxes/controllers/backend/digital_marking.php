<?php
/***************************************************************************
 *                                                                          *
 *   (c) 2004 Vladimir V. Kalynyak, Alexey V. Vinokurov, Ilya M. Shalnev    *
 *                                                                          *
 * This  is  commercial  software,  only  users  who have purchased a valid *
 * license  and  accept  to the terms of the  License Agreement can install *
 * and use this program.                                                    *
 *                                                                          *
 ****************************************************************************
 * PLEASE READ THE FULL TEXT  OF THE SOFTWARE  LICENSE   AGREEMENT  IN  THE *
 * "copyright.txt" FILE PROVIDED WITH THIS DISTRIBUTION PACKAGE.            *
 ****************************************************************************/

use Tygh\Addons\RusTaxes\ServiceProvider;

defined('BOOTSTRAP') or die('Access denied');

$marking_service = ServiceProvider::getDigitalMarkingService();

/**
 * @psalm-var array{
 *     order_id: int,
 *     order_item_id: int,
 *     marking_codes_data: array<int, list<array{
 *       marking_code_format: string,
 *       marking_code: string
 *     }>>
 * } $params
 */
$params = $_REQUEST;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($mode === 'save_codes') {
        if (!empty($params['order_id'])) {
            $marking_codes_data = !empty($params['marking_codes_data']) ? $params['marking_codes_data'] : [];
            $marking_service->updateMarkingCodesForOrder(
                $params['order_id'],
                $marking_codes_data
            );
        }

        if (!empty($_REQUEST['return_url'])) {
            return [CONTROLLER_STATUS_REDIRECT, $_REQUEST['return_url']];
        }
    }
}

if ($mode === 'marking_codes') {
    $order_id = !empty($params['order_id']) ? $params['order_id'] : 0;
    if (empty($order_id)) {
        return;
    }

    $marking_code_formats = fn_get_schema('digital_marking', 'marking_code_formats');

    $order_info = fn_get_order_info($order_id);
    $marking_extra_data = $order_info ? fn_rus_taxes_get_marking_data_variables_for_order($order_info) : [];
    if (!empty($order_info['products']) && !empty($marking_extra_data['marked_products'])) {
        $order_marked_items = $marking_extra_data['marked_products'];

        foreach ($order_marked_items as $item_id => &$product) {
            /** @var array $product */
            $product = array_merge($order_info['products'][$item_id], $product);
            $product['main_pair'] = fn_get_cart_product_icon($product['product_id'], $product);

            if (!empty($product['amount'])) {
                for ($i = 0; $i < $product['amount']; $i++) {
                    if (!empty($product['marking_codes_data'][$i])) {
                        continue;
                    }
                    $product['marking_codes_data'][$i] = [
                        'marking_code_format' => '',
                        'marking_code' => ''
                    ];
                }
            }
        }

        Tygh::$app['view']->assign('order_marked_items', $order_marked_items);
    }

    Tygh::$app['view']->assign([
        'order_id' => $order_id,
        'marking_code_formats' => $marking_code_formats
    ]);
}
