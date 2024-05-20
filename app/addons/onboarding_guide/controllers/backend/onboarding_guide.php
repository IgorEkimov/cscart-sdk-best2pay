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


use Tygh\Addons\OnboardingGuide\OnboardingGuide;
use Tygh\Enum\Addons\OnboardingGuide\StepStatusEnum;
use Tygh\BlockManager\Layout;
use Tygh\Enum\ObjectStatuses;
use Tygh\Enum\SiteArea;
use Tygh\Enum\UserTypes;
use Tygh\Enum\YesNo;
use Tygh\Registry;
use Tygh\Settings;
use Tygh\Themes\Styles;
use Tygh\Themes\Themes;
use Tygh\Tygh;
use Tygh\Api\Response;
use Tygh\Enum\NotificationSeverity;

defined('BOOTSTRAP') or die('Access denied');

/** @var string $mode */
/** @var array $auth */

if ($auth['user_type'] !== UserTypes::ADMIN || $auth['is_root'] !== YesNo::YES) {
    return [CONTROLLER_STATUS_DENIED];
}

/** @psalm-suppress PossiblyUndefinedArrayOffset */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($mode === 'step_complete') {
        /** @var \Tygh\SmartyEngine\Core $view */
        $view = Tygh::$app['view'];

        /** @var string $step_id */
        $step_id = $_REQUEST['step_id'];
        $result = OnboardingGuide::saveStepStatus($step_id, StepStatusEnum::COMPLETED);
        $is_success = !empty($result);

        if ($is_success) {
            Tygh::$app['ajax']->assign('onboarding_guide_progress', $result);
        }

        Tygh::$app['ajax']->assign('is_success', $is_success);

        $view->assign([
            'onboarding_guide_steps'    => OnboardingGuide::getSteps(),
            'onboarding_guide_progress' => OnboardingGuide::calculateProgress()
        ]);

        Registry::set('runtime.root_template', 'addons/onboarding_guide/components/progress.tpl');

        return [CONTROLLER_STATUS_OK];
    } elseif ($mode === 'step_close') {
        /** @var string $step_id */
        $step_id = $_REQUEST['step_id'];
        $result = OnboardingGuide::saveStepStatus($step_id, StepStatusEnum::CLOSED);
        $is_success = !empty($result);

        if ($is_success) {
            Tygh::$app['ajax']->assign('onboarding_guide_progress', $result);
        }

        Tygh::$app['ajax']->assign('is_success', $is_success);
    } elseif ($mode === 'action_complete') {
        /** @var string $step_id */
        $step_id = $_REQUEST['step_id'];

        /** @var string $action */
        $action = $_REQUEST['action'];

        $completed_actions = OnboardingGuide::saveStepAction($step_id, $action);

        Tygh::$app['ajax']->assign('is_success', true);
        Tygh::$app['ajax']->assign('completed_actions', $completed_actions);
    } elseif ($mode === 'onboarding_dismiss') {
        OnboardingGuide::dismiss();
        Tygh::$app['ajax']->assign('is_success', true);
    } elseif ($mode === 'onboarding_restart') {
        OnboardingGuide::restart();
        Tygh::$app['ajax']->assign('is_success', true);
    } elseif ($mode === 'update_theme') {
        if (empty($_REQUEST['new_theme_name']) || empty($_REQUEST['old_theme_name']) || empty($_REQUEST['style'])) {
            return [Response::STATUS_BAD_REQUEST];
        }

        /** @var string $old_theme_name */
        $old_theme_name = $_REQUEST['old_theme_name'];

        /** @var string $new_theme_name */
        $new_theme_name = $_REQUEST['new_theme_name'];

        /** @var string $new_theme_style */
        $new_theme_style = $_REQUEST['style'];

        $company_id = fn_get_runtime_company_id();

        /** @var \Tygh\Storefront\Repository $storefront_repository */
        $storefront_repository = Tygh::$app['storefront.repository'];
        /** @var \Tygh\Storefront\Storefront $storefront */
        $storefront = Tygh::$app['storefront'];
        $storefront->theme_name = $new_theme_name;
        $storefront_repository->save($storefront);

        $new_theme_layout = Layout::instance(0, [], $storefront->storefront_id)->getList([
            'theme_name' => $storefront->theme_name,
            'is_default' => true,
        ]);
        $new_theme_layout = reset($new_theme_layout);
        $new_theme_styles_factory = Styles::factory($new_theme_name);

        if ($old_theme_name === $new_theme_name) {
            $old_theme_style = $new_theme_styles_factory->getStyle($new_theme_layout['layout_id']);
            $old_theme_layout = $new_theme_layout;
        } else {
            $old_theme_layout = Layout::instance(0, [], $storefront->storefront_id)->getList([
                'theme_name' => $old_theme_name,
                'is_default' => true,
            ]);
            $old_theme_layout = reset($old_theme_layout);
            $old_theme_style = Styles::factory($old_theme_name)->getStyle($old_theme_layout['layout_id']);
        }

        $theme = Themes::factory(fn_get_theme_path('[theme]', SiteArea::STOREFRONT));
        $theme_manifest = $theme->getManifest();

        if (empty($theme_manifest['converted_to_css'])) {
            $new_theme_styles_factory->setStyle($new_theme_layout['layout_id'], $new_theme_style);
        }

        // Copy logos from old theme
        $old_theme_logos = fn_get_logos(null, $old_theme_layout['layout_id'], $old_theme_style, $storefront->storefront_id);

        if (isset($old_theme_logos['theme'])) {
            fn_update_logo([
                'style_id' => $new_theme_style,
                'layout_id' => $new_theme_layout['layout_id'],
                'image_path' => $old_theme_logos['theme']['image']['absolute_path'],
                'image_alt' => $old_theme_logos['theme']['image']['alt'],
                'type' => 'theme',
            ], $company_id, $storefront->storefront_id);
        }

        if (isset($old_theme_logos['mail'])) {
            fn_update_logo([
                'style_id' => $new_theme_style,
                'layout_id' => $new_theme_layout['layout_id'],
                'image_path' => $old_theme_logos['mail']['image']['absolute_path'],
                'image_alt' => $old_theme_logos['mail']['image']['alt'],
                'type' => 'mail'
            ], $company_id, $storefront->storefront_id);
        }

        // We need to re-init layout
        fn_init_layout(['s_layout' => $new_theme_layout['layout_id']]);

        // Delete compiled CSS file
        fn_clear_cache('assets');
        fn_clear_cache('registry');
        fn_clear_template_cache();

        return [CONTROLLER_STATUS_OK, 'onboarding_guide.onboarding_guide'];
    } elseif ($mode === 'update_logos') {
        fn_attach_image_pairs('logo', 'logos');
    } elseif ($mode === 'update_logos_alt') {
        $logo_data = (array) $_REQUEST['logo_image_data'];
        fn_update_image_pairs([], [], $logo_data, 0, 'logos');

        Tygh::$app['ajax']->assign('is_success', true);
    } elseif ($mode === 'update_company_settings') {
        $company_settings = (array) $_REQUEST['company_settings'];
        /** @psalm-suppress InvalidScalarArgument */
        foreach ($company_settings as $setting_name => $value) {
            Settings::instance()->updateValue($setting_name, $value);
        }
    } elseif ($mode === 'change_payment_option') {
        if (empty($_REQUEST['payment_option'])) {
            return [Response::STATUS_BAD_REQUEST];
        }

        $payment_option = $_REQUEST['payment_option'];
        $payment_options = fn_get_schema('onboarding_guide', 'payment_options_multivendor');

        if (empty($payment_options) || empty($payment_options[$payment_option])) {
            return;
        }

        foreach ($payment_options[$payment_option]['addons'] as $addons) {
            foreach ($addons as $addon_name => $required_status) {
                $status = Registry::get('addons.' . $addon_name . '.status');

                if (empty($status) && $required_status === ObjectStatuses::ACTIVE) {
                    fn_onboarding_guide_install_addon($addon_name);
                }

                if ($status !== $required_status) {
                    fn_update_addon_status($addon_name, $required_status);
                }

                if (!isset($payment_options[$payment_option]['processors_scripts'][$addon_name])) {
                    continue;
                }

                fn_onboarding_guide_install_payment($payment_options[$payment_option]['processors_scripts'][$addon_name]);
            }
        }

        return [CONTROLLER_STATUS_OK, 'onboarding_guide.onboarding_guide'];
    }

    return [CONTROLLER_STATUS_NO_CONTENT];
}

if ($mode === 'onboarding_guide') {
    Tygh::$app['view']->assign([
        'onboarding_guide_steps' => OnboardingGuide::getSteps(),
        'onboarding_guide_progress' => OnboardingGuide::calculateProgress()
    ]);
} elseif ($mode === 'customize_storefront') {
    /** @var string $type */
    $type = $_REQUEST['type'];

    /** @var \Tygh\Storefront\Storefront $storefront */
    $storefront = Tygh::$app['storefront'];

    $theme_name = fn_get_theme_path('[theme]', 'C');
    $layout = Layout::instance()->getDefault($theme_name);
    $layout_id = $layout['layout_id'];
    $storefront_id = $storefront->storefront_id;

    return [
        CONTROLLER_STATUS_REDIRECT,
        "customization.update_mode?type={$type}&status=enable&s_layout={$layout_id}&s_storefront={$storefront_id}&to_storefront=1"
    ];
} elseif ($mode === 'view_checkout') {
    $cart_products = fn_get_cart_products($auth['user_id']);

    if (
        empty($cart_products)
        || empty(reset($cart_products)['product'])
    ) {
        $customer_auth = & Tygh::$app['session']['customer_auth'];

        if (empty($customer_auth)) {
            $customer_auth = fn_fill_auth([], [], false, SiteArea::STOREFRONT);
        }

        $cart = & Tygh::$app['session']['cart'];
        if (isset($cart['vendor_id'])) {
            $cart['vendor_id'] = fn_onboarding_guide_get_last_vendor_id() ?? $cart['vendor_id'];
        }

        if (empty($cart)) {
            fn_clear_cart($cart);
        }

        $product = fn_onboarding_guide_get_last_product();

        if (empty($product)) {
            fn_set_notification(
                NotificationSeverity::WARNING,
                __('onboarding_guide.missing_products'),
                __('onboarding_guide.create_at_least_one_product_to_proceed_to_checkout'),
                'K'
            );

            return [CONTROLLER_STATUS_REDIRECT, 'index.index'];
        }

        $product_id = $product['product_id'];

        $product = [
            $product_id => ['amount' => 1],
        ];

        fn_add_product_to_cart($product, $cart, $customer_auth);
        fn_save_cart_content($cart, $auth['user_id']);
    }

    $return_url = 'profiles.act_as_user?user_id=' . $auth['user_id'] . '&area=C&redirect_url=checkout.checkout';

    return [CONTROLLER_STATUS_REDIRECT, $return_url];
} elseif ($mode === 'open_vendor_panel') {
    /** @var string $redirect */
    $redirect = $_REQUEST['redirect'];

    if (!$redirect) {
        return [Response::STATUS_BAD_REQUEST];
    }

    if ($redirect === 'products.add') {
        [$active_categories,] = fn_get_categories(['status' => ObjectStatuses::ACTIVE]);

        if (empty($active_categories)) {
            fn_update_category(['category' => 'Products without category'], 0, DESCR_SL);
        }
    }

    $company_id = fn_onboarding_guide_get_last_vendor_id();
    $redirect_url = "profiles.login_as_vendor?company_id={$company_id}&redirect_url=" . urlencode($redirect);

    return [CONTROLLER_STATUS_REDIRECT, $redirect_url];
} elseif ($mode === 'preview_last_product') {
    $product = fn_onboarding_guide_get_last_product();

    if (empty($product)) {
        fn_set_notification(NotificationSeverity::WARNING, __('onboarding_guide.missing_products'), __('onboarding_guide.missing_products'), 'K');
        return [CONTROLLER_STATUS_REDIRECT, 'index.index'];
    }

    $product_id = $product['product_id'];
    $product_data = (array) fn_get_product_data($product_id, $auth, DESCR_SL, '', true, true, true, true, false, false, true);

    $storefront_repository = Tygh::$app['storefront.repository'];
    $storefront = $storefront_repository->findByCompanyId($product_data['company_id']);
    $storefront = empty($storefront) ? $storefront_repository->findDefault() : $storefront;
    $default_language = Registry::get('settings.Appearance.frontend_default_language');

    $preview_url = fn_get_preview_url(
        "products.view?product_id={$product_id}&storefront_id={$storefront->storefront_id}",
        $product_data,
        $auth['user_id'],
        $default_language
    );

    return [CONTROLLER_STATUS_REDIRECT, $preview_url, SiteArea::STOREFRONT];
} elseif ($mode === 'restart') {
    OnboardingGuide::restart();

    return [CONTROLLER_STATUS_REDIRECT, 'index.index'];
}
