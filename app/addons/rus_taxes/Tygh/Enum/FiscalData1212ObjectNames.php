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

/**
 * FiscalData1212ObjectNames contains possible values of fiscal data 1212 objects in string format.
 *
 * @package Tygh\Addons\RusTaxes\Enum
 */
class FiscalData1212ObjectNames
{
    const COMMODITY = 'commodity';
    const EXCISE    = 'excise';
    const JOB       = 'job';
    const SERVICE   = 'service';
    const PAYMENT   = 'payment';
}
