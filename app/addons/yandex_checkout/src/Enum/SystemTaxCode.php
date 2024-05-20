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

namespace Tygh\Addons\YandexCheckout\Enum;

/**
 * Class SystemTaxCode contains possible values for the `system_tax_code` API request field.
 *
 * @package Tygh\Addons\YandexCheckout\Enum
 */
class SystemTaxCode
{
    public const OSN = 1;
    public const USN_INCOME = 2;
    public const USN_INCOME_OUTCOME = 3;
    public const ENVD = 4;
    public const ESN = 5;
    public const PATENT = 6;

    /**
     * Returns all tax systems with descriptions.
     *
     * @return array<int, string>
     */
    public static function getAll(): array
    {
        return [
            self::OSN                => __('rus_taxes.tax_system.osn'),
            self::USN_INCOME         => __('rus_taxes.tax_system.usn_income'),
            self::USN_INCOME_OUTCOME => __('rus_taxes.tax_system.usn_income_outcome'),
            self::ENVD               => __('rus_taxes.tax_system.envd'),
            self::ESN                => __('rus_taxes.tax_system.esn'),
            self::PATENT             => __('rus_taxes.tax_system.patent'),
        ];
    }
}
