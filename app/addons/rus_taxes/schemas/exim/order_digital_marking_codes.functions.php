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

use Tygh\Enum\MarkingCodeFormats;

defined('BOOTSTRAP') or die('Access denied');

/**
 * @param array{order_id?: int, order_item_id?: int} $object         Import data
 * @param array{S: int}                              $processed_data Quantity of the loaded objects
 * @param bool                                       $skip_record    Flag for skip record
 */
function fn_rus_taxes_exim_check_order_item_existence(array $object, array &$processed_data, &$skip_record): void
{
    if (!empty($object['order_id']) && !empty($object['order_item_id'])) {
        $order_item_data = db_get_row(
            'SELECT item_id FROM ?:order_details WHERE order_id = ?i AND item_id = ?i',
            $object['order_id'],
            $object['order_item_id']
        );

        if (!empty($order_item_data)) {
            return;
        }
    }

    $skip_record = true;
    $processed_data['S']++;
}

/**
 * @param array{marking_code_format?: string} $object         Import data
 * @param array{S: int}                       $processed_data Quantity of the loaded objects
 * @param bool                                $skip_record    Flag for skip record
 */
function fn_rus_taxes_exim_check_marking_code_format(array $object, array &$processed_data, &$skip_record): void
{
    if (!empty($object['marking_code_format']) && MarkingCodeFormats::isExists($object['marking_code_format'])) {
        return;
    }

    $skip_record = true;
    $processed_data['S']++;
}

/**
 * @param array{marking_code?: string} $object         Import data
 * @param array{S: int}                $processed_data Quantity of the loaded objects
 * @param bool                         $skip_record    Flag for skip record
 */
function fn_rus_taxes_exim_check_marking_code(array $object, array &$processed_data, &$skip_record): void
{
    if (!empty($object['marking_code'])) {
        return;
    }

    $skip_record = true;
    $processed_data['S']++;
}

/**
 * @param array{order_id: int, order_item_id: int} $object            Import data
 * @param array<string>                            $primary_object_id Primary object id
 */
function fn_rus_taxes_exim_clear_previous_marking_codes(array $object, array &$primary_object_id): void
{
    static $processed_order_items = [];

    if (isset($processed_order_items[$object['order_id']][$object['order_item_id']])) {
        return;
    }

    db_query(
        'DELETE FROM ?:order_digital_marking_codes WHERE order_id = ?i AND order_item_id = ?i',
        $object['order_id'],
        $object['order_item_id']
    );
    $primary_object_id = [];

    $processed_order_items[$object['order_id']][$object['order_item_id']] = true;
}
