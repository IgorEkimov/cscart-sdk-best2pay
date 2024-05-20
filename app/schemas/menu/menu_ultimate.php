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

$schema['top']['settings']['items']['Stores'] = [
    'href'     => 'settings.manage?section_id=Stores',
    'position' => 410,
    'type'     => 'setting',
];

$schema['top']['administration']['items']['stores'] = [
    'href'     => 'companies.manage',
    'position' => 1350,
    'title'    => __('storefronts'),
    'icon' => 'shopping_cart',
];

if (!fn_check_change_storefront_permission()) {
    unset($schema['top']['settings']['items']['Shippings'], $schema['top']['settings']['items']['Stores'], $schema['top']['settings']['items']['Upgrade_center']);
}

return $schema;
