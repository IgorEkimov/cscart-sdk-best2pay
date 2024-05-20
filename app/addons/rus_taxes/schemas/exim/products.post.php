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

include_once __DIR__ . '/products.functions.php';

/** @var array $schema */
$schema['export_fields']['Payment object (tag 1212)'] = [
    'db_field'    => 'fiscal_data_1212',
    'process_put' => ['fn_rus_taxes_set_payment_object', '#row', 'fiscal_data_1212'],
];

$schema['export_fields']['Is fur ware'] = [
    'db_field'    => 'mark_code_type',
    'process_put' => ['fn_rus_taxes_set_mark_code_type', '#row', 'mark_code_type'],
];

return $schema;
