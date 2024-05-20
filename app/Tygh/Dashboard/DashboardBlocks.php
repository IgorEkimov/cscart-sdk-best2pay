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

namespace Tygh\Dashboard;

use Tygh\Enum\ObjectStatuses;
use Tygh\Enum\ProductTracking;
use Tygh\Enum\UserTypes;
use Tygh\Enum\VendorStatuses;
use Tygh\Registry;
use Tygh\Tygh;

/**
 * Class DashboardBlocks list of all dashboard blocks from schema and fill content information.
 *
 * @psalm-type _contentData = array{
 *           id: string,
 *           position: int,
 *           dispatch: string,
 *           title: string,
 *           title_button?: array{
 *                    name: string,
 *                    href: string
 *           },
 *           content_data_function: string,
 *           is_selected_date?: bool,
 *           use_price_for_number?: bool,
 *           number?: float|int,
 *           number_dynamics?: float|int,
 *           preheader?: string,
 *           resource_list_tabs?: array{
 *                id: string,
 *                title?: string,
 *                content?: array{
 *                     array{
 *                       id: string,
 *                       title: string,
 *                       content: array{
 *                           array{
 *                               id: string,
 *                               name: string,
 *                               value: string,
 *                               description: string,
 *                               description_href: string,
 *                               use_price_for_value: bool,
 *                               href: string,
 *                               small_text: string,
 *                               label_id: string,
 *                               label_text: string,
 *                               label_class: string
 *                           }
 *                       }
 *                    }
 *                }
 *           },
 *           resource_list?: array{
 *               id: string,
 *               title?: string,
 *               content?: array{
 *                  array{
 *                      id: string,
 *                      name: string,
 *                      value: string,
 *                      description: string,
 *                      description_href: string
 *                  }
 *               }
 *           },
 *           graph?: array{
 *                content?: array{
 *                   array{
 *                       date: string,
 *                       prev: float,
 *                       cur: float,
 *                   }
 *                }
 *            }
 *       }
 *
 * @package Tygh\Dashboard
 */
class DashboardBlocks
{
    /**
     * @codingStandardsIgnoreStart
     * @var array $general_stats
     */
    public $general_stats = [];

    /**
     * @var array $orders_stat Order statistic
     */
    public $orders_stat = [];

    /**
     * @var array $orders Orders separated by statuses
     */
    public $orders = [];

    /**
     * @var array $order_statuses List of all order statuses
     */
    public $order_statuses = [];

    /**
     * @var array $graphs Information for sales and other graphs
     */
    public $graphs = [];

    /**
     * @var int $storefront_id Current storefront ID
     */
    public $storefront_id = 0;

    /**
     * @var int $runtime_company_id Current company ID
     */
    public $runtime_company_id = 0;

    /**
     * @var int $time_from Timestamp of the start period for analytics
     */
    public $time_from = 0;

    /**
     * @var int $time_to Timestamp of the end period for analytics
     */
    public $time_to = 0;

    /**
     * @var float $current_balance Vendor current balance
     */
    public $current_balance = 0;

    /**
     * @var array $dashboard_vendors_activity Information about vendors activity for administrators
     */
    public $dashboard_vendors_activity = [];

    /**
     * @var array $logs Last logs
     */
    public $logs = [];

    /**
     * @var string $preheader Information about selected period.
     */
    public $preheader = '';

    /**
     * @var array $schema List of all blocks for dashboard
     */
    public $schema = [];

    /**
     * Init class
     *
     * @param array $params List of data for dashboard blocks
     *
     * @return void
     */
    public function __construct($params)
    {
        foreach ($params as $key => $data) {
            if (isset($this->$key)) {
                $this->$key = $data;
            }
        }
        $date_format = Registry::get('settings.Appearance.calendar_date_format') === 'month_first' ? 'M d, Y' : 'd M Y';
        $this->preheader = date($date_format, $this->time_from) . ' - ' . date($date_format, $this->time_to);
        $this->schema = fn_get_schema('dashboard', 'blocks');
    }
    /** @codingStandardsIgnoreEnd */

    /**
     * Check blocks availability and fill content information
     *
     * @return array<string, array<string, array<string, string|float|array>>>
     *
     * @psalm-return array{
     *      array-key: array{
     *          array-key: _contentData
     *      }
     *  }
     */
    public function getDasboardBlocks()
    {
        $dashboard_blocks = [];
        foreach ($this->schema as $section => $blocks) {
            foreach ($blocks as $block_id => $block_data) {
                $block = [];
                if (
                    !empty($block_data['dispatch'])
                    && !fn_check_view_permissions($block_data['dispatch'], 'GET')
                ) {
                    continue;
                }

                if (!empty($block_data['content_data_function'])) {
                    if (method_exists($this, $block_data['content_data_function'])) {
                        $content_data = call_user_func([
                            $this,
                            $block_data['content_data_function']
                        ]);
                    } elseif (function_exists($block_data['content_data_function'])) {
                        $content_data = call_user_func($block_data['content_data_function']);
                    }
                    if (!empty($content_data)) {
                        $block = array_merge($block_data, $content_data);
                    }
                } else {
                    $block = $block_data;
                }

                fn_set_hook('get_dashboard_block_data', $block, $block_data, $this);

                if ($block) {
                    $dashboard_blocks[$section][$block_id] = $block;
                }
            }
            if (!empty($dashboard_blocks[$section])) {
                $dashboard_blocks[$section] = fn_sort_array_by_key($dashboard_blocks[$section], 'position');
            }
        }

        return $dashboard_blocks;
    }

    /**
     * Fill content information for block with sales statistics
     *
     * @return array<string, string|float|int>
     *
     * @psalm-return _contentData
     */
    private function getSalesBlockData()
    {
        $content_data = [
            'preheader' => $this->preheader
        ];

        if (!empty($this->orders_stat['orders_total'])) {
            $content_data['number'] = $this->orders_stat['orders_total']['totally_paid'] ?? 0;
            $content_data['number_dynamics'] = $this->orders_stat['diff']['sales'] ?? 0;
        }
        if (!empty($this->graphs)) {
            $content_data['graph']['content'] = [];
            foreach ($this->graphs as $chart => $graph) {
                if ($chart === 'dashboard_statistics_sales_chart') {
                    foreach ($graph as $date => $data) {
                        $content_data['graph']['content'][] = [
                            'date' => $date,
                            'prev' => $data['prev'],
                            'cur' => $data['cur']
                        ];
                    }
                }
            }
        }

        fn_set_hook('get_dashboard_sales_block_data', $content_data, $this);

        return $content_data;
    }

    /**
     * Fill content information for block with products statistics
     *
     * @return array<string, string|float|int>
     *
     * @psalm-return _contentData
     */
    private function getProductsBlockData()
    {
        $content_data = [
            'number' => $this->general_stats['products']['total_products'] ?? 0,
            'resource_list' => [
                'id' => 'analytics_card_products_resource_list',
                'title' => __('dashboard.analytics_card.products_details'),
                'content' => [
                    [
                        'id' => 'analytics_card_products_out_of_stock',
                        'name' => __('dashboard.analytics_card.out_of_stock'),
                        'href' => 'products.manage?amount_from=&amount_to=0&tracking[0]=' . ProductTracking::TRACK,
                        'value' => $this->general_stats['products']['out_of_stock_products'] ?? 0,
                        'value_href' => 'products.manage?amount_from=&amount_to=0&tracking[0]=' . ProductTracking::TRACK
                    ]
                ]
            ]
        ];

        fn_set_hook('get_dashboard_products_block_data', $content_data, $this);

        return $content_data;
    }

    /**
     * Fill content information for block with orders statistics
     *
     * @return array<string, string|float|int>
     *
     * @psalm-return _contentData
     */
    private function getOrdersBlockData()
    {
        $content_data = [
            'preheader' => $this->preheader
        ];
        if (!empty($this->orders_stat['orders'])) {
            if (!empty($this->orders_stat['prev_orders'])) {
                $orders_stat_diff_orders_percent = number_format(
                    count($this->orders_stat['prev_orders']) * 100 / count($this->orders_stat['orders'])
                );
            } else {
                $orders_stat_diff_orders_percent = '&infin;';
            }
            $content_data['number'] = count($this->orders_stat['orders']);
            $content_data['number_dynamics'] = $orders_stat_diff_orders_percent;
            $content_data['title_button'] = [
                'name' => __('dashboard.analytics_card.view_orders'),
                'href' => 'orders.manage?is_search=Y&storefront_id=' . $this->storefront_id
                    . '&period=C&time_from=' . $this->time_from
                    . '&time_to=' . $this->time_to
            ];
        }

        fn_set_hook('get_dashboard_orders_block_data', $content_data, $this);

        return $content_data;
    }

    /**
     * Fill content information for block with orders by statuses statistics
     *
     * @return array<string, string|float|int>
     *
     * @psalm-return _contentData
     */
    private function getOrdersByStatusesBlockData()
    {
        $date_format = Registry::get('settings.Appearance.date_format') . ' ' . Registry::get('settings.Appearance.time_format');
        $order_statuses = $this->order_statuses;
        $resource_list_tabs = [
            'id' => 'analytics_card_recent_orders_tabs',
            'content' => [
                [
                    'id' => 'analytics_card_recent_orders_tab_all',
                    'title' => __('all'),
                    'content' => array_map(static function ($order) use ($order_statuses, $date_format) {
                        $lower_status = fn_strtolower($order['status']);
                        return [
                            'id' => $order['order_id'],
                            'name' => __('order') . ' #' . $order['order_id'],
                            'value' => $order['total'],
                            'use_price_for_value' => true,
                            'href' => 'orders.details?order_id=' . $order['order_id'],
                            'description' => $order['lastname'] . ' ' . $order['firstname'],
                            'description_href' => !empty($order['user_id']) ? 'profiles.update?user_id=' . $order['user_id'] : '',
                            'small_text' => fn_date_format($order['timestamp'], $date_format),
                            'label_id' => $lower_status,
                            'label_text' => $order_statuses[$order['status']]['description'],
                            'label_class' => 'o-status-' . $lower_status
                        ];
                    }, $this->orders['all'])
                ]
            ]
        ];

        $iteration = 0;
        foreach ($order_statuses as $status) {
            if (empty($this->orders[$status['status']])) {
                continue;
            }

            $iteration++;
            $lower_status = fn_strtolower($status['status']);
            $resource_list_tabs['content'][$iteration] = [
                'id' => 'analytics_card_recent_orders_tab_' . $lower_status,
                'title' => $status['description'],
                'content' => array_map(static function ($order) use ($status, $date_format, $lower_status) {
                    return [
                        'id' => $order['order_id'],
                        'name' => __('order') . ' #' . $order['order_id'],
                        'value' => $order['total'],
                        'use_price_for_value' => true,
                        'href' => 'orders.details?order_id=' . $order['order_id'],
                        'description' => $order['lastname'] . ' ' . $order['firstname'],
                        'description_href' => !empty($order['user_id']) ? 'profiles.update?user_id=' . $order['user_id'] : '',
                        'small_text' => fn_date_format($order['timestamp'], $date_format),
                        'label_id' => $lower_status,
                        'label_text' => $status['description'],
                        'label_class' => 'o-status-' . $lower_status
                    ];
                }, $this->orders[$status['status']])
            ];
        }

        $content_data = [
            'resource_list_tabs' => $resource_list_tabs
        ];

        fn_set_hook('get_dashboard_orders_by_statuses_block_data', $content_data, $this);

        return $content_data;
    }

    /**
     * Fill content information for block with vendor balance
     *
     * @return ?array<string, string|float|int>
     *
     * @psalm-return _contentData|null
     */
    private function getVendorBalanceBlockData()
    {
        if (!$this->runtime_company_id) {
            return null;
        }

        $content_data = [
            'number' => $this->current_balance
        ];

        fn_set_hook('get_dashboard_vendor_balance_block_data', $content_data, $this);

        return $content_data;
    }

    /**
     * Fill content information for block with vendor activity statistic
     *
     * @return ?array<string, string|float|int>
     *
     * @psalm-return _contentData|null
     */
    private function getVendorsWithSalesBlockData()
    {
        if ($this->runtime_company_id) {
            return null;
        }

        $new_vendors_link = 'companies.manage?created_from=' . $this->time_from
            . '&created_to=' . $this->time_to
            . '&status=' . VendorStatuses::ACTIVE;
        $vendors_with_new_products_link = 'companies.manage?extend[]=products&new_products_from=' . $this->time_from
            . '&new_products_to=' . $this->time_to
            . '&status=' . VendorStatuses::ACTIVE;
        $vendors_not_logged = 'companies.manage?not_login_from=' . $this->time_from
            . '&not_login_to=' . $this->time_to
            . '&status=' . VendorStatuses::ACTIVE;
        $new_products_link = 'products.manage?period=C&status[]=' . ObjectStatuses::ACTIVE
            . '&time_from=' . $this->time_from
            . '&time_to=' . $this->time_to
            . '&company_status[]=' . VendorStatuses::ACTIVE;

        $content_data = [
            'preheader' => $this->preheader,
            'number' => $this->dashboard_vendors_activity['vendors_with_sales'] ?? 0,
            'title_button' => [
                'name' => __('dashboard.analytics_card.view_vendors'),
                'href' => 'companies.manage?sales_from=' . $this->time_from
                    . '&sales_to=' . $this->time_to
                    . '&status=' . VendorStatuses::ACTIVE
            ],
            'resource_list' => [
                'id' => 'analytics_card_products_resource_list',
                'title' => __('dashboard.analytics_card.vendor_activity'),
                'content' => [
                    [
                        'id' => 'analytics_card_vendors_with_sales_new_vendors',
                        'name' => __('dashboard.analytics_card.new_vendors'),
                        'href' => $new_vendors_link,
                        'value' => $this->dashboard_vendors_activity['new_vendors'] ?? 0,
                        'value_href' => $new_vendors_link
                    ],
                    [
                        'id' => 'analytics_card_vendors_with_sales_vendors_with_new_products',
                        'name' => __('dashboard.analytics_card.vendors_with_new_products'),
                        'href' => $vendors_with_new_products_link,
                        'value' => $this->dashboard_vendors_activity['vendors_with_new_products'] ?? 0,
                        'value_href' => $vendors_with_new_products_link
                    ],
                    [
                        'id' => 'analytics_card_vendors_with_sales_vendors_not_logged',
                        'name' => __('dashboard.analytics_card.vendors_not_logged'),
                        'href' => $vendors_not_logged,
                        'value' => $this->dashboard_vendors_activity['vendors_not_logged'] ?? 0,
                        'value_href' => $vendors_not_logged
                    ],
                    [
                        'id' => 'analytics_card_vendors_with_sales_new_products',
                        'name' => __('dashboard.analytics_card.new_products'),
                        'href' => $new_products_link,
                        'value' => $this->dashboard_vendors_activity['new_products'] ?? 0,
                        'value_href' => $new_products_link
                    ],
                ]
            ]
        ];

        fn_set_hook('get_dashboard_vendor_with_sales_block_data', $content_data, $this);

        return $content_data;
    }

    /**
     * Fill content information for block with companies or vendors statistics
     *
     * @return ?array<string, string|float|int>
     *
     * @psalm-return _contentData|null
     */
    private function getStoresBlockData()
    {
        $auth = Tygh::$app['session']['auth'];
        $content_data = null;
        if (
            $auth['user_type'] === UserTypes::ADMIN
            && $this->general_stats['companies']
            && !$this->runtime_company_id
        ) {
            $content_data['number'] = $this->general_stats['companies']['total_companies'] ?? 0;
        }

        fn_set_hook('get_dashboard_stores_block_data', $content_data, $this);

        return $content_data;
    }

    /**
     * Fill content information for block with users statistics
     *
     * @return ?array<string, string|float|int>
     *
     * @psalm-return _contentData|null
     */
    private function getCustomersBlockData()
    {
        $content_data = null;
        if ($this->general_stats['customers']) {
            $content_data['number'] = $this->general_stats['customers']['registered_customers'] ?? 0;
        }

        fn_set_hook('get_dashboard_customers_block_data', $content_data, $this);

        return $content_data;
    }

    /**
     * Fill content information for block with last logs
     *
     * @return array<string, string|float|int>
     *
     * @psalm-return _contentData
     */
    private function getLogsBlockData()
    {
        $date_format = Registry::get('settings.Appearance.date_format') . ' ' . Registry::get('settings.Appearance.time_format');
        $content_data = [
            'resource_list' => [
                'id' => 'analytics_card_products_resource_list',
                'content' => array_map(static function ($log, $log_id) use ($date_format) {

                    $type = 'log_type_' . $log['type'];

                    $item_name = __($type);
                    if (!empty($log['action'])) {
                        $item_name .= ' (' . __('log_action_' . $log['action']) . ')';
                    }

                    $item_description = $item_description_href = '';
                    switch ($log['type']) {
                        case 'users':
                            $item_description = $log['content']['user'] ?? '';
                            if (!empty($log['content']['id'])) {
                                $item_description_href = 'profiles.update?user_id=' . $log['content']['id'];
                            }
                            break;
                        case 'orders':
                            $item_name .= !empty($log['content']['status']) ? ' ' . $log['content']['status'] : '';
                            $item_description = __('order') . ' ' . $log['content']['order'];
                            $item_description_href = 'orders.details?order_id=' . $log['content']['id'];
                            break;
                        case 'products':
                            $item_description = $log['content']['product'];
                            $item_description_href = 'products.update?product_id=' . $log['content']['id'];
                            break;
                        case 'categories':
                            $item_description = $log['content']['category'];
                            $item_description_href = 'categories.update?category_id=' . $log['content']['id'];
                            break;
                    }

                    return [
                        'id' => 'analytics_card_activity_' . $log_id,
                        'name' => $item_name,
                        'value' => fn_date_format($log['timestamp'], $date_format),
                        'description' => $item_description,
                        'description_href' => $item_description_href
                    ];
                }, $this->logs, array_keys($this->logs))
            ],
        ];

        fn_set_hook('get_dashboard_logs_block_data', $content_data, $this);

        return $content_data;
    }
}
