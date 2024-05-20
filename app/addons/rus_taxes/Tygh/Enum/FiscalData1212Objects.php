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

namespace Tygh\Enum;

use ReflectionClass;

/**
 * FiscalData1212Objects contains possible values for fiscal data tag 1212 objects.
 *
 * @package Tygh\Addons\RusTaxes\Enum
 */
class FiscalData1212Objects
{
    const COMMODITY = 1;
    const EXCISE = 2;
    const JOB = 3;
    const SERVICE = 4;
    const PAYMENT = 10;
    const EXCISABLE_WITH_MARKING_CODE = 31;
    const WITH_MARKING_CODE = 33;

    /**
     * Gets list of product payment subjects tag 1212 that define marked products.
     *
     * @return array<int>
     */
    public static function getMarkedObjects()
    {
        return [
            self::EXCISABLE_WITH_MARKING_CODE,
            self::WITH_MARKING_CODE
        ];
    }

    /**
     * Checks whether payment subject tag 1212 is one of defining marked products.
     *
     * @param int|string $fiscal_data_1212 Product payment subject tag 1212
     *
     * @return bool
     */
    public static function isMarkedObject($fiscal_data_1212)
    {
        return in_array((int) $fiscal_data_1212, [self::EXCISABLE_WITH_MARKING_CODE, self::WITH_MARKING_CODE]);
    }

    /**
     * Returns all possible values.
     *
     * @return int[]
     */
    public static function getAll(): array
    {
        $refl = new ReflectionClass(FiscalData1212Objects::class);
        return $refl->getConstants();
    }

    /**
     * Checks if a value exists.
     *
     * @param int $value Check value
     */
    public static function isExists(int $value): bool
    {
        return in_array($value, self::getAll());
    }
}
