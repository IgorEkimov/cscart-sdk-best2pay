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
use Tygh\Enum\UserTypes;

defined('BOOTSTRAP') or die('Access denied');

/*
    Every item can has any additional attributes.
    The base HTML struct of menu item is:
        <li class="some classes">
            <a href="some.html">Title</a>
        </li>

    So you can use the following array structure to specify your attrs:
    'addons' => array(
        'title' => __('addons_title'),
        'href' => 'addons.manage',
        'position' => 100,
        'attrs' => array(
            'class' => 'test-addon-class', // Classes for <li>
            'main' => array( // Attributes for <li>
                'custom-li-attr' => 'my-li-attr',
            ),
            'class_href' => 'test-addon-class', // Classes for <a>
            'href' => array( // Attributes for <a>
                'custom-a-attr' => 'my-a-attr',
            ),
        ),
    ),

    As a result you will get the following HTML code:
    <li class="some classes test-addon-class" custom-li-attr="my-li-attr">
        <a href="some.html" custom-a-attr="my-a-attr">Title</a>
    </li>
*/

/**
 * @var array<string, array<string, array>> $schema
 */
$schema = [
    'top' => [
        'settings' => [
            'items' => [
                'General' => [
                    'href' => 'settings.manage?section_id=General',
                    'position' => 100,
                    'type' => 'setting',
                ],
                'Appearance' => [
                    'href' => 'settings.manage?section_id=Appearance',
                    'position' => 200,
                    'type' => 'setting',
                ],
                'Appearance_divider' => [
                    'type' => 'divider',
                    'position' => 300,
                ],
                'Company' => [
                    'href' => 'settings.manage?section_id=Company',
                    'position' => 400,
                    'type' => 'setting',
                ],
                'Checkout' => [
                    'href' => 'settings.manage?section_id=Checkout',
                    'position' => 500,
                    'type' => 'setting',
                ],
                'Emails' => [
                    'href' => 'settings.manage?section_id=Emails',
                    'position' => 700,
                    'type' => 'setting',
                ],
                'Thumbnails' => [
                    'href' => 'settings.manage?section_id=Thumbnails',
                    'position' => 800,
                    'type' => 'setting',
                ],
                'Sitemap' => [
                    'href' => 'settings.manage?section_id=Sitemap',
                    'position' => 900,
                    'type' => 'setting',
                ],
                'Upgrade_center' => [
                    'href' => 'settings.manage?section_id=Upgrade_center',
                    'position' => 1000,
                    'type' => 'setting',
                ],
                'Upgrade_center_divider' => [
                    'type' => 'divider',
                    'position' => 1100,
                ],
                'Security' => [
                    'href' => 'settings.manage?section_id=Security',
                    'position' => 1200,
                    'type' => 'setting',
                ],
                'Image_verification_divider' => [
                    'type' => 'divider',
                    'position' => 1400,
                ],
                'Logging' => [
                    'href' => 'settings.manage?section_id=Logging',
                    'position' => 1500,
                    'type' => 'setting',
                ],
                'Reports' => [
                    'href' => 'settings.manage?section_id=Reports',
                    'position' => 1600,
                    'type' => 'setting',
                ],
                'Reports_divider' => [
                    'position' => 1610,
                    'type' => 'divider',
                ],
                'settings_wizard' => [
                    'href'     => 'settings_wizard.view',
                    'position' => 1700,
                    'type' => 'setting',
                    'title'    => __('settings_wizard'),
                ],
            ],
            'position' => 700,
            'icon' => 'gear',
        ],
        'administration' => [
            'title' => __('settings'),
            'href'  => 'administration.view',
            'items' => [
                'store_mode' => [
                    'position' => 100,
                    'type' => 'title',
                    'title' => __('account'),
                    'href' => 'settings.change_store_mode',
                    'icon' => 'user',
                    'attrs' => [
                        'class_href' => 'cm-dialog-opener cm-dialog-auto-size',
                        'href' => [
                            'data-ca-target-id' => 'store_mode_dialog',
                        ],
                    ],
                ],
                'upgrade_center' => [
                    'href' => 'upgrade_center.manage',
                    'position' => 200,
                    'icon' => 'cloud_download',
                ],
                'languages' => [
                    'href' => 'languages.translations',
                    'title' => __('texts_languages'),
                    'position' => 300,
                    'icon' => 'globe',
                ],
                'import_data' => [
                    'href' => 'exim.import?section=products',
                    'alt' => 'import_presets.manage',
                    'position' => 400,
                    'icon' => 'download',
                ],
                'export_data' => [
                    'href' => 'exim.export?section=products',
                    'position' => 500,
                    'icon' => 'upload',
                ],
                'storage' => [
                    'href' => 'datakeeper.manage',
                    'alt' => 'file_editor.manage,tools.view_changes,storage.cdn',
                    'position' => 600,
                    'icon' => 'hdd',
                ],
                'settings_general' => [
                    'href' => 'settings.manage?section_id=General',
                    'alt' => 'administration.view',
                    'position' => 1000,
                    'icon' => 'gear',
                ],
                'payment_methods' => [
                    'href' => 'payments.manage',
                    'position' => 1100,
                    'icon' => 'credit_card',
                ],
                'shipping_methods' => [
                    'href' => 'shippings.manage',
                    'alt' => 'destinations.manage,countries.manage,states.manage,cities.manage',
                    'position' => 1200,
                    'icon' => 'truck',
                ],
                'taxes' => [
                    'href' => 'taxes.manage',
                    'position' => 1300,
                    'icon' => 'briefcase',
                ],
                'profile_fields' => [
                    'href' => 'profile_fields.manage',
                    'position' => 1400,
                    'icon' => 'list_alt',
                ],
                'currencies' => [
                    'href'     => 'currencies.manage',
                    'position' => 1410,
                    'icon'     => 'money',
                ],
                'statuses_management' => [
                    'href'     => 'statuses.manage&type=' . STATUSES_ORDER,
                    'position' => 1450,
                    'icon'     => 'tasks',
                ],
                'notifications' => [
                    'href' => 'notification_settings.manage?receiver_type=' . UserTypes::CUSTOMER,
                    'alt' => 'email_templates.manage,internal_templates.manage,documents.manage',
                    'position' => 1500,
                    'icon' => 'bell',
                ],
                'logs' => [
                    'href' => 'logs.manage',
                    'position' => 1600,
                    'icon' => 'list_ul',
                ],
                'sync_data_manage' => [
                    'href' => 'sync_data.manage',
                    'title' => __('sync_data'),
                    'position' => 1700,
                    'icon' => 'exchange',
                ],
            ],
            'position' => 600,
            'icon' => 'gear',
        ],
        'addons' => [
            'items' => [
                'manage_addons' => [
                    'href' => 'addons.manage',
                    'position' => 10,
                ],
            ],
            'icon' => 'puzzle_piece',
        ],
    ],

    'central' => [
        'orders' => [
            'items' => [
                'view_orders' => [
                    'href' => 'orders.manage',
                    'alt' => 'order_management',
                    'position' => 100,
                    'title' => __('orders'),
                ],
                'sales_reports' => [
                    'href' => 'sales_reports.view',
                    'position' => 200,
                ],
                'shipments' => [
                    'href' => 'shipments.manage',
                    'position' => 400,
                ],
                'users_carts' => [
                    'href' => 'cart.cart_list',
                    'position' => 500,
                    'title' => __('users_carts_menu'),
                ],
            ],
            'position' => 100,
            'icon' => 'inbox',
        ],
        'products' => [
            'title' => __('products_menu_title'),
            'items' => [
                'products' => [
                    'href' => 'products.manage',
                    'alt' => 'product_options.inventory,product_options.exceptions,products.update,products.m_update,products.add',
                    'position' => 200,
                ],
                'categories' => [
                    'href' => 'categories.manage',
                    'position' => 250,
                ],
                'features' => [
                    'href' => 'product_features.manage',
                    'position' => 300,
                ],
                'filters' => [
                    'href' => 'product_filters.manage',
                    'position' => 400,
                ],
                'options' => [
                    'href' => 'product_options.manage',
                    'position' => 500,
                ],
            ],
            'position' => 200,
            'icon' => 'tag',
        ],
        'customers' => [
            'title' => __('customers_menu_title'),
            'items' => [
                'customers' => [
                    'href' => 'profiles.manage?user_type=C',
                    'alt' => 'profiles.update?user_type=C',
                    'position' => 100,
                ],
                'administrators' => [
                    'href' => 'profiles.manage?user_type=A',
                    'alt' => 'profiles.update?user_type=A',
                    'position' => 200,
                ],
            ],
            'position' => 300,
            'icon' => 'user',
        ],
        'website' => [
            'items' => [
                'themes' => [
                    'href' => 'themes.manage',
                    'alt' => 'block_manager,templates,tabs',
                    'position' => 100,
                ],
                'pages' => [
                    'href' => 'pages.manage?get_tree=multi_level',
                    'alt'  => 'pages.manage,pages.update,pages.add',
                    'position' => 200,
                ],
                'menus' => [
                    'href' => 'menus.manage',
                    'alt' => 'static_data.manage?section=A',
                    'position' => 400,
                ],
                'seo' => [
                    'href' => 'robots.manage',
                    'position' => 500,
                ],
                'sitemap' => [
                    'href' => 'sitemap.manage',
                    'position' => 1000,
                ],
            ],
            'position' => 500,
            'icon' => 'desktop',
        ],
        'marketing' => [
            'items' => [
                'promotions' => [
                    'href' => 'promotions.manage',
                    'position' => 100,
                ],
            ],
            'position' => 400,
            'icon' => 'bullhorn',
        ],
    ],
];

// Fallback
$schema['top']['administration']['items']['shippings_taxes'] = [
    'type' => 'title',
    'position' => 5300,
    'subitems' => [
        'localizations' => [
            'href' => 'localizations.manage',
            'position' => 600,
        ],
    ]
];
$schema['top']['administration']['items']['files'] = [
    'type' => 'title',
    'position' => 5990,
];
$schema['top']['administration']['items']['backup_restore'] = [
    'type' => 'title',
    'position' => 6000,
];
$schema['top']['administration']['items']['sync_data'] = [
    'type' => 'title',
    'position' => 6500,
];
$schema['top']['administration']['items']['file_changes_detector'] = [
    'type' => 'title',
    'position' => 6700,
];

$schema['top']['design'] = [
    'items' => [
    ],
    'position' => 800,
    'icon' => 'edit_sign',
];

if (empty(fn_get_schema('sync_data', 'sync_data'))) {
    unset($schema['top']['administration']['items']['sync_data']);
    unset($schema['top']['administration']['items']['sync_data_manage']);
}

if (Registry::get('config.tweaks.disable_localizations') === true) {
    unset($schema['top']['administration']['items']['shippings_taxes']['subitems']['localizations']);
}

if (Registry::get('settings.Appearance.email_templates') === 'old') {
    if (isset($schema['top']['design']['items']['email_templates'])) {
        unset($schema['top']['design']['items']['email_templates']);
    }
    if (isset($schema['top']['design']['items']['documents'])) {
        unset($schema['top']['design']['items']['documents']);
    }
}

return $schema;
