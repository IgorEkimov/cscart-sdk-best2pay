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
use Tygh\Addons\RusTaxes\TaxType;
use Tygh\Enum\MarkingCodeFormats;
use Tygh\Enum\NotificationSeverity;
use Tygh\Enum\FiscalData1212Objects;
use Tygh\Enum\YesNo;

defined('BOOTSTRAP') or die('Access denied');

/**
 * Handler on install add-on.
 */
function fn_rus_taxes_install()
{
    $taxes_exists = db_get_field('SELECT COUNT(*) FROM ?:taxes WHERE status = ?s', 'A');

    if ($taxes_exists) {
        return;
    }

    $destinations = fn_get_destinations();

    $taxes = array(
        array(
            'tax' => __('rus_taxes.tax.vat20'),
            'regnumber' => '',
            'priority' => 0,
            'address_type' => 'S',
            'status' => 'A',
            'price_includes_tax' => 'Y',
            'tax_type' => TaxType::VAT_20,
            'rate_value' => '20',
            'rate_type' => 'P'
        ),
        array(
            'tax' => __('rus_taxes.tax.vat10'),
            'regnumber' => '',
            'priority' => 0,
            'address_type' => 'S',
            'status' => 'D',
            'price_includes_tax' => 'Y',
            'tax_type' => TaxType::VAT_10,
            'rate_value' => '10',
            'rate_type' => 'P'
        ),
        array(
            'tax' => __('rus_taxes.tax.vat0'),
            'regnumber' => '',
            'priority' => 0,
            'address_type' => 'S',
            'status' => 'D',
            'price_includes_tax' => 'Y',
            'tax_type' => TaxType::VAT_0,
            'rate_value' => '0',
            'rate_type' => 'P'
        ),
    );

    foreach ($taxes as $tax) {
        $tax['rates'] = array();

        foreach ($destinations as $destination) {
            $tax['rates'][$destination['destination_id']] = array(
                'rate_value' => $tax['rate_value'],
                'rate_type' => $tax['rate_type']
            );
        }

        fn_update_tax($tax, 0);
    }
}

/**
 * The "add_to_cart_before_select_product_simple_data" hook handler.
 *
 * Actions performed:
 *  - Adds product payment subject tag 1212 in field list for selection.
 *
 * @param array<string> $data_fields Product simple data fields
 *
 * @see fn_add_product_to_cart
 *
 * @return void
 */
function fn_rus_taxes_add_to_cart_before_select_product_simple_data(array &$data_fields)
{
    $data_fields[] = 'fiscal_data_1212';
    $data_fields[] = 'mark_code_type';
}

/**
 * The "add_to_cart" hook handler.
 *
 * Actions performed:
 *  - Adds product payment subject tag 1212 and mark code type fields to cart item data.
 *
 * @param array $cart       Cart data
 * @param int   $product_id Product identifier
 * @param int   $_id        Cart item identifier
 * @param array $_data      Additional simple product data
 *
 * @see fn_add_product_to_cart
 *
 * @return void
 *
 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint
 */
function fn_rus_taxes_add_to_cart(array &$cart, $product_id, $_id, array $_data)
{
    if (!empty($_data['fiscal_data_1212'])) {
        $cart['products'][$_id]['fiscal_data_1212'] = (int) $_data['fiscal_data_1212'];
    }

    if (empty($_data['mark_code_type'])) {
        return;
    }
    $cart['products'][$_id]['mark_code_type'] = $_data['mark_code_type'];
}

/**
 * The "create_order_details" hook handler.
 *
 * Actions performed:
 *  - Adds product payment subject tag 1212 field to stored order item data.
 *
 * @param int   $order_id      Order identifier to create details for
 * @param array $cart          Cart contents
 * @param array $order_details Ordered product details
 * @param array $extra         Product extra parameters
 * @param int   $k             Cart item identifier
 * @param array $v             Cart item data
 *
 * @see fn_create_order_details
 *
 * @return void
 *
 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint
 */
function fn_rus_taxes_create_order_details($order_id, array $cart, array &$order_details, array $extra, $k, array $v)
{
    if (empty($order_details['extra'])) {
        return;
    }

    $extra = unserialize($order_details['extra']);
    if (!empty($v['fiscal_data_1212'])) {
        $extra['fiscal_data_1212'] = $v['fiscal_data_1212'];
    }
    if (!empty($v['mark_code_type'])) {
        $extra['mark_code_type'] = $v['mark_code_type'];
    }
    $order_details['extra'] = serialize($extra);
}

/**
 * Gets variables for templates with marking data to view.
 *
 * @param array $order Order data
 *
 * @return array
 *
 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint
 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification
 */
function fn_rus_taxes_get_marking_data_variables_for_order(array $order)
{
    if (
        empty($order['products'])
        || empty($order['payment_method']['processor_params']['ffd_version'])
        || $order['payment_method']['processor_params']['ffd_version'] !== '1.2'
    ) {
        return [];
    }

    $marking_extra_data = [];
    $marking_service = ServiceProvider::getDigitalMarkingService();
    $marking_extra_data['marked_products'] = $marking_service->getMarkedProductsDataForOrder($order);

    $marked_products_count = 0;
    foreach ($order['products'] as $item_id => $product) {
        if (
            empty($product['extra']['fiscal_data_1212'])
            || !FiscalData1212Objects::isMarkedObject($product['extra']['fiscal_data_1212'])
        ) {
            continue;
        }

        $marking_extra_data['show_marking_interface'] = true;

        if (
            isset($marking_extra_data['marked_products'][$item_id]['marking_codes_data'])
            && count($marking_extra_data['marked_products'][$item_id]['marking_codes_data']) >= (int) $product['amount']
        ) {
            continue;
        }

        $marking_extra_data['show_marking_codes_notification'] = true;
        $marking_extra_data['marked_products_count'] = ++$marked_products_count;
    }

    return $marking_extra_data;
}

/**
 * The "delete_order" hook handler.
 *
 * Actions performed:
 *  - Deletes marking codes data for deleted order.
 *
 * @param int $order_id Order identifier
 *
 * @see fn_delete_order
 *
 * @return void
 */
function fn_rus_taxes_delete_order($order_id)
{
    if (empty($order_id)) {
        return;
    }

    db_query('DELETE FROM ?:order_digital_marking_codes WHERE order_id = ?i', $order_id);
}

/**
 * The "update_order" hook handler.
 *
 * Actions performed:
 *  - Recalculates mark codes for order items by amount.
 *  - Removes redundant mark codes if some order items changed.
 *
 * @param array $order    Order data
 * @param int   $order_id Order identifier
 *
 * @see fn_update_order
 *
 * @return void
 *
 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint
 */
function fn_rus_taxes_update_order(array $order, $order_id)
{
    $marking_service = ServiceProvider::getDigitalMarkingService();
    $marking_service->recalculateMarkingCodesForOrder($order, $order_id, true);
}

/**
 * The "change_order_status_pre" hook handler.
 *
 * Actions performed:
 *  - Prevents changing order status to the one that sends closing receipt. Shows notification.
 *
 * @param int    $order_id            Parent order identifier
 * @param string $status_to           New parent order status (one char)
 * @param string $status_from         Old parent order status (one char)
 * @param array  $force_notification  Array with notification rules
 * @param bool   $place_order         True, if this function have been called inside of fn_place_order function.
 * @param array  $order_info          Order information
 * @param bool   $allow_status_update Whether to allow status update
 *
 * @see fn_update_order
 *
 * @return void
 *
 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint
 */
function fn_rus_taxes_change_order_status_pre(
    $order_id,
    $status_to,
    $status_from,
    array $force_notification,
    $place_order,
    array $order_info,
    &$allow_status_update
) {
    if (
        empty($order_info['payment_method']['processor_params']['final_success_status'])
        || $order_info['payment_method']['processor_params']['final_success_status'] !== $status_to
        || empty($order_info['payment_method']['processor_params']['ffd_version'])
        || $order_info['payment_method']['processor_params']['ffd_version'] !== '1.2'
        || empty($order_info['products'])
    ) {
        return;
    }

    $marking_service = ServiceProvider::getDigitalMarkingService();
    $marked_products = $marking_service->getMarkedProductsDataForOrder($order_info);
    foreach ($order_info['products'] as $item_id => $product) {
        if (
            empty($product['extra']['fiscal_data_1212'])
            || !in_array($product['extra']['fiscal_data_1212'], FiscalData1212Objects::getMarkedObjects())
        ) {
            continue;
        }

        if (
            empty($marked_products[$item_id]['marking_codes_data'])
            || count($marked_products[$item_id]['marking_codes_data']) < (int) $product['amount']
        ) {
            $order_statuses_descriptions = fn_get_simple_statuses();
            fn_set_notification(
                NotificationSeverity::WARNING,
                __('warning'),
                __(
                    'rus_taxes.empty_mark_codes_notification',
                    [
                        '[status_to]' => $order_statuses_descriptions[$status_to],
                        '[order_id]' => $order_id,
                        '[link]' => fn_url('digital_marking.marking_codes?order_id=' . $order_id)
                    ]
                )
            );
            $allow_status_update = false;

            return;
        }
    }
}

/**
 * @param array{name: string, text: string, field: string, disabled?: string} $fields Product fields
 *
 * @see fn_get_product_fields()
 */
function fn_rus_taxes_get_product_fields(array &$fields): void
{
    $fields[] =  [
        'name'  => '[data][fiscal_data_1212]',
        'text'  => __('payment_object'),
        'field' => 'fiscal_data_1212',
    ];

    $fields[] =  [
        'name'  => '[data][mark_code_type]',
        'text'  => __('is_fur_ware'),
        'field' => 'mark_code_type',
    ];
}

/**
 * @param array{mark_code_type?: string, fiscal_data_1212?: int} $params    Product search params
 * @param array<string>                                          $fields    List of fields for retrieving
 * @param array<string, string>                                  $sortings  Sorting fields
 * @param string                                                 $condition String containing SQL-query condition possibly prepended with a logical operator (AND or OR)
 */
function fn_rus_taxes_get_products(array $params, array $fields, array $sortings, &$condition): void
{
    if (!empty($params['mark_code_type'])) {
        $condition .= db_quote(' AND products.mark_code_type = ?s', YesNo::isTrue($params['mark_code_type']) ? MarkingCodeFormats::FUR : '');
    }

    if (!empty($params['fiscal_data_1212']) && FiscalData1212Objects::isExists($params['fiscal_data_1212'])) {
        $condition .= db_quote(' AND products.fiscal_data_1212 = ?i', $params['fiscal_data_1212']);
    }
}
