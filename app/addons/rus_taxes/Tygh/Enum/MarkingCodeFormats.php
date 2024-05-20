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
 * MarkingCodeFormats contains values of marking code formats.
 *
 * @package Tygh\Addons\RusTaxes\Enum
 */
class MarkingCodeFormats
{
    const GS1M = 'gs1m';
    const FUR  = 'fur';

    /**
     * Returns all possible values.
     *
     * @return int[]
     */
    public static function getAll(): array
    {
        $refl = new ReflectionClass(self::class);
        return $refl->getConstants();
    }

    /**
     * Checks if a value exists.
     *
     * @param string $value Check value
     */
    public static function isExists(string $value): bool
    {
        return in_array($value, self::getAll());
    }
}
