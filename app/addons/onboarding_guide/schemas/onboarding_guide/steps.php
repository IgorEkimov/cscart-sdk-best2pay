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

return [
    'personalize_marketplace' => [
        'title' => fn_allowed_for('ULTIMATE')
            ? 'onboarding_guide.sb_personalize_your_marketplace_title'
            : 'onboarding_guide.personalize_your_marketplace_title',
        'template' => 'addons/onboarding_guide/steps/personalize_marketplace/personalize_marketplace.tpl',
        'position' => 10,
        'data_loader' => 'fn_onboarding_guide_get_personalize_step_data'
    ],
    'customize_storefront_design' => [
        'title' => 'onboarding_guide.make_changes_on_the_fly_title',
        'template' => 'addons/onboarding_guide/steps/customize_storefront_design.tpl',
        'position' => 20,
    ],
    'manage_sellers' => [
        'title' => 'onboarding_guide.manage_sellers',
        'template' => 'addons/onboarding_guide/steps/manage_sellers.tpl',
        'position' => 30,
        'visible' => ['fn_onboarding_guide_is_available', ['MULTIVENDOR']]
    ],
    'customize_vendor_panel' => [
        'title' => 'onboarding_guide.customize_vendor_panel_design',
        'template' => 'addons/onboarding_guide/steps/customize_vendor_panel_design.tpl',
        'position' => 40,
        'visible' => ['fn_onboarding_guide_is_available', ['MULTIVENDOR']]
    ],
    'manage_product_catalog' => [
        'title' => 'onboarding_guide.manage_products',
        'template' => 'addons/onboarding_guide/steps/manage_product_catalog.tpl',
        'position' => 50,
        'data_loader' => 'fn_onboarding_guide_get_manage_product_step_data'
    ],
    'setup_shippings' => [
        'title' => 'onboarding_guide.configure_shippings_title',
        'template' => 'addons/onboarding_guide/steps/setup_shipping.tpl',
        'position' => 55,
        'visible' => ['fn_onboarding_guide_is_available', ['ULTIMATE']]
    ],
    'payment_options' => [
        'title' => 'onboarding_guide.configure_payment_options',
        'template' => 'addons/onboarding_guide/steps/payment_options.tpl',
        'position' => 60,
        'data_loader' => 'fn_onboarding_guide_get_payment_options_step_data'
    ],
    'vendor_plan_commissions' => [
        'title' => 'onboarding_guide.set_up_commissions_and_fees',
        'template' => 'addons/onboarding_guide/steps/vendor_plan_commissions.tpl',
        'position' => 70,
        'visible' => ['fn_onboarding_guide_is_addon_available', ['vendor_plans']]
    ],
    'onboarding_process' => [
        'title' => 'onboarding_guide.preview_seller_onboarding',
        'template' => 'addons/onboarding_guide/steps/onboarding_process.tpl',
        'position' => 80,
        'visible' => ['fn_onboarding_guide_is_available', ['MULTIVENDOR']]
    ],
];
