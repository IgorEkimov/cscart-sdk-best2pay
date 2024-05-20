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

use Tygh\Addons\YandexCheckout\Enum\ProcessorScript;
use Tygh\Addons\YandexCheckout\Enum\SystemTaxCode;
use Tygh\Addons\YandexCheckout\Payments\YandexCheckout;
use Tygh\Addons\YandexCheckout\ServiceProvider;
use Tygh\Enum\NotificationSeverity;

/** @var string $mode */
if ($mode === 'processor') {
    /** @var \Tygh\SmartyEngine\Core $view */
    $view = Tygh::$app['view'];
    $processor_data = $view->getTemplateVars('processor_data');
    $payment_id = $view->getTemplateVars('payment_id');
    if ($processor_data && $processor_data['processor_script'] === ProcessorScript::YANDEX_CHECKOUT) {
        $connected_payment_methods = [];
        if ($payment_id) {
            $processor_params = $processor_data['processor_params'];
            $client = new YandexCheckout(
                $processor_params['shop_id'],
                $processor_params['scid'],
                ServiceProvider::getReceiptService()
            );
            try {
                $connected_payment_methods = $client->getPaymentMethods();
                $connected_payment_methods = array_filter($connected_payment_methods, static function ($payment_method) {
                    return !in_array($payment_method, [
                        'apple_pay',
                        'mobile_balance',
                        'google_pay',
                        'b2b_sberbank',
                        'wechat'
                    ]);
                });
                sort($connected_payment_methods);
            } catch (Exception $exception) {
                $client->getLogger()->logException($exception);
                fn_set_notification(NotificationSeverity::ERROR, __('error'), $exception->getMessage());
            }
        }
        $view->assign([
            'payment_methods' => $connected_payment_methods,
            'yandex_tax_codes' => SystemTaxCode::getAll()
        ]);
    }
}
