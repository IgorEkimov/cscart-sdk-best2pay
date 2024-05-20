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

use Tygh\Registry;

if (!Registry::get('runtime.company_id') || Registry::get('runtime.simple_ultimate')) {

    /** @var array $schema */
    $schema['em_subscribers.manage']['export'] = [
        'href'     => 'exim.export?section=subscribers',
        'text'     => __('actions.export'),
        'position' => 201
    ];

    $schema['em_subscribers.manage']['import'] = [
        'href'     => 'exim.import?section=subscribers',
        'text'     => __('actions.import'),
        'position' => 101
    ];
}

return $schema;
