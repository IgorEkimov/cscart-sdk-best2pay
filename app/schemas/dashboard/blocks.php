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
 * 'copyright.txt' FILE PROVIDED WITH THIS DISTRIBUTION PACKAGE.            *
 ****************************************************************************/

use Tygh\Enum\DashboardSections;
use Tygh\Enum\ObjectStatuses;
use Tygh\Enum\UserTypes;

defined('BOOTSTRAP') or die('Access denied');

/**
 * This schema contains all blocks on dashboard in administration panel
 *
 * @var array{
 *     array-key: array{
 *         array-key: array{
 *              id: string,
 *              position: int,
 *              dispatch: string,
 *              title: string,
 *              title_button?: array{
 *                  name: string,
 *                  href: string
 *              },
 *              content_data_function: string,
 *              is_selected_date?: bool,
 *              use_price_for_number?: bool,
 *         }
 *     }
 * } $schema
 */

$schema = [
    DashboardSections::PRIMARY => [
        'sales' => [
            'id' => 'analytics_card_sales',
            'is_selected_date' => true,
            'use_price_for_number' => true,
            'position' => 10,
            'dispatch' => 'sales_reports.view',
            'title' => __('dashboard.analytics_card.sales'),
            'title_button' => [
                'name' => __('dashboard.view_reports'),
                'href' => 'sales_reports.view'
            ],
            'content_data_function' => 'getSalesBlockData'
        ],
        'products' => [
            'id' => 'analytics_card_products',
            'title' => __('dashboard.analytics_card.active_products'),
            'position' => 20,
            'dispatch' => 'products.manage',
            'title_button' => [
                'name' => __('dashboard.analytics_card.view_products'),
                'href' => 'products.manage&status=' . ObjectStatuses::ACTIVE
            ],
            'content_data_function' => 'getProductsBlockData'
        ]
    ],
    DashboardSections::SECONDARY => [
        'orders' => [
            'id' => 'analytics_card_orders',
            'is_selected_date' => true,
            'position' => 10,
            'title' => __('dashboard.analytics_card.orders'),
            'dispatch' => 'orders.manage',
            'title_button' => [
                'name' => __('dashboard.analytics_card.view_orders'),
                'href' => 'orders.manage'
            ],
            'content_data_function' => 'getOrdersBlockData'
        ],
        'orders_by_statuses' => [
            'id' => 'analytics_card_recent_orders',
            'dispatch' => 'orders.manage',
            'position' => 20,
            'title' => __('dashboard.analytics_card.recent_orders'),
            'title_button' => [
                'name' => __('dashboard.analytics_card.view_orders'),
                'href' => 'orders.manage'
            ],
            'resource_list_tabs' => [],
            'content_data_function' => 'getOrdersByStatusesBlockData'
        ]
    ],
    DashboardSections::TERTIARY => [
        'stores' => [
            'id' => 'analytics_card_stores',
            'title' => __('dashboard.analytics_card.stores'),
            'position' => 30,
            'dispatch' => 'companies.manage',
            'title_button' => [
                'name' => __('dashboard.analytics_card.view_stores'),
                'href' => 'companies.manage'
            ],
            'content_data_function' => 'getStoresBlockData'
        ],
        'customers' => [
            'id' => 'analytics_card_registered_customers',
            'title' => __('dashboard.analytics_card.registered_customers'),
            'position' => 40,
            'dispatch' => 'profiles.manage',
            'title_button' => [
                'name' => __('dashboard.analytics_card.view_customers'),
                'href' => 'profiles.manage?user_type=' . UserTypes::CUSTOMER
            ],
            'content_data_function' => 'getCustomersBlockData'
        ],
        'logs' => [
            'id' => 'analytics_card_activity',
            'title' => __('dashboard.analytics_card.activity'),
            'position' => 50,
            'dispatch' => 'logs.manage',
            'title_button' => [
                'name' => __('dashboard.view_all'),
                'href' => 'logs.manage'
            ],
            'content_data_function' => 'getLogsBlockData'
        ]
    ],
];

if (fn_allowed_for('MULTIVENDOR')) {
    $schema[DashboardSections::TERTIARY]['stores']['title'] = __('dashboard.analytics_card.vendors');
    $schema[DashboardSections::TERTIARY]['stores']['title_button']['name'] = __('dashboard.analytics_card.view_vendors');
    $schema[DashboardSections::TERTIARY]['current_balance'] = [
        'id' => 'analytics_card_vendor_current_balance',
        'title' => __('dashboard.analytics_card.vendor_current_balance'),
        'position' => 10,
        'title_button' => [
            'name' => __('dashboard.analytics_card.view_accounting'),
            'href' => 'companies.balance'
        ],
        'use_price_for_number' => true,
        'dispatch' => 'companies.manage',
        'content_data_function' => 'getVendorBalanceBlockData'
    ];
    $schema[DashboardSections::TERTIARY]['vendors_with_sales'] = [
        'id' => 'analytics_card_vendors_with_sales',
        'is_selected_date' => true,
        'position' => 20,
        'title' => __('dashboard.analytics_card.vendors_with_sales'),
        'dispatch' => 'companies.manage',
        'content_data_function' => 'getVendorsWithSalesBlockData'
    ];
}

return $schema;
