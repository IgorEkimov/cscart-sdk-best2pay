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

use Tygh\Enum\MoneyTransferTypes;
use Tygh\Enum\ObjectStatuses;

defined('BOOTSTRAP') or die('Access denied');

if (defined('PRODUCT_BUILD') && PRODUCT_BUILD === 'RU') {
    return [
        MoneyTransferTypes::TO_OWNER => [
            'addons'      => [
                [
                    'direct_payments' => ObjectStatuses::DISABLED,
                ],
                [
                    'yandex_checkout' => ObjectStatuses::DISABLED,
                ],
                [
                    'tinkoff_multiparty' => ObjectStatuses::DISABLED,
                ]
            ],
            'name'        => 'onboarding_guide.single_payment_to_you',
            'description' => 'onboarding_guide.single_payment_to_you_description',
            'image'       => 'addons/onboarding_guide/single_payment_to_owner.svg'
        ],
        MoneyTransferTypes::SPLIT => [
            'addons'             => [
                [
                    'direct_payments' => ObjectStatuses::DISABLED,
                ],
                [
                    'rus_taxes' => ObjectStatuses::ACTIVE,
                ],
                [
                    'vendor_plans' => ObjectStatuses::ACTIVE,
                ],
                //if array, it means that one of the addons (in array) must be active or disabled
                [
                    'yandex_checkout'    => ObjectStatuses::ACTIVE,
                    'tinkoff_multiparty' => ObjectStatuses::ACTIVE
                ]
            ],
            'processors_scripts' => [
                'yandex_checkout'    => 'yandex_checkout_for_marketplaces.php',
                'tinkoff_multiparty' => 'tinkoff_multiparty.php'
            ],
            'name'               => 'onboarding_guide.single_payment_with_automatic_split',
            'description'        => 'onboarding_guide.single_rus_payment_with_automatic_split_description',
            'image'              => 'addons/onboarding_guide/single_payment_with_split.svg'
        ],
        MoneyTransferTypes::TO_VENDOR => [
            'addons'      => [
                [
                    'yandex_checkout' => ObjectStatuses::DISABLED,
                ],
                [
                    'tinkoff_multiparty' => ObjectStatuses::DISABLED,
                ],
                [
                    'direct_payments' => ObjectStatuses::ACTIVE,
                ]
            ],
            'name'        => 'onboarding_guide.separate_payments_to_sellers',
            'description' => 'onboarding_guide.separate_payments_to_sellers_description',
            'image'       => 'addons/onboarding_guide/separate_payments_to_sellers.svg'
        ],
    ];
}

return [
    MoneyTransferTypes::TO_OWNER  => [
        'addons'      => [
            [
                'direct_payments' => ObjectStatuses::DISABLED,
            ],
            [
                'paypal_commerce_platform' => ObjectStatuses::DISABLED,
            ],
            [
                'stripe_connect' => ObjectStatuses::DISABLED,
            ]
        ],
        'name'        => 'onboarding_guide.single_payment_to_you',
        'description' => 'onboarding_guide.single_payment_to_you_description',
        'image'       => 'addons/onboarding_guide/single_payment_to_owner.svg'
    ],
    MoneyTransferTypes::SPLIT     => [
        'addons'             => [
            [
                'direct_payments' => ObjectStatuses::DISABLED,
            ],
            [
                'vendor_plans' => ObjectStatuses::ACTIVE,
            ],
            //if array, it means that one of the addons (in array) must be active or disabled
            [
                'paypal_commerce_platform' => ObjectStatuses::ACTIVE,
                'stripe_connect'           => ObjectStatuses::ACTIVE
            ]
        ],
        'processors_scripts' => [
            'paypal_commerce_platform' => 'paypal_commerce_platform.php',
            'stripe_connect'           => 'stripe_connect.php'
        ],
        'name'               => 'onboarding_guide.single_payment_with_automatic_split',
        'description'        => 'onboarding_guide.single_payment_with_automatic_split_description',
        'image'              => 'addons/onboarding_guide/single_payment_with_split.svg'
    ],
    MoneyTransferTypes::TO_VENDOR => [
        'addons'      => [
            [
                'paypal_commerce_platform' => ObjectStatuses::DISABLED,
            ],
            [
                'stripe_connect' => ObjectStatuses::DISABLED,
            ],
            [
                'direct_payments' => ObjectStatuses::ACTIVE,
            ]
        ],
        'name'        => 'onboarding_guide.separate_payments_to_sellers',
        'description' => 'onboarding_guide.separate_payments_to_sellers_description',
        'image'       => 'addons/onboarding_guide/separate_payments_to_sellers.svg'
    ],
];
