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

/** @var string $mode */

if ($mode === 'processor' || $mode === 'update' || $mode === 'manage') {
    $send_receipt_schema = fn_get_schema('digital_marking', 'send_receipt');
    $send_receipt_variants = $send_receipt_schema['variants'] ?? [];

    foreach ($send_receipt_variants as $variant_name => $variant_params) {
        if (!empty($variant_params['all_payments'])) {
            continue;
        } else {
            if (isset($_REQUEST['processor_id']) || isset($_REQUEST['payment_id'])) {
                $processor_data = (isset($_REQUEST['processor_id']))
                    ? db_get_row('SELECT * FROM ?:payment_processors WHERE processor_id = ?i', $_REQUEST['processor_id'])
                    : fn_get_processor_data($_REQUEST['payment_id']);

                if (
                    !empty($processor_data['processor_script'])
                    && !empty($variant_params['allowed_processor_scripts'])
                    && is_array($variant_params['allowed_processor_scripts'])
                    && in_array($processor_data['processor_script'], $variant_params['allowed_processor_scripts'])
                ) {
                    continue;
                }

                unset($send_receipt_variants[$variant_name]);
            } else {
                unset($send_receipt_variants[$variant_name]);
            }
        }
    }

    Tygh::$app['view']->assign('send_receipt_variants', $send_receipt_variants);
    Tygh::$app['view']->assign('send_receipt_types', $send_receipt_schema['types'] ?? []);
}
