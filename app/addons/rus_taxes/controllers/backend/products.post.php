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

use Tygh\Registry;

defined('BOOTSTRAP') or die('Access denied');

if ($mode === 'manage' || $mode === 'update' || $mode === 'add' || $mode === 'm_update') {
    $fiscal_data_1212_objects = fn_get_schema('digital_marking', 'fiscal_data_1212_names');

    Tygh::$app['view']->assign('fiscal_data_1212_objects', $fiscal_data_1212_objects);

    if ($mode === 'update' || $mode === 'add') {
        Registry::set(
            'navigation.tabs.online_cash_registers',
            [
                'title' => __('online_cash_registers'),
                'js'    => true,
            ]
        );
    }

    if ($mode === 'm_update') {
        $field_names = Tygh::$app['view']->getTemplateVars('field_names');

        if (isset($field_names['fiscal_data_1212'])) {
            $field_names['fiscal_data_1212'] = __('payment_object');
        }

        if (isset($field_names['mark_code_type'])) {
            $field_names['mark_code_type'] = __('is_fur_ware');
        }

        Tygh::$app['view']->assign('field_names', $field_names);
    }
}
