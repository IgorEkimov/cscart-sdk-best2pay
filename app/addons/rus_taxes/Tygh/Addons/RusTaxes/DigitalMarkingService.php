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

namespace Tygh\Addons\RusTaxes;

use Tygh\Enum\FiscalData1212Objects;
use Tygh\Enum\MarkingCodeFormats;
use Tygh\Enum\NotificationSeverity;

class DigitalMarkingService
{
    /**
     * Gets marking codes for specified order.
     *
     * @param int $order_id Order identifier
     *
     * @return array<int|string>
     *
     * @psalm-return list<array{
     *     order_item_id: int,
     *     marking_code_format: string,
     *     marking_code: string
     * }>
     */
    public function getAllMarkingCodesForOrder($order_id)
    {
        if (empty($order_id)) {
            return [];
        }
        return db_get_array(
            'SELECT order_item_id, marking_code_format, marking_code FROM ?:order_digital_marking_codes WHERE order_id = ?i',
            $order_id
        );
    }

    /**
     * Gets marked products array with marking codes data for specified order.
     *
     * @param array $order Order data
     *
     * @return array
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification
     */
    public function getMarkedProductsDataForOrder(array $order)
    {
        if (empty($order['order_id'])) {
            return [];
        }

        $marking_codes_data = $this->getAllMarkingCodesForOrder($order['order_id']);

        $marked_products = [];
        foreach ($order['products'] as $item_id => $product) {
            if (
                empty($product['extra']['fiscal_data_1212'])
                || !FiscalData1212Objects::isMarkedObject($product['extra']['fiscal_data_1212'])
            ) {
                continue;
            }

            $marked_products[$item_id]['mark_code_type'] = $product['extra']['mark_code_type'] ?? '';

            if (empty($marking_codes_data)) {
                continue;
            }

            $marked_products[$item_id]['marking_codes_data'] = [];
            foreach ($marking_codes_data as $marking_codes_data_item) {
                if ($item_id !== (int) $marking_codes_data_item['order_item_id']) {
                    continue;
                }
                $marked_products[$item_id]['marking_codes_data'][] = [
                    'marking_code_format' => $marking_codes_data_item['marking_code_format'],
                    'marking_code'        => $marking_codes_data_item['marking_code']
                ];
            }
        }

        return $marked_products;
    }

    /**
     * Updates or creates marking codes for the specified order.
     *
     * @param int   $order_id           Order identifier
     * @param array $marking_codes_data Data of marking codes
     *
     * @psalm-param array<int, list<array{
     *   marking_code_format: string,
     *   marking_code: string
     * }>> $marking_codes_data
     *
     * @return void
     */
    public function updateMarkingCodesForOrder($order_id, array $marking_codes_data)
    {
        $data = [];
        foreach ($marking_codes_data as $item_id => $item_marking_data) {
            foreach ($item_marking_data as $marking_code_item) {
                // Validate code value for 'fur'
                if (
                    $marking_code_item['marking_code_format'] === MarkingCodeFormats::FUR
                    && !preg_match('/^.{2}-[0-9]{6}-.{10}$/', $marking_code_item['marking_code'])
                ) {
                    unset($marking_code_item['marking_code']);
                }

                if (empty($marking_code_item['marking_code'])) {
                    continue;
                }
                $marking_code_item['order_id'] = $order_id;
                $marking_code_item['order_item_id'] = $item_id;
                $data[] = $marking_code_item;
            }
        }

        // Remove previous codes for correct renewal
        db_query('DELETE FROM ?:order_digital_marking_codes WHERE order_id = ?i', $order_id);

        if (empty($data)) {
            return;
        }
        db_query('INSERT INTO ?:order_digital_marking_codes ?m', $data);
    }

    /**
     * Recalculates mark codes for order items and removes redundant.
     *
     * @param array $order    Order data
     * @param int   $order_id Order identifier
     * @param bool  $notify   Whether to send notification that changes are made
     *
     * @return void
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint
     */
    public function recalculateMarkingCodesForOrder(array $order, $order_id, $notify = false)
    {
        if (empty($order_id) || empty($order['products'])) {
            return;
        }

        $marking_code_items = db_get_hash_single_array(
            'SELECT code_id, order_item_id FROM ?:order_digital_marking_codes WHERE order_id = ?i',
            ['code_id', 'order_item_id'],
            $order_id
        );
        $marking_order_items = array_count_values($marking_code_items);

        $items_to_remove = [];
        $send_notification = false;
        foreach ($marking_order_items as $marking_order_item_id => $count) {
            if (!in_array($marking_order_item_id, array_keys($order['products']))) {
                $items_to_remove[] = $marking_order_item_id;
                continue;
            }

            // Check amount difference and remove redundant marking codes
            if (
                empty($order['products'][$marking_order_item_id]['amount'])
                || $order['products'][$marking_order_item_id]['amount'] >= $count
            ) {
                continue;
            }
            $amount_difference = $count - $order['products'][$marking_order_item_id]['amount'];

            $current_item_codes = [];
            foreach ($marking_code_items as $_code_item_id => $_order_item_id) {
                if ((int) $_order_item_id !== $marking_order_item_id) {
                    continue;
                }
                $current_item_codes[] = $_code_item_id;
            }

            sort($current_item_codes);
            $codes_to_delete = array_slice($current_item_codes, -$amount_difference);

            if (empty($codes_to_delete)) {
                continue;
            }
            db_query(
                'DELETE FROM ?:order_digital_marking_codes WHERE code_id IN (?n)',
                $codes_to_delete
            );
            $send_notification = $notify;
        }

        // Remove marking codes for deleted order items
        if (!empty($items_to_remove)) {
            db_query('DELETE FROM ?:order_digital_marking_codes WHERE order_id = ?i AND order_item_id IN (?n)', $order_id, $items_to_remove);
            $send_notification = $notify;
        }

        if (!$send_notification) {
            return;
        }
        fn_set_notification(NotificationSeverity::WARNING, __('warning'), __('rus_taxes.check_mark_codes_notification'));
    }
}
