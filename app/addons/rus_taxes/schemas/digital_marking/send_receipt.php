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
    'variants' => [
        'via_payment_method' => [
            'description'               => __('rus_taxes.send_receipt_via_payment_method_description'),
            'all_payments'              => false,
            'allowed_processor_scripts' => []
        ],
        'dont_send' => [
            'description'               => __('rus_taxes.send_receipt_dont_send_description'),
            'all_payments'              => true,
            'allowed_processor_scripts' => []
        ]
    ],
    'types' => [
        'send_prepayment_receipt' => [],
        'send_full_payment_receipt' => [],
        'send_refund_receipt' => []
    ]
];
