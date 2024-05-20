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

if (!defined('BOOTSTRAP')) { die('Access denied'); }

Tygh::$app->register(new ServiceProvider());

fn_register_hooks(
    'add_to_cart_before_select_product_simple_data',
    'add_to_cart',
    'create_order_details',
    'update_order',
    'delete_order',
    'change_order_status_pre',
    'get_product_fields',
    'get_products'
);
