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

use Tygh\Enum\UserTypes;

defined('BOOTSTRAP') or die('Access denied');

/**
 * @var array $request
 */
$request = $_REQUEST;

/**
 * @var array $schema
 */
$schema['companies.manage']['vendor_administrators'] = [
    'href' => 'profiles.manage?user_type=' . UserTypes::VENDOR,
    'text' => __('actions.vendor_administrators'),
    'text_mobile' => __('actions.vendor_administrators_mobile'),
    'position' => 100
];

$schema['companies.manage']['invite_vendors'] = [
    'href' => 'companies.invite',
    'text' => __('actions.invite_vendors'),
    'title' => __('invite_vendors_title'),
    'class' => 'cm-dialog-opener',
    'position' => 200
];

if (fn_check_permissions('exim', 'import', 'admin', 'POST')) {
    $schema['companies.manage']['import'] = [
        'href' => 'exim.import?section=vendors',
        'text' => __('actions.import'),
        'position' => 300
    ];
}

$schema['companies.manage']['export'] = [
    'href' => 'exim.export?section=vendors',
    'text' => __('actions.export'),
    'position' => 400
];

$schema['companies.manage']['invitations'] = [
    'href' => 'companies.invitations',
    'text' => __('actions.companies_invitations'),
    'position' => 500
];

if (
    !empty($request['user_type'])
    && (UserTypes::isCustomer($request['user_type']) || UserTypes::isAdmin($request['user_type']))
) {
    $schema['profiles.manage']['vendor_administrators'] = [
        'href' => 'profiles.manage?user_type=' . UserTypes::VENDOR,
        'text' => __('actions.vendor_administrators'),
        'position' => 100
    ];
}

return $schema;
