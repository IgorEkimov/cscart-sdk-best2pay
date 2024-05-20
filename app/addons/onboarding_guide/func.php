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

use Tygh\BlockManager\Layout;
use Tygh\Enum\MoneyTransferTypes;
use Tygh\Enum\ObjectStatuses;
use Tygh\Registry;
use Tygh\Settings;
use Tygh\Addons\OnboardingGuide\OnboardingGuide;
use Tygh\Enum\Addons\OnboardingGuide\StepStatusEnum;

defined('BOOTSTRAP') or die('Access denied');

/**
 * @phpcs:disable SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint
 *
 * Loads step data
 *
 * @param string $step_id Step ID
 *
 * @return array<array-key, mixed>
 */
function fn_onboarding_guide_load_step_data(string $step_id): array
{
    $schema = fn_get_schema('onboarding_guide', 'steps');

    if (isset($schema[$step_id]['data_loader']) && is_callable($schema[$step_id]['data_loader'])) {
        return call_user_func($schema[$step_id]['data_loader']);
    }

    return [];
}

/**
 * Gets setting value
 *
 * @param string $setting Setting
 *
 * @return array<string, string|bool|array>|bool
 */
function fn_onboarding_guide_get_setting_data(string $setting)
{
    $settings = Settings::instance();
    $id = $settings->getId($setting);

    if ($id === false) {
        return false;
    }

    /** @var array $setting_data */
    $setting_data = $settings->getData((int) $id);

    return $setting_data;
}

/**
 * @phpcs:disable SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint
 *
 * @psalm-suppress LessSpecificReturnStatement, MoreSpecificReturnType
 *
 * Gets personalize step data
 *
 * @return array{themes: array<array-key, mixed>, logos: array<array-key, mixed>, company_settings: array<array-key, mixed>, preview_email_template_id: int}
 */
function fn_onboarding_guide_get_personalize_step_data(): array
{
    $result = [];

    // Themes and logos
    $company_id = fn_get_runtime_company_id();
    /** @var \Tygh\Storefront\Storefront $storefront */
    $storefront = Tygh::$app['storefront'];

    if ($storefront) {
        /** @var \Tygh\Storefront\Repository $storefront_repository */
        $storefront_repository = Tygh::$app['storefront.repository'];
        $storefront = $storefront_repository->findById($storefront->storefront_id);

        if ($storefront) {
            $available_themes = fn_get_available_themes($storefront->theme_name);
            $themes = $available_themes['installed'];
            $current_theme_name = $available_themes['current']['theme_name'];

            $layout = Layout::instance(0, [], $storefront->storefront_id)->getList([
                'theme_name' => $storefront->theme_name,
                'is_default' => true,
            ]);
            $layout = reset($layout);

            foreach ($themes as $theme_name => &$theme) {
                $is_current = $theme_name === $current_theme_name;
                $theme['is_current'] = $is_current;
                $theme['style_id'] = $is_current ? $layout['style_id'] : reset($theme['styles'])['style_id'];

                if ($is_current) {
                    $current_style = array_filter($theme['styles'], static function (array $style) use ($theme) {
                        return $style['style_id'] === $theme['style_id'];
                    });

                    $theme['image'] = reset($current_style)['image'];
                } else {
                    $theme['image'] = $theme['screenshot'];
                }
            }

            $result['themes'] = $themes;
            $result['logos'] = fn_get_logos($company_id, $layout['layout_id'], $layout['style_id'], $storefront->storefront_id);
        }
    }

    // Company settings
    $company_settings = [
        'company_name' => [],
        'company_address' => [],
        'company_site_administrator' => [],
        'company_city' => [],
        'company_country' => [],
        'company_state' => [],
        'company_zipcode' => [],
        'company_phone' => [],
        'company_website' => [],
        'company_start_year' => []
    ];

    foreach ($company_settings as $setting => &$value) {
        $setting_data = fn_onboarding_guide_get_setting_data($setting);

        if ($setting_data) {
            $value = $setting_data;
        }
    }

    // Preview email
    /** @var \Tygh\Template\Mail\Repository $repository */
    $repository = Tygh::$app['template.mail.repository'];
    if (fn_allowed_for('ULTIMATE')) {
        $template = $repository->findActiveByCodeAndArea('create_profile', AREA);
    } else {
        $template = $repository->findActiveByCodeAndArea('apply_for_vendor_notification', AREA);
    }

    $result['company_settings'] = $company_settings;
    /** @psalm-suppress PossiblyFalseReference */
    $result['preview_email_template_id'] = $template->getId();

    return $result;
}

/**
 * Loads payment option step data
 *
 * @return array{current_payment_option_type: string, payment_options: array<string, array<array-key, mixed>>, products_available: bool}
 */
function fn_onboarding_guide_get_payment_options_step_data(): array
{
    return [
        'payment_options' => fn_onboarding_guide_get_available_payment_options(),
        'current_payment_option_type' => fn_onboarding_guide_get_current_payment_option_money_transfer_type(),
        'products_available' => fn_onboarding_guide_get_products_available()
    ];
}

/**
 * Install addon
 *
 * @param string $addon Internal addon name
 *
 * @return bool True if success, false otherwise
 */
function fn_onboarding_guide_install_addon(string $addon): bool
{
    fn_install_addon($addon);

    if (!fn_get_addon_version($addon)) {
        return false;
    }

    fn_update_addon_status($addon, ObjectStatuses::ACTIVE);

    return true;
}

/**
 * Install payment and set it into hidden status
 *
 * @param string $processor_script Processor script name
 *
 * @return void
 */
function fn_onboarding_guide_install_payment(string $processor_script)
{
    $processor_data = db_get_row(
        'SELECT processor_id, processor FROM ?:payment_processors WHERE processor_script = ?s',
        $processor_script
    );

    if (empty($processor_data)) {
        return;
    }

    if (!fn_get_payments(['processor_script' => $processor_script])) {
        fn_update_payment([
            'payment' => $processor_data['processor'],
            'processor_id' => $processor_data['processor_id'],
            'company_id' => 0,
            'status' => ObjectStatuses::DISABLED,
        ], 0);
    }
}

/**
 * Checks all available payment options and returns its
 *
 * @return array<string, array{addons: array<array-key, array<string, string>>, processors_scripts: array<string, string>, name: string, description: string, image: string}>
 */
function fn_onboarding_guide_get_available_payment_options(): array
{
    $available_options = [];

    if (fn_allowed_for('ULTIMATE')) {
        return $available_options;
    }

    $payment_options = fn_get_schema('onboarding_guide', 'payment_options_multivendor');

    foreach ($payment_options as $type => $payment_option) {
        $required_addons = [];

        foreach ($payment_option['addons'] as $addons) {
            $needle_addons = array_filter($addons, static function ($addon_status) {
                return $addon_status === ObjectStatuses::ACTIVE;
            });

            $required_addons = array_merge($required_addons, $needle_addons);
        }

        if (empty($required_addons)) {
            $available_options[(string) $type] = $payment_option;
            continue;
        }

        $is_option_available = false;

        foreach (array_keys($required_addons) as $addon_name) {
            $is_addon_available = fn_check_addon_snapshot($addon_name);

            if (!$is_addon_available) {
                $is_option_available = false;
                break;
            }

            $is_option_available = true;
        }

        if (!$is_option_available) {
            continue;
        }

        $available_options[(string) $type] = $payment_option;
    }

    return $available_options;
}

/**
 * Gets current payment option
 */
function fn_onboarding_guide_get_current_payment_option_money_transfer_type(): string
{
    $available_payment_options = fn_onboarding_guide_get_available_payment_options();

    foreach ($available_payment_options as $type => $payment_option) {
        $addon_list = $payment_option['addons'];

        $addons_available_for_current_option = false;

        foreach ($addon_list as $addons) {
            foreach ($addons as $addon => $required_status) {
                $status = Registry::get('addons.' . $addon . '.status');

                if (empty($status) && $required_status === ObjectStatuses::ACTIVE) {
                    $addons_available_for_current_option = false;
                    continue;
                }

                if ($status === $required_status || (empty($status) && $required_status === ObjectStatuses::DISABLED)) {
                    $addons_available_for_current_option = true;
                    break;
                }

                $addons_available_for_current_option = false;
            }

            if (!$addons_available_for_current_option) {
                break;
            }
        }

        $payments_available_for_current_option = true;

        if (isset($payment_option['processors_scripts'])) {
            foreach ($payment_option['processors_scripts'] as $processor_script) {
                if (fn_get_payments(['processor_script' => $processor_script])) {
                    continue;
                }
                $payments_available_for_current_option = false;
                break;
            }
        }

        if (!$addons_available_for_current_option || !$payments_available_for_current_option) {
            continue;
        }

        return $type;
    }

    return MoneyTransferTypes::TO_OWNER;
}

/**
 * @return false|int
 */
function fn_onboarding_guide_get_last_vendor_id()
{
    $params = [
        'sort_order' => 'desc',
        'sort_by' => 'id',
        'status' => ObjectStatuses::ACTIVE
    ];

    $auth = Tygh::$app['session']['auth'];

    $companies = fn_get_companies($params, $auth, 1);

    if (isset($companies[0][0]['company_id'])) {
        return (int) $companies[0][0]['company_id'];
    }

    return false;
}

/**
 * @phpcs:disable SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint
 *
 * @return array<array-key, mixed>
 */
function fn_onboarding_guide_get_last_product(): array
{
    $params = [
        'sort_by' => 'timestamp',
        'limit' => 1,
        'status' => ObjectStatuses::ACTIVE
    ];

    [$products] = fn_get_products($params);
    if (!empty($products)) {
        return reset($products);
    }

    return [];
}

/**
 * Checks is addon available
 */
function fn_onboarding_guide_is_addon_available(string $addon_id): bool
{
    return Registry::get('addons.' . $addon_id . '.status') === ObjectStatuses::ACTIVE;
}

/**
 * Checks is addon available by edition
 */
function fn_onboarding_guide_is_available(string $edition): bool
{
    return fn_allowed_for($edition);
}

/**
 * Complete step
 */
function fn_onboarding_guide_complete_step(string $step_id): void
{
    OnboardingGuide::saveStepStatus($step_id, StepStatusEnum::COMPLETED);
}

/**
 * Determines that onboarding guide has progress
 */
function fn_onboarding_guide_has_progress(): bool
{
    $progress = OnboardingGuide::calculateProgress();
    return $progress['completed_steps'] > 0;
}

/**
 * @phpcs:disable SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint
 *
 * @psalm-suppress LessSpecificReturnStatement, MoreSpecificReturnType
 *
 * Gets manage product catalog step data
 *
 * @return array{products_available: bool}
 */
function fn_onboarding_guide_get_manage_product_step_data(): array
{
    $result = [];

    $result['products_available'] = fn_onboarding_guide_get_products_available();

    return $result;
}

/**
 * Gets products available flag
 */
function fn_onboarding_guide_get_products_available(): bool
{
    return db_get_field('SELECT product_id FROM ?:products WHERE status = ?s LIMIT 1', ObjectStatuses::ACTIVE) > 0;
}

/**
 * Determines that onboarding guide is dismissed
 */
function fn_onboarding_guide_is_dismissed(): bool
{
    return OnboardingGuide::isDismissed();
}
