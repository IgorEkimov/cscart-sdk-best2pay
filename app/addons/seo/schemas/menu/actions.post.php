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

/** @var array $schema */
$schema['seo_rules.manage']['seo.redirects_manager'] = [
    'href'     => 'seo_redirects.manage',
    'text'     => __('seo.actions.redirects_manager'),
    'position' => 100
];

$schema['seo_rules.manage']['seo_robots'] = [
    'href'     => 'robots.manage',
    'text'     => __('actions.seo_robots'),
    'position' => 200
];

return $schema;
