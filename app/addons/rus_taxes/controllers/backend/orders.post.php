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

defined('BOOTSTRAP') or die('Access denied');

/**
 * @psalm-var array{order_id: int} $params
 */
$params = $_REQUEST;

if ($mode === 'details') {
    $order_id = !empty($params['order_id']) ? $params['order_id'] : 0;
    $order_info = fn_get_order_info($order_id, false, true, true, false);

    $marking_extra_data = $order_info ? fn_rus_taxes_get_marking_data_variables_for_order($order_info) : [];

    foreach ($marking_extra_data as $k => $v) {
        Tygh::$app['view']->assign($k, $v);
    }
}
