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

namespace Tygh\Enum\Addons\OnboardingGuide;

class StepStatusEnum
{
    const ACTIVE = 'A';
    const OPEN = 'O';
    const COMPLETED = 'C';
    const CLOSED = 'X';

    /**
     * @return string[]
     */
    public static function getValues(): array
    {
        return [self::ACTIVE, self::OPEN, self::COMPLETED, self::CLOSED];
    }

    /**
     * Determines that status is valid
     */
    public static function hasStatus(string $status): bool
    {
        return in_array($status, self::getValues());
    }

    /**
     * Determines that status is completed
     */
    public static function isCompleted(string $status): bool
    {
        return in_array($status, [self::COMPLETED, self::CLOSED]);
    }
}
