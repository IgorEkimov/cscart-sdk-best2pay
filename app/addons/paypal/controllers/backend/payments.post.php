<?php

use Tygh\Enum\Addons\Paypal\Processors;
use Tygh\Registry;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

/**
 * @var string $mode
 * @var string $action
 * @var array $auth
 */
if ($mode === 'processor') {
    $processor_id = null;
    if (isset($_REQUEST['processor_id'])) {
        $processor_id = $_REQUEST['processor_id'];
    } elseif (isset($_REQUEST['payment_id'])) {
        $payment = fn_get_payment_method_data($_REQUEST['payment_id']);
        if (isset($payment['processor_id'])) {
            $processor_id = $payment['processor_id'];
        }
    }

    $is_paypal_processor = false;
    if ($processor_id !== null) {
        $is_paypal_processor = fn_is_paypal_processor($processor_id);
    }

    if ($is_paypal_processor) {
        /** @var string $processor_script */
        $processor_script = db_get_field(
            'SELECT processor_script FROM ?:payment_processors'
            . ' WHERE processor_id = ?i',
            $processor_id
        );

        /** @var array $script_to_type_map */
        $script_to_type_map = Processors::getAllWithTypes();

        if(isset($script_to_type_map[$processor_script])) {
            $type = $script_to_type_map[$processor_script];
        } else {
            $type = null;
        }

        $paypal_currencies = fn_paypal_get_currencies($type);

        /** @var \Tygh\SmartyEngine\Core $view */
        $view = Tygh::$app['view'];

        $view->assign('paypal_currencies', $paypal_currencies);
    }
}
